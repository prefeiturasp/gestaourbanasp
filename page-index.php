<?php
/*
Template Name: Index Temp
*/
?>

<?php get_header(); ?>
<div class="wrapper" id="wrapper-second-home">
	<div id="banner-slide">
		<ul class="bjqs">
			<li class="bjqs-slide" id="slide-first">
				<span class="image">
					<img src="<?php echo bloginfo('template_url'); ?>/_tmp/1-entenda.jpg" />
				</span>
				<span class="text">
					<a href="/a-revisao-participativa/"><h2>Participe do planejamento de uma nova São Paulo</h2></a>
					<p>Você se preocupa com a sua cidade, quer espaços públicos bem cuidados, preservar o meio ambiente, se preocupa com moradia adequada para todos, quer poder ir trabalhar, passear, estudar sem enfrentar congestionamentos e longas horas em deslocamentos? Luta por qualidade de vida? <br />
						A prefeitura quer a sua participação, para avaliar a cidade que temos e planejar a que queremos..</p>
					<div>
						
						<div align="center">
						  <img id="Image-Maps_8201305031729588" src="<?php echo bloginfo('template_url'); ?>/_tmp/1-entenda-bottom.jpg" usemap="#Image-Maps_8201305031729588" border="0" width="350" height="225" alt="" />
              <map id="_Image-Maps_8201305031729588" name="Image-Maps_8201305031729588">
              <area shape="rect" coords="148,0,229,220" href="javascript://" onclick="jQuery('#slider-to-click-2').trigger('click');" alt="Veja as etapas" title="Veja as etapas"    />
              <area shape="rect" coords="264,0,345,220" href="javascript://" onclick="jQuery('#slider-to-click-3').trigger('click');" alt="Perguntas frequentes" title="Perguntas frequentes"    />
              </map>
						</div>
					</div>
				</span>
			</li>
			<li class="bjqs-slide">			
				<span class="image">
					<img src="<?php echo bloginfo('template_url'); ?>/_tmp/2-etapas-2.jpg" />
				</span>
				<span class="text">
					<a href="/etapas/"><h2>Conheças as etapas de revisão</h2></a>
					<p>A Prefeitura não pode definir o planejamento 
						da cidade sozinha. Precisa ter a participação de 
						todos os cidadãos e cidadãs nas etapas de revisão: <br /><br />

						- do Plano Diretor Estratégico<br />
						- da Lei de Parcelamento, Uso e Ocupação do Solo<br />
						- dos Planos Regionais Estratégicos<br />
						- de Leis Urbanísticas Específicas<br />
						- do Código de Obras e Edificações</p>
					<div>
						<div align="center">
						  <img id="Image-Maps_82013050317295881" src="<?php echo bloginfo('template_url'); ?>/_tmp/2-etapas-bottom.jpg" usemap="#Image-Maps_82013050317295881" border="0" width="350" height="225" alt="" />
              <map id="_Image-Maps_82013050317295881" name="Image-Maps_82013050317295881">
              <area shape="rect" coords="0,10,128,220" href="javascript://" onclick="jQuery('#slider-to-click-1').trigger('click');" alt="A revisão participativa" title="A revisão participativa"    />
              <area shape="rect" coords="252,10,345,220" href="javascript://" onclick="jQuery('#slider-to-click-3').trigger('click');" alt="Perguntas frequentes" title="Perguntas frequentes"    />
              </map>
						</div>
					</div>
				</span>
			</li>
			<li class="bjqs-slide">			
				<span class="image">
					<img id="Image-Maps_920130426172526212" src="<?php echo bloginfo('template_url'); ?>/_tmp/3-perguntas_frequentes.jpg" />
				</span>
				<span class="text">
					<a href="/perguntas-frequentes/"><h2>A Prefeitura de São Paulo tira todas as suas dúvidas</h2></a>
					<p>Navegue pelas perguntas frequentes.<br />
						Se ainda assim precisar de ajuda entre em contato a Secretaria Municipal de Desenvolvimento Urbano (SMDU). Todos os cidadãos precisam estar bem informados.</p>
					<div>
						<div align="center">
							<img id="Image-Maps_82013050317295882" src="<?php echo bloginfo('template_url'); ?>/_tmp/3-perguntas_frequentes-bottom.jpg" usemap="#Image-Maps_82013050317295882" border="0" width="350" height="225" alt="" />
              <map id="_Image-Maps_82013050317295882" name="Image-Maps_82013050317295882">
              <area shape="rect" coords="10,10,117,220" href="javascript://" onclick="jQuery('#slider-to-click-1').trigger('click');" alt="A revisão participativa" title="A revisão participativa"    />
              <area shape="rect" coords="129,10,236,220" href="javascript://" onclick="jQuery('#slider-to-click-2').trigger('click');" alt="Veja as etapas" title="Veja as etapas"    />
              </map>
						</div>
					</div>
				</span>
			</li>
		</ul>
	</div>
</div>
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
	<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/noticias"><div id="see-all-news">Veja todas as notícias</div></a>
</div>
<div id="red-area">
	<div class="wrapper" id="wrapper-fourth-home">
		<div class="left">
			<h1>Agenda</h1>
			<h3>Confira as próximas pautas discutidas.</h3>
			<div id="calendar">
				<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/agenda">
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
			<a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/agenda" class="see-all-events">Veja a agenda completa</a>
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
<?php /*<div class="wrapper" id="wrapper-fifth-home">
	<h1>Redes Sociais</h1>
	<div class="social-box">
		<img src="images/title-social-facebook.png" />
		<div class="social-inner">
			<div class="fb-like-box" data-href="http://www.facebook.com/amarelloart" data-width="250" data-height="315" data-show-faces="true" data-stream="false" data-border-color="white" data-header="false"></div>
		</div>
	</div>
	<div class="social-box">
			<img src="images/title-social-twitter.png" />
		<div class="social-inner">
			<div class="twitter-row">
				Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam auctor lorem a sapien tempus volutpat. <a href="">dlvr.it/1pj4tc</a>
				<div class="twitter-links">
					<a href="#">3hours ago</a> · <a href="#">reply</a> · <a href="#">retweet</a>
				</div>
			</div>
			<div class="twitter-row">
				Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam auctor lorem a sapien tempus volutpat. <a href="">dlvr.it/1pj4tc</a>
				<div class="twitter-links">
					<a href="#">3hours ago</a> · <a href="#">reply</a> · <a href="#">retweet</a>
				</div>
			</div>
			<div class="twitter-row">
				Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam auctor lorem a sapien tempus volutpat. <a href="">dlvr.it/1pj4tc</a>
				<div class="twitter-links">
					<a href="#">3hours ago</a> · <a href="#">reply</a> · <a href="#">retweet</a>
				</div>
			</div>
		</div>
		<div class="social-link"><a href="">Siga @gestaourbana</a></div>
	</div>
	<div class="social-box">
			<img src="images/title-social-youtube.png" />
		<div class="social-inner">
			<iframe width="250" height="205" src="http://www.youtube.com/embed/NdQoEvIaxfE" frameborder="0" allowfullscreen></iframe>
			<h3>Prefeitura de São Paulo - São Paulo 459 Anos - Comercial</h3>
			
		</div>
		<div class="social-link"><a href="">Veja o canal</a></div>
	</div>
	<div class="clear"></div>
</div>*/?>

<?php get_footer(); ?>