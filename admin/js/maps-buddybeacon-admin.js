(function( $ ) {
	'use strict';

  const $daterangeto = '#daterange_to';  
  const $daterangefrom = '#daterange_from';  

  // If 'select date' is chose, show input field, otherwise hide and set to current date.
  $(function () {

    $("#dateend_choice").change(function() {

      let val = $(this).val();

      if(val === "selectdate") {
        //show input field
        $("#daterange_to").prop('type', 'text');
      }

      else if (val === "currentdate") {
        //set datepicker to current date

    		 const $myDate =  moment().format("YYYY-MM-DD HH:mm:ss");

  		  $($daterangeto).prop('value', $myDate);
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
          }

        });

 	      if ($('#info_box_display').is(':checked')) {
 		      $(".info-box-info").css("display", "none");
      	}

  });




  // Show the 'date from' datepicker when input field is clicked
	$(function(){

    $($daterangefrom).datetimepicker();
    $($daterangefrom).datetimepicker('hide');

    $($daterangefrom).click(function() {

      $($daterangefrom).datetimepicker({
    		inline: true,
        sideBySide: true,
        widgetPositioning: {
          horizontal: 'left',
          vertical: 'bottom'
        },            
      });

      $($daterangefrom).datetimepicker('show');		 

    });
     
  });


  // Show the 'date to' datepicker when input field is clicked
	$(function(){

   // const $daterangeto = '#daterange_to';  
    $($daterangeto).datetimepicker();
    $($daterangeto).datetimepicker('hide');

    $($daterangeto).click(function() {

      $($daterangeto).datetimepicker({
        inline: true,
        sideBySide: true,
        widgetPositioning: {
          horizontal: 'auto',
          vertical: 'bottom'
        },            
      });

      $($daterangeto).datetimepicker('show');    

    });
     
  });


  //Make sure that if 'select date' is pre-selected, the input field shows.
	$(function(){

	 	const $dateendvalue = $("#dateend_choice").val();
	 	
    if  ($dateendvalue === "selectdate") {

      $("#daterange_to").prop('type', 'text');

    }

    if  ($dateendvalue === "currentdate") {
    	
    }

  });


})( jQuery );
