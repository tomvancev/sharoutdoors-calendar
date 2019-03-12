/***************************************** ****** ***********************************************/
/*****************************************  DATA  ***********************************************/
/***************************************** ****** ***********************************************/

var EventModel = function( data ) {
  this.id = data.id;
  this.post_id = data.post_id;
  this.start = data.date_from;
  this.end = data.date_to;

  this.deleteEvent = deleteEvent.bind( this, this.id );
  Events.push( this );

}, Events = [], initial = 1;

/***************************************** ****** ***********************************************/
/***************************************** CREATE ***********************************************/
/***************************************** ****** ***********************************************/

function addToCart( data ) {
  function validateEventData( data ) {
    var fieldsWithError = '';
    if ( ! moment( data.dateFrom  ).isValid() ) {
      fieldsWithError += '#calendar-dateFrom, ';
    }
    if ( ! moment( data.dateFrom  ).isValid() ) {
      fieldsWithError += '#calendar-dateTo, ';
    }
    if ( ! data.postId ) {
      alert( 'Bad post id!' );
      return false;
    }
    if ( !! fieldsWithError ) {
      jQuery( fieldsWithError.trim().slice( 0, -1 ) ).addClass( 'validation' ).on( 'keydown change', function() {
        jQuery( this ).removeClass( 'validation' ).off();
      });
      return false;
    }else {
      return true;
    }

  }

  if ( validateEventData( data ) ) {
    jQuery.ajax( SETTINGS.api_url,
      {
        method: 'POST', data:JSON.stringify( data ),
        contentType:'application/json',
        success: function( data ) {
            alert( 'Successfully added to cart' );
        }, error: function( err ) {
          a = err;console.log( err.responseJSON.code );
        }
      }
    );
  }

}

/***************************************** ****** ***********************************************/
/***************************************** DELETE ***********************************************/
/***************************************** ****** ***********************************************/

// Function deleteEvent ( postId ) {
//   function removeEventFromArray( postId ) {
//       Events = Events.filter(function( element ) {
//         return element.id != postId;
//     });
//
//   }
//
//   JQuery.ajax( SETTINGS.api_url,
//     {
//     method: 'DELETE',
//     data:JSON.stringify( { id: postId } ),
//     contentType:'application/json',
//     success: function( data ) {
//       console.log( 'delete success' );
//       removeEventFromArray( postId );
//     }, error: function( err ) {
//       a = err;
//       console.log( err );
//     }
//     });
//
// }

/***************************************** ****** ***********************************************/
/*****************************************  READ  ***********************************************/
/***************************************** ****** ***********************************************/

function getEvents() {
  jQuery.ajax( SETTINGS.api_url,
    {
      method: 'GET',
      success: function( data ) {
        Events = [];
        JSON.parse( data ).forEach(function( element ) {
          console.log( element );
          new EventModel( element );
        });

      },
      error: function( err ) {
        console.log( err );

      }
    });
}

/***************************************** ****** ***********************************************/
/*****************************************   UI   ***********************************************/
/***************************************** ****** ***********************************************/

(function( $ ) {
  var $from = $( '#calendar-dateFrom' ), $to = $( '#calendar-dateTo' ), daysQuantity;
  function handleCreateBtnClick() {
    function collectData() {
      var dateFrom =  moment( $( '#calendar-dateFrom' ).val(), 'DD/MM/YYYY'  ).format( 'YYYY-MM-DD' );
      var dateTo =  moment( $( '#calendar-dateTo' ).val(), 'DD/MM/YYYY' ).format( 'YYYY-MM-DD' );
      var postId = SETTINGS.post_id;
      var quantity = daysQuantity;
      return { dateFrom: dateFrom, dateTo: dateTo, postId: postId, quantity: quantity };

    }
      addToCart( collectData() );

  }

  function handleUiChanges( $from, $to ) {
    function calculateDaysDifference() {
      var timeDiff, from, to;
        from = moment( $from.val(), 'DD/MM/YYYY' );
        to = moment( $to.val(), 'DD/MM/YYYY' );
         timeDiff = to.diff( from, 'days' );
         daysQuantity = timeDiff;
          return timeDiff;

    }
    function changeUi( timeDiff, regularPrice, $from, $to ) {
      var price = timeDiff * regularPrice;
        $( '#daysSelected' ).text( timeDiff );
        $( '#calculationTotal' ).text( price );

    }

    if ( $from.val() && $to.val() ) {
      changeUi( calculateDaysDifference(), SETTINGS._regular_price );
    }

  }

  function getDate( element ) {
        var date;
        try {
          date = moment( element.value, 'DD/MM/YYYY' );
        } catch ( error ) {
          alert( 'Problem parsing date from input field. Incorrect format?' );
          date = null;
        }
        return date.isValid() ? date : moment().add( 3, 'years' );

      }

/***************************************** ****** ***********************************************/
/***************************************** READY ***********************************************/
/***************************************** ****** ***********************************************/

  $(function() {
    var $from = $( '#calendar-dateFrom' ), $to = $( '#calendar-dateTo' );

  function DisableSpecificDates( date ) {
      var currentdate = moment( date, 'YYYY-MM-DD' ).format( 'YYYY-MM-DD' ), i;

     for ( i = 0; i < Events.length; i++ ) {
       if ( currentdate > Events[i].start && currentdate < Events[i].end ) {
       return [false];
       }
     return [date];
    }

  }

    // From initialization
    $from.datepicker({
      dateFormat: 'dd/mm/yy',
      defaultDate: '0',
      minDate:0,
      beforeShowDay: DisableSpecificDates
    })
    .on( 'change', function() {

      handleUiChanges( $from, $to );
      $to.datepicker( 'option', 'minDate', getDate( this ).add( 3, 'days' ).format( 'DD/MM/YYYY' ) );

    }).keydown(function( e ) {
    if ( 8 == e.keyCode || 46 == e.keyCode ) {
        $.datepicker._clearDate( this );
    }
    });

    // To initialization
    $to.datepicker({
      dateFormat: 'dd/mm/yy',
      defaultDate: '+3',
      minDate:+3,
      beforeShowDay: DisableSpecificDates
    })
    .on( 'change', function() {
      handleUiChanges( $from, $to );
      $from.datepicker( 'option', 'maxDate', getDate( this ).add( -3, 'days' ).format( 'DD/MM/YYYY' ) );

    }).keydown(function( e ) {
    if ( 8 == e.keyCode || 46 == e.keyCode ) {
        $.datepicker._clearDate( this );
    }
    });

    getEvents();
    $( '#create-button' ).click( handleCreateBtnClick );

  });
}( jQuery ) );
