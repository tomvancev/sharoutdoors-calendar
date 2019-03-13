<?php

class CalendarDatabase {
  private $connection;
  private $table;
  private $sql;

  function __construct(){
    global $wpdb;
    $this->table = TMC_CALENDAR_TABLE;

  }

  public static function check_availability($dateFrom,$dateTo){
    global $wpdb;
    $results = $wpdb->get_row($wpdb->prepare(
      "CALL check_availability(%s,%s)",
      $dateFrom, $dateTo
    ));
    return $results->count == 0 ;
  }

  public static function cleanLineFeed($string){
    return str_replace(array("\r", "\n", "\t"), ' ', $string);
  }

  public function insert( $dateFrom, $dateTo, $postId, $checkingAvailability ){
    global $wpdb;
    $results = $wpdb->get_row($wpdb->prepare(
      "CALL insert_booking_data(%s,%s,%d,%d)",
      $dateFrom, $dateTo, $postId, $checkingAvailability
    ));
    return $results;
  }
  public function get_by_id($id){
    $sql = "Select * from {$this->table}
    where id = %d;";
    $prepared = $wpdb->prepare($sql,$id);
    $results = $wpdb->get_row($prepared);
    return $results;
  }

  public function read(){
    global $wpdb;
    $sql = "Select * from {$this->table};";
    $results = $wpdb->get_results($sql);
    return $results;
  }
  public function read_by_date_from($date_from){
    global $wpdb;
    $sql = "Select * from {$this->table}
    where date_from > %s or date_to > %s;";
    $sql = $this::cleanLineFeed($sql);
    $prepared = $wpdb->prepare($sql,$date_from,$date_from);
    $results = $wpdb->get_results($prepared);
    return $results;
  }

  public function read_by_date_between($date_from,$date_between){
    return 'Not implemented';
  }
  public function update_by_id($id,$event_data){
    return 'Not implemented';
  }

  public function delete_by_id($id){
    global $wpdb;
    return $wpdb->delete( $this->table, array( 'ID' => $id ) );
  }



/******************************************************************************************/
/***************************INITIATION OF PLUGIN CODE! ************************************/
/******************************************************************************************/

  public static function initPlugin(){
    // NEED TO TEST!
    global $wpdb;
    $createTableSql = "CREATE TABLE IF NOT EXISTS Ejb_tmc_booking_data(
    id INT(6) unsigned NOT NULL auto_increment,
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    post_id BIGINT UNSIGNED,
    PRIMARY KEY  (id))
    COLLATE {$wpdb->collate}";
    $dropInsertProcedureSql = "DROP PROCEDURE IF EXISTS `insert_booking_data`;";
    $createInsertProcedureSql = "CREATE PROCEDURE `insert_booking_data`( IN dateFrom DATE
    , IN dateTo DATE
    , IN postId BIGINT
    )
    BEGIN
    	declare date_from_var, date_to_var date;
        declare date_difference, second_check_count int;
           	SET date_from_var = cast(dateFrom AS date);
    		SET date_to_var = cast(dateTo AS date);
    		SET date_difference = DATEDIFF(date_to_var, date_from_var);

    		-- FIRST CHECK MIN 3 DAYS STAY
          IF date_difference >= 3 THEN
    		-- SECOND CHECK DATES NOT ALREADY BOOKED

            select COUNT(*) into second_check_count
    		from Ejb_tmc_booking_data
    		-- inside
    		where
    		( dateFrom  >= date_from and dateFrom <  date_to ) -- param dateFrom between dates
    		or
    		( dateTo > date_from and dateTo <= date_to ) -- param dateTo between dates
    		-- around
    		or
    		( date_from >= dateFrom and date_from < dateTo ) -- date_from between params
    		or
    		( date_to > dateFrom and date_to <= dateTo ) -- date_to between params
    		;

    		IF 0 = second_check_count THEN
    			insert into Ejb_tmc_booking_data VALUES (null, dateFrom, dateTo, postId);
    			select LAST_INSERT_ID() AS 'INSERT_ID';
    		ELSE
    			SELECT 'THE TARGET DATES ARE ALREADY BOOKED' AS 'ERROR';
            END IF;
    	 ELSE
    		SELECT 'INVALID DATE RANGE (MUST BE AT LEAST 3 DAYS)' AS 'ERROR';
          END IF;
  END";
    $dropCheckAvailabilityProcedure = "DROP PROCEDURE IF EXISTS `check_availability`;";
    $createCheckAvailabilityProcedure = "CREATE PROCEDURE `check_availability`(IN dateFrom date, IN dateTo date )
    BEGIN
        select COUNT(*)
    		from Ejb_tmc_booking_data
    		-- inside
    		where
    		( dateFrom  >= date_from and dateFrom <  date_to ) -- param dateFrom between dates
    		or
    		( dateTo > date_from and dateTo <= date_to ) -- param dateTo between dates
    		-- around
    		or
    		( date_from >= dateFrom and date_from < dateTo ) -- date_from between params
    		or
    		( date_to > dateFrom and date_to <= dateTo ) -- date_to between params
    		;
    END";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta( $createTableSql );
    $wpdb->query($dropCheckAvailabilityProcedure);
    $wpdb->query($createCheckAvailabilityProcedure);
    $wpdb->query($dropInsertProcedureSql);
    $wpdb->query($createInsertProcedureSql);


  }






}
