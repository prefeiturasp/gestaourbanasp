<?php
/*
Template Name: Entenda - A revisão participativa
*/
?>

<?php get_header(); ?>
<style type="text/css">
    .alignleft {
	float:left;
	margin:0 20px 20px 0;
    }
</style>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
  
  <?php //the_content(); ?>
  <div class="wrapper">
    <?php
        $content = get_the_content();
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);
        $content = str_replace('<p> </p>', '', $content);
        $content = explode('<p>',$content);
        //var_dump($content);die;
    ?>
    <?php for ($x = 0; $x < count($content); $x++) : ?>
        <?php if ($x == get_field('read_more_paragraph') && get_field('show_read_more')): ?>
          <div class="also-read">
            <?php if (function_exists('the_related')) { the_related(); }; ?>
          </div>
        <?php endif; ?>

        <p><?php echo str_replace('</p>', '',$content[$x]); ?></p>

    <?php endfor; ?>
  </div>  
<?php endwhile; ?>

<?php get_footer(); ?>