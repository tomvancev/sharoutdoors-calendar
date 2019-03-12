<?php
function register_tmc_settings() {
    register_setting( 'tmc-options-group', 'my-option-name', 'intval' );
}


add_action( 'admin_menu', 'wpdocs_register_menu_page' );
function wpdocs_register_menu_page() {
  add_menu_page(
   $page_title = 'Reservations',
   'Reservations',
   $capability = 'manage_options',
   $menu_slug = 'reservations',
   $function = 'tmc_register_menu_page',
   $icon_url = '',
   $position = 10
 );
 add_submenu_page(
   $parent_slug='reservations',
   $page_title='calendar',
   $menu_title='Calendar',
   $capability='manage_options', $menu_slug='calendar',
   $function = 'tmc_register_calendar_subpage'
 );
 add_submenu_page(
   $parent_slug='reservations',
   $page_title='calendaroptions',
   $menu_title='Options',
   $capability='manage_options', $menu_slug='options',
   $function = 'tmc_register_options_subpage'
 );

}
function tmc_register_menu_page(){
  echo 'Main Page';
}

function tmc_register_calendar_subpage(){
  echo 'Calendar';
}
function tmc_register_options_subpage(){
  echo 'Options';
}
