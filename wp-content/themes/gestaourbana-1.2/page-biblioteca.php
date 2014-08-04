<?php
/*
Template Name: Biblioteca
*/
?>

<?php get_header(); ?>
<div id="library">
	<div class="wrapper">
		<div class="inner">
            <div id="lista-artigos">
			<h1>Biblioteca</h1>
            <a id="arquivo-biblioteca"class="btn" href="mailto:imprensadu@prefeitura.sp.gov.br?subject=Gestão Urbana SP - Biblioteca: Documento enviado para análise">Envie seu arquivo</a>

            <div class="input text"><label for="search">Busca: </label><input class="search" /></div>

            <div class="input radio">
                <label>Visualização:</label>
                <input type="radio" value="grid" checked="checked" name="view-type" /><label for="view-type"> Grid</label>
                <input type="radio" value="list" name="view-type" /><label for="view-type"> Lista</label>
            </div>

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




			<ul class="list boxes">
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
  			<li class="box">
  				<a href="<?php echo get_post_meta( $post->ID, 'library_link', true ); ?>" target="_blank">
  					<div class="inner-box">
  					  <?php $terms = get_the_terms($post->ID, 'librarycategory'); ?>
  					  <?php foreach ($terms as $term): ?>
    						<img src="<?php echo bloginfo('template_url'); ?>/images/library/<?php echo $term->slug; ?>.png" class="normal" />
    						<img src="<?php echo bloginfo('template_url'); ?>/images/library/<?php echo $term->slug; ?>-hover.png" class="hover" />
  						<?php endforeach; ?>
                        <span class="type" style="display:none;"><?php echo $term->slug; ?></span>
  						<h5 class="name"><?php the_title(); ?></h5>
  					</div>
  				</a>
  			</li>
  			<?php $count++; endwhile;?>
			</ul>
            </div>

            <script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/js/list.min.js?<?php echo time(); ?>"></script>
            <script type="text/javascript">
            jQuery(document).ready(function () {


                var options = {
                    valueNames: [ 'name', 'type' ]
                };

                var hackerList = new List('lista-artigos', options);


                jQuery('input[name="view-type"]').on('click', function () {
                    if (this.value === 'list') {
                        jQuery('#lista-artigos').addClass('modo-lista');
                    }else {
                        jQuery('#lista-artigos').removeClass('modo-lista');
                    }
                });
            });
            </script>

			<?php the_content_nav(); ?>

			<div class="clear"></div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
