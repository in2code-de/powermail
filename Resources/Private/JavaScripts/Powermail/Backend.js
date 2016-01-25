/**
 * Powermail functions
 *
 * @params {jQuery} $
 * @class PowermailBackend
 */
function PowermailBackend($) {
	'use strict';

	/**
	 * This class
	 *
	 * @type {PowermailBackend}
	 */
	var that = this;

	/**
	 * Initialize
	 *
	 * @returns {void}
	 */
	this.initialize = function() {
		that.addDetailOpenListener();
		that.addVisibilityChangeListener();
		that.addDeleteMailListener();
		that.addPageBrowseParamsListener();
		that.addSortingParamsListener();
		that.addSelectLineListener();
		that.addSelectAllLinesListener();
		that.addDeleteLinesListener();
		that.addToggleLinesVisibilityListener();
		that.addExtendedSearchListener();
		that.addDatePickerListener();
		that.addExportListener();
		that.addConverterDetailsOpenListener();
		that.hidePasswords();
		that.reportingView();
	};

	/**
	 * Add listener for opening details in listview
	 *
	 * @returns {void}
	 */
	this.addDetailOpenListener = function() {
		$('.powermail_listbe_details_container').hide();
		$('.openPowermailDetails').click(function() {
			var $this = $(this);
			var $arrow = $this.closest('tr').children(':first').children(':first');
			if ($arrow.hasClass('fa')) {
				// TYPO3 7.x
				$arrow.toggleClass('fa-caret-right').toggleClass('fa-caret-down');
			} else {
				// TYPO3 6.2
				$arrow.toggleClass('t3-icon-irre-collapsed').toggleClass('t3-icon-irre-expanded');
			}
			$this
				.closest('tr')
				.next()
				.toggleClass('powermail_listbe_details_closed')
				.find('.powermail_listbe_details_container')
				.slideToggle();
		});
	};

	/**
	 * Hide/Unhide single mail
	 *
	 * @returns {void}
	 */
	this.addVisibilityChangeListener = function() {
		$(document).on('click', '.unhideMail, .hideMail', function () {
			var $this = $(this);
			var moduleUri = $this.closest('td').find('.container_module_uri').val();
			var uid = $this.closest('td').find('.container_uid').val();
			var table = $this.closest('td').find('.container_table').val();
			var hidden = that.visibilityToggleLine($this.closest('tr'));
			that.sendVisibilityRequest(table, uid, hidden, moduleUri);
		});
	};

	/**
	 * Delete single mail
	 *
	 * @returns {void}
	 */
	this.addDeleteMailListener = function() {
		$(document).on('click', '.deleteMail', function () {
			var $this = $(this);
			var moduleUri = $this.closest('td').find('.container_module_uri').val();
			var uid = $this.closest('td').find('.container_uid').val();
			var table = $this.closest('td').find('.container_table').val();
			var confirmationMessage = $this.closest('td').find('.container_label_delete_confirmation').val();
			if (confirm(confirmationMessage)) {
				that.removeLine($this.closest('tr'));
				that.sendDeleteRequest(table, uid, moduleUri);
			}
		});
	};

	/**
	 * pagebrowser (add hiddenfield with pagebrowser variables to search form and submit)
	 *
	 * @returns {void}
	 */
	this.addPageBrowseParamsListener = function() {
		$('.f3-widget-paginator a').click(function(e) {
			var href = $(this).prop('href');
			var hrefParts = href.split('&');
			for (i = 0; i < hrefParts.length; i++) {
				if (hrefParts[i].indexOf('widget') !== -1) {
					var params = hrefParts[i];
				}
			}
			if (params != undefined) {
				var paramsParts = params.split('=');
				paramsParts[0] = paramsParts[0].replace('%40', '@');
				var html = '<input type="hidden" name="' + paramsParts[0] + '" value="' + paramsParts[1] + '" />';
				$('.hiddenvalues').append(decodeURI(html));
				$('#powermail_module_search').submit();
			}
			e.preventDefault();
		});
	};

	/**
	 * sorting (add hiddenfield with sorting variables to search form and submit)
	 *
	 * @returns {void}
	 */
	this.addSortingParamsListener = function() {
		$('a.sorting').click(function(e) {
			e.preventDefault();
			var href = $(this).prop('href');
			var hrefParts = href.split('&');
			for (i = 0; i < hrefParts.length; i++) {
				if (hrefParts[i].indexOf('sorting') !== -1) {
					var params = hrefParts[i];
				}
			}
			if (params != undefined) {
				var paramsParts = params.split('=');
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
	};

	/**
	 * Select a line
	 *
	 * @returns {void}
	 */
	this.addSelectLineListener = function() {
		$('.addPowermailSelection').click(function() {
			that.selectOrDeselectLine($(this));
			that.calculateAndWriteNumbersOfSelections();
		});
	};

	/**
	 * De/select all lines
	 *
	 * @returns {void}
	 */
	this.addSelectAllLinesListener = function() {
		$('.addPowermailSelectionAll').click(function() {
			$('.addPowermailSelection').each(function() {
				that.selectOrDeselectLine($(this));
			});
			that.calculateAndWriteNumbersOfSelections();
		});
	};

	/**
	 * Delete all selected lines
	 *
	 * @returns {void}
	 */
	this.addDeleteLinesListener = function() {
		$('.powermailSelectionDelete').click(function() {
			that.deleteSelectedLines();
		});
	};

	/**
	 * Change visibility of selected lines (hide/unhide)
	 */
	this.addToggleLinesVisibilityListener = function() {
		$('.powermailSelectionHide').click(function() {
			that.toggleVisibilityOfLines($);
		});
	};

	/**
	 * Open extended search
	 *
	 * @returns {void}
	 */
	this.addExtendedSearchListener = function() {
		$('.extended_search_title').click(function() {
			var $this = $(this);
			if ($this.hasClass('powermail-close')) {
				$this
					.removeClass('powermail-close')
					.addClass('powermail-open')
					.children('span')
					.removeClass('t3-icon-move-down')
					.addClass('t3-icon-move-up');
				$('fieldset.extended_search').slideDown('', function() {
					$(this)
						.children('.powermail_module_search_field_container1')
						.children('div.powermail_module_search_field')
						.fadeTo('slow', 1);
				});
			} else {
				$this
					.removeClass('powermail-open')
					.addClass('powermail-close')
					.children('span')
					.removeClass('t3-icon-move-up')
					.addClass('t3-icon-move-down');
				$('fieldset.extended_search')
					.children('.powermail_module_search_field_container1')
					.children('div.powermail_module_search_field')
					.fadeTo('slow', 0, function() {
						$(this).parent().parent().slideUp();
					});
			}
		});

		// Show Extended Search from the Beginning
		$('fieldset.extended_search input, fieldset.extended_search select').each(function() {
			if ($(this).val() != '') {
				$('.extended_search_title').removeClass('powermail-close').addClass('powermail-open');
				$('.extended_search').removeClass('powermail-close').addClass('powermail-open');
				return;
			}
		});
		$('.powermail_module_search_field_container2 input, .powermail_module_search_field_container2 select').each(function() {
			if ($(this).val() != '') {
				$(this).parent().parent().removeClass('powermail-close').addClass('powermail-open');
				return;
			}
		});

		// Add new field
		$('.searchAddField span, .searchAddField label').click(function() {
			$('fieldset.extended_search .powermail-close').slideToggle();
		});
	};

	/**
	 * Add datepicker to date fields
	 *
	 * @returns {void}
	 */
	this.addDatePickerListener = function() {
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
	};

	/**
	 * Add export functions
	 *
	 * @returns {void}
	 */
	this.addExportListener = function() {
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
			var $this = $(this);
			if ($this.hasClass('powermail-close')) {
				$this
					.removeClass('powermail-close')
					.addClass('powermail-open')
					.children('span')
					.removeClass('t3-icon-move-down')
					.addClass('t3-icon-move-up');
				$this.next().slideDown('', function() {
					$this.next().children('div').children('div').fadeTo('slow', 1);
				});
			} else {
				$this
					.removeClass('powermail-open')
					.addClass('powermail-close')
					.children('span')
					.removeClass('t3-icon-move-up')
					.addClass('t3-icon-move-down');
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
					var $id = $(this).prop('id');
					if ($id === 'export_field_selection') {
						var $fields = $(this).sortable('toArray').toString();
						$('#export_fields').val($fields);
					}
				}
			}).disableSelection();
		});
	};

	/**
	 * Open details in converter view
	 *
	 * @returns {void}
	 */
	this.addConverterDetailsOpenListener = function() {
		$('.openHiddenTable').click(function() {
			var tr = $(this).closest('tr');
			tr.find('.dots').toggle();
			tr.find('.hiddenConverterTable').toggle();
		});
	};

	/**
	 * Simply change value of password fields
	 *
	 * @returns {void}
	 */
	this.hidePasswords = function() {
		$('.powermail_listbe_details_dd.powermail_listbe_details_type_password').html('********');
	};

	/**
	 * Format reporting view with flot.js
	 *
	 * @returns {void}
	 */
	this.reportingView = function() {
		var $table = $('.powermail_reporting_form_fields_table');
		$table.find('tr:even').addClass('even');
		$table.find('tr:first').removeClass('even');
		$table.find('tr').hide();
		$table.find('tr:first').show();
		$table.find('th').click(function() {
			var $this = $(this);
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
			var values = that.split($this.data('flot-data-values'), ',');
			var labels = that.split($this.data('flot-data-labels'), ',');
			var colors = that.split($this.data('flot-data-colors'), ',');
			for (var i = 0; i < values.length; i++) {
				var dataPackage = {
					data: values[i],
					label: labels[i],
					color: colors[i]
				};
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
							formatter: that.labelFormatter,
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
				var percent = parseFloat(obj.series.percent).toFixed(2);
				alert(""  + obj.series.label + ": " + percent + "%");
			});
		});
	};

	/**
	 * ************ INTERNAL *************
	 */

	/**
	 * Set visibility of a line
	 *
	 * @param {jQuery} $tr
	 * @returns {int} should be hidden?
	 */
	this.visibilityToggleLine = function($tr) {
		var $visibilityButton = $tr.find('.visibilityButton');
		var hidden = 1;
		if ($visibilityButton.hasClass('unhideMail')) {
			$visibilityButton
				.removeClass('t3-icon-edit-hide')
				.removeClass('unhideMail')
				.removeClass('fa-toggle-on')
				.addClass('t3-icon-edit-unhide')
				.addClass('hideMail')
				.addClass('fa-toggle-off');
			$tr
				.find('.t3-icon-tcarecords-tx_powermail_domain_model_mails')
				.addClass('transparent');
		} else {
			$visibilityButton
				.removeClass('t3-icon-edit-unhide')
				.removeClass('hideMail')
				.removeClass('fa-toggle-off')
				.addClass('t3-icon-edit-hide')
				.addClass('unhideMail')
				.addClass('fa-toggle-on');
			$tr
				.find('.t3-icon-tcarecords-tx_powermail_domain_model_mails')
				.removeClass('transparent');
			hidden = 0;
		}
		return hidden;
	};

	/**
	 * Send AJAX request to hide/unhide record
	 *
	 * @param {string} table
	 * @param {string} uid
	 * @param {int} hidden
	 * @param {string} moduleUri
	 * @returns {void}
	 */
	this.sendVisibilityRequest = function(table, uid, hidden, moduleUri) {
		var url = moduleUri + '&data[' + table + '][' + uid + '][hidden]=' + hidden + '&redirect=' + T3_THIS_LOCATION;
		$.ajax({
			url: url
		});
	};

	/**
	 * Send AJAX request to delete record
	 *
	 * @param {string} table
	 * @param {string} uid
	 * @param {string} moduleUri
	 * @returns {void}
	 */
	this.sendDeleteRequest = function(table, uid, moduleUri) {
		var url = moduleUri + '&cmd[' + table + '][' + uid + '][delete]=1&redirect=' + T3_THIS_LOCATION;
		$.ajax({
			url: url
		});
	};

	/**
	 * Remove a line
	 *
	 * @param {jQuery} $tr
	 * @returns {void}
	 */
	this.removeLine = function($tr) {
		$tr.fadeOut('slow', function() {
			$tr.next().remove();
			$tr.remove();
		});
	};

	/**
	 * Select or deselect a line
	 *
	 * @param {jQuery} $icon
	 * @returns {void}
	 */
	this.selectOrDeselectLine = function($icon) {
		if ($icon.hasClass('fa')) {
			// TYPO3 7.x
			$icon
				.toggleClass('fa-plus')
				.toggleClass('fa-minus')
				.closest('tr')
				.toggleClass('selectLine');
		} else {
			// TYPO3 6.2
			$icon
				.toggleClass('t3-icon-view-table-expand')
				.toggleClass('t3-icon-view-table-collapse')
				.closest('tr')
				.toggleClass('selectLine');
		}
	};

	/**
	 * Calculate number of selected lines and write
	 *
	 * @returns {void}
	 */
	this.calculateAndWriteNumbersOfSelections = function() {
		var number = $('.selectLine').length;
		$('.selectedLineMessage_numbers').html(number);
		if (number > 0) {
			$('.selectedLineMessage').show();
		} else {
			$('.selectedLineMessage').hide();
		}
	};

	/**
	 * Delete all selected lines
	 *
	 * @returns {void}
	 */
	this.deleteSelectedLines = function() {
		$('.selectLine').each(function() {
			var $this = $(this);
			var $td = $this.children(':last');
			var moduleUri = $td.find('.container_module_uri').val();
			var uid = $td.find('.container_uid').val();
			var table = $td.find('.container_table').val();
			that.sendDeleteRequest(table, uid, moduleUri);
			that.removeLine($this);
			that.deselectLine($this);
		});
		that.calculateAndWriteNumbersOfSelections();
	};

	/**
	 * Deselect this line
	 *
	 * @param {jQuery} $line
	 * @returns {void}
	 */
	this.deselectLine = function($line) {
		var $icon = $line.find('.addPowermailSelection');
		that.selectOrDeselectLine($icon);
	};

	/**
	 * Hide/Unhide all selected lines
	 *
	 * @returns {void}
	 */
	this.toggleVisibilityOfLines = function() {
		$('.selectLine').each(function() {
			var $this = $(this);
			var $td = $this.children(':last');
			var moduleUri = $td.find('.container_module_uri').val();
			var uid = $td.find('.container_uid').val();
			var table = $td.find('.container_table').val();
			that.sendVisibilityRequest(table, uid, that.visibilityToggleLine($this), moduleUri);
			that.deselectLine($this);
		});
		that.calculateAndWriteNumbersOfSelections();
	};

	/**
	 * split even if single value
	 *
	 * @param value
	 * @param separator
	 * @returns {Array}
	 */
	this.split = function(value, separator) {
		if (value.toString().indexOf(separator) !== -1) {
			var values = value.split(separator);
		} else {
			values = [value];
		}
		return values;
	};

	/**
	 * Format a label for reporting view
	 *
	 * @param {string} label
	 * @param {object} series
	 * @returns {string}
	 */
	this.labelFormatter = function(label, series) {
		return '<div class="flotLabel">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
	};

	// make global
	window.PowermailBackend = PowermailBackend;
}

jQuery(document).ready(function($) {
	var PowermailBackend = new window.PowermailBackend($);
	PowermailBackend.initialize();
});























