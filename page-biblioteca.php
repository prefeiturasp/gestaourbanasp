<?php
/*
Template Name: Biblioteca
*/
?>

<?php get_header(); ?>
<div id="library">
	<div class="wrapper">
		<div class="inner">
			<h1>Biblioteca</h1>
      <a id="arquivo-biblioteca"class="btn" href="mailto:gestaourbanasp@prefeitura.sp.gov.br?subject=Gestão Urbana SP - Biblioteca: Documento enviado para análise">Envie seu arquivo</a>
			<div class="nav-bar">
				<ul>
					<li>
						<a href="/index.php/biblioteca">
							<img src="<?php echo bloginfo('template_url'); ?>/images/icon-library-navbar-all.png" />
						</a>
					</li>
					<?php
            $terms=get_terms("librarycategory");
            foreach ($terms as $term) :
          ?>
					<li>
						<a href="<?php echo get_term_link($term->slug, 'librarycategory'); ?>">
							<img src="<?php echo bloginfo('template_url'); ?>/images/library/<?php echo $term->slug; ?>_big.png" />
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="clear"></div>
			<div class="boxes">
  		  <?php $paged = get_query_var('paged') ? get_query_var('paged') : 1; ?>
  		  <?php
  		  $args = array('post_type' => 'biblioteca', 'paged' => $paged, 'posts_per_page' => 1000);
  		  if (isset($global_term)) :
    		    $args['tax_query'] = array(
                                    array(
                                      'taxonomy' => 'librarycategory',
                                      'field' => 'slug',
                                      'terms' => $global_term->slug
                                    )
                                  );
  		  endif;
        $wp_query = new WP_Query( $args ); ?>
        <?php $count = 1; ?>
        <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
  			<div class="box">
  				<a href="<?php echo get_post_meta( $post->ID, 'library_link', true ); ?>" target="_blank">
  					<div class="inner-box">
  					  <?php $terms = get_the_terms($post->ID, 'librarycategory'); ?>
  					  <?php foreach ($terms as $term): ?>
    						<img src="<?php echo bloginfo('template_url'); ?>/images/library/<?php echo $term->slug; ?>.png" class="normal" />
    						<img src="<?php echo bloginfo('template_url'); ?>/images/library/<?php echo $term->slug; ?>-hover.png" class="hover" />
  						<?php endforeach; ?>
  						<h5><?php the_title(); ?></h5>
  					</div>
  				</a>
  			</div>
  			<?php $count++; endwhile;?>
			</div>
			<?php the_content_nav(); ?>

			<div class="clear"></div>
		</div>		
	</div>
</div>

<?php get_footer(); ?>
