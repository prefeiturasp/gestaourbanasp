<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$options_profile = get_option('newsletter_profile');
$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$lists = array('0' => 'All');
for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    $lists['' . $i] = '(' . $i . ') ' . $options_profile['list_' . $i];
}
?>

<div class="wrap">
    <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

    <h2>Export</h2

    <div class="preamble">
    <p>
        The import and export functions <strong>ARE NOT for backup</strong>. If you want to backup you should consider to backup the
        wp_newsletter* tables.
    </p>
    </div>

    <form method="post" action="<?php echo plugins_url('newsletter'); ?>/users/csv.php">
        <?php $controls->init(); ?>
        <table class="form-table">
            <tr>
                <td>
                    <?php $controls->select('list', $lists); ?>
                    <?php $controls->button('export', 'Export'); ?>
                </td>
            </tr>
        </table>
    </form>

</div>
