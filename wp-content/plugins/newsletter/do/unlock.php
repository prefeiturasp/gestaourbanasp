<?php
// Patch to avoid "na" parameter to disturb the call
unset($_REQUEST['na']);
unset($_POST['na']);
unset($_GET['na']);

include '../../../../wp-load.php';

$user = NewsletterSubscription::instance()->check_user();

if ($user == null || $user->status != 'C') {
  echo 'Subscriber not found, sorry.';
  die();
}

$options_main = get_option('newsletter_main', array());

setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');

header('Location: ' . $options_main['lock_url']);

die();
