
//jQuery doc ready
//See http://digwp.com/2011/09/using-instead-of-jquery-in-wordpress/
jQuery(document).ready(function($) {

	//Enable collapse/expand toggle of admin boxes (like WP dashboard)
	$(".pib-hndle").toggle(function() {
		$(this).next(".inside").slideToggle("fast");
	}, function () {
		$(this).next(".inside").slideToggle("fast");
	});

	$(".pib-handlediv").toggle(function() {
		$(this).next(".pib-hndle").next(".inside").slideToggle("fast");
	}, function() {
		$(this).next(".pib-hndle").next(".inside").slideToggle("fast");
	});

});
