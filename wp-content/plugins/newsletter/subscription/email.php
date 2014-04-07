<?php
/*
 * To customize this file, do not edit it. Instead use the sample alternative email-alternative.php
 * and copy it on
 *
 * wp-content/extensions/newsletter/subscription/email.php
 *
 * creating the folders as needed. Then customize that file.
 *
 * Remember to keep at least the line of code
 *
 * <?php echo $message; ?>
 *
 * which prints the current email body created by Newsletter based on te current subscription
 * process step.
 */

if (is_file(WP_CONTENT_DIR . '/extensions/newsletter/subscription/email.php')) {
  include WP_CONTENT_DIR . '/extensions/newsletter/subscription/email.php';
  return;
}

?><!DOCTYPE html>
<html>
    <head>
        <style type="text/css" media="all">
            a {
                text-decoration: none;
                color: #0088cc;
            }
        </style>
    </head>
    <body style="background-color: #ddd; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666; margin: 0 auto; padding: 0;">
        <br>
        <table align="center">
            <tr>
                <td style="font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666;">
                    <div style="text-align: left; max-width: 500px; border-top: 10px solid #43A4D0; border-bottom: 3px solid #43A4D0;">
                        <div style="padding: 10px 20px; color: #000; font-size: 20px; background-color: #EFEFEF; border-bottom: 1px solid #ddd">
                            <?php echo get_option('blogname'); ?>
                        </div>
                        <div style="padding: 20px; background-color: #fff; line-height: 18px">

                            <?php echo $message; ?>

                        </div>

                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>