<?php
function tmc_enqueue_scripts(){


  if(is_page(TMC_PAGE_NAME) || get_post_meta( get_the_id(), $key = 'has_calendar', $single = true )){
    wp_enqueue_script( 'tmc-moment', TMC_PLUGIN_ASSETS_URL . 'js/moment.js', $deps = array(), $ver = false, $in_footer = false );
    wp_enqueue_script( 'tmc-fullcalendar', TMC_PLUGIN_ASSETS_URL . 'js/fullcalendar.min.js', $deps = array('jquery', 'tmc-moment'), $ver = false, $in_footer = false );
    wp_enqueue_script( 'tmc-jquery-ui', TMC_PLUGIN_ASSETS_URL . 'js/jquery-ui.min.js', $deps = array('jquery', 'tmc-moment'), $ver = false, $in_footer = false );

    // localized script
    wp_register_script( 'tmc-main', TMC_PLUGIN_ASSETS_URL . 'js/main.js', $deps = array('jquery', 'tmc-fullcalendar' , 'tmc-moment'), $ver = false, $in_footer = false );
    tmc_rest_info();
    wp_enqueue_script( 'tmc-main' );


    wp_enqueue_style( 'tmc-fullcalendar', TMC_PLUGIN_ASSETS_URL . 'css/fullcalendar.min.css', $deps = array(), $ver = false, $media = 'all' );
    wp_enqueue_style( 'tmc-framework', TMC_PLUGIN_ASSETS_URL . 'css/tmc-framework.css', $deps = array(), $ver = false, $media = 'all' );
    wp_enqueue_style( 'tmc-main-styles', TMC_PLUGIN_ASSETS_URL . 'css/tmc-styles.css', $deps = array('tmc-framework'), $ver = false, $media = 'all' );
    wp_enqueue_style( 'tmc-jquery-ui', TMC_PLUGIN_ASSETS_URL . 'css/tmc-jquery-ui.min.css', $deps = array('tmc-framework'), $ver = false, $media = 'all' );
    wp_enqueue_style( 'tmc-jquery-ui-theme', TMC_PLUGIN_ASSETS_URL . 'css/jquery-ui.theme.min.css', $deps = array('tmc-framework', 'tmc-jquery-ui'), $ver = false, $media = 'all' );
    wp_enqueue_style( 'tmc-jquery-ui.structure', TMC_PLUGIN_ASSETS_URL . 'css/jquery-ui.structure.min.css', $deps = array('tmc-framework','tmc-jquery-ui'), $ver = false, $media = 'all' );

  }
}

add_action( 'wp_enqueue_scripts', 'tmc_enqueue_scripts', $priority = 10, $accepted_args = 1 );

function tmc_rest_info(){

  $settings = array(
  	'api_url'        => get_rest_url() . 'tmc/v1/index',
    '_regular_price' => get_post_meta(get_the_id(), '_regular_price',true ),
    'post_id'        => get_the_id()
  );
  wp_localize_script( 'tmc-main', 'SETTINGS', $settings );
}
