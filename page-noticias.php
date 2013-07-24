<?php
/*
Template Name: Noticias
*/
?>

<?php get_header(); ?>
<div id="news">
	<div class="wrapper">
		<div class="left">
			<div class="inner">
			  <?php $paged = get_query_var('paged') ? get_query_var('paged') : 1; ?>
			  <?php $wp_query = new WP_Query( array('post_type' => 'noticias', 'paged' => $paged, 'posts_per_page' => 5)); ?>
			  <?php $count = 1; ?>
			  <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
			    <?php if ($count == 1): ?>
  				  <div class="big-text">
  				<?php else : ?>
  				  <div class="text">
  				<?php endif; ?>
    					<a href="<?php the_permalink(); ?>">
    					  <div class="image">
                  <?php the_post_thumbnail('657xX'); ?>
                </div>
    						<p class="news-date"><?php the_time('d/m/Y'); ?></p>
    						<?php if ($count == 1): ?>
                  <h3><?php the_title(); ?></h3>
                <?php else : ?>
                  <h4><?php the_title(); ?></h4>
                <?php endif; ?>
    						<div class="inner-text">
    							<p class="subtitle"><?php the_excerpt(); ?></p>
    						</div>
    					</a>
    					<span><?php the_tags() ?></span>

    				</div>
			  <?php $count++; endwhile;?>

				<?php the_content_nav(); ?>

			</div>
		</div>

		<?php include('noticias-sidebar.php'); ?>

		<div class="clear"></div>

	</div>
</div>

<?php get_footer(); ?>