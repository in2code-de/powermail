/**
 * Powermail_Frontend main JavaScript
 */
jQuery(document).ready(function($) {
	// Submit Export Form
	$('.export_icon_xls, .export_icon_csv').click(function() {
		if ($(this).hasClass('export_icon_csv')) {
			$('#export_format').val('csv');
		}
		if ($(this).hasClass('export_icon_xls')) {
			$('#export_format').val('xls');
		}
		$('#powermail_frontend_export').submit();
	});
});