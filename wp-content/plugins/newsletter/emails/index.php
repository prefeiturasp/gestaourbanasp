<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();

if ($controls->is_action('convert')) {
    $module->convert_old_emails();
    $controls->messages = 'Converted!';
}

if ($controls->is_action('unconvert')) {
    $wpdb->query("update wp_newsletter_emails set type='email' where type='message'");
    $controls->messages = 'Unconverted!';
}

if ($controls->is_action('send')) {
    $newsletter->hook_newsletter();
    $controls->messages .= 'Delivery engine triggered.';
}

if ($controls->is_action('copy')) {
    $original = Newsletter::instance()->get_email($_POST['btn']);
    $email = array();
    $email['subject'] = $original->subject;
    $email['message'] = $original->message;
    $email['message_text'] = $original->message_text;
    $email['send_on'] = time();
    $email['type'] = 'message';
    Newsletter::instance()->save_email($email);
    $controls->messages .= 'Message duplicated.';
}

if ($controls->is_action('delete')) {
    Newsletter::instance()->delete_email($_POST['btn']);
    $controls->messages .= 'Message deleted';
}

if ($controls->is_action('delete_selected')) {
    $r = Newsletter::instance()->delete_email($_POST['ids']);
    $controls->messages .= $r . ' message(s) deleted';
}

$emails = Newsletter::instance()->get_emails('message');
?>

<div class="wrap">

    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/newsletters-module'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h5>Newsletters Module</h5>

    <h2>Newsletter List</h2>

    <div class="preamble">
        <p>Here you can manage your messages: compose, deliver, monitor.</p>
    </div>

    <?php $controls->show(); ?>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <?php if ($module->has_old_emails()) { ?>
            <div class="newsletter-message">
                <p>
                    Your Newsletter installation has emails still in old format. To get them listed, you should convert them in
                    a new format. Would you to convert them now?
                </p>
                <p>
                    <?php $controls->button('convert', 'Convert now'); ?>
                    <?php //$controls->button('unconvert', 'Unconvert (DEBUG)'); ?>
                </p>
            </div>
        <?php } ?>

        <p>
            <a href="<?php echo $module->get_admin_page_url('new'); ?>" class="button">New message</a>
            <?php $controls->button_confirm('delete_selected', 'Delete selected messages', 'Proceed?'); ?>
            <?php $controls->button('send', 'Trigger the delivery engine'); ?>
        </p>
        <table class="widefat" style="width: auto">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Id</th>
                    <th>Subject</th>
                    
                    <th>Status</th>
                    <th>Progress<sup>*</sup></th>
                    <th>Date</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($emails as &$email) { ?>
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="<?php echo $email->id; ?>"/></td>
                        <td><?php echo $email->id; ?></td>
                        <td><?php echo htmlspecialchars($email->subject); ?></td>
                        
                        <td>
                            <?php
                            if ($email->status == 'sending') {
                                if ($email->send_on > time()) {
                                    echo 'planned';
                                }
                                else {
                                    echo 'sending';
                                }
                            } else  {
                                echo $email->status;
                            }
                            ?>
                        </td>
                        <td><?php if ($email->status == 'sent' || $email->status == 'sending')echo $email->sent . ' of ' . $email->total; ?></td>
                        <td><?php if ($email->status == 'sent' || $email->status == 'sending') echo $module->format_date($email->send_on); ?></td>
                        <td><a class="button" href="<?php echo $module->get_admin_page_url('edit'); ?>&amp;id=<?php echo $email->id; ?>">Edit</a></td>
                        <td>
                            <a class="button" href="<?php echo NewsletterStatistics::instance()->get_statistics_url($email->id); ?>">Statistics</a>
                        </td>
                        <td><?php $controls->button_confirm('copy', 'Copy', 'Proceed?', $email->id); ?></td>
                        <td><?php $controls->button_confirm('delete', 'Delete', 'Proceed?', $email->id); ?></td>
                    </tr>
<?php } ?>
            </tbody>
        </table>
        <p><sup>*</sup> The expected total can change at the delivery end due to subscriptions/unsubscriptions in the meanwhile.</p>
    </form>
</div>
