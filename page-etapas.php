<?php
/*
Template Name: Entenda - Etapas
*/
?>

<?php get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
  
  <?php the_content(); ?>
  
<?php endwhile; ?>

<?php get_footer(); ?>