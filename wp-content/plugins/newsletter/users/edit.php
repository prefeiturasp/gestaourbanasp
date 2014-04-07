<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$id = (int)$_GET['id'];

if ($controls->is_action('save')) {

  // For unselected preferences, force the zero value
  for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    if (!isset($controls->data['list_' . $i])) $controls->data['list_' . $i] = 0;
  }

  $controls->data['id'] = $id;
  $r = $module->save_user($controls->data);
  if ($r === false) {
    $controls->errors = 'Unable to update, may be the email (if changed) is duplicated.';
  } else {
    $controls->messages = 'Updated.';
    $controls->data = $module->get_user($id, ARRAY_A);
  }
}

if ($controls->is_action('delete')) {
  $module->delete_user($id);
  $controls->js_redirect($module->get_admin_page_url('index'));
  return;
}

if (!$controls->is_action()) {
  $controls->data = $module->get_user($id, ARRAY_A);
}

$options_profile = get_option('newsletter_profile');
?>
<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/subscribers-module'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>
  <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

        <h2>Subscriber Edit</h2>

  <?php $controls->show(); ?>

  <form method="post" action="">
    <?php $controls->init(); ?>

    <div id="tabs">

      <ul>
        <li><a href="#tabs-1">General</a></li>
        <li><a href="#tabs-2">Preferences</a></li>
        <li><a href="#tabs-3">Profile</a></li>
        <li><a href="#tabs-4">Other</a></li>
      </ul>

      <div id="tabs-1">

        <table class="form-table">
          <tr valign="top">
            <th>Email address</th>
            <td>
              <?php $controls->text('email', 60); ?>
            </td>
          </tr>
          <tr valign="top">
            <th>First name</th>
            <td>
              <?php $controls->text('name', 50); ?>
              <div class="hints">
                If you collect only the name of the subscriber without distinction of first and last name, use
                this field.
              </div>
            </td>
          </tr>
          <tr valign="top">
            <th>Last name</th>
            <td>
              <?php $controls->text('surname', 50); ?>
            </td>
          </tr>
          <tr valign="top">
            <th>Sex</th>
            <td>
              <?php $controls->select('sex', array('n' => 'Not specified', 'f' => 'female', 'm' => 'male')); ?>
            </td>
          </tr>
          <tr valign="top">
            <th>Status</th>
            <td>
              <?php $controls->select('status', array('C' => 'Confirmed', 'S' => 'Not confirmed', 'U' => 'Unsubscribed', 'B' => 'Bounced')); ?>
            </td>
          </tr>
          <tr valign="top">
              <th>Test subscriber?</th>
              <td>
                  <?php $controls->yesno('test'); ?>
                  <div class="hints">
                      A test subscriber is a normal subscriber that is used when sending test are made, too.
                  </div>
              </td>
          </tr>

              <?php do_action('newsletter_user_edit_extra', $controls); ?>

          <tr valign="top">
            <th>Feed by mail</th>
            <td>
              <?php $controls->yesno('feed'); ?>
            </td>
          </tr>
        </table>
      </div>
      <div id="tabs-2">
        <table class="form-table">
            <tr>
                <th>Preferences</th>
                <td>
                    <?php $controls->preferences('list'); ?>
                </td>
            </tr>
        </table>
      </div>

      <div id="tabs-3">

        <table class="widefat" style="width:auto">
          <thead>
            <tr>
              <th>Number</th>
              <th>Name</th>
              <th>Value</th>
            </tr>
          </thead>
          <tbody>
                <?php
                for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
                  echo '<tr><td>(' . $i . ') ';
                  echo '</td><td>';
                  echo $options_profile['profile_' . $i];
                  echo '</td><td>';
                  $controls->text('profile_' . $i, 70);
                  echo '</td></tr>';
                }
                ?>
          </tbody>
        </table>
      </div>

      <div id="tabs-4">

        <table class="form-table">
          <tr valign="top">
            <th>Subscriber ID</th>
            <td>
              <?php $controls->value('id'); ?>
            </td>
          </tr>
          <tr valign="top">
            <th>Created</th>
            <td>
              <?php $controls->value('created'); ?>
            </td>
          </tr>
          <tr valign="top">
            <th>From IP address</th>
            <td>
              <?php $controls->value('ip'); ?>
            </td>
          </tr>
          <tr valign="top">
            <th>Secret token</th>
            <td>
              <?php $controls->text('token', 50); ?>
              <div class="hints">
                This secret token is used to access the profile page and edit profile data.
              </div>
            </td>
          </tr>
          <tr valign="top">
            <th>Profile URL</th>
            <td>
                <?php echo plugins_url('newsletter/do/profile.php') . '?nk=' . $id . '-' . $controls->data['token']; ?>
            </td>
          </tr>

        </table>
      </div>
    </div>

    <p class="submit">
      <?php $controls->button('save', 'Save'); ?>
      <?php $controls->button('delete', 'Delete'); ?>
    </p>

  </form>
</div>
