<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$options_profile = get_option('newsletter_profile');

$lists = array();
for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
  $lists['' . $i] = '(' . $i . ') ' . $options_profile['list_' . $i];
}

if ($controls->is_action('remove_unconfirmed')) {
  $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where status='S'");
  $controls->messages = $r . ' not confirmed deleted.';
}

if ($controls->is_action('remove_unsubscribed')) {
  $controls->messages = $r . ' unsubscribed deleted (profiles associated to WordPress users are never deleted).';
}

if ($controls->is_action('remove_bounced')) {
  $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where status='B'");
  $controls->messages = $r . ' bounced deleted.';
}

if ($controls->is_action('unconfirm_all')) {
  $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set status='S' where status='C'");
  $controls->messages = $r . ' unconfirmed.';
}

if ($controls->is_action('confirm_all')) {
  $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set status='C' where status='S'");
  $controls->messages = $r . ' confirmed.';
}

if ($controls->is_action('remove_all')) {
  $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE);
  $controls->messages = $r . ' deleted.';
}

if ($controls->is_action('list_add')) {
  $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . $controls->data['list'] . "=1");
  $controls->messages = $r . ' added to list ' . $controls->data['list'];
}

if ($controls->is_action('list_remove')) {
  $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . $controls->data['list'] . "=0");
  $controls->messages = $r . ' removed from list ' . $controls->data['list'];
}

if ($controls->is_action('list_delete')) {
  $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where list_" . $controls->data['list'] . "<>0");
}

if ($controls->is_action('feed')) {
  $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . $controls->data['list_feed'] . "=1 where feed=1");
}

if ($controls->is_action('list_manage')) {
  if ($controls->data['list_action'] == 'move') {
    $wpdb->query("update " . NEWSLETTER_USERS_TABLE . ' set list_' . $controls->data['list_1'] . '=0, list_' . $controls->data['list_2'] . '=1' .
            ' where list_' . $controls->data['list_1'] . '=1');
  }

  if ($controls->data['list_action'] == 'add') {
    $wpdb->query("update " . NEWSLETTER_USERS_TABLE . ' set list_' . $controls->data['list_2'] . '=1' .
            ' where list_' . $controls->data['list_1'] . '=1');
  }
}

if ($controls->is_action('resend_all')) {
    $list = $wpdb->get_results("select * from " . $wpdb->prefix . "newsletter where status='S'");
    $opts = get_option('newsletter');

    if ($list) {
        $controls->messages = 'Confirmation email sent to: ';
        foreach ($list as &$user) {
            $controls->messages .= $user->email . ' ';
            $newsletter->mail($user->email, $newsletter->replace($opts['confirmation_subject'], $user), $newsletter->replace($opts['confirmation_message'], $user));
        }
    } else {
        $controls->errors = 'No subscribers to which rensend the confirmation email';
    }

}

if ($controls->is_action('align_wp_users')) {

    // TODO: check if the user is already there
    $wp_users = $wpdb->get_results("select id, user_email, user_login from $wpdb->users");
    $count = 0;
    foreach ($wp_users as &$wp_user) {
        $module->logger->info('Adding a registered WordPress user (' . $wp_user->id . ')');

        // A subscriber is already there with the same wp_user_id? Do Nothing.
        $nl_user = $module->get_user_by_wp_user_id($wp_user->id);
        if (!empty($nl_user)) {
            $module->logger->info('Subscriber already associated');
            continue;
        }

        $module->logger->info('WP user email: ', $wp_user->user_email);

        // A subscriber has the same email? Align them if not already associated to another wordpress user
        $nl_user = $module->get_user($module->normalize_email($wp_user->user_email));
        if (!empty($nl_user)) {
            $module->logger->info('Subscriber already present with that email');
            if (empty($nl_user->wp_user_id)) {
                $module->logger->info('Linked');
                $module->set_user_wp_user_id($nl_user->id, $wp_user->id);
                continue;
            }
        }

        $module->logger->info('New subscriber created');

        // Create a new subscriber
        $nl_user = array();
        $nl_user['email'] = $module->normalize_email($wp_user->user_email);
        $nl_user['name'] = $wp_user->user_login;
        $nl_user['status'] = $controls->data['align_wp_users_status'];
        $nl_user['wp_user_id'] = $wp_user->id;
        $nl_user['referrer'] = 'wordpress';

        // Adds the force subscription preferences
        $preferences = NewsletterSubscription::instance()->options['preferences'];
        if (is_array($preferences)) {
            foreach ($preferences as $p) {
                $nl_user['list_' . $p] = 1;
            }
        }

        $module->save_user($nl_user);
        $count++;
    }
    $controls->messages = 'Total WP users aligned ' . count($wp_users) . ', total new subscribers ' . $count . '.';
}


if ($controls->is_action('bounces')) {
    $lines = explode("\n", $controls->data['bounced_emails']);
    $total = 0;
    $marked = 0;
    $error = 0;
    $not_found = 0;
    $already_bounced = 0;
    $results = '';
    foreach ($lines as &$email) {
        $email = trim($email);
        if (empty($email)) continue;

        $total++;

        $email = NewsletterModule::normalize_email($email);
        if (empty($email)) {
              $results .= '[INVALID] ' . $email . "\n";
          $error++;
            continue;
        }

        $user = NewsletterUsers::instance()->get_user($email);

        if ($user == null) {
          $results .= '[NOT FOUND] ' . $email . "\n";
          $not_found++;
          continue;
        }

        if ($user->status == 'B') {
          $results .= '[ALREADY BOUNCED] ' . $email . "\n";
          $already_bounced++;
          continue;
        }

        $r = NewsletterUsers::instance()->set_user_status($email, 'B');
        if ($r === 0) {
          $results .= '[BOUNCED] ' . $email . "\n";
        $marked++;
          continue;
        }
    }

    $controls->messages .= 'Total: ' . $total . '<br>';
    $controls->messages .= 'Bounce: ' . $marked . '<br>';
    $controls->messages .= 'Errors: ' . $error . '<br>';
    $controls->messages .= 'Not found: ' . $not_found . '<br>';
    $controls->messages .= 'Already bounced: ' . $already_bounced . '<br>';
}
?>

<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/subscribers-module'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>
  <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

    <h2>Massive Actions on Subscribers</h2>
  <p>A bug or an error using this panel can scramble the subscribers database. Please, backup before run a massive operation.</p>

  <?php $controls->show(); ?>

    <?php if (!empty($results)) { ?>

    <h3>Results</h3>

    <textarea wrap="off" style="width: 100%; height: 150px; font-size: 11px; font-family: monospace"><?php echo htmlspecialchars($results) ?></textarea>

    <?php } ?>


  <form method="post" action="">
  <?php $controls->init(); ?>

    <div id="tabs">
      <ul>
        <li><a href="#tabs-1">Global Actions</a></li>
        <li><a href="#tabs-2">Preferences Management</a></li>
        <li><a href="#tabs-3">Other</a></li>
        <li><a href="#tabs-4">Bounces</a></li>
      </ul>

      <div id="tabs-1">
        <table class="widefat" style="width: 300px;">
          <thead><tr><th>Status</th><th>Total</th><th>Actions</th></thead>
          <tr>
            <td>Total in database</td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE); ?>
            </td>
            <td nowrap>
              <?php $controls->button_confirm('remove_all', 'Delete all', 'Are you sure you want to remove ALL subscribers?'); ?>
            </td>
          </tr>
          <tr>
            <td>Confirmed</td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C'"); ?>
            </td>
            <td nowrap>
              <?php $controls->button_confirm('unconfirm_all', 'Unconfirm all', 'Are you sure? No way back.'); ?>
            </td>
          </tr>
          <tr>
            <td>Not confirmed</td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='S'"); ?>
            </td>
            <td nowrap>
              <?php $controls->button_confirm('remove_unconfirmed', 'Delete all not confirmed', 'Are you sure you want to delete ALL not confirmed subscribers?'); ?>
              <?php $controls->button_confirm('confirm_all', 'Confirm all', 'Are you sure you want to mark ALL subscribers as confirmed?'); ?>
                <?php $controls->hint('To send a comfirmation email to all, you can create a special newsletter.', 'http://www.satollo.net/plugins/newsletter/subscribers-module#resend-confirm'); ?>
              <?php //$controls->button_confirm('resend_all', 'Resend confirmation message to all', 'Are you sure?'); ?>
            </td>
          </tr>
          <tr>
            <td>Unsubscribed</td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='U'"); ?>
            </td>
            <td>
              <?php $controls->button_confirm('remove_unsubscribed', 'Delete all unsubscribed', 'Are you sure you want to delete ALL unsubscribed?'); ?>
            </td>
          </tr>
          <tr>
            <td>Import WP user</td>
            <td>
                &nbsp;
            </td>
            <td>
                Link WordPress users with status
                <?php $controls->select('align_wp_users_status', array('C'=>'Confirmed', 'S'=>'Not confirmed')); ?>
                <?php $controls->button_confirm('align_wp_users', 'Go', 'Proceed?'); ?>
                <?php $controls->hint('Please, carefully read the documentation before taking this action!', 'http://www.satollo.net/plugins/newsletter/subscribers-module#import-wp-users'); ?>
            </td>
          </tr>

          <tr>
            <td>Bounced</td>
            <td>
              <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='B'"); ?>
            </td>
            <td>
              <?php $controls->button_confirm('remove_bounced', 'Delete all bounced', 'Are you sure?'); ?>
            </td>
          </tr>
        </table>
        <p>Bounce are not detected by Newsletter plugin, you should use the <a href="http://www.satollo.net/plugins/bounce" target="_blank">Bounce plugin</a>.</p>

        <h3>Sex</h3>
        <?php
            // TODO: do them with a single query
            $all_count = $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where status='C'");
            $male_count = $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where sex='m' and status='C'");
            $female_count = $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where sex='f' and status='C'");
            $other_count = ($all_count-$male_count-$female_count)
        ?>
        <table class="widefat" style="width: 300px">
            <thead><tr><th>Sex</th><th>Total</th></thead>
            <tr><td>Male</td><td><?php echo $male_count; ?></td></tr>
            <tr><td>Female</td><td><?php echo $female_count; ?></td></tr>
            <tr><td>Not specified</td><td><?php echo $other_count; ?></td></tr>
        </table>
      </div>


      <div id="tabs-2">
        <table class="form-table">
          <tr>
            <th>Preferences management</th>
            <td>
              For preference <?php $controls->select('list', $lists); ?>:
              <?php $controls->button_confirm('list_add', 'Add it to every user', 'Are you sure?'); ?>
              <?php $controls->button_confirm('list_remove', 'Remove it from every user', 'Are you sure?'); ?>
              <?php $controls->button_confirm('list_delete', 'Delete subscribers of it', 'Are you really sure you want to delete those user from your database?'); ?>
              <br /><br />
              <?php $controls->select('list_action', array('move' => 'Change', 'add' => 'Add')); ?>
              all subscribers with preference <?php $controls->select('list_1', $lists); ?>
              to preference <?php $controls->select('list_2', $lists); ?>
              <?php $controls->button_confirm('list_manage', 'Go!', 'Are you sure?'); ?>
              <div class="hints">
                If you choose to <strong>delete</strong> users in a list, they will be
                <strong>physically deleted</strong> from the database (no way back).
              </div>
            </td>
          </tr>
          <tr>
            <th>Feed by mail</th>
            <td>
              Set preference <?php $controls->select('list_feed', $lists); ?> to feed by mail subscribers
              <?php $controls->button_confirm('feed', 'Go!', 'Are you sure?'); ?>
              <div class="hints">
              </div>
            </td>
          </tr>
        </table>
      </div>


      <div id="tabs-3">
        <p>Totals refer only confirmed subscribers.</p>
        <table class="widefat" style="width: 300px;">
          <thead><tr><th>Number</th><th>Preference</th><th>Total</th></thead>
          <?php for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) { ?>
            <tr>
              <td><?php echo $i; ?></td>
              <td><?php echo $options_profile['list_' . $i]; ?></td>
              <td>
                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where list_" . $i . "=1 and status='C'"); ?>
              </td>
            </tr>
          <?php } ?>
        </table>
      </div>

      <div id="tabs-4">
        <?php $controls->textarea('bounced_emails'); ?>
        <?php $controls->button_confirm('bounces', 'Mark those emails as bounced', 'Are you sure?'); ?>
      </div>

    </div>


  </form>
  </div>
