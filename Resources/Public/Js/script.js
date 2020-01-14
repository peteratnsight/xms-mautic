/* SCRIPT */
(function($) {


$.fn.getMauticContext = function(a){
	return false;
    var elem=$(this);
    var thisid = elem.attr('id');

    var url = "http://marketing.kongressmedia.de/context/request.js";

    $.getJSON(url + '?callback=?', function(json) { 
	if(json.hash!=0) {
		//alert(json.hash);
		$('#' + thisid).attr(a, json.hash);

		//document.getElementById(thisid).setAttribute('data-value', json.hash);

	}
    }).done(function() {
		if ( $('.assetLink')[0] ) {
			var asseturl = $('.assetLink').attr('href').replace("/showitem", json.hash + "/showitem");
			//asseturl = asseturl.replace("/showitem", json.hash + "/showitem");
			$('.assetLink').attr('href', asseturl);
			//alert(asseturl);
		}
    });

    return false;
}

/*('#mauticContext').getMauticContext("value");*/


//---------- Simple Form Validation -------------//

function checkRequired(){
  var response = 0;
  $("#xmsForm .field-required").each(function(i) {
    var elem = $(this);
    var elem_id = elem.attr('id');
    var elem_type = elem.prop('tagName');
    var elem_id_response = elem_id + "_msg";
    $('#' + elem_id_response ).remove();
    var elem_msg_box = $('<div id="' + elem_id_response + '"></div>'); 


    if(elem_type == "SELECT" && $("#" + elem_id + " option:selected").val() =="notselected") {
	elem.addClass("doRequiredValidation");
	elem.doValidation();
	$('#' + elem_id ).focus();
	response = 1;
	return false;
    }
    if(elem_type == "INPUT" && !$.trim(elem.val())) {
	elem.addClass("doRequiredValidation");
	elem.doValidation();
	$('#' + elem_id ).focus();
	response = 1;
	return false;
    }
  });
  return response;
}


$('#xmsSubscription').submit(function(e) {
	var chk = 0; //checkRequired();
	var invalid = $('#form_invalidation_check').val();

	if(chk==0 && invalid!=1) {
		$('#formcheck').val('');
		return true;
	}
	//e.preventDefault();
	//return false;
});


$('#xmsSubscription.ajaxform').submit(function(e) {
	var form = $('#xmsSubscription');

	$.ajax({
	   url: $(form).attr('action'),
	   data: $(form).serialize(),
	   datatype: "jsonp",
	   type: "POST",
	   error: function(error) {
            console.log(error);
           },
	   success: function(response) {
		$("#mauticWrapper").html(response);
	   }
	});
	e.preventDefault();
	return false;
});




}( jQuery ));