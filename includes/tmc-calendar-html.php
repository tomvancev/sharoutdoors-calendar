<?php

function calendarPageHtml(){
// add option for Minimum stay
$minimumStay = 2;

    ?>
<div>
  <div class="calculation-area">
    <p>Number of days selected: <span id="daysSelected">0</span></p>
    <p>Total: <span id="calculationTotal">0</span> <span id="currency">Euro</span></p>
  </div>
  <div class="form-group">
    <div class="row row--gutters">
      <div class="row__medium-6">
        <label for="dateFrom">Date From</label>
        <input type="text" class="form-control datepicker" id="calendar-dateFrom" />
      </div>
      <div class="row__medium-6">
        <label for="dateTo">Date To</label>
        <input type="text" class="form-control datepicker" id="calendar-dateTo" />
      </div>
    </div>
    <p class='small-text'>* Minimum stay is <?= $minimumStay ?> nights</p>
    <button id="create-button">Book Now!</button>
  </div>
</div>
  <?php
}

// function renderCalculatorHtml($content){
//   if(is_page(TMC_PAGE_NAME)){
//       ob_start();
//       calendarPageHtml();
//       $out = ob_get_contents();
//       ob_end_clean();
//       return $out;
//   }
// }

// add_filter('the_content', 'renderCalculatorHtml');
