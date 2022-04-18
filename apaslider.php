<?php

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
        'menu_name' => 'اسلایدر آپارات',
        'name_admin_bar' => 'اسلایدر آپارات',
        'archives' => 'آرشیو اسلاید ها',
        'attributes' => 'ویژگی های اسلاید',
        'parent_item_colon' => 'اسلاید والد:',
        'all_items' => 'همه اسلاید ها',
        'add_new_item' => 'اسلاید جدید',
        'add_new' => 'اسلاید جدید',
        'new_item' => 'اسلاید جدید',
        'edit_item' => 'ویرایش اسلاید',
        'update_item' => 'آپدیت کردن اسلاید',
        'view_item' => 'نمایش اسلاید',
        'view_items' => 'نمایش همه اسلایدها',
        'search_items' => 'جست و جوی اسلاید',
        'not_found' => 'اسلایدی پیدا نشد',
        'not_found_in_trash' => 'در سطل زباله یافت نشد',
        'featured_image' => 'تصویر شاخص',
        'set_featured_image' => 'ثبت تصویر شاخص',
        'remove_featured_image' => 'حذف تصویر شاخص',
        'use_featured_image' => 'استفاده به عنوان تصویر شاخص',
        'insert_into_item' => 'بارگزاری در این اسلاید',
        'uploaded_to_this_item' => 'در این آیتم بارگزاری شد',
        'items_list' => 'لیست اسلاید ها',
        'items_list_navigation' => 'پیمایش لیست اسلاید ها',
        'filter_items_list' => 'فیلتر کردن لیست اسلاید ها',
    );
    $args = array(
        'label' => 'ویدیو',
        'description' => 'ویدیوهایی که باید در اسلایدر آپارات نشان داده شوند',
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
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'post',
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
    $value = get_post_meta($post->ID, '_apaslider_video_url', true);
    ?>
    <label for='apaslider_video_url_input' style='font-size: large;'>آدرس ویدیو آپارات را وارد کنید:</label>
    <input name='apaslider_video_url_input' id='apaslider_video_url_input'
           style='width: 100%; margin-top: 1em; padding: 0.5em;' placeholder='https://www.aparat.com/v/r5RS8'
           value='<?php echo $value ?>'/>
    <?php
}

function apaslider_save_video_url($post_id)
{
    if (array_key_exists('apaslider_video_url_input', $_POST)) {
        update_post_meta(
            $post_id,
            '_apaslider_video_url',
            $_POST['apaslider_video_url_input']
        );
    }
}

add_action('save_post', 'apaslider_save_video_url');

/**
 * short code
 * */

function apaslider_shortcode($atts = array(), $content = null)
{

//    $content = do_shortcode( $content );
// echo plugin_dir_url( __FILE__ ) . 'public/img/play-circle.png';
//    explode("/",$attrs[""])


    $args = array('post_type' => 'apaslider');

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
            display: flex;
            align-content: center;
            align-items: center;
            justify-content: space-evenly;
            flex-direction: row;
            background-color: #616060;
            color: white;
            padding: 2em 5em 4em 5em;
            flex-wrap: wrap;
        }

        .apaslider_slider {
            width: 50%;
            display: flex;
            align-content: stretch;
            justify-content: center;
            align-items: center;
        }

        .apaslider_discription {
            width: 30%;
        }

        .apaslider_discription > h2 {
            font-weight: 700;
            font-size: 24px;
            line-height: 38px;
        }

        .apaslider_discription > p {
            font-style: normal;
            font-weight: 400;
            font-size: 15px;
            line-height: 23px;
            text-align: right;

            color: #EAEAEA;
        }
        .apaslider_left , .apaslider_right {
            margin: 0 1em;
        }
        .apaslider_slider > .h_iframe-aparat_embed_frame{
            display: none;
        }

    </style>

    <div class="apaslider_container">


        <div class="apaslider_slider">
            <img src='<?php echo plugin_dir_url(__FILE__) . "public/img/l.png" ?>' class="apaslider_left">

            <? echo $vids_xml ?>
            <img src='<?php echo plugin_dir_url(__FILE__) . "public/img/r.png" ?>' class="apaslider_right" >
        </div>


        <div class="apaslider_discription">
            <h2 style="text-align: right">نظرات دانشجویان ما درباره سفیرساعی</h2>
            <div style="width: 100%;height: 1px; background: #FF1D25;"></div>
            <p style="text-align: right">سالها تجربه در ارائه خدمات اعزام دانشجو سبب شده است که سفیرساعی خانواده ای به
                وسعت ایران عزیز داشته باشد.
                عزیزانی که از نقاط مختلف کشور به ما اعتماد کردند و در حال حاضر در مراکز معتبر آموزشی دنیا تحصیل می کنند
                و مایه مباحات ماست نظراتی که درباره ما ثبت نموده اند.</p>
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

            $(".apaslider_left").click(function (){
                if(index> 0)
                    index--;
                else
                    index = length - 1;
                console.log("clike left", index);
                all.hide();
                all.eq(index).show();
            });

            $(".apaslider_right").click(function (){
                if(index< length-1)
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

?>