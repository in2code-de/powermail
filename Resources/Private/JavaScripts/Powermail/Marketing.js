/**
 * Powermail functions
 *
 * @params {jQuery} $
 * @class PowermailMarketing
 */
function PowermailMarketing($) {
	'use strict';

	/**
	 * This class
	 *
	 * @type {PowermailMarketing}
	 */
	var that = this;

	/**
	 * Initialize
	 *
	 * @returns {void}
	 */
	this.initialize = function() {
		that.sendMarketingInformation();
	};

	/**
	 * Send marketing information to eID script
	 *
	 * @returns {void}
	 */
	this.sendMarketingInformation = function() {
		var $marketingInformation = $('#powermail_marketing_information');
		var data = '';
		data += 'tx_powermail_pi1[language]=' + $marketingInformation.data('language');
		data += '&id=' + $marketingInformation.data('pid');
		data += '&tx_powermail_pi1[pid]=' + $marketingInformation.data('pid');
		data += '&tx_powermail_pi1[mobileDevice]=' + (that.isMobile() ? 1 : 0);
		data += '&tx_powermail_pi1[referer]=' + encodeURIComponent(document.referrer);

		jQuery.ajax({
			url: that.getBaseUrl() + '/index.php?&eID=powermailEidMarketing',
			data: data,
			cache: false
		});
	};

	/**
	 * Check if user device is mobile or not
	 *
	 * @return bool
	 */
	this.isMobile = function() {
		var ua = navigator.userAgent;
		var checker = {
			iphone:ua.match(/(iPhone|iPod|iPad)/),
			blackberry:ua.match(/BlackBerry/),
			android:ua.match(/Android/)
		};
		return (checker.iphone || checker.blackberry || checker.android);
	};

	/**
	 * Return BaseUrl as prefix
	 *
	 * @return {string} Base Url
	 */
	this.getBaseUrl = function() {
		var baseurl;
		if ($('base').length > 0) {
			baseurl = $('base').prop('href');
		} else {
			if (window.location.protocol != "https:") {
				baseurl = 'http://' + window.location.hostname;
			} else {
				baseurl = 'https://' + window.location.hostname;
			}
		}
		return baseurl;
	}
}

jQuery(document).ready(function($) {
	var PowermailMarketing = new window.PowermailMarketing($);
	PowermailMarketing.initialize();
});
