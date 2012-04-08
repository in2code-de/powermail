/**
 * Powermail main JavaScript for Backend Module
 */
jQuery(document).ready(function($) {

	/**
	 * List View
	 */

	// slideToggle Details
	$('.powermail_listbe_details_container').hide();
	$('.openPowermailDetails').click(function() {
		$(this).parent().parent().next().children().children().slideToggle();
	});

	// Hide/Unhide Mails
    $('.unhideMail, .hideMail').live('click', function() {
		$this = $(this);
		formToken = $this.siblings('.container_formtoken').val();
		uid = $this.siblings('.container_uid').val();
		table = $this.siblings('.container_table').val();

		if ($this.hasClass('unhideMail')) {
			$this.removeClass('t3-icon-edit-hide').removeClass('unhideMail').addClass('t3-icon-edit-unhide').addClass('hideMail');
			$this.parent().parent().children().first().children('.t3-icon').addClass('transparent');
			hidden = 1;
		} else {
			$this.removeClass('t3-icon-edit-unhide').removeClass('hideMail').addClass('t3-icon-edit-hide').addClass('unhideMail');
			$this.parent().parent().children().first().children('.t3-icon').removeClass('transparent');
			hidden = 0;
		}
		url = 'tce_db.php?&data[' + table + '][' + uid + '][hidden]=' + hidden + '&redirect=' + T3_THIS_LOCATION + '&vC=b601970a97' + formToken + '&prErr=1&uPT=1';
        $.ajax({
            url: url
        });
    });

	// Delete Mail
	$('.deleteMail').live('click', function() {
		$this = $(this);
		formToken = $this.siblings('.container_formtoken').val();
		uid = $this.siblings('.container_uid').val();
		table = $this.siblings('.container_table').val();
		confirmationMessage = $this.siblings('.container_label_delete_confirmation').val();

		if (confirm(confirmationMessage)) {
			$this.parent().parent().fadeOut('slow', function() {
				$(this).next().hide().prev().remove();
			});

			url = 'tce_db.php?&cmd[' + table + '][' + uid + '][delete]=1&redirect=' + T3_THIS_LOCATION + '&vC=3c76f1d3bb&prErr=1&uPT=1' + formToken
			$.ajax({
				url: url
			});
		}
	});

	// pagebrowser (add hiddenfield with pagebrowser variables to search form and submit)
	$('.f3-widget-paginator a').click(function(e) {
		href = $(this).attr('href');
		hrefParts = href.split('&');
		for (i = 0; i < hrefParts.length; i++) {
			if (hrefParts[i].indexOf('widget') != -1) {
				params = hrefParts[i];
			}
		}
		if (params != undefined) {
			paramsParts = params.split('=');
			paramsParts[0] = paramsParts[0].replace('%40', '@');
			html = '<input type="hidden" name="' + paramsParts[0] + '" value="' + paramsParts[1] + '" />';
			$('.hiddenvalues').append(decodeURI(html));
			$('#powermail_module_search').submit();
		}
		e.preventDefault();
	});

	// sorting (add hiddenfield with sorting variables to search form and submit)
	$('a.sorting').click(function(e) {
		href = $(this).attr('href');
		hrefParts = href.split('&');
		for (i = 0; i < hrefParts.length; i++) {
			if (hrefParts[i].indexOf('sorting') != -1) {
				params = hrefParts[i];
			}
		}
		if (params != undefined) {
			paramsParts = params.split('=');
			paramsParts[0] = paramsParts[0].replace('%40', '@');
			html = '<input type="hidden" name="' + paramsParts[0] + '" value="' + paramsParts[1] + '" />';
			$('.hiddenvalues').append(decodeURI(html));
			$('#powermail_module_search').submit();
		}
		e.preventDefault();
	});



	/**
	 * List View: Extended Search
	 */
	// Toogle Extended Search
	$('.extended_search_title').click(function() {
		$this = $(this);
		if ($this.hasClass('close')) {
			$this.removeClass('close').addClass('open').children('span').removeClass('t3-icon-move-down').addClass('t3-icon-move-up');
			$('fieldset.extended_search').slideDown('', function() {
				$(this).children('.powermail_module_search_field_container1').children('div.powermail_module_search_field').fadeTo('slow', 1);
			});
		} else {
			$this.removeClass('open').addClass('close').children('span').removeClass('t3-icon-move-up').addClass('t3-icon-move-down');
			$('fieldset.extended_search').children('.powermail_module_search_field_container1').children('div.powermail_module_search_field').fadeTo('slow', 0, function() {
				$(this).parent().parent().slideUp();
			});
		}
	});
	// Show Extended Search from the Beginning
	$('fieldset.extended_search input, fieldset.extended_search select').each(function() {
		if ($(this).val() != '') { // if there is content in one of the extended fields
			$('.extended_search_title').removeClass('close').addClass('open');
			$('.extended_search').removeClass('close').addClass('open');
			return;
		}
	});
	$('.powermail_module_search_field_container2 input, .powermail_module_search_field_container2 select').each(function() {
		if ($(this).val() != '') { // if there is content in one of the extended fields
			$(this).parent().parent().removeClass('close').addClass('open');
			return;
		}
	});

	// Datepicker
	$('.powermail_date').datepicker({
		dateFormat: $('.container_datepicker_dateformat:first').val(),
		dayNamesMin: [
			$('.container_datepicker_day_so:first').val(),
			$('.container_datepicker_day_mo:first').val(),
			$('.container_datepicker_day_tu:first').val(),
			$('.container_datepicker_day_we:first').val(),
			$('.container_datepicker_day_th:first').val(),
			$('.container_datepicker_day_fr:first').val(),
			$('.container_datepicker_day_sa:first').val()
		],
		monthNames: [
			$('.container_datepicker_month_jan:first').val(),
			$('.container_datepicker_month_feb:first').val(),
			$('.container_datepicker_month_mar:first').val(),
			$('.container_datepicker_month_apr:first').val(),
			$('.container_datepicker_month_may:first').val(),
			$('.container_datepicker_month_jun:first').val(),
			$('.container_datepicker_month_jul:first').val(),
			$('.container_datepicker_month_aug:first').val(),
			$('.container_datepicker_month_sep:first').val(),
			$('.container_datepicker_month_oct:first').val(),
			$('.container_datepicker_month_nov:first').val(),
			$('.container_datepicker_month_dec:first').val()
		],
		nextText: '&gt;',
		prevText: '&lt;',
		firstDay: 1
	});

	// Add new field
	$('.searchAddField span, .searchAddField label').click(function() {
		$('fieldset.extended_search .close').slideToggle();
	});



	/**
	 * List View: Extended Export
	 */
	// Submit Icons
	$('.export_icon_xls, .export_icon_csv').click(function() {
		if ($(this).hasClass('export_icon_csv')) {
			$('#export_format').val('csv');
		}
		if ($(this).hasClass('export_icon_xls')) {
			$('#export_format').val('xls');
		}
		$('#powermail_module_export').submit();
	});
	// Toogle Extended Export
	$('.extended_export_title').click(function() {
		$this = $(this);
		if ($this.hasClass('close')) {
			$this.removeClass('close').addClass('open').children('span').removeClass('t3-icon-move-down').addClass('t3-icon-move-up');
			$this.next().slideDown('', function() {
				$this.next().children('div').children('div').fadeTo('slow', 1);
			});
		} else {
			$this.removeClass('open').addClass('close').children('span').removeClass('t3-icon-move-up').addClass('t3-icon-move-down');
			$this.next().children('div').children('div').fadeTo('slow', 0, function() {
				$this.next().slideUp();
			});
		}
	});
	// Multiselect
	$(function() {
		$('.extended_export_field, .extended_export_field_container').sortable({
			connectWith: '.connected',
			update: function(event, ui) {
				$id = $(this).attr('id');
				if ($id == 'export_field_selection') {
					$fields = $(this).sortable('toArray').toString();
					$('#export_fields').val($fields);
				}
			}
		}).disableSelection();
	});



	/**
	 * Reporting View: Form
	 */
	$('.powermail_reporting_form_fields_table').find('tr:even').addClass('even');
	$('.powermail_reporting_form_fields_table').find('tr:first').removeClass('even');
	$('.powermail_reporting_form_fields_table').find('tr').hide();
	$('.powermail_reporting_form_fields_table').find('tr:first').show();
	$('.powermail_reporting_form_fields_table th').click(function() {
		$this = $(this);
		if ($this.hasClass('close')) {
			$this.removeClass('close').addClass('open');
		} else {
			$this.removeClass('open').addClass('close');
		}
		$this.parent().siblings().slideToggle('fast');
	});
});