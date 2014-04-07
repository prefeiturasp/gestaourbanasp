<?php
// Patch to avoid "na" parameter to disturb the call
unset($_REQUEST['na']);
unset($_POST['na']);
unset($_GET['na']);

require_once '../../../../wp-load.php';

$user = NewsletterSubscription::instance()->subscribe();
if ($user->status == 'E') NewsletterSubscription::instance()->show_message('error', $user->id);
if ($user->status == 'C') NewsletterSubscription::instance()->show_message('confirmed', $user->id);
if ($user->status == 'S') NewsletterSubscription::instance()->show_message('confirmation', $user->id);