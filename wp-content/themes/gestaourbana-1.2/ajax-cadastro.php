<?php
	$error_email = false;
	
	if ($_POST && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])<=7 &&
         strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
	{ 
		if (trim($_POST['email']) == '' || !validEmail($_POST['email'])) 
		{
			$error_email = true;
		}
		if (!$error_email)
		{
			mail('gestaourbanasp@prefeitura.sp.gov.br', '(Gestão)Novo e-mail para cadastro em base!', $_POST['email']);
			$enviado = true;
		}
	}
	
	function tratarStrings($string){
    	return htmlentities(utf8_decode(trim($string)));
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





if ($error_email)
{
	echo '<div style="color:red" class="error">E-mail incorreto</div>';
}

if ($enviado)
{
	echo '<div class="sucess">Seu e-mail foi cadastrado com sucesso</div>';
}


?>