<?php
/*
Template Name: Equipe
*/
?>

<?php get_header(); ?>
<link rel="stylesheet" type="text/css" href="style-equipe.css"/>
<div id="default-inner" class="equipe">
	<div class="wrapper">
		<div class="left">
			<div class="inner">
				<h3>Equipe</h3>
				<div class="text">
					<h4 class="collaborator">Colaboradores:</h4>
					<br />
					<p><h5>Direção e Planejamento</h5>	
					Vinicius Russo</p>
					<p><h5>Design e Ilustração</h5>
					Juliana Ciapolletto</p>
					<br /><br />
					<h4 class="tecnology">Tecnologias:</h4>
					<br />	
					<p><h5>Sistema</h5>
					Wordpress</p>
				</div>	
			</div>			
		</div>
		
		<?php include 'noticias-sidebar.php'; ?>
		<div class="clear"></div>
		
	</div>
</div>
<?php get_footer(); ?>