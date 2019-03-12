CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_booking_data`( IN dateFrom DATE
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
  			SELECT 'THE TARGET DATES ARE ALREADY BOOKED';
      END IF;
 ELSE
		SELECT 'INVALID DATE RANGE (MUST BE AT LEAST 3 DAYS)' AS 'ERROR';
  END IF;
END
