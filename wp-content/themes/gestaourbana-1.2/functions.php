<?php

  add_editor_style('style.css');
  add_editor_style('css/style-entenda-introducao.css');
  add_editor_style('css/style-entenda-etapas.css');

  /**
   * SETUP THEME IMAGES SIZES
   */
  if ( function_exists( 'add_theme_support' ) ) {
  add_theme_support( 'post-thumbnails' );
    add_image_size( '96xX', 96, 0 );
    add_image_size( '150xX', 150, 0 );
    add_image_size( '170xX', 170, 0 );
    add_image_size( '657xX', 657, 0 );
    add_image_size( '510xX', 510, 0 );
    add_image_size( '365x195', 664, 195, true);
    add_image_size( '470x270', 470, 270, true);
  }

  /**
   * SETUP MENUS
   */
  function register_my_menus() {
    register_nav_menus(
      array(
        'header-menu' => __( 'Header Menu' ),
        'extra-menu' => __( 'Footer Menu' ),
        'revisao-menu' => __('Revisão dos instrumentos menu'),
        'arco-menu' => __('Arco do futuro menu'),
        'territorio-menu' => __('Território CEU menu'),
        'dialogo-menu' => __('Centro Diálogo Aberto'),
        'projetos-especiais' => __('Projetos Especiais'),
        'menu-interno-plano-diretor' => __('Menu interno - Plano Diretor'),
        'menu-interno-parcelamento-ocupacao-solo' => __('Menu interno - Parcelamento e Ocupação do Solo')
      )
    );
  }
  add_action( 'init', 'register_my_menus' );

  /**
   * SETUP PAGE NAVIGATION
   */

  if ( ! function_exists( 'the_content_nav' ) ) :
  /**
   * Display navigation to next/previous pages when applicable
   */
  function the_content_nav() {
    global $wp_query;

    if ( $wp_query->max_num_pages > 1 ) :?>
      <div class="pages">
        <div class="prev"><?php echo previous_posts_link( 'Anterior' ); ?></div>
        <div class="next"><?php echo next_posts_link( 'Próxima' ); ?></div>
        <div class="clear"></div>
      </div>
    <?php endif;
  }
  endif; // twentyeleven_content_nav

  /**
   * SETUP COMMENTS
   */

   /* function alter_comment_form_fields($fields){
      //if (esc_attr($commenter['comment_author']) == '') { $commenter['comment_author'] = __('Seu Nome*:'); }
      $commenter['comment_author'] = __('Seu Nome*:');
      //if (esc_attr($commenter['comment_email']) == '') { $commenter['comment_email'] = __('Seu Email*:'); }
      $commenter['comment_email'] = __('Seu Email*:');
      //if (esc_attr($commenter['comment_url']) == '') { $commenter['comment_url'] = __('Seu site*:'); }
      $commenter['comment_url'] = __('Seu site*:');
      //if (esc_attr($commenter['comment_comment']) == '') { $commenter['comment_comment'] = __('Sua mensagem*:'); }
      $commenter['comment_comment'] = __('Sua mensagem*:');
      $fields['author'] = '<input type="text" name="author" id="author" title="' . esc_attr( $commenter['comment_author'] ) . '" class="defaultText defaultTextActive" ' . $aria_req . '>';
        $fields['email'] = '<input type="text" name="email" id="email" title="' . esc_attr( $commenter['comment_email'] ) . '" class="defaultText defaultTextActive" ' . $aria_req . '>';
        $fields['url'] = '<input type="text" name="url" id="url" title="' . esc_attr( $commenter['comment_url'] ) . '" class="defaultText defaultTextActive" ' . $aria_req . '>';
        $fields['comment'] = '<textarea name="comment" class="defaultText defaultTextActive" title="'.$commenter['comment_comment'].'"></textarea>';
        return $fields;
    }

    add_filter('comment_form_default_fields','alter_comment_form_fields');*/

  /**
   * SETUP THEME PAGE TYPES
   */

  /********************************************************************************/
  /**************** CUSTOM AGENDA                   *******************************/
  /********************************************************************************/
  add_action('init', 'agenda_register');
  function agenda_register() {
    $labels = array(
      'name' => __('Agenda'),
      'singular_name' => __('Agenda'),
      'add_new' => __('Novo evento'),
      'add_new_item' => __('Adicionar'),
      'edit_item' => __('Editar'),
      'new_item' => __('Novo'),
      'view_item' => __('Ver'),
      'search_items' => __('Procurar'),
      'not_found' =>  __('Nada encontrado'),
      'not_found_in_trash' => __('Nada encontrado na lixeira'),
      'parent_item_colon' => ''
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true,
      'menu_icon' => get_stylesheet_directory_uri() . '/images/admin/icon-calendar.png',
      'rewrite' => array('slug' => 'agenda','with_front' => FALSE),
      'capability_type' => 'post',
      '_builtin' => false,
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array('title','editor'/*,'excerpt'*/, 'thumbnail'),
      //'taxonomies' => array('category', 'post_tag')
      );
    register_post_type( 'agenda' , $args );
    flush_rewrite_rules( );
  }

  // Show Columns

  add_filter ("manage_edit-agenda_columns", "agenda_edit_columns");
  add_action ("manage_posts_custom_column", "agenda_custom_columns");

  function agenda_edit_columns($columns) {

  $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "title" => "Evento",
      "col_ev_date" => "Data",
      "col_ev_times" => "Hora",
      "col_ev_location" => "Local"
      );
  return $columns;
  }

  function agenda_custom_columns($column)
  {
  global $post;
  $custom = get_post_custom();
  switch ($column)
  {
  case "col_ev_date":
      // - show dates -
      if ($custom["agenda_show_date"][0] != '')
      {
        echo date('d/m/Y',$custom["agenda_show_date"][0]);
      } else {
        echo 'Num futuro não muito distante';
      }
  break;
  case "col_ev_times":
      // - show times -
      echo $custom["agenda_hour"][0];
  break;
  case "col_ev_location":
      // - show times -
      echo $custom["agenda_location"][0];
  break;

  }
  }

  // Show Meta-Box

  add_action( 'admin_init', 'agenda_create' );

  function agenda_create() {
      add_meta_box('agenda_meta_date', 'Data', 'agenda_meta_date', 'agenda');
      add_meta_box('agenda_meta_hour', 'Horários', 'agenda_meta_hour', 'agenda');
      add_meta_box('agenda_meta_location', 'Local', 'agenda_meta_location', 'agenda');
  }

  function agenda_meta_date () {
    // - grab data -

    global $post;
    $custom = get_post_custom($post->ID);
    $meta_value = $custom["agenda_show_date"][0];

    //if ($meta_value == null) {$meta_value = time(); }

    // - output -

    ?>
    <div class="meta">
      <input type="hidden" name="events-nonce" value="<?php echo wp_create_nonce( 'events-nonce' ); ?>" />
      <input name="agenda_show_date_d" class="location" value="<?php if ($meta_value != null) { echo date('d',$meta_value); }?>" size="2"/>
      <input name="agenda_show_date_m" class="location" value="<?php if ($meta_value != null) { echo date('m',$meta_value); }?>" size="2"/>
      <input name="agenda_show_date_y" class="location" value="<?php if ($meta_value != null) { echo date('Y',$meta_value); }?>" size="4"/>
      <em>DD/MM/YYYY</em>
    </div>
    <?php
  }

  function agenda_meta_location () {
    // - grab data -

    global $post;
    $custom = get_post_custom($post->ID);
    $meta_value = $custom["agenda_location"][0];

    // - output -

    ?>
    <div class="meta">
      <input name="agenda_location" class="location" value="<?php echo $meta_value; ?>" style="width:90%"/>
    </div>
    <?php
  }

  function agenda_meta_hour () {
    // - grab data -

    global $post;
    $custom = get_post_custom($post->ID);
    $meta_value = $custom["agenda_hour"][0];

    // - output -

    ?>
    <div class="meta">
      <textarea name="agenda_hour" class="hour" style="width:90%"><?php echo $meta_value; ?></textarea>
    </div>
    <?php
  }

  // Save Data

  add_action ('save_post', 'save_agenda');

  function save_agenda(){

    global $post;

    // - still require nonce

    if ( !wp_verify_nonce( $_POST['events-nonce'], 'events-nonce' )) {
        return $post->ID;
    }

    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;

    //if(!isset($_POST["agenda_location"])):
    //return $post;
    //endif;
    $updatelocation = $_POST["agenda_location"];
    update_post_meta($post->ID, "agenda_location", $updatelocation );

    if($_POST["agenda_show_date_y"] != ''):
      $updatedate = strtotime($_POST["agenda_show_date_y"].'-'.$_POST["agenda_show_date_m"].'-'.$_POST["agenda_show_date_d"].' 23:59:59');
    else :
      $updatedate = '';
    endif;
    update_post_meta($post->ID, "agenda_show_date", $updatedate );

    //if(!isset($_POST["agenda_hour"])):
    //return $post;
    //endif;
    $updatehour = $_POST["agenda_hour"];
    update_post_meta($post->ID, "agenda_hour", $updatehour );

  }

  // Customize Update Messages

  add_filter('post_updated_messages', 'events_updated_messages');

  function events_updated_messages( $messages ) {

    global $post, $post_ID;

    $messages['agenda'] = array(
      0 => '', // Unused. Messages start at index 1.
      1 => sprintf( __('Event updated. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => __('Event updated.'),
      /* translators: %s: date and time of the revision */
      5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
      6 => sprintf( __('Event published. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
      7 => __('Event saved.'),
      8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
      9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>'),
        // translators: Publish box date format, see http://php.net/date
        date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
      10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );

    return $messages;
  }

  // JS Datepicker UI

  function events_styles() {
      global $post_type;
      if( 'agenda' != $post_type )
          return;
      wp_enqueue_style('ui-datepicker', get_bloginfo('template_url') . '/css/jqueryui/jquery-ui-1.10.2.custom.min.css');
  }

  function events_scripts() {
      global $post_type;
      if( 'agenda' != $post_type )
          return;
      wp_enqueue_script('jquery-ui', get_bloginfo('template_url') . '/js/jquery-ui-1.10.2.custom.min.js', array('jquery'));
      wp_enqueue_script('ui-datepicker', get_bloginfo('template_url') . '/js/jquery.ui.datepicker.min.js');
      wp_enqueue_script('custom_script', get_bloginfo('template_url').'/js/script-admin.js', array('jquery'));
  }

  add_action( 'style-admin.css', 'events_styles', 1000 );

  add_action( 'init', 'add_agenda_rules' );
  function add_agenda_rules() {
      add_rewrite_rule(
          "([^/]+)/data/?([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/?$",
          "index.php?name=$matches[1]&date=$matches[2]",
          "top");
  }

  /********************************************************************************/

  /*******************************************************************************/

  /********************************************************************************/
  /**************** CUSTOM NOTÍCIAS                 *******************************/
  /********************************************************************************/
  add_action('init', 'noticias_register');
  function noticias_register() {
    $labels = array(
      'name' => __('Notícias'),
      'singular_name' => __('Notícia'),
      'add_new' => __('Nova notícia'),
      'add_new_item' => __('Adicionar'),
      'edit_item' => __('Editar'),
      'new_item' => __('Novo'),
      'view_item' => __('Ver'),
      'search_items' => __('Procurar'),
      'not_found' =>  __('Nada encontrado'),
      'not_found_in_trash' => __('Nada encontrado na lixeira'),
      'parent_item_colon' => ''
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true,
      'menu_icon' => get_stylesheet_directory_uri() . '/images/admin/icon_news.png',
      'rewrite' => array('slug' => 'noticias'),
      'capability_type' => 'post',
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array('title','editor','thumbnail','excerpt','comments'),
      'taxonomies' => array('newscategory', 'post_tag')
      );
    register_post_type( 'noticias' , $args );
    flush_rewrite_rules( );
  }
  
  /*******************************************************************************/
  /************************CUSTOM TAXONOMY PROJETOS***************/
  add_action('init', 'add_project_taxonomy', 0);
  function add_project_taxonomy() {
    $labels = array(
        'name' => _x( 'Projetos', 'taxonomy general name' ),
        'singular_name' => _x( 'Category', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Categories' ),
        'popular_items' => __( 'Popular Categories' ),
        'all_items' => __( 'Todos os projetos' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Category' ),
        'update_item' => __( 'Update Category' ),
        'add_new_item' => __( 'Add New Category' ),
        'new_item_name' => __( 'New Category Name' ),
        'separate_items_with_commas' => __( 'Separate categories with commas' ),
        'add_or_remove_items' => __( 'Add or remove categories' ),
        'choose_from_most_used' => __( 'Choose from the most used categories' ),
    );

    register_taxonomy('projetos', array('noticias', 'agenda', 'biblioteca'), array(
        'label' => __('Projetos'),
        'labels' => $labels,
        'hierarchical' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'projetos' ),
        'show_in_nav_menus' => true,
    ));
  }
  
  function add_project_meta_data() {
    echo '<div class="form-field">';
      echo '<label for="term_meta[custom_term_meta]">Cor:</label>';
      echo '<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]">';
    echo '</div>';
    
    echo '<div class="form-field">';
      echo '<label for="term_meta[custom_term_meta]">Aparecer no topo:</label>';
      echo '<input type="text" name="term_meta[custom_term_meta_topo]" value="S">';
    echo '</div>';
  }
  add_action('projetos_add_form_fields', 'add_project_meta_data', 10, 2 );
  
  function edit_project_meta_data($term) {
    $termId = $term->term_id;
    $termMeta = get_option('taxonomy_'.$termId);
    
    if (!empty($termMeta['custom_term_meta'])) {
      echo '<tr class="form-field">';
        echo '<th scope="row" valign="top">';
          echo '<label for="term_meta[custom_term_meta]">Cor:</label>';
        echo '</th>';
        echo '<td>';
          echo '<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="'.$termMeta['custom_term_meta'].'">';
        echo '</td>';
      echo '</tr>';
      
      echo '<tr class="form-field">';
        echo '<th scope="row" valign="top">';
          echo '<label for="term_meta[custom_term_meta]">Aparecer no topo:</label>';
        echo '</th>';
        echo '<td>';
          echo '<input type="text" name="term_meta[custom_term_meta_topo]" id="term_meta[custom_term_meta_topo]" value="'.$termMeta['custom_term_meta_topo'].'">';
        echo '</td>';
      echo '</tr>';
    }
  }
  add_action('projetos_edit_form_fields', 'edit_project_meta_data', 10, 2 );
  
  function save_projetos_custom_meta($term_id) {
    if ( isset( $_POST['term_meta'] ) ) {
      $t_id = $term_id;
      $term_meta = get_option( "taxonomy_$t_id" );
      $cat_keys = array_keys( $_POST['term_meta'] );
      foreach ( $cat_keys as $key ) {
        if ( isset ( $_POST['term_meta'][$key] ) ) {
          $term_meta[$key] = $_POST['term_meta'][$key];
        }
      }
      // Save the option array.
      update_option( "taxonomy_$t_id", $term_meta );
    }
  }
  
  add_action( 'edited_projetos', 'save_projetos_custom_meta', 10, 2 );  
  add_action( 'create_projetos', 'save_projetos_custom_meta', 10, 2 );

  add_filter("manage_edit-noticias_columns", "noticias_edit_columns");
  function noticias_edit_columns($columns){
    $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "title" => "Title",
      "author" => "Author",
    "date" => "Data",
    );
    return $columns;
  }

  function create_noticiascategory_taxonomy() {

    $labels = array(
        'name' => _x( 'Categories', 'taxonomy general name' ),
        'singular_name' => _x( 'Category', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Categories' ),
        'popular_items' => __( 'Popular Categories' ),
        'all_items' => __( 'All Categories' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Category' ),
        'update_item' => __( 'Update Category' ),
        'add_new_item' => __( 'Add New Category' ),
        'new_item_name' => __( 'New Category Name' ),
        'separate_items_with_commas' => __( 'Separate categories with commas' ),
        'add_or_remove_items' => __( 'Add or remove categories' ),
        'choose_from_most_used' => __( 'Choose from the most used categories' ),
    );

    register_taxonomy('newscategory','noticias', array(
        'label' => __('Category'),
        'labels' => $labels,
        'hierarchical' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'noticias-category' ),
    ));
  }

add_action( 'init', 'create_noticiascategory_taxonomy', 0 );

  /********************************************************************************/

  /********************************************************************************/
  /**************** CUSTOM BIBLIOTECA               *******************************/
  /********************************************************************************/
  add_action('init', 'biblioteca_register');
  function biblioteca_register() {
    $labels = array(
      'name' => __('Biblioteca'),
      'singular_name' => __('Biblioteca'),
      'add_new' => __('Novo post'),
      'add_new_item' => __('Adicionar'),
      'edit_item' => __('Editar'),
      'new_item' => __('Novo'),
      'view_item' => __('Ver'),
      'search_items' => __('Procurar'),
      'not_found' =>  __('Nada encontrado'),
      'not_found_in_trash' => __('Nada encontrado na lixeira'),
      'parent_item_colon' => ''
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true,
      'menu_icon' => get_stylesheet_directory_uri() . '/images/admin/icon_library.png',
      'rewrite' => array('slug' => 'biblioteca'),
      'capability_type' => 'post',
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array('title',/*'editor','thumbnail','excerpt','comments'*/ 'page-attributes'),
      'taxonomies' => array('biblioteca-category')
      );
    register_post_type( 'biblioteca' , $args );
    flush_rewrite_rules( );
  }

  add_filter("manage_edit-biblioteca_columns", "biblioteca_edit_columns");
  function biblioteca_edit_columns($columns){
    $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "title" => "Title",
      "menu_order" => "Ordem",
      "author" => "Author",
    "date" => "Data",
    );
    return $columns;
  }

  function create_bibliotecacategory_taxonomy() {

    $labels = array(
        'name' => _x( 'Categories', 'taxonomy general name' ),
        'singular_name' => _x( 'Category', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Categories' ),
        'popular_items' => __( 'Popular Categories' ),
        'all_items' => __( 'All Categories' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Category' ),
        'update_item' => __( 'Update Category' ),
        'add_new_item' => __( 'Add New Category' ),
        'new_item_name' => __( 'New Category Name' ),
        'separate_items_with_commas' => __( 'Separate categories with commas' ),
        'add_or_remove_items' => __( 'Add or remove categories' ),
        'choose_from_most_used' => __( 'Choose from the most used categories' ),
    );

    register_taxonomy('librarycategory','biblioteca', array(
        'label' => __('Category'),
        'labels' => $labels,
        'hierarchical' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'biblioteca' ),
        'show_in_nav_menus' => false,
    ));
  }

  add_action( 'init', 'create_bibliotecacategory_taxonomy', 0 );

  add_action( 'admin_init', 'biblioteca_create' );

  function biblioteca_create() {
      add_meta_box('biblioteca_meta_link', 'Link', 'biblioteca_meta_link', 'biblioteca');
  }

  function biblioteca_meta_link () {
    // - grab data -

    global $post;
    $custom = get_post_custom($post->ID);
    $meta_value = $custom["library_link"][0];

    // - output -

    ?>
    <div class="meta">
      <input type="hidden" name="library-nonce" value="<?php echo wp_create_nonce( 'library-nonce' ); ?>" />
      <input name="library_link" class="link" value="<?php echo $meta_value; ?>" style="width:90%" />
    </div>
    <?php
  }

  add_action ('save_post', 'save_biblioteca');

  function save_biblioteca(){

    global $post;

    // - still require nonce

    if ( !wp_verify_nonce( $_POST['library-nonce'], 'library-nonce' )) {
        return $post->ID;
    }

    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;


    update_post_meta($post->ID, "library_link", $_POST['library_link'] );

  }

  /********************************************************************************/

  /********************************************************************************/
  /**************** CUSTOM PERGUNTAS FREQUENTES     *******************************/
  /********************************************************************************/
  add_action('init', 'faq_register');
  function faq_register() {
    $labels = array(
      'name' => __('Perguntas frequentes'),
      'singular_name' => __('Pergunta'),
      'add_new' => __('Nova pergunta'),
      'add_new_item' => __('Adicionar'),
      'edit_item' => __('Editar'),
      'new_item' => __('Nova'),
      'view_item' => __('Ver'),
      'search_items' => __('Procurar'),
      'not_found' =>  __('Nada encontrado'),
      'not_found_in_trash' => __('Nada encontrado na lixeira'),
      'parent_item_colon' => ''
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true,
      'menu_icon' => get_stylesheet_directory_uri() . '/images/admin/icon_faq.png',
      'rewrite' => array('slug' => 'faq'),
      'capability_type' => 'post',
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array('title','editor'/*,'thumbnail','excerpt','comments', 'page-attributes'*/)
      );
    register_post_type( 'wp_super_faq' , $args );
    flush_rewrite_rules( );
  }

  add_filter("manage_edit-faq_columns", "faq_edit_columns");
  function faq_edit_columns($columns){
    $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "title" => "Title",
      "author" => "Author",
    "date" => "Data",
    );
    return $columns;
  }

  /********************************************************************************/


/********************************************************************************/
  /**************** CUSTOM HOME SLIDER            *******************************/
  /******************************************************************************/
  add_action('init', 'slider_register');
  function slider_register() {
    $labels = array(
      'name' => __('Slider'),
      'singular_name' => __('Slider'),
      'add_new' => __('Novo slider'),
      'add_new_item' => __('Adicionar'),
      'edit_item' => __('Editar'),
      'new_item' => __('Novo'),
      'view_item' => __('Ver'),
      'search_items' => __('Procurar'),
      'not_found' =>  __('Nada encontrado'),
      'not_found_in_trash' => __('Nada encontrado na lixeira'),
      'parent_item_colon' => ''
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true,
      'menu_icon' => get_stylesheet_directory_uri() . '/images/admin/icon-calendar.png',
      'rewrite' => array('slug' => 'slider','with_front' => FALSE),
      'capability_type' => 'post',
      '_builtin' => false,
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array('title','editor', 'page-attributes'/*,'excerpt', 'thumbnail'*/),
      //'taxonomies' => array('category', 'post_tag')
      );
    register_post_type( 'slider' , $args );
    flush_rewrite_rules( );
  }

  // Show Columns

  //add_filter ("manage_edit-slider_columns", "slider_edit_columns");

  function slider_edit_columns($columns) {

  $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "title" => "Título",
      'menu_order' => "Ordem"
      );
  return $columns;
  }




  /********************************************************************************/

  /**
   * CALL ON ACTIVATE/DEACTIVATE THEME
   */
  wp_register_theme_activation_hook('gestaourbana', 'gestaourbana_theme_activate');
  wp_register_theme_deactivation_hook('gestaourbana', 'gestaourbana_theme_deactivate');

  /**
   *
   * @desc registers a theme activation hook
   * @param string $code : Code of the theme. This can be the base folder of your theme. Eg if your theme is in folder 'mytheme' then code will be 'mytheme'
   * @param callback $function : Function to call when theme gets activated.
   */
  function wp_register_theme_activation_hook($code, $function) {
      $optionKey="theme_is_activated_" . $code;
      if(!get_option($optionKey)) {
          call_user_func($function);
          update_option($optionKey , 1);
      }
  }

  /**
   * @desc registers deactivation hook
   * @param string $code : Code of the theme. This must match the value you provided in wp_register_theme_activation_hook function as $code
   * @param callback $function : Function to call when theme gets deactivated.
   */
  function wp_register_theme_deactivation_hook($code, $function)
  {
      // store function in code specific global
      $GLOBALS["wp_register_theme_deactivation_hook_function" . $code]=$function;

      // create a runtime function which will delete the option set while activation of this theme and will call deactivation function provided in $function
      $fn=create_function('$theme', ' call_user_func($GLOBALS["wp_register_theme_deactivation_hook_function' . $code . '"]); delete_option("theme_is_activated_' . $code. '");');

      // add above created function to switch_theme action hook. This hook gets called when admin changes the theme.
      // Due to wordpress core implementation this hook can only be received by currently active theme (which is going to be deactivated as admin has chosen another one.
      // Your theme can perceive this hook as a deactivation hook.)
      add_action("switch_theme", $fn);
  }

  function gestaourbana_theme_activate()
  {
      $default_pages = array(
          array(
              'title' => 'Index',
              'content' => '',
              'template' => 'index.php'
              ),
          array(
              'title' => 'Notícias',
              'content' => '',
              'template' => 'page-noticias.php'
              ),
          array(
              'title' => 'Participe',
              'content' => '',
              'template' => 'page-participe.php'
              ),
          array(
              'title' => 'Agenda',
              'content' => '',
              'template' => 'page-agenda.php'
              ),
          array(
              'title' => 'Biblioteca',
              'content' => '',
              'template' => 'page-biblioteca.php'
              ),
          array(
              'title' => 'A revisão participativa',
              'content' => file_get_contents(get_bloginfo('template_directory') . '/_html/a-revisao-participativa.html'),
              'template' => 'page-a-revisao-participativa.php'
              ),
          array(
              'title' => 'Etapas',
              'content' => file_get_contents(get_bloginfo('template_directory') . '/_html/etapas.html'),
              'template' => 'page-etapas.php'
              ),
          array(
              'title' => 'Perguntas frequentes',
              'content' => '',
              'template' => 'page-perguntas-frequentes.php'
              ),
          array(
              'title' => 'Contato',
              'content' => '',
              'template' => 'pagecontato.php'
              ),
          array(
              'title' => 'Termos de uso',
              'content' => '',
              'template' => 'page-termos_de_uso.php'
              ),
          array(
              'title' => 'Desenvolvimento',
              'content' => '',
              'template' => 'page-desenvolvimento.php'
              ),
          array(
              'title' => 'Equipe',
              'content' => '',
              'template' => 'page-equipe.php'
              )
      );
      $existing_pages = get_pages();
      $existing_titles = array();

      foreach ($existing_pages as $page)
      {
          $existing_titles[] = $page->post_title;
      }

      foreach ($default_pages as $new_page)
      {
          if( !in_array( $new_page['title'], $existing_titles ) )
          {
              // create post object
              $add_default_pages = array(
                  'post_title' => $new_page['title'],
                  'post_content' => $new_page['content'],
                  'post_status' => 'publish',
                  'post_type' => 'page',
                  'page_template' => $new_page['template']
                );

              // insert the post into the database
              $result = wp_insert_post($add_default_pages);
          }
      }

  }

  function gestaourbana_theme_deactivate()
  {
     // code to execute on theme deactivation
  }

  /**
   * REMOVE OPTIONS FROM MENU
   */

   function remove_menus () {
    global $menu;
      //$restricted = array(__('Dashboard'), __('Posts'), __('Media'), __('Links'), __('Pages'), __('Appearance'), __('Tools'), __('Users'), __('Settings'), __('Comments'), __('Plugins'));
      $restricted = array(__('Posts'));
      //$restricted = array();
      end ($menu);
      while (prev($menu)){
        $value = explode(' ',$menu[key($menu)][0]);
        if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
      }
    }
    add_action('admin_menu', 'remove_menus');


  /**
   * IMPORTED FUNCTION
   */
  if ( ! function_exists( 'twentyeleven_comment' ) ) :
  /**
   * Template for comments and pingbacks.
   *
   * To override this walker in a child theme without modifying the comments template
   * simply create your own twentyeleven_comment(), and that function will be used instead.
   *
   * Used as a callback by wp_list_comments() for displaying the comments.
   *
   * @since Twenty Eleven 1.0
   */
  function twentyeleven_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
      case 'pingback' :
      case 'trackback' :
    ?>
    <li class="post pingback">
      <p><?php _e( 'Pingback:', 'twentyeleven' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?></p>
    <?php
        break;
      default :
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
      <article id="comment-<?php comment_ID(); ?>" class="comment">
        <div class="comment-meta">
          <div class="comment-author vcard">
            <?php
              $avatar_size = 68;
              if ( '0' != $comment->comment_parent )
                $avatar_size = 39;

              echo get_avatar( $comment, $avatar_size );

              /* translators: 1: comment author, 2: date and time */
              printf( __( '%1$s em %2$s <span class="says">disse:</span>', 'twentyeleven' ),
                sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
                sprintf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                  esc_url( get_comment_link( $comment->comment_ID ) ),
                  get_comment_time( 'c' ),
                  /* translators: 1: date, 2: time */
                  sprintf( __( '%1$s as %2$s', 'twentyeleven' ), get_comment_date(), get_comment_time() )
                )
              );
            ?>

            <?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
          </div><!-- .comment-author .vcard -->

          <?php if ( $comment->comment_approved == '0' ) : ?>
            <em class="comment-awaiting-moderation"><?php _e( 'Seu comentário está aguardando moderação.', 'twentyeleven' ); ?></em>
            <br />
          <?php endif; ?>

        </div>

        <div class="comment-content"><?php comment_text(); ?></div>

        <div class="reply">
          <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Responder <span>&darr;</span>', 'twentyeleven' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
        </div><!-- .reply -->
      </article><!-- #comment-## -->

    <?php
        break;
    endswitch;
  }
  endif; // ends check for twentyeleven_comment()

// Sidebars

  register_sidebar(array(
  'name' => __( 'Notícias - Barra lateral' ),
  'id' => 'noticias-sidebar',
  'description' => __( 'Widgets nesta área serão apresentados nas páginas da seção notícias.' ),
  'before_title' => '<h1 class="widget-title">',
  'after_title' => '</h1>',
  'before_widget' => '<div id="%1$s" class="widget %2$s" box>',
  'after_widget'  => '</div>'
));

  register_sidebar(array(
  'name' => __( 'Páginas - Barra lateral' ),
  'id' => 'paginas-sidebar',
  'description' => __( 'Widgets nesta área serão apresentados nas páginas simples' ),
  'before_title' => '<h1 class="widget-title">',
  'after_title' => '</h1>',
  'before_widget' => '<div id="%1$s" class="widget %2$s" box>',
  'after_widget'  => '</div>'
));


// Widgets

//load widget
//add_action( 'widgets_init', 'register_my_widget' );

//init widget
//function register_my_widget() {
//    register_widget( 'noticias_widget' );
//}

//enclose widget
//class noticias_widget extends WP_Widget {}

  //Adding the Open Graph in the Language Attributes
// function add_opengraph_doctype( $output ) {
//     return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
//   }
// add_filter('language_attributes', 'add_opengraph_doctype');

//Lets add Open Graph Meta Info

// function insert_fb_in_head() {
//   global $post;
//   if ( !is_singular()) //if it is not a post or a page
//     return;
//         echo '<meta property="fb:admins" content="161422927240513"/>';
//         echo '<meta property="og:title" content="' . get_the_title() . '"/>';
//         echo '<meta property="og:type" content="article"/>';
//         echo '<meta property="og:url" content="' . get_permalink() . '"/>';
//         echo '<meta property="og:site_name" content="Gestão Urbana SP"/>';
//   if(!has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
//     $default_image="http://example.com/image.jpg"; //replace this with a default image on your server or an image in your media library
//     echo '<meta property="og:image" content="' . $default_image . '"/>';
//   }
//   else{
//     $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
//     echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
//   }
//   echo "
// ";
// }
// add_action( 'wp_head', 'insert_fb_in_head', 5 );

//Create a Walker to change the way wp_list_categories displays the output
//Here I'm removing the links from the output

function get_breadcrumb_data($postId, $taxonomyName, $left = false) {
    $outPut = array();
    $postTerms = get_the_terms($postId, $taxonomyName);
    
    if (!empty($postTerms)) {
      $postTermsId = array();
      foreach ($postTerms as $term) {
        if (empty($term->parent)) {
          $customTermMeta = get_term_by('slug', $term->slug, $taxonomyName, ARRAY_A);
          if (!empty($customTermMeta)) {
            $customTermMetaData = get_option('taxonomy_'.$term->term_id);
            $postTermsId[]= array(
              'id' => $term->term_id,
              'name' => $term->name,
              'color' => $customTermMetaData['custom_term_meta']
            );
          }
        }
      }
      
      if (!empty($postTermsId)) {
        foreach ($postTermsId as $postTermId) {
          $childTerms = get_terms( $taxonomyName, array( 'child_of' => $postTermId['id']));
          $outPut['parent']['name'] = $postTermId['name'];
          $outPut['parent']['color'] = $postTermId['color'];
          $outPut['parent']['childs'] = $childTerms;
        }
      }
      
      
      if (!empty($outPut)) {
        ?>
        <div class="taxonomy-breadcrumb <?php echo($left) ? 'left' :'';?>">
            <div class="float">
              <div class="breadcrumb-border" style="border-right: 20px solid <?php echo $outPut['parent']['color'];?>"></div>
              <div class="breadcrumb-first breadcrumb-text" style="background: <?php echo $outPut['parent']['color'];?>">
                <a href="">&bull;<?php echo $outPut['parent']['name'];?></a>
              </div>
            </div>  
            <?php
              foreach($outPut['parent']['childs'] as $child):
                  ?>
                    <div class="float">
                      <div class="breadcrumb-border-next" style="border-right: 14px solid <?php echo $outPut['parent']['color'];?>"></div>
                      <div class="breadcrumb-next breadcrumb-text" style="background: <?php echo $outPut['parent']['color'];?>">
                          <a href="">&bull;<?php echo $child->name;?></a>
                      </div>
                    </div>  
                  <?php
              endforeach;
            ?>
        </div>    
      <?php
      }  
    }
}

// Add Shortcode
function rodape_centro_dialogo_aberto_shorcode( $atts ) {

  wp_enqueue_style( 'widgets-cda', get_stylesheet_directory_uri().'/css/widgets-centro-dialogo-aberto.css' );

  // Attributes
  extract( shortcode_atts(
    array(
      'mostrar_biblioteca' => 'sim',
      'mostrar_noticias' => 'sim',
      'mostrar_navegador' => 'sim',
    ), $atts )
  );

  // Code
  $output = '
  </div>
  <div id="strip-rodape-centro-dialogo-aberto">
  <div class="wrapper">
    <h5>saiba mais sobre o centro diálogo aberto</h5>
    <div class="noticias">
      <h6 id="cda-noticias">Notícias</h6>
      <ul>
        ';
  $the_query = new WP_Query( 
    array ( 'tax_query' => array(
      array(
        'taxonomy' => 'post_tag',
        'field' => 'slug',
        'terms' => 'centro-dialogo-aberto'
      )
    ), 
    'orderby'=>'modified',
    'post_type' => 'noticias', 
    'posts_per_page' => 3 ) );
  while ( $the_query->have_posts() ):
    $the_query->the_post();
    $output .= '<li>
    <p class="news-date">'.get_the_date().'</p>
    <a class="news-title" href="'.get_permalink().'">' . get_the_title() . '</a>
    <a class="news-excerpt" href="'.get_permalink().'">' . get_the_excerpt() . '</a>
    </li>
        ';
  endwhile;
  wp_reset_postdata();
  $output .= '
      </ul>
      <a class="ver-todos" href="'.site_url('/tag/centro-dialogo-aberto/').'">» Ver todas as notícias sobre o Centro Diálogo Aberto</a>
    </div>
    <div class="biblioteca">
      <h6 id="cda-biblioteca">Biblioteca</h6>
      <ul>
        ';
  $the_query = new WP_Query( array ( 'tax_query' => array(
      array(
        'taxonomy' => 'post_tag',
        'field' => 'slug',
        'terms' => 'centro-dialogo-aberto'
      ),
    ),  'orderby'=>'modified', 'post_type' => 'biblioteca', 'posts_per_page' => 6 ) );
  while ( $the_query->have_posts() ):
    $the_query->the_post();
    $output .= '<li>» <a href="'.get_permalink().'">' . get_the_title() . '</a></li>
        ';
  endwhile;
  wp_reset_postdata();
  $output .= '
      </ul>
    </div>
  </div>
  </div>
  <div id="cda-navegador">
    <a class="biblioteca" href="#cda-biblioteca">biblioteca</a>
    <a class="noticias" href="#cda-noticias">noticias</a>
  </div>
  <div class="wrapper page-dialogo">';
  return $output;

}
add_shortcode( 'rodape-centro-dialogo-aberto', 'rodape_centro_dialogo_aberto_shorcode' );


// Add Shortcode
function form_aviso_plataforma_participativa_shorcode( $atts ) {

  wp_enqueue_style( 'widgets-cda', get_stylesheet_directory_uri().'/css/widgets-centro-dialogo-aberto.css' );

  // Attributes
  extract( shortcode_atts(
    array(
      'mostrar_biblioteca' => 'sim',
      'mostrar_noticias' => 'sim',
      'mostrar_navegador' => 'sim',
    ), $atts )
  );
  
  $output = '<!-- Begin MailChimp Signup Form -->
<div id="mc_embed_signup">
<form action="http://prefeitura.us7.list-manage.com/subscribe/post?u=dd2d95999955c44afed480456&amp;id=9ed02a74da" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
<i class="cda-icone-comentario"></i><p class="legend">Deixe aqui seu e-mail para ser avisado assim que nossa<br/> Ferramenta Participativa estiver pronta:</p>
<div class="mc-field-group">
  <input type="text" value="" placeholder="Nome" name="FNAME" class="" id="mce-FNAME">
</div>
<div class="mc-field-group">
  <input type="email" value="" placeholder="E-mail" name="EMAIL"  class="required email" id="mce-EMAIL">
</div>
  <div id="mce-responses" class="clear">
    <div class="response" id="mce-error-response" style="display:none"></div>
    <div class="response" id="mce-success-response" style="display:none"></div>
  </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;"><input type="text" name="b_dd2d95999955c44afed480456_9ed02a74da" value=""></div>
  <div class="clear"><input type="submit" value="Enviar" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
</form>
</div>

<!--End mc_embed_signup-->';
  return $output;

}
add_shortcode( 'aviso-plataforma-participativa', 'form_aviso_plataforma_participativa_shorcode' );

/********************************************************************************/
/* envia classe css da cor conforme projeto da noticia */
/********************************************************************************/
function cor_barra_conforme_projeto( $id )
{
    $terms = get_the_terms( $id, 'projetos' );
    $projeto = "";
    $classeCss = "";
    if ( $terms && ! is_wp_error( $terms ) ) :
        $first = true;
        foreach ( $terms as $term )
        {
            if ( $first )
            {
                $projeto = $term->slug;
                $first = false;
            }
            else
            {

            }
        }
        $classeCss = classe_css_conforme_projeto($projeto);
    endif;
    return $classeCss;
}

/********************************************************************************/
/* envia classe de css de cor conforme projeto */
/********************************************************************************/
function classe_css_conforme_projeto( $projeto )
{
    $classeCss = "";
    if ($projeto == "plano-diretor" || $projeto == "revisao-dos-instrumentos" || $projeto == 'parcelamento-ocupacao-solo')
    {
        $classeCss = "RevisaoInstrumentos";
    }
    else if ($projeto == "arco-do-futuro")
    {
        $classeCss = "ArcoFuturo";
    }
    else if ($projeto == "centro-dialogo-aberto")
    {
        $classeCss = "CentroDialogoAberto";
    }
    else if ($projeto == "territorios-ceu")
    {
        $classeCss = "TerritoriosCeu";
    }
    else if ($projeto == "projetos-especiais")
    {
        $classeCss = "ProjetosEspeciais";
    }
    return $classeCss;
}

/********************************************************************************/
/* envia classe css da cor conforme projeto do slider */
/********************************************************************************/
function cor_barra_conforme_projeto_slider( $id )
{
    $term = get_term( $id ,'projetos');
    $projeto = "";
    $classeCss = "";
    
    $projeto = $term->slug;
    $classeCss = classe_css_conforme_projeto($projeto);
    return $classeCss;
}

/********************************************************************************/
/* mostrar menu interno */
/********************************************************************************/
function mostrar_menu_interno( $projeto_id )
{
    $term = get_term( $projeto_id ,'projetos');
    $projeto = "";

    if ($term)
        {
        $projeto = $term->slug;

        if ($projeto == 'plano-diretor')
        {
            return wp_nav_menu( array( 'theme_location' => 'menu-interno-plano-diretor' ) );
        }
        
        if ($projeto == 'parcelamento-ocupacao-solo')
        {
            return wp_nav_menu( array( 'theme_location' => 'menu-interno-parcelamento-ocupacao-solo' ) );
        }
    }
}