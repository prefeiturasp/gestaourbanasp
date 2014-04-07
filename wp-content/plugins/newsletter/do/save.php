<?php
include '../../../../wp-load.php';

$user = NewsletterSubscription::instance()->save_profile();
NewsletterSubscription::instance()->show_message('profile', $user, NewsletterSubscription::instance()->options['profile_saved']);
