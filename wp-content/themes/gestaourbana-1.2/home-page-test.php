<?php

/*
 * Template Name: Home Page Test
 */

 get_header(); ?>

<a href="#modal_homenagem" style="display:none;" id="hidden_link" class="fancybox">ver homenagem</a>
<div style="display: none">
<div id="modal_homenagem" style="padding:25px; margin:25px;">
<h3 style="text-align:center">JORGE WILHEIM</h3>
<h4 style="text-align:center">HOMENAGEM DO CONSELHO MUNICPAL DE POLÍTICA URBANA</h4><br/>
<p style="text-align:center"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/homenagem-jorge-wilheim.png" alt="Imagem representativa sobre trabalho do homenageado" /></p>
<br/>
<p>Idéias e interpretações vão além de qualquer tempo, atravessando eras e formas físicas de apresentação. Bem mais que planejar uma construção ou dividir espaços para sua melhor ocupação, Jorge Wilheim é responsável por um legado inestimável de emblemáticos projetos, obras e conceitos, entre os quais vários cartões postais paulistanos, como o Vale do Anhangabaú (1º entre 94 em concurso público para a reurbanização, 1981-91), o Parque Anhembi (1967-73) e o Pátio do Colégio (projeto de reurbanização, 1975).</p>
<p>Dedicou 60 de seus 85 anos em torno de idéias capazes de recuperar, preservar e promover a melhoria da qualidade de vida nas cidades, influenciando gerações, não só de arquitetos e urbanistas, mas de geógrafos, engenheiros, sociólogos e demais profissionais.  Foi ator ativo no desenvolvimento urbano do país e responsável pela elaboração de diversos planos diretores pelo Brasil. Como um dos renovadores da urbanística no País, Wilheim sempre teve distinta atuação profissional, tanto no Brasil como no exterior. Ocupou diversos cargos públicos, tendo também papel destacado em funções no Instituto dos Arquitetos do Brasil e como Secretário Geral Adjunto da divisão da ONU para a realização da 2ª Conferência das Nações Unidas sobre Assentamentos Humanos (Habitat II), realizada em Istambul, em 1996. Anos antes, participou das reuniões preparatórias da Conferência das Nações Unidas para o Meio Ambiente Humano, em Estocolmo (1972), marco da governança ambiental mundial.</p>
<p>No entanto, mais do que tudo, se pode definir Jorge Wilheim como um humanista, que tão bem transitou no campo da arquitetura, do urbanismo, das artes, da técnica e da política. Como poucos, soube pautar sua trajetória profissional conciliando o "homem de idéias" (o pensador) ao "homem da prática" (o implementador). A ele se deve, por exemplo, a criação do PROCON, da Fundação Seade, da EMTU, do "passe do trabalhador", da primeira utilização oficial de álcool combustível no País (conhecido mais tarde como Proálcool). Empreendedor e organizado na sua forma de trabalho, Wilheim imaginava as coisas com começo, meio e fim.</p>
<p>Muito antes de ser "moda", soube trabalhar a multi e transdisciplinaridade das equipes que chefiava e introduzir, em seus projetos e idéias, o conceito de sustentabilidade. Conciliou como poucos, a questão ambiental e o planejamento urbano, até então duas matrizes do pensamento distintas e até mesmo compreendidas como antagônicas. Não é a toa que acumulou ao longo de seu extenso currículo na vida pública, os cargos de Secretário de Economia e Planejamento e de Meio Ambiente do Estado de São Paulo. Foi também, por duas vezes, Secretario Municipal de Planejamento, nas gestões de Mario Covas e de Marta Suplicy.</p>
<p>Em sua segunda participação à frente da pasta, coordenou a elaboração do Plano Diretor Estratégico vigente, tendo grande participação dos diversos segmentos da sociedade. Partindo de um raciocínio territorial sob a forma de redes, propôs uma visão de cidade estrutural e integrada. Introduziu a questão ambiental ao planejamento e desenvolvimento urbano adotando, de forma pioneira, a rede hídrica como elemento estruturador do espaço da cidade.</p>
<p>Sensível as demandas da sociedade, transformou de forma definitiva a maneira como se relacionam os diversos atores que constroem a cidade, através da criação, no PDE, de instrumentos inovadores como o Conselho Municipal de Política Urbana e o Fundo Municipal de Desenvolvimento Urbano - FUNDURB. Ainda sob sua coordenação, se deu a elaboração e aprovação da atual lei de uso e ocupação do solo e os 31 planos regionais estratégicos das subprefeituras de São Paulo, onde buscou romper a tradicional dicotomia centro - periferia.</p>
<p>No último ano, com a disposição para o debate de idéias que lhe era peculiar, vinha contribuindo ativamente no processo de revisão do Plano Diretor Estratégico conduzido pela Prefeitura. Apresentou inúmeras propostas e demonstrou ser um colaborador incansável na construção de uma São Paulo melhor para todos.</p>
<p>Talvez esse tenha sido justamente seu maior legado à cidade, o de verdadeiro pensador da cidade, com seu olhar curioso e visionário, capaz de identificar e propor soluções para suas grandes carências. A Prefeitura de São Paulo agradece à Jorge Wilheim pela sua inestimável cotribuição.</p>
<p>Em 1966 elaborou o Plano Urbanístico Tietê, disponível no link a seguir e os originais na biblioteca da SPUrbanismo.</p>
<br/>
<p><a href="/arquivos/130523_Plano_basico_urbanistico_do_tiete_1966_JW_resumida.ppt">Plano Urbanístico do Tietê (JORGE WILHEIM ARQUITETOS ASSOCIADOS) - 1966</a></p>
</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {

    jQuery(".fancybox")
        .fancybox({
            type: 'inline',
            autoSize: false,
            height: 450,
            maxHeight: 450,
            margin: [5, 30, 5, 30]
    });
if (document.cookie.replace(/(?:(?:^|.*;\s*)someCookieName\s*\=\s*([^;]*).*$)|^.*$/, "$1") !== "true") {
jQuery("#hidden_link").fancybox().trigger('click');  
document.cookie = "someCookieName=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
}
});
</script>
<div class="wrapper" id="wrapper-second-home">
        <div id="banner-slide">
                <ul class="bjqs">
                  <?php $slide_query = new WP_Query( array('post_type' => 'slider', 'posts_per_page' => 3, 'orderby'=>'menu_order', 'order' => 'ASC')); ?>
      <?php $count = 1; ?>
      <?php while ( $slide_query->have_posts() ) : $slide_query->the_post(); ?>
                        <li class="bjqs-slide bjqs-slide-<?php echo $count; ?>" id="slide-first">
                                <div class="right image">
                                  <?php $image = get_field('big_image'); ?>
                                        <img src="<?php echo $image['sizes']['510xX'] ?>" />
                                </div>
                                <div class="left text">
                                        <a href="<?php the_field('link'); ?>"><h2><?php the_title(); ?></h2></a>
                                        <p><?php the_content(); ?></p>
                                </div>
                <div class="toolbar-links left">
                    <?php
                        $image_chamada_1 = get_field('image_chamada_1');
                        $link_chamada_1 = get_field('link_chamada_1');

                        $image_chamada_2 = get_field('image_chamada_2');
                        $link_chamada_2 = get_field('link_chamada_2');

                        $image_chamada_3 = get_field('image_chamada_3');
                        $link_chamada_3 = get_field('link_chamada_3');
                    ?>
                    <ul>
                        <?php if ( (!empty($link_chamada_1)) && (!empty($image_chamada_1)) ) { ?>
                        <li>
                            <a href="<?php echo $link_chamada_1; ?>">
                                <img src="<?php echo $image_chamada_1['sizes']['150xX']; ?>" />
                            </a>
                        </li>
                        <?php
                        }
                        if ( (!empty($link_chamada_2)) && (!empty($image_chamada_2)) ) { ?>
                        <li>
                            <a href="<?php echo $link_chamada_2; ?>">
                                <img src="<?php echo $image_chamada_2['sizes']['150xX']; ?>" />
                            </a>
                        </li>
                        <?php
                        }
                        if ( (!empty($link_chamada_3)) && (!empty($image_chamada_3)) ) { ?>
                        <li>
                            <a href="<?php echo $link_chamada_3; ?>">
                                <img src="<?php echo $image_chamada_3['sizes']['150xX']; ?>" />
                            </a>
                        </li>
                        <?php } ?>
                    </ul>

                </div>
                <?php $image = get_field('small_image'); ?>
                        </li>
                        <?php $count++; endwhile;wp_reset_query();?>
                </ul>
        </div>
</div>

<!--script>
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
<br /><br /-->
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
                        <p class="news-text"><?php echo get_the_excerpt(); ?></p>

                        <?php get_breadcrumb_data(get_the_ID(), 'projetos');?>
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
                                <p class="news-text"><?php echo get_the_excerpt(); ?></p>
                                <?php get_breadcrumb_data(get_the_ID(), 'projetos', true);?>
                        </div>
    <?php endif; ?>
    <?php if ($count == 3): ?>
                <div class="news cell">
                        <a href="<?php the_permalink(); ?>">
                                <p class="news-date"><?php the_time('d/m/Y'); ?></p>
                                <p class="news-title"><?php the_title(); ?></p>
                                </a>
                                <p class="news-text"><?php echo get_the_excerpt(); ?></p>
                                <?php get_breadcrumb_data(get_the_ID(), 'projetos', true);?>
                </div>
    <?php endif; ?>
    <?php if ($count == 4): ?>
                <div class="news cell last">
                        <a href="<?php the_permalink(); ?>">
                                <p class="news-date"><?php the_time('d/m/Y'); ?></p>
                                <p class="news-title"><?php the_title(); ?></p>
                                </a>
<p class="news-text"><?php echo get_the_excerpt(); ?></p>
                                <?php get_breadcrumb_data(get_the_ID(), 'projetos', true);?>
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
                <?php $event_query = new WP_Query( array('post_type' => 'agenda', 'paged' => $paged, 'posts_per_page' => 2, 'meta_query' => array(array( 'key' => 'agenda_show_date','value' => time(),'compare' => '>='),),'orderby' => 'meta_value_num','order' => 'ASC','meta_key' => 'agenda_show_date')); ?>
                <?php if ( $event_query->have_posts() ) { ?>
                                <a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/evento">
                                  <?php while ( $event_query->have_posts() ) : $event_query->the_post(); ?>
                                        <div class="event">
                                                <div class="event-date">
                                                                <?php
                                                                        _e(date('d', get_post_meta( $post->ID, 'agenda_show_date', true )) . ' | ' . strftime('%B', get_post_meta( $post->ID, 'agenda_show_date', true )));
                                                                ?>
                                                </div>
                                                <div class="event-text">
                                                        <?php the_title(); ?>
                                                </div>

                                                        <?php get_breadcrumb_data(get_the_ID(), 'projetos', true);?>

                                                <div class="clear"></div>
                                        </div>
                                        <?php $count++; endwhile;?>
                                </a>
                <?php } else { ?>
                    <p style="display: inline-block; margin: 10px; font-family: 'museoSlab', Arial, Helvetica, sans-serif;">Não há eventos no momento. Confira os <a style="color: #000; text-decoration:underline;" href="<?php echo get_bloginfo( 'url' ); ?>/agenda-completa/">eventos já realizados</a>.</p>
                <?php } ?>
                        </div>
                        <!--a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/evento" class="see-all-events">Veja a agenda completa</a-->
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
 
