<?php
$posts = get_posts();
$theme_options['theme_background'] = 'purple.png';
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <style type="text/css">
    </style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-image:url(<?php echo $theme_url; ?>/images/<?php echo $theme_options['theme_background']; ?>);background-repeat: repeat-x;background-color: #efefef;">

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" valign="top">

            <table border="0" cellpadding="10" cellspacing="0" width="600" style="background-color: #FAFAFA;">
                <tr>
                    <td valign="top">
                        <table border="0" cellpadding="10" cellspacing="0" width="100%">
                            <tr>
                                <td valign="top" align="left" style="color: #505050;font-family: Arial;font-size: 10px;">
                                    You are receiving this email because you subscribed to <?php echo get_option('blogname'); ?>. <a href="{profile_url}">Click here to change your subscription</a>.
                                </td>
                                <td valign="top" width="190" align="left" style="color: #505050;font-family: Arial;font-size: 10px;">
                                    Is this email not displaying correctly?<br><a href="{email_url}" style="color: #336699;font-weight: normal;text-decoration: underline;">View it online</a>.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF">
                <tr>
                    <td align="center" valign="middle" height="150" style="color: #202020;font-family: Arial;font-size: 34px;font-weight: bold;line-height: 100%;padding: 5px 0 -2px 0;border-width: 1px 0px;border-style: solid;border-color: #dddddd;">
                        <?php echo get_option('blogname'); ?>
                    </td>
                </tr>
            </table>
            <table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF">
                <tr>

                    <td valign="top">
                        <table border="0" cellpadding="20" cellspacing="0" width="200">
                            <tr>
                                <td valign="top" align="left" style="color: #505050;font-family: Arial;font-size: 12px;line-height: 150%;">
                                    Sidebar text
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left">
                                    <?php foreach (array('facebook','twitter','youtube') as $social) { ?>
                                    <?php if (!empty($theme_options["theme_$social"])) { ?>
                                    <a href="<?php echo $theme_options["theme_$social"]; ?>"><img src="<?php echo $theme_url; ?>/images/<?php echo $social; ?>.png" alt="<?php echo $social; ?>"></a>
                                    <?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td valign="top">
                        <table border="0" cellpadding="20" cellspacing="0" width="100%" bgcolor="#ffffff">
                            <tr>
                                <td valign="top">

                                    <?php foreach ($posts as $post) { setup_postdata($post); ?>
                                    <h2 style="color: #202020;font-family: Arial;font-size: 20px;font-weight: bold;margin-top: 0;margin-bottom: 10px;border-bottom: 1px solid #efefef;">
                                        <?php the_title(); ?>
                                    </h2>
                                    <center><img src="<?php echo newsletter_get_post_image($post->ID, 'medium');?>"></center>
                                    <div style="color: #505050;font-family: Arial;font-size: 14px;line-height: 150%;">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    <?php } ?>

                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
        </td>
    </tr>

</table>
<br><br>

</body>
</html>