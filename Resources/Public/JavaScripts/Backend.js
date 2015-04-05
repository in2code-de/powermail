/**
 * Powermail main JavaScript for Backend Module
 */
jQuery(document).ready(function($) {

	/**
	 * List View
	 */

	// slideToggle Details
	$('.powermail_listbe_details_container').hide();
	$('.openPowermailDetails, .powermail_listbe_overview > .col-title').click(function() {
		$(this).closest('tr').next().toggleClass('powermail_listbe_details_closed').find('.powermail_listbe_details_container').slideToggle();
	});

	// Hide/Unhide Mails
	$(document).on('click', '.unhideMail, .hideMail', function () {
		$this = $(this);
		formToken = $this.closest('td').find('.container_formtoken').val();
		uid = $this.closest('td').find('.container_uid').val();
		table = $this.closest('td').find('.container_table').val();

		if ($this.hasClass('unhideMail')) {
			$this
				.removeClass('t3-icon-edit-hide')
				.removeClass('unhideMail')
				.removeClass('fa-toggle-on')
				.addClass('t3-icon-edit-unhide')
				.addClass('hideMail')
				.addClass('fa-toggle-off');
			$this
				.closest('tr')
				.find('.t3-icon:first')
				.addClass('transparent');
			hidden = 1;
		} else {
			$this
				.removeClass('t3-icon-edit-unhide')
				.removeClass('hideMail')
				.removeClass('fa-toggle-off')
				.addClass('t3-icon-edit-hide')
				.addClass('unhideMail')
				.addClass('fa-toggle-on');
			$this
				.closest('tr')
				.find('.t3-icon:first')
				.removeClass('transparent');
			hidden = 0;
		}
		url = 'tce_db.php?&data[' + table + '][' + uid + '][hidden]=' + hidden + '&redirect=' + T3_THIS_LOCATION + '&vC=b601970a97' + formToken + '&prErr=1&uPT=1';
		$.ajax({
			url: url
		});
	});

	// Delete Mail
	$(document).on('click', '.deleteMail', function () {
		$this = $(this);
		formToken = $this.closest('td').find('.container_formtoken').val();
		uid = $this.closest('td').find('.container_uid').val();
		table = $this.closest('td').find('.container_table').val();
		confirmationMessage = $this.closest('td').find('.container_label_delete_confirmation').val();

		if (confirm(confirmationMessage)) {
			$this.closest('tr').fadeOut('slow', function() {
				$(this).next().remove();
				$(this).remove();
			});

			url = 'tce_db.php?&cmd[' + table + '][' + uid + '][delete]=1&redirect=' + T3_THIS_LOCATION + '&vC=3c76f1d3bb&prErr=1&uPT=1' + formToken
			$.ajax({
				url: url
			});
		}
	});

	// pagebrowser (add hiddenfield with pagebrowser variables to search form and submit)
	$('.f3-widget-paginator a').click(function(e) {
		href = $(this).prop('href');
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
		e.preventDefault();
		href = $(this).prop('href');
		hrefParts = href.split('&');
		for (i = 0; i < hrefParts.length; i++) {
			if (hrefParts[i].indexOf('sorting') != -1) {
				params = hrefParts[i];
			}
		}
		if (params != undefined) {
			paramsParts = params.split('=');
			paramsParts[0] = paramsParts[0].replace('%40', '@');

			var sortingContainer = $('.extended_export_sorting_container[name="' + decodeURI(paramsParts[0]) + '"]');
			if (sortingContainer.length) {
				// remove already existing hidden fields
				sortingContainer.remove();
			}
			// add new hidden field
			var html = '<input type="hidden" name="' + paramsParts[0] + '" value="' + paramsParts[1] + '" />';
			$('.hiddenvalues').append(decodeURI(html));

			$('#powermail_module_search').submit();
		}
	});

	// Hide Password values
	$('.powermail_listbe_details_dd.powermail_listbe_details_type_password').html('********');



	/**
	 * List View: Extended Search
	 */
	// Toogle Extended Search
	$('.extended_search_title').click(function() {
		$this = $(this);
		if ($this.hasClass('powermail-close')) {
			$this.removeClass('powermail-close').addClass('powermail-open').children('span').removeClass('t3-icon-move-down').addClass('t3-icon-move-up');
			$('fieldset.extended_search').slideDown('', function() {
				$(this).children('.powermail_module_search_field_container1').children('div.powermail_module_search_field').fadeTo('slow', 1);
			});
		} else {
			$this.removeClass('powermail-open').addClass('powermail-close').children('span').removeClass('t3-icon-move-up').addClass('t3-icon-move-down');
			$('fieldset.extended_search').children('.powermail_module_search_field_container1').children('div.powermail_module_search_field').fadeTo('slow', 0, function() {
				$(this).parent().parent().slideUp();
			});
		}
	});
	// Show Extended Search from the Beginning
	$('fieldset.extended_search input, fieldset.extended_search select').each(function() {
		if ($(this).val() != '') { // if there is content in one of the extended fields
			$('.extended_search_title').removeClass('powermail-close').addClass('powermail-open');
			$('.extended_search').removeClass('powermail-close').addClass('powermail-open');
			return;
		}
	});
	$('.powermail_module_search_field_container2 input, .powermail_module_search_field_container2 select').each(function() {
		if ($(this).val() != '') { // if there is content in one of the extended fields
			$(this).parent().parent().removeClass('powermail-close').addClass('powermail-open');
			return;
		}
	});

	// Datepicker
	$('.powermail_date').each(function() {
		var $this = $(this);
		var datepickerStatus = true;
		var timepickerStatus = true;
		if ($this.data('datepicker-settings') === 'date') {
			timepickerStatus = false;
		} else if ($this.data('datepicker-settings') === 'time') {
			datepickerStatus = false;
		}

		// create datepicker
		$this.datetimepicker({
			format: $this.data('datepicker-format'),
			timepicker: timepickerStatus,
			datepicker: datepickerStatus,
			lang: 'en',
			i18n:{
				en:{
					months: $this.data('datepicker-months').split(','),
					dayOfWeek: $this.data('datepicker-days').split(',')
				}
			}
		});
	});

	// Add new field
	$('.searchAddField span, .searchAddField label').click(function() {
		$('fieldset.extended_search .powermail-close').slideToggle();
	});



	/**
	 * List View: Extended Export
	 */
	// On submit
	$('#powermail_module_search').click(function() {
		$('#forwardToAction').val('list');
	});
	// On export
	$('.export_icon_xls, .export_icon_csv').click(function() {
		if ($(this).hasClass('export_icon_csv')) {
			$('#forwardToAction').val('exportCsv');
		}
		if ($(this).hasClass('export_icon_xls')) {
			$('#forwardToAction').val('exportXls');
		}
		$(this).closest('form').submit();
	});
	// Toogle Extended Export
	$('.extended_export_title').click(function() {
		$this = $(this);
		if ($this.hasClass('powermail-close')) {
			$this.removeClass('powermail-close').addClass('powermail-open').children('span').removeClass('t3-icon-move-down').addClass('t3-icon-move-up');
			$this.next().slideDown('', function() {
				$this.next().children('div').children('div').fadeTo('slow', 1);
			});
		} else {
			$this.removeClass('powermail-open').addClass('powermail-close').children('span').removeClass('t3-icon-move-up').addClass('t3-icon-move-down');
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
				$id = $(this).prop('id');
				if ($id === 'export_field_selection') {
					$fields = $(this).sortable('toArray').toString();
					$('#export_fields').val($fields);
				}
			}
		}).disableSelection();
	});




	/**
	 * Converter
	 */
	$('.openHiddenTable').click(function() {
		var tr = $(this).closest('tr');
		tr.find('.dots').toggle();
		tr.find('.hiddenConverterTable').toggle();
	});



	/**
	 * Reporting View: Form
	 */
	var $table = $('.powermail_reporting_form_fields_table');
	$table.find('tr:even').addClass('even');
	$table.find('tr:first').removeClass('even');
	$table.find('tr').hide();
	$table.find('tr:first').show();
	$table.find('th').click(function() {
		$this = $(this);
		if ($this.hasClass('powermail-close')) {
			$this.removeClass('powermail-close').addClass('powermail-open');
		} else {
			$this.removeClass('powermail-open').addClass('powermail-close');
		}
		$this.parent().siblings().slideToggle('fast');
	});

	// Flot.js
	$('*[data-flot-active="1"]').each(function() {
		var $this = $(this);
		var data = [];
		var values = split($this.data('flot-data-values'), ',');
		var labels = split($this.data('flot-data-labels'), ',');
		var colors = split($this.data('flot-data-colors'), ',');
		for (var i = 0; i < values.length; i++) {
			var dataPackage = {
				data: values[i],
				label: labels[i],
				color: colors[i]
			}
			data.push(dataPackage);
		}
		$.plot($this, data, {
			series: {
				pie: {
					show: true,
					innerRadius: 0.5,
					radius: 1,
					opacity: 0.3,
					color: '#FF0000',
					label: {
						show: true,
						radius: 1,
						formatter: labelFormatter,
						background: {
							opacity: 0.8
						}
					},
					combine: {
						color: '#999',
						threshold: 0.1
					}
				}
			},
			grid: {
				hoverable: true,
				clickable: true
			}
		});
		$this.bind("plothover", function(event, pos, obj) {
			if (!obj) {
				return;
			}
			var percent = parseFloat(obj.series.percent).toFixed(2);
			$("#hover").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
		});
		$this.bind("plotclick", function(event, pos, obj) {
			if (!obj) {
				return;
			}
			percent = parseFloat(obj.series.percent).toFixed(2);
			alert(""  + obj.series.label + ": " + percent + "%");
		});
	});

	/**
	 * @param label
	 * @param series
	 * @returns {string}
	 */
	function labelFormatter(label, series) {
		return '<div class="flotLabel">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
	}
});

/**
 * split even if single value
 *
 * @param value
 * @param separator
 * @returns {Array}
 */
function split(value, separator) {
	if (value.toString().indexOf(separator) !== -1) {
		var values = value.split(separator);
	} else {
		values = [value];
	}
	return values;
}