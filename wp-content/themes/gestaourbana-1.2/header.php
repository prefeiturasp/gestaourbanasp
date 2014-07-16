<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, minimumscale=1.0, maximum-scale=1.0" />
	<title>Gestão Urbana SP</title>
	<meta name="description" content=" Participe do planejamento de uma nova São Paulo">
	<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'stylesheet_url' ); ?>?<?php echo time(); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-agenda-interna.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-agenda-sidebar.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-agenda.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-biblioteca.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-contato.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-entenda-etapas.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-entenda-introducao.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-entenda-perguntas.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-equipe.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-home.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-interna.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-noticias-interna.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-noticias-sidebar.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-noticias.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style-comments.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/glDatePicker.flatwhite.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/jquery.fancybox.css"/>
	<!--script type="text/javascript" src="//misc.prefeitura.sp.gov.br/v2/startup.js"></script-->
	<!--[if lt IE 9]>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style.ie.css"/>
	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript">
	  var template_url = "<?php echo bloginfo('template_url'); ?>";
	  var slider = 'slider';
	</script>

<?php
wp_enqueue_script('respond', get_stylesheet_directory_uri() . '/js/respond.min.js');
wp_enqueue_script('site-script', get_stylesheet_directory_uri() . '/js/script.js', array( 'jquery' ));
wp_enqueue_script('bjqs', get_stylesheet_directory_uri() . '/js/bjqs-1.3.js', array( 'jquery' ));
wp_enqueue_script('glDatePicker', get_stylesheet_directory_uri() . '/js/glDatePicker.js', array( 'jquery' ));
wp_enqueue_script('jquery.fancybox', get_stylesheet_directory_uri() . '/js/jquery.fancybox.js', array( 'jquery' ));

wp_head();

?>
</head>
<!--[if lt IE 7 ]> <body class="ie6"> <![endif]-->
<!--[if IE 7 ]> <body class="ie7"> <![endif]-->
<!--[if IE 8 ]> <body class="ie8"> <![endif]-->
<!--[if IE 9 ]> <body class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <body> <!--<![endif]-->

	<div id="asn-warning" style="display:none; position: fixed; left: 0px; border-bottom-style: solid; border-bottom-width: 1px; border-bottom-color: rgb(223, 221, 203); top: 0px; margin: 0px; width: 100%; z-index: 99999; color: #000; font-size: 10pt; padding: 0px; background-color: rgb(255, 252, 223); text-align: left; background-position: initial initial; background-repeat: initial initial;">
	    <a href="http://www.updateyourbrowser.net/" style="color: rgb(79, 77, 59); text-decoration: none; font-style: normal; font-variant: normal; font-weight: normal; font-size: 9pt; line-height: 14px; font-family: 'Trebuchet MS', Arial, Helvetica; padding-right: 30px; display: block;" target="_blank">
	        <span id="asn-outofdate" style="display: block;  color: #fff; float: left; padding: 10px 18px 10px 8px; background: #d7322f; ">Navegador desatualizado!</span>
	        <span style="display: block; padding: 10px 0 0 10px; float: left;">Para visualizar este site corretamente,</span>
	        <span style="display: block; padding: 10px 4px; float: left;text-decoration: underline;">faça a atualização.</span></a>
	</div>
	
	<header>
		<div class="inner">
			<div class="wrapper">
				<ul>
					<li id="first">
<!--						<a href="<?php //echo get_bloginfo( 'url' ); ?>" title="home"><img src="<?php //echo bloginfo('template_url'); ?>/images/logo-gestao_urbana-home.png" /></a>-->
                                                <a href="<?php echo get_bloginfo( 'url' ); ?>" title="home"><img src="<?php echo bloginfo('template_url'); ?>/images/logo_gestao.jpg" /></a>
					</li>
					<li  id="second">
<!--						<img src="<?php //echo bloginfo('template_url'); ?>/images/logo-prefeitura.png" />-->
                                            <img src="<?php echo bloginfo('template_url'); ?>/images/logo_prefeitura.jpg" />
					</li>
					<li>
                                            <?php wp_nav_menu( array( 'theme_location' => 'header-menu' ) ); ?>
                                        </li>
					<!--<li id="newsletter">
						<div class="left label"  style="padding-top: 4px;">
							Cadastre-se e<br />receba notícias
						</div>
						<div class="left" id="register-newsletter-box">
							<?php /*<form id="register-newsletter" class="ajax_submit_form" action="<?php echo bloginfo('template_url'); ?>/ajax-cadastro.php" method="post"> */ ?>
							<form id="register-newsletter" class="ajax_submit_form" action="<?php echo plugins_url( 'newsletter/do/subscribe.php' ); ?>" method="post">
								<input type="text" class="defaultText defaultTextActive" name="ne" id="register-newsletter-input" title="Seu e-mail" /><input type="submit" value="OK" />
							</form>
						</div>
						<div class="clear"></div>
					</li>-->
					<li class="last">
						<!--<div class="left label">
						  Redes<br />sociais
						</div>-->
						<div class="right social-buttons">
							<iframe src="<?php echo bloginfo('template_url'); ?>/social-bar-home.php" name="social-bar-home" frameborder="0" height="30" width="113" scrolling="no"></iframe>
						</div>
						<div class="clear"></div>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
		</div>
	</header>
	<div id="nav">
		<div class="wrapper" id="wrapper-first">
		  <?php //wp_nav_menu( array( 'theme_location' => 'header-menu' ) ); ?>
			<div id="search">
				<?php /*<input type="text" value="Pesquisar" /><input type="submit" value="" />*/ ?>
			</div>
			<div class="clear"></div>
		</div>
        <!-- #wrapper -->
	</div>
	<div id="project-nav">
		<div class="wrapper" id="wrapper-first">
			<div class="menu-projetos">
				<!--<div class="label">
					<b>Acontecendo agora:</b>
				</div>-->
				<?php wp_nav_menu(array('theme_location' => 'revisao-menu')); ?>
				<?php wp_nav_menu(array('theme_location' => 'arco-menu')); ?>
				<?php wp_nav_menu(array('theme_location' => 'dialogo-menu')); ?>
                                <?php wp_nav_menu(array('theme_location' => 'territorio-menu')); ?>
                                <?php wp_nav_menu(array('theme_location' => 'projetos-especiais')); ?>
			</div>
		</div>	
	</div>
    <div class="wrapper" id="mensagem-alerta" style="background-color: #fffbcc !important;">
        <!--p style="color:#000;">COLABORE NA <b>MINUTA PARTICIPATIVA</b> ATÉ ÀS 23:59 DE HOJE (06/09) - <a style="color:#000; text-decoration:underline;" href="http://minuta.gestaourbana.prefeitura.sp.gov.br">CLIQUE AQUI</a></p-->
        <!-- <p style="color:#000;">ATENÇÃO: O PRAZO PARA CONTRIBUIR NA MINUTA PARTICIPATIVA FOI ENCERRADO.</p> -->
    </div>