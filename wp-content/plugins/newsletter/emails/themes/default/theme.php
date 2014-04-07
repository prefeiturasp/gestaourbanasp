<?php
/*
 * Some variables are already defined:
 *
 * - $theme_options An array with all theme options
 * - $theme_url Is the absolute URL to the theme folder used to reference images
 * - $theme_subject Will be the email subject if set by this theme
 *
 */

global $newsletter, $post;

$color = $theme_options['theme_color'];
if (empty($color)) $color = '#0088cc';

if (isset($theme_options['theme_posts'])) $posts = get_posts(array('showposts'=>10));

?><!DOCTYPE html>
<html>
    <head>
        <!-- Not all email client take care of styles inserted here -->
        <style type="text/css" media="all">
            a {
                text-decoration: none;
                color: <?php echo $color; ?>;
            }
        </style>
    </head>
    <body style="background-color: #ddd; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666; margin: 0 auto; padding: 0;">
        <br>
        <table align="center">
            <tr>
                <td style="font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666;">
                    <div style="text-align: left; max-width: 500px; border-top: 10px solid <?php echo $color; ?>; border-bottom: 3px solid <?php echo $color; ?>;">
                        <div style="padding: 10px 20px; color: #000; font-size: 20px; background-color: #EFEFEF; border-bottom: 1px solid #ddd">
                            <?php echo get_option('blogname'); ?>
                        </div>
                        <div style="padding: 20px; background-color: #fff; line-height: 18px">

                            <p style="text-align: center"><a target="_blank"  href="{email_url}">View this email online</a></p>

                            <?php if (empty($posts)) { ?>
                            <p>Here you can start to write your message. Be polite with your readers! Do not forget the subsject of this message.</p>
                            <?php } else { ?>
                            <table cellpadding="5">
                                <?php foreach ($posts as $post) { setup_postdata($post); ?>
                                    <tr>
                                        <?php if (isset($theme_options['theme_thumbnails'])) { ?>
                                        <td><a target="_blank"  href="<?php echo get_permalink(); ?>"><img width="75" src="<?php echo newsletter_get_post_image($post->ID); ?>" alt="image"></a></td>
                                        <?php } ?>
                                        <td valign="top">
                                            <a target="_blank"  href="<?php echo get_permalink(); ?>" style="font-size: 20px; line-height: 26px"><?php the_title(); ?></a>
                                            <?php if (isset($theme_options['theme_excerpts'])) the_excerpt(); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <?php } ?>

                            <p>To change your subscription, <a target="_blank"  href="{profile_url}">click here</a>.
                        </div>

                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>