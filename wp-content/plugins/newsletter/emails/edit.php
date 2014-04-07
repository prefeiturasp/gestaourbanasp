<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();


// Always required
$email_id = $_GET['id'];
$email = Newsletter::instance()->get_email($email_id, ARRAY_A);

// If there is no action we assume we are enter the first time so we populate the
// $nc->data with the editable email fields
if (!$controls->is_action()) {
    $controls->data = $email;
    if (!empty($email['preferences'])) $controls->data['preferences'] = explode(',', $email['preferences']);
    if (!empty($email['sex'])) $controls->data['sex'] = explode(',', $email['sex']);
    $email_options = unserialize($email['options']);
    if (is_array($email_options)) {
        $controls->data = array_merge($controls->data, $email_options);
    }
}

if ($controls->is_action('test') || $controls->is_action('save') || $controls->is_action('send') || $controls->is_action('editor')) {

    // If we were editing with visual editor (==0), we must read the extra <body> content
    if ($email['editor'] == 0) {
        $x = strpos($email['message'], '<body');
        if ($x !== false) {
            $x = strpos($email['message'], '>', $x);
            $email['message'] = substr($email['message'], 0, $x + 1) . $controls->data['message'] . '</body></html>';
        } else {
            $email['message'] = '<html><body>' . $controls->data['message'] . '</body></html>';
        }
    } else {
        $email['message'] = $controls->data['message'];
    }
    $email['message_text'] = $controls->data['message_text'];
    $email['subject'] = $controls->data['subject'];
    $email['track'] = $controls->data['track'];

    // Builds the extended options
    $email['options'] = array();
    $email['options']['preferences_status'] = $controls->data['preferences_status'];
    $email['options']['preferences'] = $controls->data['preferences'];
    $email['options']['sex'] = $controls->data['sex'];
    $email['options']['status'] = $controls->data['status'];
    $email['options']['wp_users'] = $controls->data['wp_users'];

    $email['options'] = serialize($email['options']);

    if (is_array($controls->data['preferences'])) $email['preferences'] = implode(',', $controls->data['preferences']);
    else $email['preferences'] = '';

    if (is_array($controls->data['sex'])) $email['sex'] = implode(',', $controls->data['sex']);
    else $email['sex'] = '';

    // Before send, we build the query to extract subscriber, so the delivery engine does not
    // have to worry about the email parameters
    if ($controls->data['status'] == 'S') {
        $query = "select * from " . $wpdb->prefix . "newsletter where status='S'";
    } else {
        $query = "select * from " . $wpdb->prefix . "newsletter where status='C'";
    }

    if ($controls->data['wp_users'] == '1') {
        $query .= " and wp_user_id<>0";
    }

    $preferences = $controls->data['preferences'];
    if (is_array($preferences)) {

        // Not set one of the preferences specified
        if ($controls->data['preferences_status'] == 1) {
            $query .= " and (";
            foreach ($preferences as $x) {
                $query .= "list_" . $x . "=0 or ";
            }
            $query = substr($query, 0, -4);
            $query .= ")";
        }
        else {
            $query .= " and (";
            foreach ($preferences as $x) {
                $query .= "list_" . $x . "=1 or ";
            }
            $query = substr($query, 0, -4);
            $query .= ")";
        }
    }

    $sex = $controls->data['sex'];
    if (is_array($sex)) {
        $query .= " and sex in (";
        foreach ($sex as $x) {
            $query .= "'" . $x . "', ";
        }
        $query = substr($query, 0, -2);
        $query .= ")";
    }

    $email['query'] = $query;
    if ($controls->is_action('test')) {
        $email['total'] = 0;
    } else {
        $email['total'] = $wpdb->get_var(str_replace('*', 'count(*)', $query));
    }
    $email['sent'] = 0;
    $email['last_id'] = 0;
    $email['send_on'] = $controls->data['send_on'];

    if ($controls->is_action('editor')) {
        $email['editor'] = $email['editor'] == 0?1:0;
    }
    
    // Cleans up of tag
    $email['message'] = NewsletterModule::clean_url_tags($email['message']);

    $res = Newsletter::instance()->save_email($email);
    if ($res === false) {
        $controls->errors = 'Unable to save. Try to deactivate and reactivate the plugin may be the database is out of sync.';
    }

    $controls->data['message'] = $email['message'];

    $controls->messages .= 'Saved.<br>';
}

if ($controls->is_action('send')) {

    $wpdb->update($wpdb->prefix . 'newsletter_emails', array('status' => 'sending'), array('id' => $email_id));
    $email['status'] = 'sending';
    $controls->messages .= "Email added to the queue.";
}

if ($controls->is_action('pause')) {
    $wpdb->update($wpdb->prefix . 'newsletter_emails', array('status' => 'paused'), array('id' => $email_id));
    $email['status'] = 'paused';
}

if ($controls->is_action('continue')) {
    $wpdb->update($wpdb->prefix . 'newsletter_emails', array('status' => 'sending'), array('id' => $email_id));
    $email['status'] = 'sending';
}

if ($controls->is_action('abort')) {
    $wpdb->query("update " . $wpdb->prefix . "newsletter_emails set last_id=0, total=0, sent=0, status='new' where id=" . $email_id);
    $email['status'] = 'new';
    $email['total'] = 0;
    $email['sent'] = 0;
    $email['last_id'] = 0;
    $controls->messages = "Sending aborted.";
}

if ($controls->is_action('test')) {
    $users = NewsletterUsers::instance()->get_test_users();
    if (count($users) == 0) {
        $controls->errors = 'There are no test subscribers. Read more about test subscribers <a href="http://www.satollo.net/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
    } else {
        Newsletter::instance()->send(Newsletter::instance()->get_email($email_id), $users);
        $controls->messages .= 'Test emails sent to ' . count($users) . ' test subscribers. Read more about test subscribers <a href="http://www.satollo.net/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
    }
}


if ($email['editor'] == 0) {
    $x = strpos($controls->data['message'], '<body');
    // Some time the message in $nc->data is already cleaned up, it depends on action called
    if ($x !== false) {
        $x = strpos($controls->data['message'], '>', $x);
        $y = strpos($controls->data['message'], '</body>');

        $controls->data['message'] = substr($controls->data['message'], $x + 1, $y - $x - 1);
    }
}

?>

<script type="text/javascript" src="<?php echo plugins_url('newsletter'); ?>/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
    tinyMCE.init({
        mode : "specific_textareas",
        editor_selector : "visual",
        theme : "advanced",
        plugins: "table,fullscreen,legacyoutput",
        theme_advanced_disable : "styleselect",
        theme_advanced_buttons1_add: "forecolor,blockquote,code",
        theme_advanced_buttons3 : "tablecontrols,fullscreen",
        relative_urls : false,
        theme_advanced_statusbar_location: "bottom",
        remove_script_host : false,
        theme_advanced_resizing : true,
        theme_advanced_toolbar_location : "top",
        document_base_url : "<?php echo get_option('home'); ?>/",
        content_css: "<?php echo plugins_url('newsletter') . '/emails/css.php?id=' . $email_id . '&' . time(); ?>"
    });

    jQuery(document).ready(function() {
        jQuery('#upload_image_button').click(function() {
            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
            return false;
        });

        window.send_to_editor = function(html) {
            imgurl = jQuery('img',html).attr('src');
            //jQuery('#upload_image').val(imgurl);
            tinyMCE.execCommand('mceInsertContent',false,'<img src="' + imgurl + '" />');
            tb_remove();
        }
    });
</script>

<div class="wrap">

    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/newsletters-module'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h5>Newsletters Module</h5>

    <h2>Edit Newsletter</h2>
    <?php
    if ($controls->data['status'] == 'S') {
        echo '<div class="newsletter-message">Warning! This email is configured to be sent to NOT CONFIRMED subscribers.</div>';
    }
    ?>

    <?php $controls->show(); ?>

    <form method="post" action="" id="newsletter-form">
        <?php $controls->init(); ?>

        <p class="submit">
            <?php if ($email['status'] != 'sending') $controls->button('save', 'Save'); ?>
            <?php if ($email['status'] != 'sending') $controls->button_confirm('test', 'Save and test', 'Save and send test emails to test addresses?'); ?>

            <?php if ($email['status'] == 'new') $controls->button_confirm('send', 'Send', 'Start a real delivery?'); ?>
            <?php if ($email['status'] == 'sending') $controls->button_confirm('pause', 'Pause', 'Pause the delivery?'); ?>
            <?php if ($email['status'] == 'paused') $controls->button_confirm('continue', 'Continue', 'Continue the delivery?'); ?>
            <?php if ($email['status'] != 'new') $controls->button_confirm('abort', 'Abort', 'Abort the delivery?'); ?>
            <?php $controls->button_confirm('editor', 'Save and switch to ' . ($email['editor'] == 0 ? 'HTML source' : 'visual') . ' editor', 'Sure?'); ?>
        </p>

        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Message</a></li>
                <li><a href="#tabs-2">Message (textual)</a></li>
                <li><a href="#tabs-3">Who will receive it</a></li>
                <li><a href="#tabs-4">Status</a></li>
                <!--<li><a href="#tabs-5">Documentation</a></li>-->
            </ul>


            <div id="tabs-1">
                <table class="form-table">
                    <tr valign="top">
                        <th>Subject</th>
                        <td>
                            <?php $controls->text('subject', 70); ?>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th>Message</th>
                        <td>
                            <input id="upload_image_button" type="button" value="Choose or upload an image" />
                            <?php $email['editor'] == 0 ? $controls->editor('message', 30) : $controls->textarea_fixed('message', '100%', '400'); ?>
                            <div class="hints">
                                Tags: <strong>{name}</strong> receiver name;
                                <strong>{unsubscription_url}</strong> unsubscription URL;
                                <strong>{token}</strong> the subscriber token; <strong>{profile_url}</strong> link to user subscription options page;
                                <strong>{np_aaa}</strong> user profile data named "aaa".
                            </div>
                        </td>
                    </tr>
                </table>
            </div>


            <div id="tabs-2">
                <p>
                    This is the textual version of your newsletter. If you empty it, only an HTML version will be sent but
                    is an anti-spam best practice to include a text only version.
                </p>
                <table class="form-table">
                    <tr valign="top">
                        <th>Message</th>
                        <td>
                            <?php $controls->textarea_fixed('message_text', '100%', '250'); ?>
                        </td>
                    </tr>
                </table>
            </div>


            <div id="tabs-3">
                <table class="form-table">
                    <tr valign="top">
                        <th>Approximative number of receivers</th>
                        <td>
                            <?php
                            echo $wpdb->get_var(str_replace('*', 'count(*)', $email['query']));
                            ?>
                            <div class="hints">
                            If you change selections below, save the email to update this values.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Sex</th>
                        <td>
                            <?php $controls->checkboxes_group('sex', array('f'=>'Women', 'm'=>'Men', 'n'=>'Not specified')); ?>
                            <div class="hints">
                                Leaving all sex options unselected means to NOT filter by sex.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Preferences</th>
                        <td>
                            Subscribers with at least one preference
                            <?php $controls->select('preferences_status', array(0=>'ACTIVE', 1=>'NOT ACTIVE')); ?>
                            between the selected ones below:

                            <?php $controls->preferences_group('preferences', true); ?>
                            <div class="hints">
                                You can address the newsletter to subscribers who selected at least one of the options or to who
                                has not selected at least one of the options.
                                <a href="http://www.satollo.net/plugins/newsletter/newsletter-preferences" target="_blank">Read more about the "NOT ACTIVE" usage</a>.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Track clicks and opening?</th>
                        <td>
                            <?php $controls->yesno('track'); ?>
                            <div class="hints">
                                When this option is enabled, each link in the email text will be rewritten and clicks
                                on them intercepted.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Status</th>
                        <td>
                            <?php $controls->select('status', array('C'=>'Confirmed', 'S'=>'Not confirmed')); ?>

                            <div class="hints">
                                <strong>Warning! Use this option with care!</strong>
                                <br>
                                You should NEVER send emails to not confirmed subscribers, but if you need to send them
                                an email to ask for confirmation, you can use this option.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Registered users?</th>
                        <td>
                            <?php $controls->yesno('wp_users'); ?>

                            <div class="hints">
                                Limit to the subscribers which are WordPress users as well.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>


            <div id="tabs-4">
                <table class="form-table">
                    <tr valign="top">
                        <th>Send on</th>
                        <td>
                            <?php $controls->datetime('send_on'); ?> (<?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format')); ?> )
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Email status</th>
                        <td><?php echo $email['status']; ?></td>
                    </tr>
                    <tr valign="top">
                        <th>Email sent</th>
                        <td><?php echo $email['sent']; ?> of <?php echo $email['total']; ?></td>
                    </tr>
                    <tr valign="top">
                        <th>Query</th>
                        <td><?php echo $email['query']; ?></td>
                    </tr>
                </table>
            </div>

            <!--
            <div id="tabs-5">
                <p>Tags documented below can be used on newsletter body. Some of them can be used on subject as well.</p>

                <p>
                    Special tags, like the preference setting tag, can be used to highly interact with your subscribers, see
                    the Newsletter Preferences page for examples.
                </p>
                --

                <dl>
                    <dt>{set_preference_N}</dt>
                    <dd>
                        This tag creates a URL which, once clicked, set the preference numner N on the user profile and redirecting the
                        subscriber to his profile panel. Preferences can be configured on Subscription/Form fields panel.
                    </dd>
                </dl>

                </ul>
            </div>
            -->

        </div>

    </form>
</div>
