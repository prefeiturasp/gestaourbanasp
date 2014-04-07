<?php
header('Content-Type: text/html;charset=UTF-8');
include '../../../../wp-load.php';

$user_info = get_userdata(get_current_user_id());
if ($user_info->user_level < 7) die('Only the administrator can view the preview');

$module = NewsletterFeed::instance();
$email = $module->create_email($module->options, 0);

echo $email['message'];