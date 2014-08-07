<?php
/*
 * Template Name: Página sem sidebar Sem Título
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div id="news-inner">
  <div class="wrapper">
    <div class="left" style="width:100%;">
      <div class="inner">
        <div class="text">
          <?php /*<p class="news-date"><?php echo the_time('d/m/Y'); ?></p>*/ ?>
          <h3><?php //echo the_title(); ?></h3>
          <?php /*<div class="subtitle"><?php echo the_excerpt(); ?></div>*/ ?>
          <?php /*<div class="social"><img src="<?php echo bloginfo('template_url'); ?>/_tmp/news-inner-social.png" /></div>*/ ?>
          <?php /*<div class="author">Por <?php the_author(); ?> </div>*/ ?>
          <div class="inner-text">
                <?php if (get_field('mostrar_menu') == "sim")
                    {
                ?>
                <div class="menu-do-projeto">
                    <?php mostrar_menu_interno( get_field('projeto') );?>
                </div>
                <?php 
                    }
                ?>
            <?php
              $content = get_the_content();
              $content = apply_filters('the_content', $content);
              $content = str_replace(']]>', ']]&gt;', $content);
              $content = str_replace('<p> </p>', '', $content);
              $content = explode('<p>',$content);
             ?>
            <?php for ($x = 0; $x < count($content); $x++) : ?>
              <?php if ($x == get_field('read_more_paragraph') && get_field('show_read_more')): ?>
                <div class="also-read">
                  <?php if (function_exists('the_related')) { the_related(); }; ?>
                </div>
              <?php endif; ?>

              <p><?php echo str_replace('</p>', '',$content[$x]); ?></p>

            <?php endfor; ?>

            <div class="clear"></div>
          </div>
        </div>
        <?php if (get_the_tags() != '') :  ?>
        <div class="tags"><?php the_tags('Tags: ', ', ', '<br />'); ?> </div>
        <?php endif; ?>
        <!--Comentários-->
        <?php comments_template( '', true ); ?>
      </div>
    </div>


    <div class="clear"></div>

  </div>
</div>

<?php endwhile; ?>

<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery('ul#topicos-menu li.parent > a').click(function() {
            // Expande ou</span> retrai o elemento ul.sub-menu dentro do elemento pai (ul#menu li.parent)
            jQuery('ul.topicos-submenu', jQuery(this).parent()).slideToggle('fast', function() {
                    // Depois de expandir ou retrair, troca a classe 'aberto' do <a> clicado
                    jQuery(this).parent().toggleClass('aberto');
            });
            return false;
    });
});

jQuery(document).ready(function()
{
    jQuery('ul#lei-menu li.parent > a').click(function() {
            // Expande ou</span> retrai o elemento ul.sub-menu dentro do elemento pai (ul#menu li.parent)
            jQuery('ul.lei-submenu', jQuery(this).parent()).slideToggle('fast', function() {
                    // Depois de expandir ou retrair, troca a classe 'aberto' do <a> clicado
                    jQuery(this).parent().toggleClass('aberto');
            });
            return false;
    });
});

jQuery(document).ready(function()
{
    jQuery('ul.lei-submenu li.parent > a').click(function() {
            // Expande ou</span> retrai o elemento ul.sub-menu dentro do elemento pai (ul#menu li.parent)
        jQuery('ul.lei-submenu1', jQuery(this).parent()).slideToggle('fast', function() {
                    // Depois de expandir ou retrair, troca a classe 'aberto' do <a> clicado
                    jQuery(this).parent().toggleClass('aberto');
            });
            return false;
    });
});

</script>

<?php get_footer(); ?>
