<div id="news-sidebar" class="right sidebar">
	<div class="inner">

		<?php dynamic_sidebar('paginas-sidebar') ?>

		<!--
		<div class="box-destaque box">
			<div class="title">
				<img src="<?php echo bloginfo('template_url'); ?>/images/title-news_sidebar-destaque.png" />
	    	</div>

	    	<?php $news_query = new WP_Query( array('post_type' => 'noticias', 'posts_per_page' => 3,'orderby' => 'meta_value_num','order' => 'DESC','meta_key' => 'views_counter_'.date('M'))); ?>
	    	<?php $count = 1; ?>
	    	<?php while ( $news_query->have_posts() ) : $news_query->the_post(); ?>

	      	<div class="text">
	        <?php the_post_thumbnail('96xX'); ?>
	        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	        <div class="clear"></div>
	    </div>
	    <?php endwhile;?>
	    </div>
		-->


		<div class="box-agenda box">
			<div class="title">
				<img src="<?php echo bloginfo('template_url'); ?>/images/title-news_sidebar-agenda.png" />
			</div>

			<div id="calendar">
				<?php $event_query = new WP_Query( array('post_type' => 'agenda', 'posts_per_page' => 3, 'meta_query' => array(array( 'key' => 'agenda_show_date','value' => time(),'compare' => '>='),),'orderby' => 'meta_value_num','order' => 'ASC','meta_key' => 'agenda_show_date')); ?>
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
				<div class="clear"></div>
			</div>
			<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/evento">Veja a agenda completa</a>
		</div>


		<!--
		<div class="box-biblioteca box">
			<div class="title">
				<img src="<?php echo bloginfo('template_url'); ?>/images/title-news_sidebar-biblioteca.png" />
			</div>
			<p>Todo material de apoio sobre legislação relativa a cidade. Assista vídeos e leia os artigos. Informe-se!</p>
			<p><a href="/index.php/biblioteca"><img src="<?php echo bloginfo('template_url'); ?>/images/icon-news-sidebar-library.png" /></a></p>
			<p><a href="/index.php/biblioteca">Veja todos os arquivos</a></p>
		</div>
		-->

		<!--
		<div class="box-saibamais box">
			<img id="Image-Maps_8201305031754171" src="<?php echo bloginfo('template_url'); ?>/images/box-news-sidebar-saibamais.png" usemap="#Image-Maps_8201305031754171" border="0" width="301" height="225" alt="" />
      		<map id="_Image-Maps_8201305031754171" name="Image-Maps_8201305031754171">
      		<area shape="rect" coords="220,43,295,220" href="/index.php/perguntas-frequentes/" alt="Perguntas frequentes" title="Perguntas frequentes"    />
      		<area shape="rect" coords="125,36,199,220" href="/index.php/etapas/" alt="Etapas" title="Etapas"    />
      		<area shape="rect" coords="2,30,109,220" href="/index.php/a-revisao-participativa/" alt="A revisão participativa" title="A revisão participativa"    />
      		</map>
		</div>
		-->
	</div>
</div>