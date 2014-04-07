<?php

include '../../../../wp-load.php';

list($email_id, $user_id, $url, $anchor) = explode(';', base64_decode($_GET['r']), 4);
$wpdb->insert(NEWSLETTER_STATS_TABLE,
    array(
        'email_id' => $email_id,
        'user_id' => $user_id,
        'url' => $url,
        'anchor' => $anchor
    )
);

header('Location: ' . $url);
