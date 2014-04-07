<?php
/*
Template Name: Blank Template
*/
?>

<style type="text/css">
	.page-blank {
		font-size:90%;
		font-family: Helvetica,Arial,sans-serif;
		color: #6B6B6B;
		line-height: 1.4em
	}
</style>

<div class="page-blank">
	<?php $recent = new WP_Query("page_id=256"); while($recent->have_posts()) : $recent->the_post();?>
       <h3><?php the_title(); ?></h3>
       <?php the_content(); ?>
	<?php endwhile; ?>
</div>