<?php
/**
 * Dropbox Uploader
 *
 * Copyright (c) 2009 Jaka Jancar
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Jaka Jancar [jaka@kubje.org] [http://jaka.kubje.org/]
 * @version 1.1.5
 */
class EZPZDBXUploader {
    protected $email;
    protected $password;
    protected $caCertSourceType = self::CACERT_SOURCE_SYSTEM;
    const CACERT_SOURCE_SYSTEM = 0;
    const CACERT_SOURCE_FILE = 1;
    const CACERT_SOURCE_DIR = 2;
    protected $caCertSource;
    protected $loggedIn = false;
    protected $cookies = array();

    /**
     * Constructor
     *
     * @param string $email
     * @param string|null $password
     */
    public function __construct($email, $password) {
        // Check requirements
        if (!extension_loaded('curl'))
            throw new Exception('Dropbox Extension for EZPZ OCB requires the cURL extension to be enabled on your server.');

        $this->email = $email;
        $this->password = $password;
    }

    public function setCaCertificateFile($file)
    {
        $this->caCertSourceType = self::CACERT_SOURCE_FILE;
        $this->caCertSource = $file;
    }

    public function setCaCertificateDir($dir)
    {
        $this->caCertSourceType = self::CACERT_SOURCE_DIR;
        $this->caCertSource = $dir;
    }

    public function upload($filename, $remoteDir='/') {
        if (!file_exists($filename) or !is_file($filename) or !is_readable($filename))
            throw new Exception("File '$filename' does not exist or is not readable.");

        if (!is_string($remoteDir))
            throw new Exception("Remote directory must be a string, is ".gettype($remoteDir)." instead.");

        if (!$this->loggedIn)
            $this->login();

        $data = $this->request('https://www.dropbox.com/home');
        $token = $this->extractToken($data, 'https://dl-web.dropbox.com/upload');

        $data = $this->request('https://dl-web.dropbox.com/upload', true, array('plain'=>'yes', 'file'=>'@'.$filename, 'dest'=>$remoteDir, 't'=>$token));
        if (strpos($data, 'HTTP/1.1 302 FOUND') === false)
            throw new Exception('Dropbox upload of $filename failed!');
    }

    protected function login() {
        $data = $this->request('https://www.dropbox.com/login');
 //       $token = $this->extractToken($data, '/login');

//        $data = $this->request('https://www.dropbox.com/login', true, array('login_email'=>$this->email, 'login_password'=>$this->password, 't'=>$token));

		$data = $this->request('https://www.dropbox.com/login', true, array('login_email'=>$this->email, 'login_password'=>$this->password));

        if (stripos($data, 'location: /home') === false){
            set_status('transfer', 'unset');
            throw new Exception("<p style='text-align: center;'>EZPZ OCB was unable to connect with Dropbox.<br/>Please verify the Dropbox login information under \"Extension Settings\"</p>");
		} else {
			set_status('error', 'unset');
		}

        $this->loggedIn = true;
    }

    protected function request($url, $post=false, $postData=array()) {
//        print_r ($postData);
//        echo "<br/>";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        switch ($this->caCertSourceType) {
            case self::CACERT_SOURCE_FILE:
                curl_setopt($ch, CURLOPT_CAINFO, $this->caCertSource);
                break;
            case self::CACERT_SOURCE_DIR:
                curl_setopt($ch, CURLOPT_CAPATH, $this->caCertSource);
                break;
        }
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, $post);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        // Send cookies
        $rawCookies = array();
        foreach ($this->cookies as $k=>$v)
            $rawCookies[] = "$k=$v";
        $rawCookies = implode(';', $rawCookies);
        curl_setopt($ch, CURLOPT_COOKIE, $rawCookies);

        $data = curl_exec($ch);

        if ($data === false)
            throw new Exception('Cannot execute request: '.curl_error($ch));

        // Store received cookies
        preg_match_all('/Set-Cookie: ([^=]+)=(.*?);/i', $data, $matches, PREG_SET_ORDER);
        foreach ($matches as $match)
            $this->cookies[$match[1]] = $match[2];

        curl_close($ch);

        return $data;
    }

    protected function extractToken($html, $formAction) {
        if (!preg_match('/<form [^>]*'.preg_quote($formAction, '/').'[^>]*>.*?(<input [^>]*name="t" [^>]*value="(.*?)"[^>]*>).*?<\/form>/is', $html, $matches) || !isset($matches[2]))
            throw new Exception("Cannot extract token! (form action=$formAction)");
        return $matches[2];
    }

}
?>