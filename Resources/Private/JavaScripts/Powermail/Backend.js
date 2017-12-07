/**
 * Powermail functions
 *
 * @params {jQuery} $
 * @class PowermailBackend
 */
function PowermailBackend($) {
	'use strict';

	/**
	 * Initialize
	 *
	 * @returns {void}
	 */
	this.initialize = function() {
		addDetailOpenListener();
		addVisibilityChangeListener();
		addDeleteMailListener();
		addPageBrowseParamsListener();
		addSortingParamsListener();
		addSelectLineListener();
		addSelectAllLinesListener();
		addDeleteLinesListener();
		addToggleLinesVisibilityListener();
		addExtendedSearchListener();
		addDatePickerListener();
		addExportListener();
		addConverterDetailsOpenListener();
		hidePasswords();
		reportingView();
	};

	/**
	 * Add listener for opening details in listview
	 *
	 * @returns {void}
	 * @private
	 */
	var addDetailOpenListener = function() {
		// $('*[data-action="powermailDetailsContainer"]').hide();
		$('*[data-action="openPowermailDetails"]').click(function() {
			var $this = $(this);
			$this.closest('tr').find('.icon:first').toggle();
			var $iconLast = $this.closest('tr').find('.openPowermailDetailsIcons .icon:last');
			if ($iconLast.is(':visible')) {
				$iconLast.css('display', 'none');
			} else {
				$iconLast.css('display', 'inline-block');
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
	 * @private
	 */
	var addVisibilityChangeListener = function() {
		$(document).on('click', '.unhideMail, .hideMail', function () {
			var $this = $(this);
			var moduleUri = $this.closest('td').find('.container_module_uri').val();
			var uid = $this.closest('td').find('.container_uid').val();
			var table = $this.closest('td').find('.container_table').val();
			var hidden = visibilityToggleLine($this.closest('tr'));
			sendVisibilityRequest(table, uid, hidden, moduleUri);
		});
	};

	/**
	 * Delete single mail
	 *
	 * @returns {void}
	 * @private
	 */
	var addDeleteMailListener = function() {
		$(document).on('click', '*[data-action="deleteMail"]', function () {
			var $this = $(this);
			var moduleUri = $this.closest('td').find('.container_module_uri').val();
			var uid = $this.closest('td').find('.container_uid').val();
			var table = $this.closest('td').find('.container_table').val();
			var confirmationMessage = $this.closest('td').find('.container_label_delete_confirmation').val();
			if (confirm(confirmationMessage)) {
				removeLine($this.closest('tr'));
				sendDeleteRequest(table, uid, moduleUri);
			}
		});
	};

	/**
	 * pagebrowser (add hiddenfield with pagebrowser variables to search form and submit)
	 *
	 * @returns {void}
	 * @private
	 */
	var addPageBrowseParamsListener = function() {
		$('.powermail_list .pagination a').click(function(event) {
			event.preventDefault();

			var href = $(this).prop('href');
			var hrefParts = href.split('&');
			var params = '';
			for (var i = 0; i < hrefParts.length; i++) {
				if (hrefParts[i].indexOf('widget') !== -1) {
					params = hrefParts[i];
				}
			}

			if (params !== '') {
				var paramsParts = params.split('=');
				paramsParts[0] = paramsParts[0].replace('%40', '@');
				var html = '<input type="hidden" name="' + paramsParts[0] + '" value="' + paramsParts[1] + '" />';
				var $filterForm = $('#powermail_module_search');
				$filterForm.append(decodeURI(html));
				$filterForm.submit();
			}
			return false;
		});
	};

	/**
	 * sorting (add hiddenfield with sorting variables to search form and submit)
	 *
	 * @returns {void}
	 * @private
	 */
	var addSortingParamsListener = function() {
		$('a.sorting').click(function(event) {
			event.preventDefault();
			var href = $(this).prop('href');
			var hrefParts = href.split('&');
			var params = '';
			for (var i = 0; i < hrefParts.length; i++) {
				if (hrefParts[i].indexOf('sorting') !== -1) {
					params = hrefParts[i];
				}
			}
			if (params !== '') {
				var paramsParts = params.split('=');
				paramsParts[0] = paramsParts[0].replace('%40', '@');

				var sortingContainer = $('.extended_export_sorting_container[name="' + decodeURI(paramsParts[0]) + '"]');
				if (sortingContainer.length) {
					// remove already existing hidden fields
					sortingContainer.remove();
				}
				// add new hidden field
				var html = '<input type="hidden" name="' + paramsParts[0] + '" value="' + paramsParts[1] + '" />';
				var $filterForm = $('#powermail_module_search');
				$filterForm.append(decodeURI(html));
				$filterForm.submit();
			}
		});
	};

	/**
	 * Select a line
	 *
	 * @returns {void}
	 * @private
	 */
	var addSelectLineListener = function() {
		$('.addPowermailSelection').click(function() {
			selectOrDeselectLine($(this));
			calculateAndWriteNumbersOfSelections();
		});
	};

	/**
	 * De/select all lines
	 *
	 * @returns {void}
	 * @private
	 */
	var addSelectAllLinesListener = function() {
		$('.addPowermailSelectionAll').click(function() {
			$('.addPowermailSelection').each(function() {
				selectOrDeselectLine($(this));
			});
			calculateAndWriteNumbersOfSelections();
		});
	};

	/**
	 * Delete all selected lines
	 *
	 * @returns {void}
	 * @private
	 */
	var addDeleteLinesListener = function() {
		$('.powermailSelectionDelete').click(function() {
			deleteSelectedLines();
		});
	};

	/**
	 * Change visibility of selected lines (hide/unhide)
	 *
	 * @returns {void}
	 * @private
	 */
	var addToggleLinesVisibilityListener = function() {
		$('.powermailSelectionHide').click(function() {
			toggleVisibilityOfLines($);
		});
	};

	/**
	 * Show Extended Search from the Beginning
	 *
	 * @returns {void}
	 * @private
	 */
	var addExtendedSearchListener = function() {
		$('#extended_search input, #extended_search select').not('*[type="submit"]').each(function() {
			if ($(this).val() !== '') {
				$('#extended_search').addClass('in');
				return;
			}
		});
	};

	/**
	 * Add datepicker to date fields
	 *
	 * @returns {void}
	 * @private
	 */
	var addDatePickerListener = function() {
		if ($.fn.datetimepicker) {
			$('input[data-datepicker="true"]').each(function () {
				var $this = $(this);
				var datepickerStatus = true;
				var timepickerStatus = true;
				if ($this.data('datepicker-settings') === 'date') {
					timepickerStatus = false;
				} else if ($this.data('datepicker-settings') === 'time') {
					datepickerStatus = false;
				}
				$this.datetimepicker({
					format: $this.data('datepicker-format'),
					timepicker: timepickerStatus,
					datepicker: datepickerStatus,
					lang: 'en',
					i18n: {
						en: {
							months: $this.data('datepicker-months').split(','),
							dayOfWeek: $this.data('datepicker-days').split(',')
						}
					}
				});
			});
			$('*[data-datepicker-opener="true"]').click(function () {
				$(this).prev().datetimepicker('show');
			});
		}
	};

	/**
	 * Add export functions
	 *
	 * @returns {void}
	 * @private
	 */
	var addExportListener = function() {
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

		// Reset on submit
		$('*[data-action="searchall_submit"]').click(function() {
			$('#forwardToAction').val('list');
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
	 * @private
	 */
	var addConverterDetailsOpenListener = function() {
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
	 * @private
	 */
	var hidePasswords = function() {
		$('.powermail_listbe_details_dd.powermail_listbe_details_type_password').html('********');
	};

	/**
	 * Format reporting view with flot.js
	 *
	 * @returns {void}
	 * @private
	 */
	var reportingView = function() {
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
			var values = split($this.data('flot-data-values'), ',');
			var labels = split($this.data('flot-data-labels'), ',');
			var colors = split($this.data('flot-data-colors'), ',');
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
	 * @private
	 */
	var visibilityToggleLine = function($tr) {
		var $visibilityButton = $tr.find('.visibilityButton');
		var hidden = 0;
		if ($visibilityButton.hasClass('unhideMail')) {
			$visibilityButton
				.removeClass('unhideMail')
				.removeClass('fa-toggle-off')
				.addClass('hideMail')
				.addClass('fa-toggle-on');
			$tr.find('.powermailRecordIcon').children(':first').removeClass('hide');
			$tr.find('.powermailRecordIcon').children(':last').addClass('hide');
		} else {
			$visibilityButton
				.removeClass('hideMail')
				.removeClass('fa-toggle-on')
				.addClass('unhideMail')
				.addClass('fa-toggle-off');
			$tr.find('.powermailRecordIcon').children().last().removeClass('hide');
			$tr.find('.powermailRecordIcon').children().first().addClass('hide');
			hidden = 1;
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
	 * @private
	 */
	var sendVisibilityRequest = function(table, uid, hidden, moduleUri) {
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
	 * @private
	 */
	var sendDeleteRequest = function(table, uid, moduleUri) {
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
	 * @private
	 */
	var removeLine = function($tr) {
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
	 * @private
	 */
	var selectOrDeselectLine = function($icon) {
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
	 * @private
	 */
	var calculateAndWriteNumbersOfSelections = function() {
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
	 * @private
	 */
	var deleteSelectedLines = function() {
		$('.selectLine').each(function() {
			var $this = $(this);
			var $td = $this.children(':last');
			var moduleUri = $td.find('.container_module_uri').val();
			var uid = $td.find('.container_uid').val();
			var table = $td.find('.container_table').val();
			sendDeleteRequest(table, uid, moduleUri);
			removeLine($this);
			deselectLine($this);
		});
		calculateAndWriteNumbersOfSelections();
	};

	/**
	 * Deselect this line
	 *
	 * @param {jQuery} $line
	 * @returns {void}
	 * @private
	 */
	var deselectLine = function($line) {
		var $icon = $line.find('.addPowermailSelection');
		selectOrDeselectLine($icon);
	};

	/**
	 * Hide/Unhide all selected lines
	 *
	 * @returns {void}
	 * @private
	 */
	var toggleVisibilityOfLines = function() {
		$('.selectLine').each(function() {
			var $this = $(this);
			var $td = $this.children(':last');
			var moduleUri = $td.find('.container_module_uri').val();
			var uid = $td.find('.container_uid').val();
			var table = $td.find('.container_table').val();
			sendVisibilityRequest(table, uid, visibilityToggleLine($this), moduleUri);
			deselectLine($this);
		});
		calculateAndWriteNumbersOfSelections();
	};

	/**
	 * split even if single value
	 *
	 * @param value
	 * @param separator
	 * @returns {Array}
	 * @private
	 */
	var split = function(value, separator) {
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
	 * @private
	 */
	var labelFormatter = function(label, series) {
		return '<div class="flotLabel">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
	};

	// make global
	window.PowermailBackend = PowermailBackend;
}

jQuery(document).ready(function($) {
	var PowermailBackend = new window.PowermailBackend($);
	PowermailBackend.initialize();
});























