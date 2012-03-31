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

$.fn.tabs = function(options){
	var $this = $(this);
	options = $.extend({
		container: 'fieldset',
		header: 'legend'
	},options);

	// generate menu
	var string = '<ul id="powermail_tabmenu">';
	$this.children(options.container).children(options.header).each(function(i){
		if (i == 0) {
			string += '<li class="act">';
		} else {
			string += '<li>';
		}
		string += $(this).html();
		string += '</li>';
	});
	string += '</ul>';
	$this.prepend(string);

	// initial show first fieldset
	$this.children(options.container).hide();
	$this.find(options.container).first().show();

	// Stop submit
	$this.submit(function(e) {
		//e.preventDefault();
	});

	$('#powermail_tabmenu li').live('click', function() {
		$('#powermail_tabmenu li').removeClass('act');
		$(this).addClass('act');
		$this.children(options.container).hide(); // hide all fieldsets
		var index = $(this).index('#powermail_tabmenu li'); // get index
		$this.children(options.container).slice(index, index +1).show(); // show selected fieldset
	});
}