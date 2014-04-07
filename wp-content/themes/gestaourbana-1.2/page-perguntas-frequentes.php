<?php
/*
Template Name: Entenda - Perguntas frequentes
*/
?>

<?php get_header(); ?>
<div id="perguntas">
	<div class="wrapper" id="wrapper-second-perguntas">
		<div class="left">
			<div class="inner">
				<h1>Perguntas Frequentes</h1>
				<br /><br />
				<?php $wp_query = new WP_Query( array('post_type' => 'wp_super_faq')); ?>
				<?php $count = 1;?>
				<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
				<div class="text">
					<p><a href="javascript://"><?php the_title(); ?></a></p>
					<div class="inner-text <?php if ($count == 1) { echo 'open'; } ?>">	
						<p><?php the_content(); ?></p>
					</div>
				</div>
				<?php $count++; endwhile;?>
			</div>
		</div>
		<div class="right">
			<div class="inner">
				<img id="Image-Maps_1201305031436012" src="<?php echo bloginfo('template_url'); ?>/_tmp/perguntas-1.jpg" usemap="#Image-Maps_1201305031436012" border="0" width="213" height="234" alt="" />
        <map id="_Image-Maps_1201305031436012" name="Image-Maps_1201305031436012">
        <area shape="rect" coords="158,41,208,229" href="/index.php/etapas/" alt="Etapas" title="Etapas"    />
        <area shape="rect" coords="0,41,116,229" href="/index.php/a-revisao-participativa/" alt="A revisão participativa" title="A revisão participativa"    />
        </map>
				<div class="contato">
					<img src="<?php echo bloginfo('template_url'); ?>/images/title-perguntas-contato.png" />
				</div>
				<div class="text">
					<p><div class="secretaria">Secretaria Municipal de Desenvolvimento Urbano (SMDU)</div>
					Prefeitura de São Paulo - Rua São Bento, 405, Centro - 17º e 18º andar
					CEP 01011-100 - São Paulo - SP<br />
					Telefone: (11) 3113 7500</p>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php get_footer(); ?>