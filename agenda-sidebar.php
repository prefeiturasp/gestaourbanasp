<link rel="stylesheet" type="text/css" href="style-agenda-sidebar.css"/>
<div id="agenda-sidebar" class="right sidebar">
	<div class="inner">
		<div class="box-calendar box">
			<div class="title">
				<img src="<?php echo bloginfo('template_url'); ?>/images/title-agenda-sidebar-calendar.png" />
			</div>
			<div class="calendar">
			  <input type="text" id="mydate" gldp-id="mydate" />
        <div gldp-el="mydate"
             style="width:400px; height:300px; position:absolute; top:70px; left:100px;">
        </div>
				<?php /*<img src="<?php echo bloginfo('template_url'); ?>/_tmp/calendar.png" />*/ ?>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(function() {
  $('#mydate').glDatePicker(
  {
      showAlways: true,
      cssName: 'flatwhite',
      specialDates: [
<?php $wp_query = new WP_Query( array('post_type' => 'agenda', 'paged' => $paged, 'posts_per_page' => 5, 'meta_query' => array(array( 'key' => 'agenda_show_date','value' => time(),'compare' => '>='),),'orderby' => 'meta_value_num','order' => 'ASC','meta_key' => 'agenda_show_date')); ?>
<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
          {
              date: new Date(<?php echo date('Y', get_post_meta( $post->ID, 'agenda_show_date', true )); ?>, <?php echo date('m', get_post_meta( $post->ID, 'agenda_show_date', true )) - 1; ?>, <?php echo date('d', get_post_meta( $post->ID, 'agenda_show_date', true )); ?>),
              data: { message: '<?php the_title(); ?>' },
          },
<?php endwhile; wp_reset_query();?>
      ],
      onClick: function(target, cell, date, data) {
          target.val(date.getFullYear() + ' - ' +
                      date.getMonth() + ' - ' +
                      date.getDate());
          document.location = document.location + 'data/' + date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();  
      }
      });
});
</script>