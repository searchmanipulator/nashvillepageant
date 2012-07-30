<?php
global $wpdb;
$updated = get_option('ezpz_ocb_auto_update');
$date = $updated['faq'];
?>
<style>
    #news-body {
        font-family: Georgia, 'Times New Roman' , 'Bitstream Charter', Times,serif;
        text-shadow:rgba(255,255,255,1) 0 1px 0;
        color:#464646;
        margin: 2px 18px 2px 20px;
        text-align: justify;
        //border: thin black solid;
    }

    .page-item{}
        
</style>

<div id="news-body">
<?php // Get RSS Feed(s)
include_once(ABSPATH . WPINC . '/feed.php');

// Get a SimplePie feed object from the specified feed source.
$rss = fetch_feed('http://ezpzsolutions.net/blog/_/category/ezpz-ocb-news/feed/');
if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly
    // Figure out how many total items there are, but limit it to 5.
    $maxitems = $rss->get_item_quantity(5);

    // Build an array of all the items, starting with element 0 (first element).
    $rss_items = $rss->get_items(0, $maxitems);
endif;
?>

<ul>
    <?php if ($maxitems == 0) echo '<li>No items.</li>';
    else
    // Loop through each feed item and display each item as a hyperlink.
    foreach ( $rss_items as $item ) : 
    $title = $item->get_title();
    $date = $item->get_date('F jS, Y');
    $content = $item->get_content(); ?>
    <li class="page-item">
        <p><?php echo $content; ?></p>
    </li>
    <?php endforeach; ?>
</ul>


</div>
<?php

?>