<?php
/*
 * This is a pre packaged theme options page. Every option name
 * must start with "theme_" so Newsletter can distinguish them from other
 * options that are specific to the object using the theme.
 *
 * An array of theme default options should always be present and that default options
 * should be merged with the current complete set of options as shown below.
 *
 * Every theme can define its own set of options, the will be used in the theme.php
 * file while composing the email body. Newsletter knows nothing about theme options
 * (other than saving them) and does not use or relies on any of them.
 *
 * For multilanguage purpose you can actually check the constants "WP_LANG", until
 * a decent system will be implemented.
 */
$theme_defaults = array(
    'theme_title'=>get_option('blogname'),
    'theme_email_url'=>'You\'re receiving this email because you subscribed it at ' . get_option('blogname') .
    ' as {email}. To read this email online <a href="{email_url}">click here</a>. To modify your subscription <a href="{profile_url}">click here</a>.',
    'theme_profile_url'=>'To modify your subscription, <a href="{profile_url}">click here</a>.',
    'theme_color' =>'#0088cc',
    'theme_max_posts' => '10',
    'theme_full_post' => '0',
    );

// Mandatory!
$controls->merge_defaults($theme_defaults);
?>
<table class="form-table">
    <tr valign="top">
        <th>Title</th>
        <td>
            <?php $controls->text('theme_title', 70); ?>
        </td>
    </tr>
    <tr valign="top">
        <th>Header message (small font)</th>
        <td>
            <?php $controls->textarea('theme_email_url'); ?>
        </td>
    </tr>
    <tr valign="top">
        <th>Preamble (before the post list)</th>
        <td>
            <?php $controls->wp_editor('theme_preamble'); ?>
        </td>
    </tr>
    <tr valign="top">
        <th>Footer message (small font)</th>
        <td>
            <?php $controls->textarea('theme_profile_url'); ?>
        </td>
    </tr>
    <tr>
        <th>Base color</th>
        <td><?php $controls->color('theme_color'); ?></td>
    </tr>
    <tr>
        <th>How to show posts</th>
        <td><?php $controls->select('theme_full_post', array(0=>'Excerpt', 1=>'Full content')); ?></td>
    </tr>
    <tr>
        <th>Show thumbnails</th>
        <td><?php $controls->checkbox('theme_thumbnails'); ?></td>
    </tr>
</table>
