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
	<script type="text/javascript" src="//misc.prefeitura.sp.gov.br/v2/startup.js"></script>
	<!--[if lt IE 9]>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/css/style.ie.css"/>
	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript">
	  var template_url = "<?php echo bloginfo('template_url'); ?>";
	  var slider = 'slider';
	</script>
	<script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/js/respond.min.js"></script>
	<!--[if lte IE 8 ]> <script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/js/jquery-1.9.1.min.js"></script> <![endif]-->
	<!--[if (gte IE 9)|!(IE)]><!--> <script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/js/jquery-2.0.0.min.js"></script> <!--<![endif]-->
	<script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/js/bjqs-1.3.js"></script>
	<script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/js/glDatePicker.js"></script>
	<script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/js/jquery.fancybox.js"></script>
	<script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/js/script.js?<?php echo time(); ?>"></script>
	
 	<meta property="fb:app_id" content="171948572963967" />
	<meta property="og:url" content="http://www.gestaourbana.prefeitura.sp.gov.br/" />
	<meta property="og:title" content="Gestão Urbana SP" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="http://www.gestaourbana.prefeitura.sp.gov.br/images/logo-gestao_urbana.png" />
	<meta property="og:description" content="Se você é preocupado com a sua cidade, quer espaços públicos bem cuidados, preservar o meio ambiente, se preocupa com moradia adequada para todos, quer poder ir trabalhar, passear, estudar sem enfrentar congestionamentos e longas horas nos deslocamentos? Luta por qualidade de vida? A prefeitura quer…" />
	<meta property="og:updated_time" content="<?php echo time(); ?>" />
	
	<?php //wp_head(); ?>
</head>
<!--[if lt IE 7 ]> <body class="ie6"> <![endif]-->
<!--[if IE 7 ]> <body class="ie7"> <![endif]-->
<!--[if IE 8 ]> <body class="ie8"> <![endif]-->
<!--[if IE 9 ]> <body class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <body> <!--<![endif]-->

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId=391372857648079";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<header>
		<div class="inner">
			<div class="wrapper">
				<ul>
					<li id="first">
						<a href="<?php echo get_bloginfo( 'url' ); ?>" title="home"><img src="<?php echo bloginfo('template_url'); ?>/images/logo-gestao_urbana.png" /></a>
					</li>
					<li  id="second">
						<img src="<?php echo bloginfo('template_url'); ?>/images/logo-prefeitura.png" />
					</li>
					<li id="newsletter">
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
					</li>
					<li class="last">
						<div class="left label">
						  Redes<br />sociais
						</div>
						<div class="right social-buttons">
							<?php /*<img src="<?php echo bloginfo('template_url'); ?>/images/btn-facebook-27x27.png" />*/ ?>
							 <div class="fb-like" data-href="https://www.facebook.com/pmsp.smdu" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false"></div>
							<a href="https://twitter.com/pmsp_smdu" class="twitter-follow-button" data-show-count="false" data-lang="pt" data-show-screen-name="false">Seguir @pmsp_smdu</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

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
		  <?php wp_nav_menu( array( 'theme_location' => 'header-menu' ) ); ?>
			<div id="search">
				<?php /*<input type="text" value="Pesquisar" /><input type="submit" value="" />*/ ?>
			</div>
			<div class="clear"></div>
		</div> <!-- #wrapper -->
	</div>