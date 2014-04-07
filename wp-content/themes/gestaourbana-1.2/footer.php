	<footer>
		<div class="wrapper">
			<div class="footer-col">
				<div class="footer-row">
					<img src="<?php echo bloginfo('template_url'); ?>/images/logo-gestao_urbana.png">
				</div>
				<div class="footer-row">
					<img src="<?php echo bloginfo('template_url'); ?>/images/logo-prefeitura.png">
				</div>
			</div>
			<div class="footer-col">
				<?php /*<div class="footer-row">
					<!-- <a href="equipe.php">Equipe</a>
					<a href="desenvolvimento.php">Desenvolvimento</a> -->
					<a href="termos.php">Termos de uso</a>
					<a href="contato.php">Contato</a>
				</div>*/ ?>
				<?php wp_nav_menu( array( 'theme_location' => 'extra-menu' ) ); ?>
				<br /><br /><br /><br />
				<div class="footer-row contato">
					Secretaria Municipal de Desenvolvimento Urbano (SMDU) - Prefeitura de São Paulo<br />
					Rua São Bento, 405, Centro - 17º e 18º andar<br />
					CEP 01011-100 - São Paulo - SP<br />
					Telefone: (11) 3113 7500<br />
				</div>
			</div>
			<div class="footer-col">
				<div class="footer-row">
					<div class="left label">
						Redes sociais:
					</div>
					<div class="left social-buttons">
						<a href="https://www.facebook.com/pmsp.smdu" target="_blank"><img src="<?php echo bloginfo('template_url'); ?>/images/btn-facebook-27x27.png" /></a>
						<a href="https://twitter.com/pmsp_smdu" target="_blank"><img src="<?php echo bloginfo('template_url'); ?>/images/btn-twitter-27x27.png" /></a>
						<a href="http://www.youtube.com/user/pmspsmdu" target="_blank"><img src="<?php echo bloginfo('template_url'); ?>/images/icon-youtube.png" /></a>
						<?php /*<img src="images/btn-plus-27x27.png" />*/ ?>
					</div>
					<div class="clear"></div>
				</div>
				<div class="footer-row">
					<?php /*<div class="left label">
						Compartilhe:
					</div>
					<div class="left social-buttons">
						<img src="_tmp/btns-social-share.png" />
					</div>*/ ?>
					<p style="font-size: 12px">Todo o conteúdo do site está disponível sob licença <a href="http://creativecommons.org/licenses/by-sa/3.0/deed.pt_BR" target="_blank">Creative Commons.</a> O código deste site é livre, consulte nossa página sobre <a href="<?php echo get_bloginfo( 'url' ); ?>/index.php/desenvolvimento">desenvolvimento</a>.</p>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</footer>
	<?php wp_footer(); ?>
	
	<script type="text/javascript">
		jQuery(document).ready(function() {
			var userBrowser = jQuery.browser;
			if (userBrowser.msie) {
				var userBrowserVersion = userBrowser.version;
				if (userBrowserVersion == "7.0" || userBrowserVersion == "6.0") {
					jQuery('#asn-warning').css('display', 'block');
				}
			}
		});
	</script>
	<script type="text/javascript"> 
		var $buoop = {vs:{i:7,f:15,o:10.6,s:4,n:9}}
		$buoop.ol = window.onload; 
		window.onload=function(){ 
		 try {if ($buoop.ol) $buoop.ol();}catch (e) {} 
		 var e = document.createElement("script"); 
		 e.setAttribute("type", "text/javascript"); 
		 e.setAttribute("src", "http://browser-update.org/update.js"); 
		 document.body.appendChild(e); 
		} 
	</script>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
 		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
 		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
 		ga('create', 'UA-40469751-1', 'sp.gov.br');
		 ga('send', 'pageview');
	</script>
</body>

</html>