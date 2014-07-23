<?php
/*
Template Name: Sistematização - Especial
*/
$using_ie6 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== FALSE);
$using_ie7 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.') !== FALSE);

if ($using_ie7 || $using_ie6) {
    header('Location: '.get_bloginfo('template_url').'/images/infografico-relatorio-de-atividades.png');
}

?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7 lt-ie6"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 9]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Gestão Urbana - Revisão Participativa dos Instrumentos de Planejamento Urbano</title>

        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/style-sistematizacao.css"/>

        <script src="<?php bloginfo('template_url'); ?>/js/sistematizacao/vendor/modernizr.js"></script>
        <script type="text/javascript">var themeURL = "<?php bloginfo('template_url'); ?>/";</script>
    </head>
<?php get_header(); ?>
    <body>
        <br>
        <div class="intro container">
            <div class="skyscraper">

                <h1>Revisão Participativa dos Instrumentos<br>de Planejamento e Gestão Urbana</h1>

                <ul class="milestones">
                    <li class="featured process">PLANO<br>DIRETOR<br>ESTRATÉGICO</li>
                    <li class="separator-process">&nbsp;</li>
                    <li class="process">LEI DE PARCELAMENTO,<br>USO E OCUPAÇÃO<br>DO SOLO</li>
                    <li class="separator-process">&nbsp;</li>
                    <li class="process">PLANOS<br>REGIONAIS<br>ESTRATÉGICOS</li>
                    <li class="separator-process">&nbsp;</li>
                    <li class="process">CÓDIGO<br>DE OBRAS E<br>EDIFICAÇÕES</li>
                </ul>

                <hr class="separator-intro">

            </div>
        </div>

        <div class="etapa-1 container">
            <p class="subtitle"><img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/pesquisas.png" alt="">A revisão do Plano Diretor Estratégico (PDE) é o principal<br> instrumento para o planejamento da cidade. Essa revisão<br> está acontecendo em  4 etapas e já estamos na última.</p>
            <p class="subtitle nivel2">Acompanhe os resultados até o momento:</p>

            <h2><span>Etapa</span>Avaliação dos resultados positivos e negativos do PDE.</h2>

            <div class="content">
                <h3 class="avaliacao">avaliações temáticas</h3>

                <h3 class="dialogo">diálogos com segmentos</h3>

                <iframe id="ytplayer" type="text/html" width="355" height="200" src="http://www.youtube.com/embed/kj__KOeJ3Hs?start=2540" frameborder="0"></iframe>

                <h3 class="conferencia">6ª conferência municipal</h3>

                <div class="pure-g">
                    <div class="pure-u-1-2">
                        <a href="http://www.youtube.com/user/pmspsmdu/videos?sort=da&flow=list&view=0" target="_blank" class="all-youtube-videos"><i class="icon-youtube"></i>Ver todos os vídeos</a>
                    </div>
                    <div class="pure-u-1-2 sub-content">
                        <div class="slideshow-6-conferencia">
                            <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-6a/1.jpg" width="300" height="197">
                            <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-6a/2.jpg" width="300" height="197">
                            <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-6a/3.jpg" width="300" height="197">
                            <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-6a/4.jpg" width="300" height="197">
                            <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-6a/5.jpg" width="300" height="197">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="etapa-2 container">
            <h2><span>Etapa</span></h2>
            <p class="subtitle">Levantamento de propostas em oficinas realizadas nas 31 subprefeituras e em canal eletrônico.</p>

            <i class="propostas-presenciais"></i>
            <i class="propostas-total"></i>
            <i class="propostas-online"></i>

            <a href="javascript:void(0);" class="all-photos"><i class="icon-fotos"></i>Fotos das Oficinas</a>

            <div class="oficinas-presenciais">
                <h3>Oficinas Presenciais realizadas nas 31 Subprefeituras</h3>

                <div id="mosaic" class="masonry">
                    <div class="item w2">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/1.jpg" alt="Texto da legenda">
                    </div>
                    <div class="item w1">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/2.jpg" alt="Texto da legenda">
                    </div>
                    <div class="item w3">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/4.jpg" alt="Texto da legenda">
                    </div>
                    <div class="item w1">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/3.jpg" alt="Texto da legenda">
                    </div>
                    <div class="item w3">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/5.jpg" alt="Texto da legenda">
                    </div>

                    <div class="item w1">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/7.jpg" alt="Texto da legenda">
                    </div>
                    <div class="item w1">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/6.jpg" alt="Texto da legenda">
                    </div>
                    <div class="item w1">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/8.jpg" alt="Texto da legenda">
                    </div>

                    <div class="item w1">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/9.jpg" alt="Texto da legenda">
                    </div>
                    <div class="item w2">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/10.jpg" alt="Texto da legenda">
                    </div>

                    <div class="item w1">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/11.jpg" alt="Texto da legenda">
                    </div>
                    <div class="item w1">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/fotos-etapa-2/12.jpg" alt="Texto da legenda">
                    </div>

                </div>

                <a href="https://www.facebook.com/pmsp.smdu/photos_albums" class="facebook-photos" target="_blank"><i class="icon-facebook"></i>Ver todas as fotos</a>
           </div>
        </div>

        <div class="etapa-3 container">

            <h2><strong>Etapa</strong><span>Sistematização das propostas recebidas.</span></h2>

            <h3>COM  quais  objetivos  da  revisão<br>As  propostas  apresentadas  se  relacionam?</h3>

            <div class="filter-bar">
                <i class="map-default"></i>
                <p class="title-filters">
                    <span class="name">Mostrando todas as Subprefeituras</span>
                    <a href="javascript;" class="choice-filter">Filtrar por Subprefeitura<i class="icon-arrow-down"></i></a>
                </p>
                <div class="content-filters">
                    <ul class="sub-prefeituras">
                        <li><a href="javascript;" data-value="ALL">Todas</a></li>
                        <li><a href="javascript;" data-value="S_1">Aricanduva/Vila Formosa</a></li>
                        <li><a href="javascript;" data-value="S_2">Butantã</a></li>
                        <li><a href="javascript;" data-value="S_3">Campo Limpo</a></li>
                        <li><a href="javascript;" data-value="S_4">Capela do Socorro</a></li>
                        <li><a href="javascript;" data-value="S_5">Casa Verde</a></li>
                        <li><a href="javascript;" data-value="S_6">Cidade Ademar</a></li>
                        <li><a href="javascript;" data-value="S_7">Cidade Tiradentes</a></li>
                        <li><a href="javascript;" data-value="S_8">Ermelino Matarazzo</a></li>
                        <li><a href="javascript;" data-value="S_9">Freguesia do Ó/Brasilândia</a></li>
                        <li><a href="javascript;" data-value="S_10">Guaianases</a></li>
                        <li><a href="javascript;" data-value="S_11">Ipiranga</a></li>
                        <li><a href="javascript;" data-value="S_12">Itaim Paulista</a></li>
                        <li><a href="javascript;" data-value="S_13">Itaquera</a></li>
                        <li><a href="javascript;" data-value="S_14">Jabaquara</a></li>
                        <li><a href="javascript;" data-value="S_15">Jaçanã/Tremembé</a></li>
                        <li><a href="javascript;" data-value="S_16">Lapa</a></li>
                        <li><a href="javascript;" data-value="S_17">M'Boi Mirim</a></li>
                        <li><a href="javascript;" data-value="S_18">Mooca</a></li>
                        <li><a href="javascript;" data-value="S_19">Parelheiros</a></li>
                        <li><a href="javascript;" data-value="S_20">Penha</a></li>
                        <li><a href="javascript;" data-value="S_21">Perus</a></li>
                        <li><a href="javascript;" data-value="S_22">Pinheiros</a></li>
                        <li><a href="javascript;" data-value="S_23">Pirituba/Jaraguá</a></li>
                        <li><a href="javascript;" data-value="S_24">Santana/Tucuruvi</a></li>
                        <li><a href="javascript;" data-value="S_25">Santo Amaro</a></li>
                        <li><a href="javascript;" data-value="S_26">São Mateus</a></li>
                        <li><a href="javascript;" data-value="S_27">São Miguel Paulista</a></li>
                        <li><a href="javascript;" data-value="S_28">Sé</a></li>
                        <li><a href="javascript;" data-value="S_29">Vila Maria/Vila Guilherme</a></li>
                        <li><a href="javascript;" data-value="S_30">Vila Mariana</a></li>
                        <li><a href="javascript;" data-value="S_31">Vila Prudente</a></li>
                    </ul>
                </div>
            </div>

            <div class="result-chart">
                <h4>OBJETIVOS MAIS DEBATIDOS</h4>

                <ul class="targets">
                    <li data-value="1">
                        <p class="objetivo">Ampliar as oportunidades de trabalho com distribuição na cidade toda</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="3">3%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="13">13.5%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="524">524</span></p>
                        </div>
                    </li>
                    <li data-value="2">
                        <p class="objetivo">Melhorar a qualidade do transporte coletivo público e as condições para ciclistas e pedestres reduzindo congestionamentos</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="5">5.1%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="9.5">9.5%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="466">466</span></p>
                        </div>
                    </li>
                    <li data-value="3">
                        <p class="objetivo">Maior aproveitamento dos terrenos ao longo dos principais eixos de transporte coletivo com moradias e trabalho</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="2.1">2.1%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="5.5">5.5%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="242">242</span></p>
                        </div>
                    </li>
                    <li data-value="4">
                        <p class="objetivo">Diminuir os impactos negativos dos empreendimentos e infraestruturas</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="1.2">1.2%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="4.6">4.6%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="185">185</span></p>
                        </div>
                    </li>
                    <li data-value="5">
                        <p class="objetivo">Proteger e recuperar o patrimônio ambiental (rios, represas, vegetação, qualidade do ar)</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="3.5">3.5%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="3.6">3.6%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="228">228</span></p>
                        </div>
                    </li>
                    <li data-value="6">
                        <p class="objetivo">Proteger e recuperar os diversos patrimônios culturais</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="1.0">1.0%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="1.8">1.8%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="91">91</span></p>
                        </div>
                    </li>
                    <li data-value="7">
                        <p class="objetivo">Ampliar o acesso às terras urbanas para a produção habitacional de interesse social</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="6.4">6.4%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="2.3">2.3%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="277">277</span></p>
                        </div>
                    </li>
                    <li data-value="8">
                        <p class="objetivo">Melhorar as condições de vida e de moradia nas favelas e loteamentos irregulares com regularização fundiária</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="5.9">5.9%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="1.4">1.4%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="231">231</span></p>
                        </div>
                    </li>
                    <li data-value="9">
                        <p class="objetivo">Solucionar os problemas nas áreas com riscos de inundações, deslizamentos e solos contaminados existentes e prevenir o surgimento de novas situações vulneráveis</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="0.8">0.8%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="1.0">1.0%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="58">58</span></p>
                        </div>
                    </li>
                    <li data-value="10">
                        <p class="objetivo">Melhorar a oferta de serviços, equipamentos e infraestruturas urbanas nos bairros</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="7.3">7.3%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="5.5">5.5%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="406">406</span></p>
                        </div>
                    </li>
                    <li data-value="11">
                        <p class="objetivo">Promover espaços urbanos qualificados para grupos sociais vulneráveis (crianças, idosos, gestantes, pessoas com deficiência)</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="2">2%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="3.6">3.6%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="180">180</span></p>
                        </div>
                    </li>
                    <li data-value="12">
                        <p class="objetivo">Fortalecer o planejamento e a gestão urbana, descentralizados com participação e controle social</p>
                        <div class="total-propostas">
                            <p class="percentage-oficina hastip" title="% de propostas das oficinas"><span data-value="4.3">4.3%</span></p>
                            <p class="percentage-online hastip" title="% de propostas enviadas online"><span data-value="5.0">5.0%</span></p>
                            <p class="absolute hastip" title="Valor absoluto de propostas"><i class="icon-proposta"></i><span data-value="296">296</span></p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="analytics-online">
                <h4>Análise das propostas enviadas por canal eletrônico</h4>

                <div class="where-came parameter">
                    <h5>De onde veio a maior parte das propostas?</h5>

                    <div class="pure-g">
                        <div class="pure-u-1-2 heat-map">
                            <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/heat-map-propostas.png" alt="">
                        </div>
                        <div class="pure-u-1-2 legenda">
                            <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/heat-map-propostas-legenda.png" alt="">
                        </div>
                    </div>
                </div>

                <div class="specific-region parameter">
                    <h5>Essas propostas tem como foco alguma região específica?</h5>

                    <div class="content">
                        <img src="<?php bloginfo('template_url'); ?>/images/sistematizacao/chart-pizza-regions.png" alt="">
                    </div>
                </div>
            </div>
            <hr class="separator-etapa-3">
        </div>

        <div class="etapa-4 container">
            <i class="title-etapa-4"></i>
            <h2>
                <strong>Etapa</strong>
                <span>Devolutiva e Discussões Públicas da Minuta do Projeto de Lei</span>
            </h2>

            <div class="content">

                <i class="icon-joinha-pde"></i>
                <i class="icon-relatorio"></i>
                <h3>O  PLANO  DIRETOR DE  SÃO  PAULO  ESTÁ FICANDO PRONTO!</h3>

                <h4>
                    A participação popular com milhares de propostas e contribuições foram fundamentais para gerar a Minuta de Projeto de Lei do PDE.
                    <span>Na etapa final, foi possível participar de duas maneiras: comentando online diretamente nos trechos do documento e também indo às atividades devolutivas e audiências públicas.</span>
                </h4>

            </div>

            <div class="actions">
                <a href="http://minuta.gestaourbana.prefeitura.sp.gov.br/" class="button discussao"><i class="icon-minuta"></i><span class="action">Leia a Minuta Participativa e <br>veja as centenas de contribuições<br> feitas pela sociedade.</span></a>
                <a href="http://gestaourbana.prefeitura.sp.gov.br/evento/" class="button eventos"><i class="icon-agenda"></i><span class="action">Confira a agenda com todas as atividades realizadas.</span></a>
            </div>

        </div>

        <script type="text/javascript" src="http://updateyourbrowser.net/asn.js"> </script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create', 'UA-40469751-1', 'sp.gov.br');ga('send','pageview');
        </script>

        <script src="<?php bloginfo('template_url'); ?>/js/sistematizacao/main.js"></script>
</body>
<?php get_footer(); ?>
</html>
