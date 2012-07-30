<?php
// Silence is golden.

function ml_clean_siteurl($url) {
    $url="http://".$_SERVER[HTTP_HOST];
    return $url;
}
add_filter('option_siteurl', 'ml_clean_siteurl');

?>
