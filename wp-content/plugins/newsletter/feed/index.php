<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = NewsletterFeed::instance();
$controls = new NewsletterControls();

if (!$controls->is_action()) {
    $controls->data = $module->options;
}
else {

    if ($controls->is_action('reset')) {
        $controls->data = $module->reset_options();
        $controls->messages = 'Options restored.';
    }

    if ($controls->is_action('save')) {
        $controls->data['add_new'] = $controls->data['subscription'] == 2?1:0;
        if (!is_numeric($controls->data['max_posts'])) $controls->data['max_posts'] = 10;
        $module->save_options($controls->data);
    }

    if ($controls->is_action('add_all')) {
        $result = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set feed=1 where feed=0");
        $controls->messages = $result . ' subscribers has been activated.';
    }

    if ($controls->is_action('remove_all')) {
        $result = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set feed=0 where feed=1");
        $controls->messages = $result . ' subscribers has been deactivated.';
    }

    if ($controls->is_action('test')) {
        $users = NewsletterUsers::instance()->get_test_users();
        if (empty($users)) {
            $controls->errors = 'There are no test subscribers. Read more about test subscribers <a href="http://www.satollo.net/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
        }
        else {
            $email = $module->create_email($controls->data);
            Newsletter::instance()->send($email, $users);

            $controls->messages = 'Test email sent to: ';
            foreach ($users as &$user) $controls->messages .= $user->email . ' ';
        }

    }

    if ($controls->is_action('delete')) {
        $wpdb->query("delete from " . $wpdb->prefix . "newsletter_emails where id=" . $_POST['btn']);
    }

    if ($controls->is_action('reset_time')) {
        $module->save_last_run(0);
        $controls->messages = 'Reset. On next run all posts are considered as new';
    }

    if ($controls->is_action('back_time')) {
        $module->add_to_last_run(-3600*24);
        $controls->messages = 'Set.';
    }

    if ($controls->is_action('forward_time')) {
        $module->add_to_last_run(3600*24);
        $controls->messages = 'Set.';
    }

    if ($controls->is_action('now_time')) {
        $module->save_last_run(time());
        $controls->messages = 'Set.';
    }
}

?>

<div class="wrap">

    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h5>Feed by Mail (Demo)</h5>

    <h2>Main configuration</h2>

    <?php $controls->show(); ?>

    <div class="preamble">
    <p>
        <strong>This is a demo version of the real <a href="http://www.satollo.net/plugins/newsletter/feed-by-mail-module" target="_blank">Feed by Mail</a> module.</strong>
    </p>
    <p>
        Anyway, options saved on this panel will be preserved if you install the real module and you can already offer to
        subscribers the option to opt-in this service.
    </p>
    <p>
        If you don't want to see this panel on menu, disable it on Welcome panel.
    </p>
    </div>

<form method="post" action="">
    <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-1">Configuration</a></li>
                <li><a href="#tabs-2">Theme options</a></li>
                <li><a href="#tabs-3">Preview</a></li>
                <li><a href="#tabs-4">New posts</a></li>
                <li><a href="#tabs-5">Emails</a></li>
                <li><a href="#tabs-6">Actions and statistics</a></li>
            </ul>

            <div id="tabs-1">

    <table class="form-table">
        <tr valign="top">
            <th>Enabled?</th>
            <td>
                <?php $controls->yesno('enabled'); ?>
                <div class="hints">
                    When disabled, no emails will be sent but subscription to this service will continue to work.
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th>Service name</th>
            <td>
                <?php $controls->text('name', 50); ?>
                <div class="hints">
                    This name is shown on subscription and profile forms so user can subscribe or unsubscribe from it.
                </div>
            </td>
        </tr>

        <tr>
            <th>On subscription...</th>
            <td>
                <?php $controls->select('subscription', array(0=>'Do nothing', 1=>'Show this service option', 2=>'Add it to every new subscriber')); ?>
                <div class="hints">
                    This setting is valid even if the service is disabled, so users that subscribe will be added to the feed by mail service
                    but no feed by mail email will be sent.
                </div>
            </td>
        </tr>

        <tr valign="top">
            <th>Days</th>
            <td>
                Monday&nbsp;<?php $controls->yesno('day_1'); ?>
                Tuesday&nbsp;<?php $controls->yesno('day_2'); ?>
                Wednesday&nbsp;<?php $controls->yesno('day_3'); ?>
                Thursday&nbsp;<?php $controls->yesno('day_4'); ?>
                Friday&nbsp;<?php $controls->yesno('day_5'); ?>
                Saturday&nbsp;<?php $controls->yesno('day_6'); ?>
                Sunday&nbsp;<?php $controls->yesno('day_7'); ?>
            </td>
        </tr>

        <tr valign="top">
            <th>Delivery hour</th>
            <td>
                <?php $controls->hours('hour'); ?>
            </td>
        </tr>

        <tr valign="top">
            <th>Max posts to extract</th>
            <td>
                <?php $controls->text('max_posts', 5); ?>
            </td>
        </tr>

        <tr valign="top">
            <th>Categories to EXCLUDE</th>
            <td>
                <?php $controls->categories(); ?>
            </td>
        </tr>

        <tr valign="top">
            <th>Track link clicks?</th>
            <td>
                <?php $controls->yesno('track'); ?>
            </td>
        </tr>

        <tr valign="top">
            <th>Subject</th>
            <td>
                <?php $controls->text('subject', 50); ?>
                <div class="hints">
                    The subject of emails sent. If you leave it empty, the last post title is used. You can use the Newsletter tags.
                </div>
            </td>
        </tr>
    </table>
            </div>


            <div id="tabs-2">
                <table class="form-table">
                    <tr valign="top">
                        <th>Theme</th>
                        <td>
                            <?php $controls->select('theme', $module->themes->get_all()); ?>
                            (save to load the new theme options and update the preview)
                            <?php //$controls->button('theme_change', 'Change'); ?>

                            <div class="hints">
                                Send a test to see the theme layout. Custom themes are stored on wp-content/plugins/newsletter-custom/themes-feed.
                            </div>
                        </td>
                    </tr>
                </table>

                <?php
                $file = $module->themes->get_file_path($controls->data['theme'], 'theme-options.php');
                if (is_file($file)) {
                    require $file;
                }
                ?>
            </div>


            <div id="tabs-3">
                <div class="tab-preamble">
                    <p>
                        This is only a preview to see how the theme will generate emails, it's not the actual email that will be sent
                        next time.
                    </p>
                </div>
                <iframe src="<?php echo plugins_url('newsletter'); ?>/feed/preview.php?<?php echo time(); ?>" width="100%" height="700"></iframe>
            </div>


            <div id="tabs-4">
                <div class="tab-preamble">
                    <p>
                        Posts below are the one will be included on next email (scheduled future posts are not counted so
                        more posts could be included).
                    </p>
                </div>
                <table class="form-table">
                    <tr valign="top">
                        <th>Last run</th>
                        <td>
                            <?php echo $module->date($module->get_last_run()); ?>
                            <?php $controls->button_confirm('reset_time', 'Reset as it never ran', 'Are you sure?'); ?>
                            <?php $controls->button('back_time', 'Back one day'); ?>
                            <?php $controls->button('forward_time', 'Forward one day'); ?>
                            <?php $controls->button('now_time', 'Set to now'); ?>
                            <div class="hints">
                                Moving the last run, you can include or exclude posts on next message. See the list below.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>New posts from last run</th>
                        <td>
                            <?php
                            global $post;
                            $posts = $module->get_posts();
                            list($new_posts, $old_posts) = $module->split_posts($posts, $module->get_last_run());
                            foreach ($new_posts as $post) {
                                setup_postdata($post);
                                ?>
                                [<?php echo the_ID(); ?>] <?php echo $module->date($module->m2t($post->post_date_gmt)); ?> <a target="_blank" href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a><br />
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </div>


            <div id="tabs-5">

                <div class="tab-preamble">
                    <p>
                        <strong>No emails will be generated by this demo module.</strong>
                    </p>
                </div>

                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                            <tr>
                                <td>66</td>
                                <td>This week updates!</td>
                                <td>2013-02-23</td>
                                <td>
                                    sent (2356/2356)
                                </td>
                                <td><a class="button" href="#">Statistics</a></td>
                            </tr>
                    </tbody>
                </table>

            </div>


            <div id="tabs-6">
                <?php
                $total_feed = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where feed=1 and status='C'");
                $total = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C'");
                ?>
                <div class="tab-preamble">
                    <p>
                        Here you can run some massive actions on subscribers. These button works really!
                    </p>
                </div>
                <?php $controls->button_confirm('add_all', 'Add this service to all subscribers', 'Proceed?'); ?>
                <?php $controls->button_confirm('remove_all', 'Remove this service from all subscribers', 'Proceed?'); ?>

                <h3>Statistics</h3>
                <p>
                Active subscribers: <?php echo $total_feed; ?> of <?php echo $total; ?>
                </p>
            </div>

        </div>

    <div class="newsletter-buttons newsletter-buttons-bottom">
        <?php $controls->button('save', 'Save'); ?>
        <?php $controls->button('reset', 'Reset'); ?>
        <?php $controls->button('test', 'Test'); ?>
    </div>


</form>


</div>