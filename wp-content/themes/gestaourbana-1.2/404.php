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

	.content.quatro-zero-quatro {
		min-height: 175px;
		width: 280px;
		margin: 0 auto;
		position: relative;
		top:35px;
        text-align: center;
	}
    .content.quatro-zero-quatro a img {
        margin-left:-75px;
    }
</style>

<div class="wrapper">
	<div class="content quatro-zero-quatro">
			<h1>Oops!<br /> Erro.</h1>
			<h2>Página não encontrada</h2>
			<a href="<?php bloginfo('url') ?>">
				<img src="<?php bloginfo('template_url') ?>/images/404-btn-back.png">
			</a>
	</div>
</div>

<?php get_footer(); ?>