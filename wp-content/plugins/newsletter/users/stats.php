<?php
    $all_count = $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter");
    $options_profile = get_option('newsletter_profile');

    $module = NewsletterUsers::instance();
?>


<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/subscribers-module'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>
<?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

    <h2>Subscriber Statistics</h2>

<p>Counts are limited to confirmed subscribers.</p>

<h3>Overview</h3>
<table class="widefat" style="width: 300px;">
    <thead><tr><th>Status</th><th>Total</th></thead>
    <tr valign="top">
        <td>Any</td>
        <td>
            <?php echo $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter"); ?>
        </td>
    </tr>
    <tr>
        <td>Confirmed</td>
        <td>
            <?php echo $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where status='C'"); ?>
        </td>
    </tr>
    <tr>
        <td>Not confirmed</td>
        <td>
            <?php echo $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where status='S'"); ?>
        </td>
    </tr>
    <tr>
        <td>Subscribed to feed by mail</td>
        <td>
            <?php echo $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where status='C' and feed=1"); ?>
        </td>
    </tr>
    <tr>
        <td>Unsubscribed</td>
        <td>
            <?php echo $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where status='U'"); ?>
        </td>
    </tr>
    <tr>
        <td>Bounced</td>
        <td>
            <?php echo $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where status='B'"); ?>
        </td>
    </tr>
</table>

<h3>Totals by preference</h3>
<p><a href="http://www.satollo.net/plugins/newsletter/newsletter-preferences" target="_blank">Click here know more about preferences.</a> They can be configured on Subscription/Form field panel.</p>
<table class="widefat" style="width: 300px;">
    <thead><tr><th>Preference</th><th>Total</th></thead>
    <?php for ($i=1; $i<=NEWSLETTER_LIST_MAX; $i++) { ?>
    <?php if (empty($options_profile['list_' . $i])) continue; ?>
    <tr>
        <td><?php echo '(' . $i . ') ' . $options_profile['list_' . $i]; ?></td>
        <td>
            <?php echo $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where list_" . $i . "=1 and status='C'"); ?>
        </td>
    </tr>
    <?php } ?>
</table>

<h3>Referrer</h3>
<?php
    $list = $wpdb->get_results("select referrer, count(*) as total from " . $wpdb->prefix . "newsletter where status='C' group by referrer order by referrer");
?>
<table class="widefat" style="width: 300px">
    <thead><tr><th>Referrer</th><th>Total</th></thead>
    <?php foreach($list as $row) { ?>
    <tr><td><?php echo $row->referrer; ?></td><td><?php echo $row->total; ?></td></tr>
    <?php } ?>
</table>

<h3>Source</h3>
<?php
    $list = $wpdb->get_results("select http_referer, count(*) as total from " . $wpdb->prefix . "newsletter where status='C' group by http_referer order by count(*) desc");
?>
<table class="widefat" style="width: 300px">
    <thead><tr><th>URL</th><th>Total</th></thead>
    <?php foreach($list as $row) { ?>
    <tr><td><?php echo $row->http_referer; ?></td><td><?php echo $row->total; ?></td></tr>
    <?php } ?>
</table>

<h3>Sex</h3>
<?php
    $male_count = $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where sex='m'");
    $female_count = $wpdb->get_var("select count(*) from " . $wpdb->prefix . "newsletter where sex='f'");
    $other_count = ($all_count-$male_count-$female_count)
?>
<table class="widefat" style="width: 300px">
    <thead><tr><th>Sex</th><th>Total</th></thead>
    <tr><td>Male</td><td><?php echo $male_count; ?></td></tr>
    <tr><td>Female</td><td><?php echo $female_count; ?></td></tr>
    <tr><td>Not specified</td><td><?php echo $other_count; ?></td></tr>
</table>

<p>
    <div id="sex-chart" style="width:400; height:300"></div>
</p>


    <h3>Subscriptions over time</h3>

    <h4>Subscriptions by month</h4>
    <?php
    $months = $wpdb->get_results("select count(*) as c, concat(year(created), '-', date_format(created, '%m')) as d from " . $wpdb->prefix . "newsletter where status='C' group by concat(year(created), '-', date_format(created, '%m')) order by d desc limit 24");
    ?>

    <div id="months-chart" style="width:400; height:300"></div>

    <table class="widefat" style="width: 300px">
        <thead>
        <tr valign="top">
            <th>Date</th>
            <th>Subscribers</th>
        </tr>
        </thead>
        <?php foreach ($months as $day) { ?>
        <tr valign="top">
            <td><?php echo $day->d; ?></td>
            <td><?php echo $day->c; ?></td>
        </tr>
        <?php } ?>
    </table>


    <h4>Subscriptions by day</h4>
    <?php
    $list = $wpdb->get_results("select count(*) as c, date(created) as d from " . $wpdb->prefix . "newsletter where status='C' group by date(created) order by d desc limit 365");
    ?>
    <table class="widefat" style="width: 300px">
        <thead>
        <tr valign="top">
            <th>Date</th>
            <th>Subscribers</th>
        </tr>
        </thead>
        <?php foreach ($list as $day) { ?>
        <tr valign="top">
            <td><?php echo $day->d; ?></td>
            <td><?php echo $day->c; ?></td>
        </tr>
        <?php } ?>
    </table>


</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

  // Load the Visualization API and the piechart package.
  google.load('visualization', '1.0', {'packages':['corechart']});

  // Set a callback to run when the Google Visualization API is loaded.
  google.setOnLoadCallback(drawChart);

      function drawChart() {

      // Create the data table.
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Topping');
      data.addColumn('number', 'Slices');
      data.addRows([
        ['None', <?php echo $other_count; ?>],
        ['Female', <?php echo $female_count; ?>],
        ['Male', <?php echo $male_count; ?>]
      ]);

      // Set chart options
      var options = {'title':'Gender',
                     'width':400,
                     'height':300};

      var chart = new google.visualization.PieChart(document.getElementById('sex-chart'));
      chart.draw(data, options);

      var months = new google.visualization.DataTable();
      months.addColumn('string', 'Topping');
      months.addColumn('number', 'Slices');

      <?php foreach ($months as $day) { ?>
      months.addRow(['<?php echo $day->d; ?>', <?php echo $day->c; ?>]);
      <?php } ?>

      var options = {'title':'By months', 'width':400, 'height':300};

      var chart = new google.visualization.BarChart(document.getElementById('months-chart'));
      chart.draw(months, options);
    }

</script>