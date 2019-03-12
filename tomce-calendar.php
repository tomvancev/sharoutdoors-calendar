<?php
/*
Plugin Name: Tomce's Calendar
Description: Calendar for a specific pupropse.
Version: 1.1.0
Author: Tomce
*/
define( "TMC_CALENDAR_TABLE",'Ejb_tmc_booking_data');
define( "TMC_CALENDAR_TABLE_PK",'id');

define( "TMC_PAGE_NAME", 'calendar' );
define( "TMC_POST_TYPE_EVENT", 'calendar-event');
define( "TMC_PLUGIN_URL", plugins_url( '/', __FILE__ ) );
define( "TMC_PLUGIN_ASSETS_URL", plugins_url( 'assets/', __FILE__ ) );
define( "TMC_PLUGIN_INCLUDES_DIR", plugin_dir_path( __FILE__ ) . 'includes/' );
define( "TMC_PLUGIN_SQL_DIR", plugin_dir_path( __FILE__ ) . 'includes/sql/' );


include(TMC_PLUGIN_INCLUDES_DIR . 'classes/CalendarEvent.php');
include(TMC_PLUGIN_INCLUDES_DIR . 'classes/CalendarDatabase.php');
include(TMC_PLUGIN_INCLUDES_DIR . 'tmc-rest-api.php');
include(TMC_PLUGIN_INCLUDES_DIR . 'tmc-enqueue-scripts.php');
include(TMC_PLUGIN_INCLUDES_DIR . 'tmc-calendar-html.php');
include(TMC_PLUGIN_INCLUDES_DIR . 'tmc-woocommerce-integration.php');
include(TMC_PLUGIN_INCLUDES_DIR . 'tmc-admin-page.php');
