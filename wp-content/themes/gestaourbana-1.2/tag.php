<?php get_header(); ?>
<style type="text/css">
	.page-tag h4 {margin-bottom:0 !important;}
	.page-tag .page-title {margin-bottom: 30px !important}
</style>

<div id="news" class="page-tag">
	<div class="wrapper">
		<div class="left">
			<div class="inner">

				<h2 class="page-title"><?php
					printf( __( 'Tag: %s', 'twentyten' ), '<span>' . single_tag_title( '', false ) . '</span>' );
				?></h2>

				<?php
					global $query_string;
					$posts = query_posts($query_string.'&post_type=noticias');
					if ( have_posts() ) : while ( have_posts() ) : the_post(); 
					?>
				<div class="text">
					<h4><a href="<?php the_permalink();?>"><?php the_title() ?></a></h4>
					<?php the_excerpt() ?>
				</div>
					<?php endwhile; ?>
					<?php endif; ?>
			</div><!-- -->

		</div><!--  -->
		<?php include('noticias-sidebar.php'); ?><div class="clear"></div>
	</div>
</div>
<?php get_footer(); ?>