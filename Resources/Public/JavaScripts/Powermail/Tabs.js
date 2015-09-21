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
		var $this = jQuery(this);
		options = jQuery.extend({
			container: 'fieldset',
			header: 'legend',
			tabs: true,
			navigation: true,
			openTabOnError: true,
			tabIndex: true
		}, options);

		// initial show first fieldset
		hideAllFieldsets($this, options);
		$this.find(options.container).first().show();

		generateTabNavigation($this, options);
		generateButtonNavigation($this, options);

		if ($.fn.parsley && $('form[data-parsley-validate="data-parsley-validate"]').length && $('.powermail_morestep').length) {
			$('form[data-parsley-validate="data-parsley-validate"]').parsley().subscribe('parsley:field:validated', function() {
				$('#powermail_tabmenu > li').removeClass('parsley-error');

				// if error occurs
				if (!$('form[data-parsley-validate="data-parsley-validate"]').parsley().isValid()) {

					// for each field with an error
					$('.parsley-error').each(function() {
						var errorIndex = $('.powermail_fieldset').index($(this).closest('.powermail_fieldset'));
						var tabWithError = $('#powermail_tabmenu > li').slice(errorIndex, errorIndex + 1);
						tabWithError.addClass('parsley-error');
					});
				}
			});
		}

		// open tab with error
		if (options.openTabOnError) {
			$.listen('parsley:field:error', function() {
				setTimeout(function() {
					$('.powermail_tabmenu > .parsley-error:first').click();
				}, 50);
			});
		}
	};

	/**
	 * Show Tab
	 *
	 * @param {object} tab
	 * @param {object} form
	 * @param {array} options
	 * @param {int} clickedIndex
	 * @return {void}
	 */
	function showTab(tab, form, options, clickedIndex) {
		$('.powermail_tabmenu li', form).removeClass('act');
		tab.addClass('act');
		hideAllFieldsets(form, options)
		$('.powermail_fieldset', form).slice(clickedIndex, clickedIndex + 1).show();
	}

	/**
	 * Hide all fieldsets
	 *
	 * @param {object} element
	 * @param {array} options
	 * @return {void}
	 */
	function hideAllFieldsets(element, options) {
		element.children(options.container).hide();
	}

	/**
	 * Generate Button Navigation
	 *
	 * @param {object} element
	 * @param {array} options
	 * @return {void}
	 */
	function generateButtonNavigation(element, options) {
		if (!options.navigation) {
			return;
		}

		// buttons
		element.children(options.container).each(function(i) {
			var navigationContainer = $('<div />')
				.addClass('powermail_fieldwrap')
				.addClass('powermail_tab_navigation')
				.appendTo($(this));
			;
			if (i > 0) {
				navigationContainer.append(createPreviousButton(element, options));
			}
			if (i < (element.children(options.container).length - 1)) {
				navigationContainer.append(createNextButton(element, options));
			}
		});
	}

	/**
	 * Create next button
	 *
	 * @param {object} element
	 * @param {array} options
	 * @return {object}
	 */
	function createPreviousButton(element, options) {
		return $('<a />')
			.prop('href', '#')
			.addClass('powermail_tab_navigation_previous')
			.html('<')
			.click(function(e) {
				e.preventDefault();
				showPreviousTab(element, options);
			});
	}

	/**
	 * Create next button
	 *
	 * @param {object} element
	 * @param {array} options
	 * @return {object}
	 */
	function createNextButton(element, options) {
		return $('<a />')
			.prop('href', '#')
			.addClass('powermail_tab_navigation_next')
			.html('>')
			.click(function(e) {
				e.preventDefault();
				showNextTab(element, options);
			});
	}

	/**
	 * Show next Tab
	 *
	 * @param {object} element
	 * @param {array} options
	 * @return {void}
	 */
	function showNextTab(element, options) {
		var currentActiveTab = element.find('#powermail_tabmenu > li').index($('#powermail_tabmenu .act'));
		element.find('#powermail_tabmenu > li.act').removeClass('act').next().addClass('act');
		hideAllFieldsets(element, options);
		element.find('.powermail_fieldset').slice(currentActiveTab + 1, currentActiveTab + 2).show();
	}

	/**
	 * Show previous Tab
	 *
	 * @param {object} element
	 * @param {array} options
	 * @return {void}
	 */
	function showPreviousTab(element, options) {
		var currentActiveTab = element.find('#powermail_tabmenu > li').index($('#powermail_tabmenu .act'));
		element.find('#powermail_tabmenu > li.act').removeClass('act').prev().addClass('act');
		hideAllFieldsets(element, options);
		element.find('.powermail_fieldset').slice(currentActiveTab - 1, currentActiveTab).show();
	}

	/**
	 * Generate Tabs
	 *
	 * @param {object} element
	 * @param {array} options
	 * @return {void}
	 */
	function generateTabNavigation(element, options) {
		if (!options.tabs) {
			return;
		}

		// generate menu
		var $ul = $('<ul />', {
			'id': 'powermail_tabmenu',
			'class': 'powermail_tabmenu'
		}).insertBefore(
			element.children(options.container).filter(':first')
		);

		// all containers
		element.children(options.container).each(function(i, $fieldset){
			//tab_menu
			var li = $('<li/>')
				.html($(this).children(options.header).html())
				.addClass((i==0) ? 'act' : '')
				.addClass('item' + i)
				.on('click keypress', {
					container: element.children(options.container),
					fieldset: $($fieldset)
				}, function() {
					var indexTab = $('.powermail_tabmenu li', element).index($(this));
					showTab($(this), element, options, indexTab);
				});
			if (options.tabIndex) {
				li.prop('tabindex', i);
			}
			$ul.append(li);
		});
	}
});