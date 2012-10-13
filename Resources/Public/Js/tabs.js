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

$.fn.powermailTabs = function(options) {
	'use strict';
	var $this = $(this);
	options = $.extend({
		container: 'fieldset',
		header: 'legend'
	},options);

	// generate menu
	var $ul = $('<ul />', {
		'id': 'powermail_tabmenu',
		'class': 'powermail_tabmenu'
	}).insertBefore($this.children(options.container).filter(':first'));

	//all containers
	$this.children(options.container).each(function(i, $fieldset){
		//tab_menu
		$ul.append(
			$('<li/>')
			.html($(this).children(options.header).html())
			.addClass((i==0) ? 'act' : '')
			.click({
				container: $this.children(options.container),
				fieldset: $($fieldset)
			}, function(e){
				$('.powermail_tabmenu li', $this).removeClass('act');
				$(this).addClass('act');
				e.data.container.hide();
				e.data.fieldset.show()
			})
		)
	});

	// initial show first fieldset
	$this.children(options.container).hide();
	$this.find(options.container).first().show();

	// Stop submit
	$this.submit(function(e) {
		//e.preventDefault();
	});
}