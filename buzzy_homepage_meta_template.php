<?php
 
/*
Plugin Name: Home Page Promotions
Description: Enables using a custom home page that promotes books
Author: Adam Kiryk
Version: 1.0
Author URI: http://akiryk.github.com
*/

/**
 * Move all "advanced" metaboxes above the default editor
 */
add_action('edit_form_after_title', function() {
    global $post, $wp_meta_boxes;
    do_meta_boxes(get_current_screen(), 'advanced', $post);
    unset($wp_meta_boxes[get_post_type($post)]['advanced']);
});


/**
 * Adds a meta box to the post editing screen
 */
function buzzy_homepage_hero_meta() {
  // check for a template type
  $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
  $template_file = get_post_meta($post_id, '_wp_page_template', TRUE);

  if ($template_file == 'home-main.php') {
    add_meta_box( 'prfx_meta', __( 'Main Book Promotion', 'prfx-textdomain' ), 'buzzy_homepage_hero_meta_callback', 'page', 'advanced', 'high' );
  }
}
add_action( 'add_meta_boxes', 'buzzy_homepage_hero_meta' );

/**
 * Adds the meta box stylesheet when appropriate
 */
function buzzy_homepage_admin_styles(){
    global $typenow;
    if( $typenow == 'page' ) {
        wp_enqueue_style( 'prfx_meta_box_styles', plugin_dir_url( __FILE__ ) . 'buzzy_homepage_meta.css' );
    }
}
add_action( 'admin_print_styles', 'buzzy_homepage_admin_styles' );


/**
 * Loads the image management javascript
 */
function buzzy_homepage_image_enqueue() {
    global $typenow;
    if( $typenow == 'page' ) {
        wp_enqueue_media();
 
        // Registers and enqueues the required javascript.
        wp_register_script( 'meta-box-image', plugin_dir_url( __FILE__ ) . 'meta-box-image.js', array( 'jquery' ) );
        wp_localize_script( 'meta-box-image', 'meta_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'prfx-textdomain' ),
                'button' => __( 'Use this image', 'prfx-textdomain' ),
            )
        );
        wp_enqueue_script( 'meta-box-image' );
    }
}
add_action( 'admin_enqueue_scripts', 'buzzy_homepage_image_enqueue' );

/**
 * Outputs the content of the meta box
 */
function buzzy_homepage_hero_meta_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
  $prfx_stored_meta = get_post_meta( $post->ID );
  ?>

  <p>
    <label for="mb-book-title-" class="prfx-row-title major-label"><?php _e( 'Title/Heading', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-book-title-1" class="meta-text-input major-input" value="<?php if ( isset ( $prfx_stored_meta['mb-book-title-1'] ) ) echo $prfx_stored_meta['mb-book-title-1'][0]; ?>" />
  </p>
  <p>
    <label for="mb-book-subheading-1" class="prfx-row-title"><?php _e( 'Subtitle/Subheading', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-book-subheading-1" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-book-subheading-1'] ) ) echo $prfx_stored_meta['mb-book-subheading-1'][0]; ?>" />
  </p>
  <p>
    <label for="mb-landing-page-url" class="prfx-row-title"><?php _e( 'Book Page URL', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-landing-page-url" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-landing-page-url'] ) ) echo $prfx_stored_meta['mb-landing-page-url'][0]; ?>" />
  </p>
  <p>
    <label for="mb-callout-primary" class="prfx-row-title"><?php _e( 'Callout', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-callout-primary" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-callout-primary'] ) ) echo $prfx_stored_meta['mb-callout-primary'][0]; ?>" />
  </p>
  <p>
    <span class="prfx-row-title"><?php _e( 'Callout Style', 'prfx-textdomain' )?></span>
    <div class="prfx-row-content">
        <label for="mb-callout-style-1">
            <input type="radio" name="mb-callout-style" id="mb-callout-style-1" value="callout-major" <?php if ( isset ( $prfx_stored_meta['mb-callout-style'] ) ) checked( $prfx_stored_meta['mb-callout-style'][0], 'callout-major' ); ?>>
            <?php _e( 'Style 1 (red text)', 'prfx-textdomain' )?>
        </label>
        <label for="mb-callout-style-2">
            <input type="radio" name="mb-callout-style" id="mb-callout-style-2" value="callout-minor" <?php if ( isset ( $prfx_stored_meta['mb-callout-style'] ) ) checked( $prfx_stored_meta['mb-callout-style'][0], 'callout-minor' ); ?>>
            <?php _e( 'Style 2 (blue background)', 'prfx-textdomain' )?>
        </label>
    </div>
</p>
  <p>
    <label for="mb-callout-secondary" class="prfx-row-title"><?php _e( 'Secondary Callout', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-callout-secondary" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-callout-secondary'] ) ) echo $prfx_stored_meta['mb-callout-secondary'][0]; ?>" />
  </p>
  <div class="flex-container">
    <label for="meta-image" class="prfx-row-title"><?php _e( 'Image Upload', 'prfx-textdomain' )?></label>
    <span class="flex-image-thumb"><img src="<?php echo $prfx_stored_meta['meta-image'][0]; ?>" /></span>
    <input type="hidden" name="meta-image" id="meta-image" value="<?php if ( isset ( $prfx_stored_meta['meta-image'] ) ) echo $prfx_stored_meta['meta-image'][0]; ?>" />
    <input type="button" id="meta-image-button" class="button add-image" value="<?php _e( 'Choose or Upload an Image', 'prfx-textdomain' )?>" />
  </div>
   <p>
    <label for="mb-blurb" class="prfx-row-title"><?php _e( 'Endorsement blurb (keep it short)', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-blurb" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-blurb'] ) ) echo $prfx_stored_meta['mb-blurb'][0]; ?>" />
  </p>
  <p>
    <label for="mb-blurb-source" class="prfx-row-title"><?php _e( 'Endorsement source', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-blurb-source" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-blurb-source'] ) ) echo $prfx_stored_meta['mb-blurb-source'][0]; ?>" />
  </p>

  <?php
}

/**
 * Outputs the content of the secondary books callback
 */


/**
 * Saves the custom meta input
 */
function buzzy_homepage_hero_meta_save( $post_id ) {
 
  // Checks save status
  $is_autosave = wp_is_post_autosave( $post_id );
  $is_revision = wp_is_post_revision( $post_id );
  $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

  // Exits script depending on save status
  if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
      return;
  }

  // Checks for input and sanitizes/saves if needed
  if( isset( $_POST[ 'mb-book-title-1' ] ) ) {
    update_post_meta( $post_id, 'mb-book-title-1', sanitize_text_field( $_POST[ 'mb-book-title-1' ] ) );
  }
  if( isset( $_POST[ 'mb-book-subheading-1' ] ) ) {
    update_post_meta( $post_id, 'mb-book-subheading-1', sanitize_text_field( $_POST[ 'mb-book-subheading-1' ] ) );
  }
  if( isset( $_POST[ 'mb-landing-page-url' ] ) ) {
    update_post_meta( $post_id, 'mb-landing-page-url', sanitize_text_field( $_POST[ 'mb-landing-page-url' ] ) );
  }
  if( isset( $_POST[ 'mb-callout-primary' ] ) ) {
    update_post_meta( $post_id, 'mb-callout-primary', sanitize_text_field( $_POST[ 'mb-callout-primary' ] ) );
  }
  if( isset( $_POST[ 'mb-callout-secondary' ] ) ) {
    update_post_meta( $post_id, 'mb-callout-secondary', sanitize_text_field( $_POST[ 'mb-callout-secondary' ] ) );
  }

  // Checks for input and saves if needed
  if( isset( $_POST[ 'mb-callout-style' ] ) ) {
    update_post_meta( $post_id, 'mb-callout-style', $_POST[ 'mb-callout-style' ] );
  } else {
    update_post_meta( $post_id, 'mb-callout-style', 'callout-minor' );
  }

  // Save image
  if( isset( $_POST[ 'meta-image' ] ) ) {
    update_post_meta( $post_id, 'meta-image', $_POST[ 'meta-image' ] );
  }

  // Save blurb
  if( isset( $_POST[ 'mb-blurb' ] ) ) {
    update_post_meta( $post_id, 'mb-blurb', $_POST[ 'mb-blurb' ] );
  }
  if( isset( $_POST[ 'mb-blurb-source' ] ) ) {
    update_post_meta( $post_id, 'mb-blurb-source', $_POST[ 'mb-blurb-source' ] );
  }
}
add_action( 'save_post', 'buzzy_homepage_hero_meta_save' );


/**
 * Dynamic book meta boxes
 **/

/* Do something with the data entered */
add_action( 'save_post', 'dynamic_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function dynamic_add_custom_box() {
   // check for a template type
  $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
  $template_file = get_post_meta($post_id, '_wp_page_template', TRUE);

  if ($template_file == 'home-main.php') {
    add_meta_box(
      'dynamic_sectionid', __( 'Secondary Book Promotions', 'myplugin_textdomain' ), 'dynamic_inner_custom_box', 'page', 'normal', 'high');
  }
}

add_action( 'add_meta_boxes', 'dynamic_add_custom_box' );

/* Prints the box content */
function dynamic_inner_custom_box() {
    global $post;
    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMeta_noncename' );
    
    $prfx_stored_meta = get_post_meta( $post->ID );
    ?>

    <p>
      <label for="mb-secondary-title" class="prfx-row-title major-label"><?php _e( 'Title/Heading', 'prfx-textdomain' )?></label>
      <input type="text" name="mb-secondary-title" class="meta-text-input major-input" value="<?php if ( isset ( $prfx_stored_meta['mb-secondary-title'] ) ) echo $prfx_stored_meta['mb-secondary-title'][0]; ?>" />
    </p>

    <div id="meta_inner">
    <?php

    //get the saved meta as an arry
    $books = get_post_meta($post->ID,'mb-secondary-books',true);

    $c = 0;
    if ( count( $books ) > 0 ) {
      echo '<div class="secondary-books">';
      foreach( $books as $book ) {
        if ( isset( $book['title'] ) || isset( $book['url'] ) ) {
         printf( '<div class="single-secondary-book"><p>' .
            '<label class="prfx-row-title">Book Title</label>' .
            '<input type="text" class="meta-text-input" name="mb-secondary-books[%1$s][title]" value="%2$s" />' .
          '</p>' .
          '<p>' .
            '<label class="prfx-row-title">Subtitle</label>' .
            '<input type="text" class="meta-text-input" name="mb-secondary-books[%1$s][subtitle]" value="%3$s" />' .
          '</p>' .
          '<p>' .
          '<label class="prfx-row-title">Book Page URL</label>' .
            '<input type="text" class="meta-text-input" name="mb-secondary-books[%1$s][book-url]" value="%4$s" />' .
          '</p>' .
           '<p>' .
            '<div class="flex-container"><label class="prfx-row-title">Image</label>' .
            '<span class="flex-image-thumb  %1$s"><img src="%5$s" /></span>' .
            '<input type="hidden" class="image-url" name="mb-secondary-books[%1$s][meta-image]" value="%5$s" />' .
            '<input type="button" class="add-image button" value="Add/Change Image" /></div>' . 
          '</p>' .
          '<p>' .
            '<label class="prfx-row-title">Description</label>' .
            '<textarea rows="10" class="meta-text-input" name="mb-secondary-books[%1$s][desc]" value="%6$s">%6$s</textarea>' .
          '</p>' .
          '<p>' .
            '<label class="prfx-row-title">Buy Link</label>' .
            '<input type="url" class="meta-text-input" name="mb-secondary-books[%1$s][buylink]" value="%7$s" />' .
          '</p>' .
          '<span class="remove button">%8$s</span></div>', $c, $book['title'], $book['subtitle'], $book['book-url'], $book['meta-image'], $book['desc'], $book['buylink'], __( 'Remove Book' ) );
                $c = $c +1;
        }
      } 
      echo '</div>';
    }

    ?>
<span id="here"></span>
<span class="add button"><?php _e('Add A Book'); ?></span>
<script>
    var $ =jQuery.noConflict();
    $(document).ready(function() {
        var count = <?php echo $c; ?>;
        $(".add").click(function() {
            count = count + 1;

            $('#here').append('<div class="secondary-books">' +
              '<p>' +
                '<label class="prfx-row-title">Book Title</label>' +
                '<input type="text" class="meta-text-input" name="mb-secondary-books['+count+'][title]" value="" />' +
              '</p>' +
              '<p>' +
                '<label class="prfx-row-title">Subtitle</label>' +
                '<input type="text" class="meta-text-input" name="mb-secondary-books['+count+'][subtitle]" value="" />' +
              '</p><p>' +
                '<label class="prfx-row-title">Book Page URL</label>' + 
                '<input type="url" class="meta-text-input" name="mb-secondary-books['+count+'][book-url]" value="" />' +
              '</p>' +
              '<p>' +
                '<div class="flex-container dynamic"><label class="prfx-row-title">Image</label>' + 
                '<span class="flex-image-thumb"></span>' +
                '<input type="hidden" class="image-url" name="mb-secondary-books['+count+'][meta-image]" value="" />' +
                '<input type="button" class="add-image button" value="Add/Change Image" /></div>' + 
              '</p>' +
              '<p>' +
                '<label class="prfx-row-title">Description</label>' + 
                '<textarea class="meta-text-input" name="mb-secondary-books['+count+'][desc]"></textarea>' +
              '</p>' +
              '<p>' +
                '<label class="prfx-row-title">Buy Link</label>' + 
                '<input type="url" class="meta-text-input" name="mb-secondary-books['+count+'][buylink]" value="" />' +
              '</p>' +
              '<span class="remove button">Remove Book</span></div>' );
            return false;
        });
        $(".remove").live('click', function() {
            $(this).parent().remove();
        });
    });
    </script>
</div><?php

}

/* When the post is saved, saves our custom data */
function dynamic_save_postdata( $post_id ) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !isset( $_POST['dynamicMeta_noncename'] ) )
        return;

    if ( !wp_verify_nonce( $_POST['dynamicMeta_noncename'], plugin_basename( __FILE__ ) ) )
        return;

    // OK, we're authenticated: we need to find and save the data

    if( isset( $_POST[ 'mb-secondary-title' ] ) ) {
      update_post_meta( $post_id, 'mb-secondary-title', sanitize_text_field( $_POST[ 'mb-secondary-title' ] ) );
    }

    $books = $_POST['mb-secondary-books'];

    update_post_meta($post_id,'mb-secondary-books',$books);
}