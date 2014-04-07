<?php
/**
 * This is a generic viewer for sent emails. It is not binded to one shot emails, it can display even the emails from
 * updates or feed by mail module.
 */
include '../../../../wp-load.php';

// TODO: Change to Newsletter::instance()->get:email(), not urgent
$email = Newsletter::instance()->get_email((int)$_GET['id']);
if (empty($email)) die('Email not found');

$user = NewsletterSubscription::instance()->get_user_from_request();

if (is_file(WP_CONTENT_DIR . '/extensions/newsletter/view.php')) {
  include WP_CONTENT_DIR . '/extensions/newsletter/view.php';
  die();
}

echo $newsletter->replace($email->message, $user);
?>