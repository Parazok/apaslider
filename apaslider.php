<?php
/**
 * Plugin Name:       apaslider
 * Plugin URI:        https://doggydev.ir
 * Description:       a plugin to display aparat videos
 * Version:           1.1
 * Requires at least: 5.0
 * Author:            Parazok (parsa yadpa)
 * Author URI:        https://doggydev.ir/resume
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */



/**
 * Activate the plugin.
 */
function apaslider_activate()
{
    // Trigger our function that registers the custom post type plugin.
    apaslider_setup_post_type();
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();

}

register_activation_hook(__FILE__, 'apaslider_activate');

/**
 * Deactivation hook.
 */
function apaslider_deactivate()
{
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type('aparat-videos');
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'apaslider_deactivate');





/**
 * Register the "apaslider" custom post type
 */

function apaslider_setup_post_type() {

    $labels = array(
        'name'                  => 'ویدیوهای آپارات',
        'singular_name'         => 'ویدیو',
        'menu_name'             => 'اسلایدر آپارات',
        'name_admin_bar'        => 'اسلایدر آپارات',
        'archives'              => 'آرشیو اسلاید ها',
        'attributes'            => 'ویژگی های اسلاید',
        'parent_item_colon'     => 'اسلاید والد:',
        'all_items'             => 'همه اسلاید ها',
        'add_new_item'          => 'اسلاید جدید',
        'add_new'               => 'اسلاید جدید',
        'new_item'              => 'اسلاید جدید',
        'edit_item'             => 'ویرایش اسلاید',
        'update_item'           => 'آپدیت کردن اسلاید',
        'view_item'             => 'نمایش اسلاید',
        'view_items'            => 'نمایش همه اسلایدها',
        'search_items'          => 'جست و جوی اسلاید',
        'not_found'             => 'اسلایدی پیدا نشد',
        'not_found_in_trash'    => 'در سطل زباله یافت نشد',
        'featured_image'        => 'تصویر شاخص',
        'set_featured_image'    => 'ثبت تصویر شاخص',
        'remove_featured_image' => 'حذف تصویر شاخص',
        'use_featured_image'    => 'استفاده به عنوان تصویر شاخص',
        'insert_into_item'      => 'بارگزاری در این اسلاید',
        'uploaded_to_this_item' => 'در این آیتم بارگزاری شد',
        'items_list'            => 'لیست اسلاید ها',
        'items_list_navigation' => 'پیمایش لیست اسلاید ها',
        'filter_items_list'     => 'فیلتر کردن لیست اسلاید ها',
    );
    $args = array(
        'label'                 => 'ویدیو',
        'description'           => 'ویدیوهایی که باید در اسلایدر آپارات نشان داده شوند',
        'labels'                => $labels,
        'supports'              => array( 'title',  ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 10,
        'menu_icon'             => 'dashicons-video-alt3',
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
    );
    register_post_type( 'apaslider', $args );

}
add_action( 'init', 'apaslider_setup_post_type', 0 );

/**
 * add needed meta box and data to apaslider post type
 * */
function apaslider_meta_box() {
    $screens = [ 'apaslider', ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'apaslider',                 // Unique ID
            'ویدیو',      // Box title
            'apaslider_meta_box_html',  // Content callback, must be of type callable
            $screen                            // Post type
        );
    }
}
add_action( 'add_meta_boxes', 'apaslider_meta_box' );

function apaslider_meta_box_html( $post ) {
    $value = get_post_meta( $post->ID, '_apaslider_video_url', true );
    ?>
    <label for="apaslider_video_url_input" style="font-size: large;">آدرس ویدیو آپارات را وارد کنید:</label>
    <input name="apaslider_video_url_input" id="apaslider_video_url_input" style="width: 100%; margin-top: 1em; padding: 0.5em;" placeholder="https://www.aparat.com/v/r5RS8" value="<?php echo $value?>"/>
    <?php
}

function apaslider_save_video_url( $post_id ) {
    if ( array_key_exists( 'apaslider_video_url_input', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_apaslider_video_url',
            $_POST['apaslider_video_url_input']
        );
    }
}
add_action( 'save_post', 'apaslider_save_video_url' );

/**
 * short code
 * */

function wporg_shortcode( $atts = array(), $content = null ) {
    // do something to $content

    // run shortcode parser recursively
    $content = do_shortcode( $content );

    // always return
    return $content;
}
add_shortcode( 'wporg', 'wporg_shortcode' );

?>