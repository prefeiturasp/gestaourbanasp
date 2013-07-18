<?php
/*
Template Name: Contato
*/
?>

<?php get_header(); ?>

<?php
  $error_name = false;
  $error_email = false;
  $error_message = false;
  $enviado = false;
  
  if ($_POST && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])<=7 &&
         strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
  {
    if (trim(@$_POST['post_name']) == '') 
    {
      $error_name = true;
    } 
    if (trim(@$_POST['email']) == '' || !validEmail(@$_POST['email'])) 
    {
      $error_email = true;
    }
    if (trim(@$_POST['message']) == '') 
    {
      $error_message = true;
    }
    if (!$error_email && !$error_message && !$error_name)
    {
      $mensagem = "Nome: " . tratarStrings($_POST['post_name'])."\n";
      $mensagem .= "Email: " . tratarStrings($_POST['email'])."\n";
      $mensagem .= "Mensagem: " . tratarStrings($_POST['message'])."\n"; 
      mail('gestaourbanasp@prefeitura.sp.gov.br', 'Contato gestão urbana!', $mensagem);
      $enviado = true;
      $_POST['post_name'] = $_POST['email'] = $_POST['message'] = null;
    }
  }
  
  function tratarStrings($string){
      return utf8_decode(trim($string));
  }
  /**
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/
function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}
?>
<div id="default-inner" class="contato">
  <div class="wrapper">
    <div class="left">
      <div class="inner">
        <h3>Contato</h3>
        <h5>Secretaria Municipal de Desenvolvimento <br />Urbano (SMDU) – Prefeitura de São Paulo</h5>
        <p>Rua São Bento, 405, Centro – 17º e 18º andar<br />
        CEP 01011-100 – São Paulo – SP<br />
        Telefone: (11) 3113-7500</p>
        <br />

<p>Atenção<br />
Pedidos de serviço, obras, fiscalização ou vistorias não serão atendidos por este formulário e devem ser formalizados no Sistema SAC, da Central 156 ou nas Praças de Atendimento das Subprefeituras. Com o registro do pedido, você, cidadão(ã), obtém um número para acompanhamento da solicitação.</p>

        <?php if ($enviado) {
          echo '<h4 class="sent" style="color:blue">Mensagem enviada.</h4>';
        } ?>
        <div class="form">
          <form action="<?php bloginfo('url')?><?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
            <!-- <input type="hidden" name="form_title"/> -->
            <p><span class="label">Nome</span> (obrigatório)<br />
            <input type="text" name="post_name" value="<?php echo @$_POST['post_name']; ?>" />
            <?php if($_POST && $error_name) { echo '<div class="error">Por favor, digite seu nome!</div>'; } ?></p>
            <p><span class="label">E-mail</span> (obrigatório)<br />
            <input type="text" name="email" value="<?php echo @$_POST['email']; ?>" />
            <?php if($_POST && $error_email) { echo '<div class="error">Por favor, um e-amil válido!</div>'; } ?></p>
            <p><span class="label">Mensagem</span> (obrigatório)<br />
            <textarea name="message"><?php echo @$_POST['message']; ?></textarea>
            <?php if($_POST && $error_message) { echo '<div class="error">Por favor, digite a mensagem!</div>'; } ?></p>
            <p><input type="submit" value="Enviar" /></p>
          </form>
          <?php //echo do_shortcode('[cfdb-save-form-post]') ?>
        </div>
      </div>
    </div>    
    
    <?php include('noticias-sidebar.php'); ?>
    <div class="clear"></div>
    
  </div>
</div>

<?php get_footer(); ?>