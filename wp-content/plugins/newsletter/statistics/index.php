<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = NewsletterStatistics::instance();
$controls = new NewsletterControls();
$emails = Newsletter::instance()->get_emails();

if ($controls->is_action('save')) {
    $module->save_options($controls->data);
    $controls->messages = 'Saved.';
}
?>

<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/statistics-module'; ?>

    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h5>Statistics Module</h5>
    
    <h2>Configuration and Email List</h2>

    <p>
        This module is a core part of Newsletter that collects statistics about sent emails: how many have
        been read, how many have been clicked and so on.
    </p>
    <p>
        To see the statistics of each single email, you should click the "statistics" button
        you will find near each message where they are listed (like on Newsletters panel). For your
        convenience, below there is a list of each email sent by Newsletter till now.
    </p>
    <p>
        A more advanced report for each email can be generated installing the Advanced Statistics Module
        from <a href="http://www.satollo.net/downloads" target="_blank">this page</a>.
    </p>

    <table class="widefat" style="width: auto">
        <thead>
            <tr>
                <th>Id</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Type</th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($emails as &$email) { ?>
                <tr>
                    <td><?php echo $email->id; ?></td>
                    <td><?php echo htmlspecialchars($email->subject); ?></td>
                    <td><?php echo $email->date; ?></td>
                    <td><?php echo $email->type; ?></td>
                    <td>
                        <?php echo $email->status; ?>
                        (<?php echo $email->sent; ?>/<?php echo $email->total; ?>)
                    </td>
                    <td>
                        <a class="button" href="<?php echo NewsletterStatistics::instance()->get_statistics_url($email->id); ?>">statistics</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>    
</div>
