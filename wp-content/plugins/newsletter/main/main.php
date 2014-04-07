<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();

if (!$controls->is_action()) {
    $controls->data = get_option('newsletter_main');
} else {
    if ($controls->is_action('remove')) {

        $wpdb->query("delete from " . $wpdb->prefix . "options where option_name like 'newsletter%'");

        $wpdb->query("drop table " . $wpdb->prefix . "newsletter, " . $wpdb->prefix . "newsletter_stats, " .
                $wpdb->prefix . "newsletter_emails, " .
                $wpdb->prefix . "newsletter_work");

        echo 'Newsletter plugin destroyed. Please, deactivate it now.';
        return;
    }

    if ($controls->is_action('save')) {
        $errors = null;

        // Validation
        $controls->data['sender_email'] = $newsletter->normalize_email($controls->data['sender_email']);
        if (!$newsletter->is_email($controls->data['sender_email'])) {
            $controls->errors .= 'The sender email address is not correct.<br />';
        }

        $controls->data['return_path'] = $newsletter->normalize_email($controls->data['return_path']);
        if (!$newsletter->is_email($controls->data['return_path'], true)) {
            $controls->errors .= 'Return path email is not correct.<br />';
        }

        //$controls->data['test_email'] = $newsletter->normalize_email($controls->data['test_email']);
        //if (!$newsletter->is_email($controls->data['test_email'], true)) {
        //    $controls->errors .= 'Test email is not correct.<br />';
        //}

        $controls->data['reply_to'] = $newsletter->normalize_email($controls->data['reply_to']);
        if (!$newsletter->is_email($controls->data['reply_to'], true)) {
            $controls->errors .= 'Reply to email is not correct.<br />';
        }

        if (empty($controls->errors)) {
            update_option('newsletter_main', $controls->data);
        }
    }

    if ($controls->is_action('smtp_test')) {

        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';
        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->SMTPDebug = true;
        $mail->CharSet = 'UTF-8';
        $message = 'This Email is sent by PHPMailer of WordPress';
        $mail->IsHTML(false);
        $mail->Body = $message;
        $mail->From = $controls->data['sender_email'];
        $mail->FromName = $controls->data['sender_name'];
        if (!empty($controls->data['return_path'])) $mail->Sender = $options['return_path'];
        if (!empty($controls->data['reply_to'])) $mail->AddReplyTo($controls->data['reply_to']);

        $mail->Subject = '[' . get_option('blogname') . '] SMTP test';

        $mail->Host = $controls->data['smtp_host'];
        if (!empty($controls->data['smtp_port'])) $mail->Port = (int) $controls->data['smtp_port'];

        $mail->SMTPSecure = $controls->data['smtp_secure'];

        if (!empty($controls->data['smtp_user'])) {
            $mail->SMTPAuth = true;
            $mail->Username = $controls->data['smtp_user'];
            $mail->Password = $controls->data['smtp_pass'];
        }

        $mail->SMTPKeepAlive = true;
        $mail->ClearAddresses();
        $mail->AddAddress($controls->data['smtp_test_email']);
        ob_start();
        $mail->Send();
        $mail->SmtpClose();
        $debug = htmlspecialchars(ob_get_clean());

        if ($mail->IsError()) $controls->errors = $mail->ErrorInfo;
        else $controls->messages = 'Success.';

        $controls->messages .= '<textarea style="width:100%;height:250px;font-size:10px">';
        $controls->messages .= $debug;
        $controls->messages .= '</textarea>';
    }
}
?>

<div class="wrap">

    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/newsletter-configuration'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h2>Newsletter Main Configuration</h2>

    <?php $controls->show(); ?>

    <div class="preamble">
    <p>
        Do not be scared by all those configurations. Only <strong>basic settings</strong> are important and should be reviewed to
        make Newsletter plugin work correctly. If something doesn't work, run a test from
        <a href="admin.php?page=newsletter_main_diagnostic">diagnostic panel</a>.
    </p>
    </div>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-1">Basic settings</a></li>
                <li><a href="#tabs-2">Advanced settings</a></li>
                <li><a href="#tabs-5">SMTP</a></li>
                <li><a href="#tabs-3">Content locking</a></li>
            </ul>

            <div id="tabs-1">

                <!-- Main settings -->
                <div class="tab-preamble">
                <p>
                </p>
                </div>

                <table class="form-table">

                    <tr valign="top">
                        <th>Sender email address</th>
                        <td>
                            <?php $controls->text_email('sender_email', 40); ?> (valid email address)

                            <div class="hints">
                                Insert here the email address from which subscribers will se your email coming. Since this setting can
                                affect the reliability of delivery,
                                <a href="http://www.satollo.net/plugins/newsletter/newsletter-configuration#sender" target="_blank">read my notes here</a> (important).
                                Generally use an address within your domain name.
                            </div>
                        </td>
                    </tr>
                        <th>Sender name</th>
                        <td>
                            <?php $controls->text('sender_name', 40); ?> (optional)

                            <div class="hints">
                                Insert here the name which subscribers will see as the sender of your email (for example your blog or website's name). Since this setting can affect the reliability of delivery (usually under Windows)
                                <a href="http://www.satollo.net/plugins/newsletter/newsletter-configuration#sender" target="_blank">read my notes here</a>.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Max emails per hour</th>
                        <td>
                            <?php $controls->text('scheduler_max', 5); ?>
                            <div class="hints">
                                Newsletter delivery engine respects this limit and it should be set to a value less than the maximum allowed by your provider
                                (Hostgator: 500 per hour, Dreamhost: 100 per hour, Go Daddy: 1000 per day using their SMTP, Gmail: 500 per day).
                                Read <a href="http://www.satollo.net/plugins/newsletter/newsletter-delivery-engine" target="_blank">more on delivery engine</a> (important).
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Return path</th>
                        <td>
                            <?php $controls->text_email('return_path', 40); ?> (valid email address, default empty)
                            <div class="hints">
                                Email address where delivery error messages are sent by mailing systems (eg. mailbox full, invalid address, ...).<br>
                                Some providers do not accept this field: they can block emails or force it to a different value affecting the delivery reliability.
                                <a href="http://www.satollo.net/plugins/newsletter/newsletter-configuration#return-path" target="_blank">Read my notes here</a> (important).
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Reply to</th>
                        <td>
                            <?php $controls->text_email('reply_to', 40); ?> (valid email address)
                            <div class="hints">
                                This is the email address where subscribers will reply (eg. if they want to reply to a newsletter). Leave it blank if
                                you don't want to specify a different address from the sender email above. Since this setting can
                                affect the reliability of delivery,
                                <a href="http://www.satollo.net/plugins/newsletter/newsletter-configuration#reply-to" target="_blank">read my notes here</a> (important).
                            </div>
                        </td>
                    </tr>

                </table>
            </div>

            <div id="tabs-2">

                <!-- General parameters -->

                <table class="form-table">

                    <tr valign="top">
                        <th>Enable access to editors?</th>
                        <td>
                            <?php $controls->yesno('editor'); ?>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th>API key</th>
                        <td>
                            <?php $controls->text('api_key', 40); ?>
                            <div class="hints">
                                When non-empty can be used to directly call the API for external integration. See API documentation on
                                documentation panel.
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th>Styling</th>
                        <td>
                            <?php $controls->textarea('css'); ?>
                            <div class="hints">
                                Add here your own css to style the forms. The whole form is enclosed in a div with class
                                "newsletter" and it's made with a table (guys, I know about your table less design
                                mission, don't blame me too much!)
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Email body content encoding</th>
                        <td>
                            <?php $controls->select('content_transfer_encoding', array('' => 'Default', '8bit' => '8 bit', 'base64' => 'Base 64')); ?>
                            <div class="hints">
                                Sometimes setting it to Base 64 solves problem with old mail servers (for example truncated or unformatted emails.
                                <a href="http://www.satollo.net/plugins/newsletter/newsletter-configuration#enconding" target="_blank">Read more here</a>.
                            </div>
                        </td>
                    </tr>

                </table>

            </div>


            <div id="tabs-5">
                <div class="tab-preamble">
                    <p>
                        <strong>These options can be overridden by modules which integrates with external
                        SMTPs (like MailJet, SendGrid, ...) if installed and activated.</strong>
                    </p>
                <p>
                    To use an external SMTP (mail sending service), fill in the SMTP data and activate it. SMTP will be used for any
                    messages sent by Newsletter (subscription messages and newsletters). SMTP is required to send email with Gmail or
                    GoDaddy hosting account.
                    Read more <a href="http://www.satollo.net/godaddy-using-smtp-external-server-on-shared-hosting" target="_blank">here</a>.
                    The "test" button below sends an email to the first test address configured above and works even if SMTP is not enabled. If you get a "connection refused"
                    message, check the SMTP settings if they are correct, then contact your hosting provider. If you get a "relay denied" contact your
                    SMTP service provider.
                </p>
                <p>
                    Consider <a href="http://www.satollo.net/affiliate/sendgrid" target="_blank">SendGrid</a> for a serious and reliable SMTP service.
                </p>
                </div>

                <table class="form-table">
                    <tr>
                        <th>Enable external SMTP?</th>
                        <td><?php $controls->yesno('smtp_enabled'); ?></td>
                    </tr>
                    <tr>
                        <th>SMTP host/port</th>
                        <td>
                            host: <?php $controls->text('smtp_host', 30); ?>
                            port: <?php $controls->text('smtp_port', 6); ?>
                            <?php $controls->select('smtp_secure', array('' => 'No secure protocol', 'tls' => 'TLS protocol', 'ssl' => 'SSL protocol')); ?>
                            <div class="hints">
                                Leave port empty for default value (25). To use Gmail try host "smtp.gmail.com" and port "465" and SSL protocol (without quotes).
                                For GoDaddy use "relay-hosting.secureserver.net".
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Authentication</th>
                        <td>
                            user: <?php $controls->text('smtp_user', 30); ?>
                            password: <?php $controls->text('smtp_pass', 30); ?>
                            <div class="hints">
                                If authentication is not required, leave "user" field blank.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Test email address</th>
                        <td>
                            <?php $controls->text('smtp_test_email', 30); ?>
                            <div class="hints">
                                SMTP test will be addressed to this email
                            </div>
                        </td>
                    </tr>
                </table>
                <?php $controls->button('smtp_test', 'Test'); ?>

            </div>


            <div id="tabs-3">
                <!-- Content locking -->
                <div class="tab-preamble">
                    <p>
                        Please, <a href="http://www.satollo.net/plugins/newsletter/newsletter-locked-content" target="_blank">read more here how to use and configure</a>,
                        since it can incredibly increase your subscription rate.
                    </p>
                </div>
                <table class="form-table">
                    <tr valign="top">
                        <th>Tags or categories to lock</th>
                        <td>
                            <?php $controls->text('lock_ids', 70); ?>
                            <div class="hints">
                                Use tag or category slug or id, comma separated.
                            </div>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th>Unlock destination URL</th>
                        <td>
                            <?php $controls->text('lock_url', 70); ?>
                            <div class="hints">
                                This is a web address (URL) where users are redirect when they click on unlocking URL ({unlock_url})
                                inserted in newsletters and welcome message.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Denied content message</th>
                        <td>
			<?php $controls->textarea('lock_message'); ?>
                            <?php wp_editor( $controls->data['lock_message'], 'lock_message', array('textarea_name'=>'options[lock_message]') ); ?>

                            <?php //$controls->textarea('lock_message'); ?>
                            <div class="hints">
                                This message is shown in place of protected post or page content which is surrounded with
                                [newsletter_lock] and [/newsletter_lock] short codes or in place of the full content if they are
                                in categories or have tags as specified above.<br />
                                You can use the {subscription_form} tag to display the subscription form.<br>
                                <strong>Remeber to add the {unlock_url} on the welcome email so the user can unlock the content.</strong>
                            </div>
                        </td>
                    </tr>
                </table>

            </div>


        </div> <!-- tabs -->

        <p class="submit">
            <?php $controls->button('save', 'Save'); ?>
            <?php $controls->button_confirm('remove', 'Totally remove this plugin', 'Really sure to totally remove this plugin. All data will be lost!'); ?>
        </p>

    </form>
    <p></p>
</div>
