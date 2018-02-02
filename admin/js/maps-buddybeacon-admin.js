(function( $ ) {
	'use strict';

  const $bookingdateto = '#daterange_to';  
  const $bookingdatefrom = '#daterange_from';  

  // If 'select date' is chose, show input field, otherwise hide and set to current date.
  $(function () {

    $("#dateend_choice").change(function() {

      var val = $(this).val();

      if(val === "selectdate") {
        //show input field
        $("#daterange_to").prop('type', 'text');
      }

      else if (val === "currentdate") {
        //set datepicker to current date

    		var $myDate =  moment().format("YYYY-MM-DD HH:mm:ss");

  		  $($bookingdateto).prop('value', $myDate);
        $("#daterange_to").prop('type', 'hidden');
      }

    });
  });



  // Hide the info box styling fields in 'add map' if 'hide info box' is checked
  $(function () {
 
    $('#info_box_display').change(function() {

          if ($(this).is(':checked')) {
             $(".info-box-info").css("display", "none");
          }

          else {
          	$(".info-box-info").css("display", "table-row");
             console.log("supposed to show");
          }

        });

 	      if ($('#info_box_display').is(':checked')) {
 		      $(".info-box-info").css("display", "none");
      	}

  });




  // Show the 'date from' datepicker when input field is clicked
	$(function(){

    $($bookingdatefrom).datetimepicker();
    $($bookingdatefrom).datetimepicker('hide');

    $($bookingdatefrom).click(function() {

      $($bookingdatefrom).datetimepicker({
    		inline: true,
        sideBySide: true,
        widgetPositioning: {
          horizontal: 'auto',
          vertical: 'bottom'
        },            
      });

      $($bookingdatefrom).datetimepicker('show');		 

    });
     
  });


  // Show the 'date to' datepicker when input field is clicked
	$(function(){

    var $bookingdate = '#daterange_to';  
    $($bookingdateto).datetimepicker();
    $($bookingdateto).datetimepicker('hide');

    $($bookingdateto).click(function() {

      $($bookingdateto).datetimepicker({
        inline: true,
        sideBySide: true,
        widgetPositioning: {
          horizontal: 'auto',
          vertical: 'bottom'
        },            
      });

      $($bookingdateto).datetimepicker('show');    

    });
     
  });


  //Make sure that if 'select date' is pre-selected, the input field shows.
	$(function(){

	 	var $dateendvalue = $("#dateend_choice").val();
	 	
    if  ($dateendvalue === "selectdate") {

      $("#daterange_to").prop('type', 'text');

    }

    if  ($dateendvalue === "currentdate") {
    	
    }

  });


})( jQuery );

