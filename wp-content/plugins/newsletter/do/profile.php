<?php
// Patch to avoid "na" parameter to disturb the call
unset($_REQUEST['na']);
unset($_POST['na']);
unset($_GET['na']);

// This page is linked to {profile_url} tag.

include '../../../../wp-load.php';

$user = NewsletterSubscription::instance()->get_user_from_request();
if ($user == null) die('No subscriber found.');

NewsletterSubscription::instance()->show_message('profile', $user);
