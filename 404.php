<?php get_header(); ?>

<style type="text/css">
	h1{
		text-transform: uppercase;
		font-size:75px;
		line-height: 75px;
	}

	h2 {
		font-size: 20px;
	}

	.content {
		min-height: 175px;
		width: 780px;
		margin: 0 auto;
		position: relative;
		top:35px;
	}
</style>

<div class="wrapper">
	<div class="content">
		<div class="left">
			<img src="<?php bloginfo('template_url') ?>/images/404-image.png" width="329" height="250" style="margin-left:15px"> 
		</div>
		<div class="right">
			<h1>Oops!<br /> Erro.</h1>
			<h2>Página não encontrada</h2>
			<a href="<?php bloginfo('url') ?>">
				<img src="<?php bloginfo('template_url') ?>/images/404-btn-back.png" style="position: relative; left:-60px">
			</a>
		</div>
	</div>
</div>

<?php get_footer(); ?>