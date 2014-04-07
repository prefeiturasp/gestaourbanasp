<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();

if ($controls->is_action('save')) {
    update_option('newsletter_log_level', $controls->data['log_level']);
    update_option('newsletter_diagnostic', $controls->data);
    $controls->messages = 'Loggin levels saved.';
}

if ($controls->is_action('trigger')) {
    $newsletter->hook_newsletter();
    $controls->messages = 'Delivery engine triggered.';
}

if ($controls->is_action('undismiss')) {
    update_option('newsletter_dismissed', array());
    $controls->messages = 'Notices restored.';
}

if ($controls->is_action('trigger_followup')) {
    NewsletterFollowup::instance()->send();
    $controls->messages = 'Follow up delivery engine triggered.';
}

if ($controls->is_action('engine_on')) {
    wp_clear_scheduled_hook('newsletter');
    wp_schedule_event(time() + 30, 'newsletter', 'newsletter');
    $controls->messages = 'Delivery engine reactivated.';
}

if ($controls->is_action('upgrade')) {
    // TODO: Compact them in a call to Newsletter which should be able to manage the installed modules
    Newsletter::instance()->upgrade();
    NewsletterUsers::instance()->upgrade();
    NewsletterSubscription::instance()->upgrade();
    NewsletterEmails::instance()->upgrade();
    NewsletterStatistics::instance()->upgrade();
    $controls->messages = 'Upgrade forced!';
}

if ($controls->is_action('delete_transient')) {
    delete_transient($_POST['btn']);
    $controls->messages = 'Deleted.';
}

if ($controls->is_action('test_wp')) {

    if ($controls->data['test_email'] == $newsletter->options['sender_email']) {
        $controls->messages .= 'You are using as test email the same configured as sender email. Test can fail because that.<br />';
    }

    $text = 'This is a simple test email sent directly with the WordPress mailing functionality' . "\r\n" .
            'in the same way WordPress sends notifications of new comment or registered users.' . "\r\n\r\n" .
            'This email is in pure text format and the sender should be wordpress@youdomain.tld (but it can be forced to be different with specific plugins.';

    $r = wp_mail($controls->data['test_email'], 'Newsletter: direct WordPress email test', $text);

    if ($r) {
        $controls->messages .= 'Direct WordPress email sent<br />';
    } else {
        $controls->errors .= 'Direct WordPress email NOT sent: ask your provider if your web space is enabled to send emails.<br />';
    }
}

if ($controls->is_action('send_test')) {

    if ($controls->data['test_email'] == $controls->data['sender_email']) {
        $controls->messages .= 'You are using as test email the same configured as sender email. Test can fail because that.<br />';
    }

    $text = 'This is a pure textual email sent using the sender data set on basic Newsletter settings.' . "\r\n" .
            'You should see it to come from the email address you set on basic Newsletter plugin setting.';
    $r = $newsletter->mail($controls->data['test_email'], 'Newsletter: pure text email', array('text' => $text));


    if ($r) $controls->messages .= 'Newsletter TEXT test email sent.<br />';
    else $controls->errors .= 'Newsletter TEXT test email NOT sent: try to change the sender data, remove the return path and the reply to settings.<br />';

    $text = '<p>This is a <strong>html</strong> email sent using the <i>sender data</i> set on Newsletter main setting.</p>';
    $text .= '<p>You should see some "mark up", like bold and italic characters.</p>';
    $text .= '<p>You should see it to come from the email address you set on basic Newsletter plugin setting.</p>';
    $r = $newsletter->mail($controls->data['test_email'], 'Newsletter: pure html email', $text);
    if ($r) $controls->messages .= 'Newsletter HTML test email sent.<br />';
    else $controls->errors .= 'Newsletter HTML test email NOT sent: try to change the sender data, remove the return path and the reply to settings.<br />';


    $text = array();
    $text['html'] = '<p>This is an <b>HTML</b> test email part sent using the sender data set on Newsletter main setting.</p>';
    $text['text'] = 'This is a textual test email part sent using the sender data set on Newsletter main setting.';
    $r = $newsletter->mail($controls->data['test_email'], 'Newsletter: both textual and html email', $text);
    if ($r) $controls->messages .= 'Newsletter: both textual and html test email sent.<br />';
    else $controls->errors .= 'Newsletter both TEXT and HTML test email NOT sent: try to change the sender data, remove the return path and the reply to settings.<br />';
}

if (empty($controls->data)) $controls->data = get_option('newsletter_diagnostic');
?>
<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/newsletter-diagnostic'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h2>Diagnostic</h2>

    <?php $controls->show(); ?>

    <div class="preamble">
    <p>
        If something is not working, here are some test procedures and diagnostics. But before you try these,
        write down any modifications or configuration changes that you may have made.
        For example: Did you use sender email or name? What was the return path? What was the reply to?
    </p>
    </div>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <h3>Test</h3>
        Email: <?php $controls->text('test_email'); ?>
        <?php $controls->button('test_wp', 'Send an email with WordPress'); ?>
        <?php $controls->button('send_test', 'Send few emails with Newsletter'); ?>
        <div class="hints">
            First test emailing with WordPress if it does not work you need to contact your provider. Test on different addresses.
            <br>
            Second test emailing with Newsletter. You must receive three distinct email in different formats.
            <br>
            If the WordPress test works but Newsletter test doesn't, check the main configuration and try to change the sender,
            return path and reply to email addresses.
        </div>


        <h3>System Check and Upgrade</h3>
        <p>
            Tables below contain some system parameter that can affect Newsletter plugin working mode. When asking for support consider to
            report those values.
        </p>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-1">Logging</a></li>
                <li><a href="#tabs-2">Semaphores and Crons</a></li>
                <li><a href="#tabs-4">System</a></li>
                <li><a href="#tabs-upgrade">Maintainance</a></li>
            </ul>

            <!-- LOGGING -->
            <div id="tabs-1">

                <h4>Logging</h4>
                <p>
                    The logging feature of Newsletter, when enabled, writes detailed information of the working
                    status inside some (so called) log files. Log files, one per module, are stored inside the folder
                    <code>wp-content/logs/newsletter</code>.
                </p>

                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Active since</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                Log level
                            </td>
                            <td>
                                <?php $controls->log_level('log_level'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Log folder
                            </td>
                            <td>
                                <?php
                                $dir = WP_CONTENT_DIR . '/logs/newsletter';
                                if (!is_dir($dir)) {
                                    echo '<span class="newsletter-error-span">The log folder does not exists, no logging possible!</span>';
                                } else {
                                    echo 'The log folder exists.';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p><?php $controls->button('save', 'Save'); ?></p>
            </div>

            <!-- SEMAPHORES -->
            <div id="tabs-2">
                <h4>Semaphores</h4>
                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Active since</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                Newsletter delivery
                            </td>
                            <td>
                                <?php
                                $value = get_transient('newsletter_main_engine');
                                if ($value) echo (time() - $value) . ' seconds';
                                else echo 'Not set';
                                ?>
                                <?php $controls->button('delete_transient', 'Delete', null, 'newsletter_main_engine'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h4>Crons</h4>
                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>Function</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                WordPress Cron System
                            </td>
                            <td>
                                <?php
                                if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) echo 'DISABLED. (really bad, see <a href="http://www.satollo.net/?p=2015" target="_tab">this page)</a>';
                                else echo "ENABLED. (it's ok)";
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                WordPress schedules
                            </td>
                            <td>
                                <?php
                                $schedules = wp_get_schedules();
                                if (empty($schedules)) {
                                    echo 'Really bad, no schedules found, missing even the WordPress default schedules!';
                                } else {
                                    $found = false;

                                    foreach ($schedules as $key=>&$data) {
                                        if ($key == 'newsletter') $found = true;
                                        echo $key . ' - ' . $data['interval'] . ' s<br>';

                                     }

                                     if (!$found) {
                                         echo 'The "newsletter" schedule was not found, email delivery won\'t work.';
                                     }
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Delivery Engine
                            </td>
                            <td>
                                <?php echo NewsletterModule::format_scheduler_time('newsletter'); ?>
                                <?php $controls->button('trigger', 'Trigger now'); ?>
                                <br>
                                If inactive or always in "running now" status your blog has a problem: <a href="http://www.satollo.net/?p=2015" target="_blank">read more here</a>.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Feed by Mail
                            </td>
                            <td>
                                <?php echo NewsletterModule::format_scheduler_time('newsletter_feed'); ?>
                                <?php //$controls->button('trigger_followup', 'Trigger now'); ?>
                                <br>
                                This time is not necessarily when the email will be sent but when Feed by Mail does its check to see if
                                this is a planned day and if there is something to send.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Follow Up
                            </td>
                            <td>
                                <?php echo NewsletterModule::format_scheduler_time('newsletter_followup'); ?>
                                <br>
                                Indicates when the Follow Up system runs again (usually every hour) to check for new follow up to send out.
                                <?php //$controls->button('trigger_followup', 'Trigger now'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                SendGrid bounce checking
                            </td>
                            <td>
                                <?php echo NewsletterModule::format_scheduler_time('newsletter_sendgrid_bounce'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                MailJet bounce checking
                            </td>
                            <td>
                                <?php echo NewsletterModule::format_scheduler_time('newsletter_mailjet_bounce'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- SYSTEM -->
            <div id="tabs-4">
                <h4>System parameters</h4>

                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Database Wait Timeout</td>
                            <td>
                                <?php $wait_timeout = $wpdb->get_var("select @@wait_timeout"); ?>
                                <?php echo $wait_timeout; ?> (seconds)
                            </td>
                        </tr>
                        <tr>
                            <td>PHP Execution Time</td>
                            <td>
                                <?php echo ini_get('max_execution_time'); ?> (seconds)
                            </td>
                        </tr>
                        <tr>
                            <td>NEWSLETTER_MAX_EXECUTION_TIME</td>
                            <td>
                                <?php if (defined('NEWSLETTER_MAX_EXECUTION_TIME')) {
                                    echo NEWSLETTER_MAX_EXECUTION_TIME . 'seconds';
                                } else {
                                    echo 'Not set';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>NEWSLETTER_CRON_INTERVAL</td>
                            <td>
                                <?php echo NEWSLETTER_CRON_INTERVAL . 'seconds'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>PHP Memory Limit</td>
                            <td>
                                <?php echo @ini_get('memory_limit'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>WordPress Memory limit</td>
                            <td>
                                <?php echo WP_MEMORY_LIMIT; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Absolute path</td>
                            <td>
                                <?php echo ABSPATH; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Tables Prefix</td>
                            <td>
                                <?php echo $wpdb->prefix; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Database Charset and Collate</td>
                            <td>
                                <?php echo DB_CHARSET; ?> <?php echo DB_COLLATE; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Hook "phpmailer_init"</td>
                            <td>
                                Obsolete.<br>
                                <?php
                                $filters = $wp_filter['phpmailer_init'];
                                if (!is_array($filters)) echo 'No actions attached';
                                else {
                                    foreach ($filters as &$filter) {
                                        foreach ($filter as &$entry) {
                                            if (is_array($entry['function'])) echo get_class($entry['function'][0]) . '->' . $entry['function'][1];
                                            else echo $entry['function'];
                                            echo '<br />';
                                        }
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>File permissions</td>
                            <td>
                                <?php
                                $index_permissions = fileperms(ABSPATH . '/index.php');
                                $subscribe_permissions = fileperms(NEWSLETTER_DIR . '/do/subscribe.php');
                                if ($index_permissions != $subscribe_permissions) {
                                    echo 'Plugin file permissions differ from blog index.php permissions, that may compromise the subscription process';
                                } else {
                                    echo 'OK';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div id="tabs-upgrade">
                <p>
                    Plugin and modules are able to upgrade them self when needed. If you urgently need to try to force an upgrade, press the
                    button below.
                </p>
                <p>
                    <?php $controls->button('upgrade', 'Force an upgrade'); ?>
                </p>

                <p>
                    Restore al dismissed messages
                </p>
                <p>
                    <?php $controls->button('undismiss', 'Restore'); ?>
                </p>
            </div>
        </div>

    </form>

</div>
