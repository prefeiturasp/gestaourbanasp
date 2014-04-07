<?php get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
<div id="agenda-inner">
	<div class="wrapper">
		<div class="left">
			<div class="inner">
				<h1>Agenda</h1>
				<div class="text">
				  <?php if (get_post_meta( $post->ID, 'agenda_show_date', true ) != '') : ?>
					<h2><?php _e(date('d', get_post_meta( $post->ID, 'agenda_show_date', true ))); ?> <?php _e(strftime('%b', get_post_meta( $post->ID, 'agenda_show_date', true ))); ?> | <?php echo ucfirst(__(date('l', get_post_meta( $post->ID, 'agenda_show_date', true )))); ?></h2>
					<?php endif; ?>
					<?php the_post_thumbnail('170xX'); ?>
					<h4><?php the_title(); ?></h4>
					<div class="clear"></div>
					<div class="time"><?php _e(get_post_meta( $post->ID, 'agenda_hour', true )); ?></div>
					<br />
					<div class="place"><?php _e(get_post_meta( $post->ID, 'agenda_location', true )); ?></div>
					<br />
					<div class="information">Informações:</div>
					<p><?php the_content(); ?></p>
				</div>

				<?php /*<div class="see-also">
					<img src="<?php echo bloginfo('template_url'); ?>/images/title-news_interna-veja_tambem.png" />
					<ul>
						<li>
							<img src="<?php echo bloginfo('template_url'); ?>/_tmp/4.jpg" width="190" height="190" />
							<div class="subtitle">Arco do Futuro: Secretarias Municipais iniciam diálogo</div>
						</li>
						<li>
							<img src="<?php echo bloginfo('template_url'); ?>/_tmp/4.jpg" width="190" height="190" />
							<div class="subtitle">Arco do Futuro: Secretarias Municipais iniciam diálogo</div>
						</li>
						<li>
							<img src="<?php echo bloginfo('template_url'); ?>/_tmp/4.jpg" width="190" height="190" />
							<span class="subtitle">Arco do Futuro: Secretarias Municipais iniciam diálogo</span>
						</li>
					</ul>
				</div>	*/ ?>
			</div>



		</div>

		<?php include("noticias-sidebar.php"); ?>

		<div class="clear"></div>

	</div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>