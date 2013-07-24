<?php
/*
Template Name: Index
*/
?>

<?php get_header(); ?>
<div class="wrapper" id="wrapper-second-home">
	<div id="banner-slide">
		<ul class="bjqs">
		  <?php $slide_query = new WP_Query( array('post_type' => 'slider', 'posts_per_page' => 3, 'orderby'=>'menu_order', 'order' => 'ASC')); ?>
      <?php $count = 1; ?>
      <?php while ( $slide_query->have_posts() ) : $slide_query->the_post(); ?>
			<li class="bjqs-slide bjqs-slide-<?php echo $count; ?>" id="slide-first">
				<div class="right image">
				  <?php $image = get_field('big_image'); ?>
					<img src="<?php echo $image['sizes']['565xX'] ?>" />
				</div>
				<div class="left text">
					<a href="<?php the_field('link'); ?>"><h2><?php the_title(); ?></h2></a>
					<p><?php the_content(); ?></p>
					<div>
						<br /><br />
						<div align="center" class="slider-nav-images">

						</div>
					</div>
				</div>
			</li>
			<?php $count++; endwhile;wp_reset_query();?>
		</ul>
	</div>
</div>

<script>
  jQuery('.slider-nav-images').each(function(index) {
    jQuery(this).html(
      <?php $slide_query = new WP_Query( array('post_type' => 'slider', 'posts_per_page' => 3, 'orderby'=>'menu_order', 'order' => 'ASC')); ?>
      <?php $count = 1; ?>
      <?php while ( $slide_query->have_posts() ) : $slide_query->the_post(); ?>
        '<a href="javascript://" onclick="jQuery(\'#slider-to-click-<?php echo $count; ?>\').trigger(\'click\');">' +
          <?php $image = get_field('small_image'); ?>
          '<img src="<?php echo $image['sizes']['126xX'] ?>" class="slider-nav-image-image-<?php echo $count; ?>" />' +
        '</a>' <?php if ($count < 3) { echo '+'; } ?>
      <?php $count++; endwhile; wp_reset_query();?>
    );
  });
</script>
<br /><br />
<br /><br />
<br /><br />
<div class="wrapper" id="wrapper-third-home">
	<h1>Notícias</h1>


	<?php $news_query = new WP_Query( array('post_type' => 'noticias', 'posts_per_page' => 4)); ?>
	<?php $count = 1; ?>
  <?php while ( $news_query->have_posts() ) : $news_query->the_post(); ?>

    <?php if ($count == 1): ?>

  	<div class="top-news">
  	  <?php if (get_the_post_thumbnail()): ?>
    		<div class="image">
    			<?php the_post_thumbnail('365x195'); ?>
    		</div>
  		<?php endif; ?>
  		<div class="news">
  			<a href="<?php the_permalink(); ?>" />
  				<p class="news-date"><?php the_time('d/m/Y'); ?></p>
  				<p class="news-title"><?php the_title(); ?></p>
  			</a>
  			<p class="news-text"><?php the_excerpt(); ?></p>
  		</div>
  		<div class="clear"></div>
  	</div>

  	<?php else : ?>

  	<?php if ($count == 2): ?>
    	<div class="bottom-news table">
    		<div class="news cell first">
    	 		<a href="<?php the_permalink(); ?>">
    				<p class="news-date"><?php the_time('d/m/Y'); ?></p>
    				<p class="news-title"><?php the_title(); ?></p>
    	   </a>
    	   <p class="news-text"><?php the_excerpt(); ?></p>
    		</div>
    <?php endif; ?>
    <?php if ($count == 3): ?>
    		<div class="news cell">
    	 		<a href="<?php the_permalink(); ?>">
    				<p class="news-date"><?php the_time('d/m/Y'); ?></p>
    				<p class="news-title"><?php the_title(); ?></p>
    	    </a>
    	    <p class="news-text"><?php the_excerpt(); ?></p>
    		</div>
    <?php endif; ?>
    <?php if ($count == 4): ?>
    		<div class="news cell last">
    	 		<a href="<?php the_permalink(); ?>">
    				<p class="news-date"><?php the_time('d/m/Y'); ?></p>
    				<p class="news-title"><?php the_title(); ?></p>
    	  	</a>
    	  	<p class="news-text"><?php the_excerpt(); ?></p>
    		</div>
    		<div class="clear"></div>
    	</div>
  	<?php endif; ?>
  	<?php endif; ?>
	<?php $count++; endwhile;?>
	<a href="<?php echo get_bloginfo( 'url' ); ?>/noticia"><div id="see-all-news">Veja todas as notícias</div></a>
</div>
<div id="red-area">
	<div class="wrapper" id="wrapper-fourth-home">
		<div class="left">
			<h1>Agenda</h1>
			<h3>Confira as próximas pautas discutidas.</h3>
			<div id="calendar">
				<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/evento">
				  <?php $event_query = new WP_Query( array('post_type' => 'agenda', 'paged' => $paged, 'posts_per_page' => 2, 'meta_query' => array(array( 'key' => 'agenda_show_date','value' => time(),'compare' => '>='),),'orderby' => 'meta_value_num','order' => 'ASC','meta_key' => 'agenda_show_date')); ?>
				  <?php while ( $event_query->have_posts() ) : $event_query->the_post(); ?>
  					<div class="event">
  						<div class="event-date">
  							<div class="number"><?php _e(date('d', get_post_meta( $post->ID, 'agenda_show_date', true ))); ?></div>
  							<div class="month"><?php _e(date('M', get_post_meta( $post->ID, 'agenda_show_date', true ))); ?></div>
  						</div>
  						<div class="event-text">
  							<?php the_title(); ?>
  						</div>
  						<div class="clear"></div>
  					</div>
					<?php $count++; endwhile;?>
				</a>
			</div>
			<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/evento" class="see-all-events">Veja a agenda completa</a>
		</div>
		<div class="right">
			<h1>Biblioteca</h1>
			<h3>Todo material de apoio sobre legislação relativa a cidade.<br />Assista vídeos e leia os artigos. Informe-se!</h3>
			<div class="library-icons icons-left">
				<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/biblioteca"><img src="<?php echo bloginfo('template_url'); ?>/images/icon-videos.png" /></a>
			</div>
			<div class="library-icons icons-middle">
				<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/biblioteca"><img src="<?php echo bloginfo('template_url'); ?>/images/icon-images.png" /></a>
			</div>
			<div class="library-icons icons-right">
				<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/biblioteca"><img src="<?php echo bloginfo('template_url'); ?>/images/icon-legislation.png" /></a>
			</div>
			<div class="clear"></div>
			<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/biblioteca" class="see-all-events">Veja todos os arquivos</a>
		</div>
		<div class="clear"></div>
	</div>
</div>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId=391372857648079";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="wrapper" id="wrapper-fifth-home">
	<h1>Redes Sociais</h1>
	<div class="social-box">
		<img src="<?php bloginfo('template_url') ?>/images/title-social-facebook.png" />
		<div class="social-inner">
			<div class="fb-like-box" data-href="https://www.facebook.com/pmsp.smdu" data-width="255" data-height="325" data-show-faces="true" data-stream="false" data-show-border="false" data-header="false"></div>
		</div>
	</div>
	<div class="social-box">
		<img src="<?php bloginfo('template_url') ?>/images/title-social-twitter.png" />
		<div class="social-inner">
			<a class="twitter-timeline" href="https://twitter.com/pmsp_smdu" data-widget-id="349239983451803649" data-chrome="nofooter transparent noheader">Tweets by @pmsp_smdu</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
	</div>
	<div class="social-box">
		<img src="<?php bloginfo('template_url') ?>/images/title-social-youtube.png" />
		<div class="social-inner">
			<?php echo do_shortcode('[youtubechannel channelname="pmspsmdu" numvideos="1" width="255" showtitle="No"]') ?>
			<p>
				<a href="http://www.youtube.com/user/pmspsmdu" target="_blank">
					Veja o canal
				</a>
			</p>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>