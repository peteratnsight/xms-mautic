var $j = jQuery.noConflict();

$j(document).ready(function($) {

$('#listSelector').on('change', function() {
  $('#dashboard-form').submit();
});



});