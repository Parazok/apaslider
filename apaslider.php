<?php

/*

Plugin Name: apaslider
Plugin URI: doggydev.ir
Version: 2.1
Description: a plugin for aparat videos
Author: parazok (parsa yadpa)
Author URI: doggydev.ir/parazok
License: MIT

*/

/*
 * just for debugging easier
 * */
function debug_to_console($data)
{
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

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
    unregister_post_type('apaslider');
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'apaslider_deactivate');


/**
 * Register the 'apaslider' custom post type
 */

function apaslider_setup_post_type()
{

    $labels = array(
        'name' => 'ویدیوهای آپارات',
        'singular_name' => 'ویدیو',
        'menu_name' => 'ویدیوهای آپارات',
        'name_admin_bar' => 'ویدیوی آپارات',
        'archives' => 'آرشیو ویدیو ها',
        'attributes' => 'ویژگی های ویدیو',
        'parent_item_colon' => 'ویدیو والد:',
        'all_items' => 'همه ویدیو ها',
        'add_new_item' => 'ویدیو جدید',
        'add_new' => 'ویدیو جدید',
        'new_item' => 'ویدیو جدید',
        'edit_item' => 'ویرایش ویدیو',
        'update_item' => 'آپدیت کردن ویدیو',
        'view_item' => 'نمایش ویدیو',
        'view_items' => 'نمایش همه ویدیوها',
        'search_items' => 'جست و جوی ویدیو',
        'not_found' => 'ویدیوی پیدا نشد',
        'not_found_in_trash' => 'در سطل زباله یافت نشد',
        'featured_image' => 'تصویر شاخص',
        'set_featured_image' => 'ثبت تصویر شاخص',
        'remove_featured_image' => 'حذف تصویر شاخص',
        'use_featured_image' => 'استفاده به عنوان تصویر شاخص',
        'insert_into_item' => 'بارگزاری در این ویدیو',
        'uploaded_to_this_item' => 'در این آیتم بارگزاری شد',
        'items_list' => 'لیست ویدیو ها',
        'items_list_navigation' => 'پیمایش لیست ویدیو ها',
        'filter_items_list' => 'فیلتر کردن لیست ویدیو ها',
    );
    $args = array(
        'label' => 'ویدیو',
        'description' => 'ویدیوهایی که باید در ویدیور آپارات نشان داده شوند',
        'labels' => $labels,
        'supports' => array('title',),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 10,
        'menu_icon' => 'dashicons-video-alt3',
        'show_in_admin_bar' => false,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'videos'), // my custom slug
    );
    register_post_type('apaslider', $args);

}

add_action('init', 'apaslider_setup_post_type', 0);

/**
 * add needed meta box and data to apaslider post type
 * */
function apaslider_meta_box()
{
    $screens = ['apaslider',];
    foreach ($screens as $screen) {
        add_meta_box(
            'apaslider',                 // Unique ID
            'ویدیو',      // Box title
            'apaslider_meta_box_html',  // Content callback, must be of type callable
            $screen                            // Post type
        );
    }
}

add_action('add_meta_boxes', 'apaslider_meta_box');

function apaslider_meta_box_html($post)
{
    $custom = get_post_custom($post->ID);
//    $value = get_post_meta($post->ID, '_apaslider_video_url', true);
    $value = $custom['_apaslider_video_url'][0];
    $is_students_review = $custom['_apaslider_is_std_rev'][0];
    $img = $custom['_apaslider_img'][0];
//    debug_to_console($value);
//    debug_to_console($is_students_review);
    ?>
    <label for='apaslider_video_url_input' style='font-size: large;'>آدرس ویدیو آپارات را وارد کنید:</label>
    <input name='apaslider_video_url_input' id='apaslider_video_url_input'
           style='width: 100%; margin-top: 1em; padding: 0.5em;'
           placeholder='https://www.aparat.com/v/r5RS8'
           value='<?php echo $value ?>'/>
    <a style='width: 100%; margin-bottom: 1em; padding: 0.5em;' href="<?php echo $value ?>"><?php echo $value ?></a>
    <br>
    <br>

    <label for='apaslider_is_students_review' style='margin-top: 1em; font-size: small;'>اگر این ویدیو از نظرات
        دانشجویان است تیک بزنید:</label>
    <input name='apaslider_is_students_review' id='apaslider_is_students_review'
           style=' padding: 0.5em;'
           type="checkbox"
           <?php if ($is_students_review == true) { ?>checked="checked"<?php } ?>
    />


    <?php if (isset($img)) { ?>
    <p>عکس کاور ویدیوی آپارات (بصورت اتوماتیک دریافت میشود):</p>
    <img style="width: 400px; margin: 1em; border: 2px dashed #0a4b78" src="<?php echo $img ?>">


    <?php
}
}

function apaslider_save($post_id)
{
    if (array_key_exists('apaslider_video_url_input', $_POST)) {
        update_post_meta(
            $post_id,
            '_apaslider_video_url',
            $_POST['apaslider_video_url_input']
        );
    }


//    if (array_key_exists('apaslider_is_students_review', $_POST)) {
//        update_post_meta(
//            $post_id,
//            '_apaslider_is_std_rev',
//            $_POST['apaslider_is_students_review']
//        );
//    }

    update_post_meta($post_id, "_apaslider_is_std_rev", $_POST["apaslider_is_students_review"]);


    if (array_key_exists('apaslider_video_url_input', $_POST)) {

        $url = $_POST['apaslider_video_url_input'];
        $url = explode("/", $url);
        $url = "https://www.aparat.com/etc/api/video/videohash/" . end($url);
        $r = wp_remote_get($url, array());
        $body = wp_remote_retrieve_body($r);
        update_post_meta($post_id, "_apaslider_img", json_decode($body, true)["video"]["big_poster"]);
    }


    return $post_id;

}

add_action('save_post', 'apaslider_save');

/**
 * short code
 * */

function apaslider_shortcode($atts = array(), $content = null)
{

//    $content = do_shortcode( $content );
// echo plugin_dir_url( __FILE__ ) . 'public/img/play-circle.png';
//    explode("/",$attrs[""])
//

//    $args = array(
//        'post_type'		=> 'post',
//        'meta_query'	=> array(
//            'relation'		=> 'AND',
//            array(
//                'key'	 	=> 'color',
//                'value'	  	=> array('red', 'orange'),
//                'compare' 	=> 'IN',
//            ),
//            array(
//                'key'	  	=> 'featured',
//                'value'	  	=> '1',
//                'compare' 	=> '=',
//            ),
//        ),
//    );

    $args = array(
        'post_type' => 'apaslider',
        'meta_key' => '_apaslider_is_std_rev',
        'meta_value' => 'on'
    );

    $the_query = new WP_Query($args);
    $urls = array();

    if ($the_query->have_posts()) :

        while ($the_query->have_posts()) :
            $the_query->the_post();
            $url = get_post_meta(get_the_ID(), '_apaslider_video_url', false)[0];
            $url = explode("/", $url);
            $urls[] = end($url);

        endwhile;

        wp_reset_postdata();

    else :

        return "<p>هیچ ویدیوی آپاراتی وجود ندارد</p>";

    endif;
    $vids_xml = "";
    foreach ($urls as $url) {
        ob_start();
        ?>

        <div class='h_iframe-aparat_embed_frame'><span style='display:block;padding-top:57%'></span>
            <iframe src='<?php echo "https://www.aparat.com/video/video/embed/videohash/" . $url . "/vt/frame" ?>'
                    allowfullscreen='false' webkitallowfullscreen='false' mozallowfullscreen='false'></iframe>
        </div>


        <?php
        $vids_xml .= ob_get_clean();
    }

    $xml = "";
    ob_start();
    ?>


    <style>
        .h_iframe-aparat_embed_frame {
            position: relative;
            background: #FFFFFF;
            border: 3px solid #FAFAFA;
            box-sizing: border-box;
            box-shadow: 0px 4px 49px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            overflow: hidden;
            width: 90%;
        }

        .h_iframe-aparat_embed_frame .ratio {
            display: block;
            width: 100%;
            height: auto
        }

        .h_iframe-aparat_embed_frame iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%
        }

        .apaslider_container {
            background-color: #616060;
            width: 100vw;
            align-items: center;
            text-align: -webkit-center;
        }

        .apaslider_content {
            display: flex;
            align-content: center;
            align-items: center;
            justify-content: space-evenly;
            flex-direction: row-reverse;
            color: white;
            flex-wrap: wrap;
            /*direction: ltr;*/
            max-width: 1140px;
            padding: 3em 0 6em 0;
        }

        .apaslider_slider {
            width: 60%;
            display: flex;
            align-content: stretch;
            justify-content: center;
            align-items: center;
            margin-left: -30px;

        }

        .apaslider_discription_container{
            width: 40%;

        }

        .apaslider_discription {
            overflow: hidden;
            max-width: 450px;

        }

        .apaslider_discription > h2 {
            font-weight: 700;
            font-size: 24px;
            line-height: 38px;
            color: white;
        }

        .apaslider_discription > p {
            font-style: normal;
            font-weight: 400;
            font-size: 15px;
            line-height: 23px;
            text-align: right;

            font-family: 'IRANSans';

            color: #EAEAEA;
        }

        .apaslider_left, .apaslider_right {
            cursor: pointer;
            margin: 0 1em;
        }

        .apaslider_slider > .h_iframe-aparat_embed_frame {
            display: none;
        }

        .apaslider_more_video,
        .apaslider_more_video > a {

            font-family: 'IRANSans';
            font-style: normal;
            font-weight: 400;
            font-size: 16px;
            line-height: 25px;
            /* identical to box height */

            text-align: right;

            color: #B6CBF2;
        }

    </style>

    <div class="apaslider_container">

        <div class="apaslider_content">


            <div class="apaslider_slider">
                <img src='<?php echo plugin_dir_url(__FILE__) . "public/img/r.png" ?>' class="apaslider_left">

                <? echo $vids_xml ?>
                <img src='<?php echo plugin_dir_url(__FILE__) . "public/img/l.png" ?>' class="apaslider_right">
            </div>

            <div class="apaslider_discription_container">

                <div class="apaslider_discription">
                    <h2 style="text-align: right">نظرات دانشجویان ما درباره سفیرساعی</h2>
                    <div style="width: 100%;height: 2px; background: #FF1D25; margin-left: 2em;"></div>
                    <p style="text-align: right">سالها تجربه در ارائه خدمات اعزام دانشجو سبب شده است که سفیرساعی خانواده
                        ای به
                        وسعت ایران عزیز داشته باشد.
                        عزیزانی که از نقاط مختلف کشور به ما اعتماد کردند و در حال حاضر در مراکز معتبر آموزشی دنیا تحصیل
                        می کنند
                        و مایه مباحات ماست نظراتی که درباره ما ثبت نموده اند.</p>
                    <p style="text-align: left;" class="apaslider_more_video">
                        <a
                                href="<?php echo get_post_type_archive_link("apaslider") ?>"
                        >ویدیوهای بیشتر</a>
                    </p>
                </div>
            </div>

        </div>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        var index = 0;
        $(document).ready(function () {
            var all = $(".apaslider_slider > .h_iframe-aparat_embed_frame");
            var length = all.length;
            all.hide();
            all.eq(index).show();

            $(".apaslider_left").click(function () {
                if (index > 0)
                    index--;
                else
                    index = length - 1;
                console.log("clike left", index);
                all.hide();
                all.eq(index).show();
            });

            $(".apaslider_right").click(function () {
                if (index < length - 1)
                    index++;
                else
                    index = 0;
                console.log("clike right", index);
                all.hide();
                all.eq(index).show();
            });


        });
    </script>

    <?php

    $xml .= ob_get_clean();

    return $xml;

}

add_shortcode('apaslider', 'apaslider_shortcode');


/**
 * taxonomy
 * */


/*
//hook into the init action and call create_topics_nonhierarchical_taxonomy when it fires

add_action( 'init', 'create_topics_nonhierarchical_taxonomy', 0 );

function create_topics_nonhierarchical_taxonomy() {

// Labels part for the GUI

    $labels = array(
        'name' => _x( 'نوع', 'taxonomy general name' ),
        'singular_name' => _x( 'نوع', 'taxonomy singular name' ),
        'search_items' =>  __( 'جست و جو' ),
        'popular_items' => __( 'محبوب ها' ),
        'all_items' => __( 'همه‌ی انواع ویدیو ها' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'ویرایش نوع' ),
        'update_item' => __( 'بروز رسانی نوع' ),
        'add_new_item' => __( 'اضافه کردن یک نوع جدید' ),
        'new_item_name' => __( 'اضافه کردن نام جدید' ),
        'separate_items_with_commas' => __( 'مقادیر را با ویرگول جدا کنید' ),
        'add_or_remove_items' => __( 'افزودن یا حذف نوع ها' ),
        'choose_from_most_used' => __( 'انتخاب از بیشترین استفاده شده ها' ),
        'menu_name' => __( 'انواع ویدیوی آپارات' ),
    );

// Now register the non-hierarchical taxonomy like tag

    register_taxonomy('aparat-types','apaslider',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
//        'rewrite' => array( 'slug' => 'topic' ),
    ));
}

*/


?>