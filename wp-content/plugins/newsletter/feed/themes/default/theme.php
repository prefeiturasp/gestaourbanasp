<?php

// Mandatory
global $post;

/*
 * Some variables are already defined:
 *
 * - $posts Contains all the posts (new and old) with the maximum specified on Feed by Mail configuration.
 * - $last_run Is the last time an email was sent and the time value to use to split the posts
 * - $theme_options An array with all theme options
 * - $theme_url Is the absolute URL to the theme folder used to reference images
 * - $theme_subject Will be the email subject if set by this theme
 *
 * Pay attention that on this new version there is no more the user available: the theme is used once to compose the
 * email and then sent to the delivery engine.
 *
 * Refer to http://codex.wordpress.org/Function_Reference/setup_postdata for the post cicle. It MUST be written as
 *
 * foreach($new_posts as $post) { setup_postdata($post)
 *
 */

// Get new an old posts
list($new_posts, $old_posts) = NewsletterModule::split_posts($posts, $last_run);
$color = $theme_options['theme_color'];
?><!DOCTYPE html>
<html>
    <head>
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
                        <p>This is a demo</p>
                        <div style="padding: 10px 20px; color: #000; font-size: 20px; background-color: #EFEFEF; border-bottom: 1px solid #ddd">
                            <?php echo $theme_options['theme_title']; ?>
                        </div>
                        <div style="padding: 20px; background-color: #fff; line-height: 18px">

                            <p><small><?php echo $options['theme_email_url']; ?></small></p>

                            <?php echo $options['theme_preamble']; ?>

                            <table>
                                <?php foreach($new_posts as $post) { setup_postdata($post); ?>

                                    <tr>
                                        <td colspan="2">
                                            <br>
                                            <a style="text-decoration: none; font-size: 18px" href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a><br>
                                        </td>
                                    </tr>
                                    <?php if ($theme_options['theme_full_post'] == 1) { ?>
                                    <tr>
                                        <td colspan="2">
                                            <?php the_content(); ?>
                                        </td>
                                    </tr>
                                    <?php } else { ?>
                                    <tr>
                                        <td valign="top" style="padding-right: 10px;">
                                            <?php if (isset($theme_options['theme_thumbnails'])) { ?>
                                            <a href="<?php echo get_permalink(); ?>"><img src="<?php echo NewsletterModule::get_post_image($post->ID, 'thumbnail', WP_CONTENT_URL . '/newsletter/feed/images/blank.png'); ?>" withd="100" height="100"></a>
                                            <?php } ?>
                                        </td>
                                        <td valign="top">
                                            <small><?php echo preg_replace('/<\\/*p>/i', '', get_the_excerpt()); ?></small>
                                        </td>
                                    </tr>
                                    <?php } ?>

                                <?php } ?>

                            </table>

                            <?php if (!empty($old_posts)) { ?>
                                <table>
                                   <?php foreach($old_posts as $post) { setup_postdata($post); ?>
                                   <tr>
                                       <td valign="top">
                                        <a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
                                       </td>
                                   </tr>
                                   <?php } ?>
                               </table>
                             <?php } ?>

                            <p><small><?php echo $theme_options['theme_profile_url']; ?></small></p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>