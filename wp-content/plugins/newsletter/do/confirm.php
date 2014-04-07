<?php
// Patch to avoid "na" parameter to disturb the call
unset($_REQUEST['na']);
unset($_POST['na']);
unset($_GET['na']);

// This page is linked to {subscription_confirm_url} tag.

include '../../../../wp-load.php';

$user = NewsletterSubscription::instance()->confirm();
if ($user->status == 'E') {
    NewsletterSubscription::instance()->show_message('error', $user->id);
} else {
    NewsletterSubscription::instance()->show_message('confirmed', $user);
}
