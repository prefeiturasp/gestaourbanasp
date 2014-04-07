<?php

// This page is linked to {subscription_confirm_url} tag.

require_once '../../../../wp-load.php';
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

// TODO: Check the user capabilities
if (!current_user_can('administrator')) die('Only administrators allowed.');

$controls = new NewsletterControls();

if ($controls->is_action('export')) {
    NewsletterUsers::instance()->export();
}
