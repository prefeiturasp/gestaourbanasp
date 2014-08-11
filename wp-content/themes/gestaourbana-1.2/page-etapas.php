<?php
/*
Template Name: Entenda - Etapas
*/
?>

<?php get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

  <div class="wrapper">
  <?php the_content(); ?>
  </div>

<?php endwhile; ?>

<?php get_footer(); ?>