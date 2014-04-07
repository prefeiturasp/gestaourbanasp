<table class="form-table">
    <tr>
        <th>Base color</th>
        <td><?php $controls->color('theme_color'); ?></td>
    </tr>
    <tr>
        <th>Add latest posts</th>
        <td><?php $controls->checkbox('theme_posts'); ?></td>
    </tr>
    <tr>
        <th>Add post thumbnails</th>
        <td><?php $controls->checkbox('theme_thumbnails'); ?></td>
    </tr>
    <tr>
        <th>Add post excerpts</th>
        <td><?php $controls->checkbox('theme_excerpts'); ?></td>
    </tr>
</table>