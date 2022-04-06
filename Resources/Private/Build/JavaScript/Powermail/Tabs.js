/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alexander Kellner <alexander.kellner@in2code.de>, in2code
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

jQuery(document).ready(function($) {
	$.fn.powermailTabs = function(options) {
		'use strict';
		var $form = jQuery(this);
		options = jQuery.extend({
			container: 'fieldset',
			header: 'legend',
			tabs: true,
			navigation: true,
			openTabOnError: true,
			tabIndex: true,
			tabMenuClassName: 'btn-group',
			tabMenuItemActiveClassName: 'btn-primary',
			tabMenuItemErrorClassName: 'btn-danger'
		}, options);

		showOnlyFirstFieldset($form, options);
		generateTabNavigation($form, options);
		generateButtonNavigation($form, options);
		cleanAndAddErrorClassToTabs($form, options);
		openTabWithError($form, options);
	};

	/**
	 * initial show first fieldset
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @returns {void}
	 */
	function showOnlyFirstFieldset($form, options) {
		hideAllFieldsets($form, options);
		$form.find(options.container).first().show();
	}

	/**
	 * Generate Tabs
	 *
	 * @param {jQuery} $form Complete form object
	 * @param {array} options
	 * @return {void}
	 */
	function generateTabNavigation($form, options) {
		if (!options.tabs) {
			return;
		}

		// generate menu
		var $tabMenu = $('<div />', {
			'class': options.tabMenuClassName
		}).insertBefore(
			$form.children(options.container).filter(':first')
		);

		// all containers
		$form.children(options.container).each(function(i, $fieldset) {
			//tab_menu
			//<button type="button" class="btn btn-default">Left</button>
			var li = $('<button/>')
				.html($(this).children(options.header).html())
				.addClass((i==0) ? options.tabMenuItemActiveClassName : '')
				.addClass('item' + i)
				.addClass('btn btn-default')
				.prop('type', 'button')
				.on('click keypress', {
					container: $form.children(options.container),
					fieldset: $($fieldset)
				}, function() {
					var $listItem = $(this);
					var indexTab = $listItem.parent().children().index($listItem);
					showTab($form, options, $listItem, indexTab);
				});
			if (options.tabIndex) {
				li.prop('tabindex', i);
			}
			$tabMenu.append(li);
		});
	}

	/**
	 * Generate Button Navigation
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @return {void}
	 */
	function generateButtonNavigation($form, options) {
		if (!options.navigation) {
			return;
		}

		// buttons
		$form.children(options.container).each(function(i) {
			var navigationContainer = $('<div />')
				.addClass('powermail_fieldwrap')
				.addClass('powermail_tab_navigation')
				.appendTo($(this));
			if (i > 0) {
				navigationContainer.append(createPreviousButton($form, options));
			}
			if (i < ($form.children(options.container).length - 1)) {
				navigationContainer.append(createNextButton($form, options));
			}
		});
	}

	/**
	 * Add error class to tab and show first if Parsley.js is active
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @return {void}
	 */
	function cleanAndAddErrorClassToTabs($form, options) {
		if ($.fn.parsley && $form.data('parsley-validate') === 'data-parsley-validate') {
			$form.parsley().subscribe('parsley:field:validated', function() {
				removeErrorClassFromTabs($form, options);
				addErrorClassToTabs($form, options);
			});
		}
	}

	/**
	 * Open Tab with error
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @return {void}
	 */
	function openTabWithError($form, options) {
		if (options.openTabOnError && $.fn.parsley) {
			$.listen('parsley:field:error', function() {
				setTimeout(function() {
					$form
						.find('.' + options.tabMenuClassName + ' > .' + options.tabMenuItemErrorClassName + ':first')
						.click();
				}, 50);
			});
		}
	}

	/**
	 ************* Internal *************
	 */

	/**
	 * Show Tab by index
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @param {object} $listItem
	 * @param {int} clickedIndex
	 * @return {void}
	 */
	function showTab($form, options, $listItem, clickedIndex) {
		$activeTab = getActiveTabMenuListItem($form, options);
		$activeTab.removeClass(options.tabMenuItemActiveClassName);
		$listItem.addClass(options.tabMenuItemActiveClassName);
		hideAllFieldsets($form, options);
		$('.powermail_fieldset', $form).slice(clickedIndex, clickedIndex + 1).show();
	}

	/**
	 * Hide all fieldsets
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @return {void}
	 */
	function hideAllFieldsets($form, options) {
		$form.children(options.container).hide();
	}

	/**
	 * Create next button
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @return {object}
	 */
	function createPreviousButton($form, options) {
		return $('<a />')
			.prop('href', '#')
			.addClass('btn btn-warning')
			.html('<')
			.click(function(e) {
				e.preventDefault();
				showPreviousTab($form, options);
			});
	}

	/**
	 * Create next button
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @return {object}
	 */
	function createNextButton($form, options) {
		return $('<a />')
			.prop('href', '#')
			.addClass('btn btn-primary pull-right')
			.html('>')
			.click(function(e) {
				e.preventDefault();
				showNextTab($form, options);
			});
	}

	/**
	 * Show next Tab
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @return {void}
	 */
	function showNextTab($form, options) {
		var currentActiveTabIndex = getIndexOfCurrentActiveTab($form, options);
		$activeTab = getActiveTabMenuListItem($form, options);
		$activeTab.removeClass(options.tabMenuItemActiveClassName).next().addClass(options.tabMenuItemActiveClassName);
		showFieldsetByIndex($form, options, currentActiveTabIndex + 1);
	}

	/**
	 * Show previous Tab
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @return {void}
	 */
	function showPreviousTab($form, options) {
		var currentActiveTabIndex = getIndexOfCurrentActiveTab($form, options);
		$activeTab = getActiveTabMenuListItem($form, options);
		$activeTab.removeClass(options.tabMenuItemActiveClassName).prev().addClass(options.tabMenuItemActiveClassName);
		showFieldsetByIndex($form, options, currentActiveTabIndex - 1);
	}

	/**
	 * Show a powermail fieldset by given index
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @param {int} index
	 * @return {void}
	 */
	function showFieldsetByIndex($form, options, index) {
		hideAllFieldsets($form, options);
		$form.find('.powermail_fieldset').slice(index, index + 1).show();
	}

	/**
	 * Get index of current active tab
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @returns {int}
	 */
	function getIndexOfCurrentActiveTab($form, options) {
		var $listItems = getCurrentTabMenuListItems($form, options);
		var currentActiveTabIndex = $listItems.index(getActiveTabMenuListItem($form, options));
		return parseInt(currentActiveTabIndex);
	}

	/**
	 * Get all list items from tabmenu
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @returns {jQuery}
	 */
	function getCurrentTabMenuListItems($form, options) {
		return $form.find('.' + options.tabMenuClassName).children();
	}

	/**
	 * Get active list item in tabmenu
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @returns {jQuery}
	 */
	function getActiveTabMenuListItem($form, options) {
		var $listItems = getCurrentTabMenuListItems($form, options);
		return $listItems.filter('.' + options.tabMenuItemActiveClassName);
	}

	/**
	 * Remove error classes from tab navigation
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @returns {void}
	 */
	function removeErrorClassFromTabs($form, options) {
		var $tabMenuListItems = getCurrentTabMenuListItems($form, options);
		$tabMenuListItems.removeClass(options.tabMenuItemErrorClassName);
	}

	/**
	 * Add error classes to tab navigation
	 *
	 * @param {object} $form Complete form object
	 * @param {array} options
	 * @returns {void}
	 */
	function addErrorClassToTabs($form, options) {
		if (!$form.parsley().isValid()) {
			// iterate through all fields with errors
			$form.find('.parsley-error').each(function() {
				var errorIndex = $form.find('.powermail_fieldset').index($(this).closest('.powermail_fieldset'));
				var $tabMenuListItems = getCurrentTabMenuListItems($form, options);
				var $tabWithError = $tabMenuListItems.slice(errorIndex, errorIndex + 1);
				$tabWithError.addClass(options.tabMenuItemErrorClassName);
			});
		}
	}
});
