jQuery(document).ready(function() {
	var data = '';
	data += 'tx_powermail_pi1[language]=' + $('#powermail_marketing_information').data('language');
	data += '&tx_powermail_pi1[pid]=' + $('#powermail_marketing_information').data('pid');
	data += '&tx_powermail_pi1[mobileDevice]=' + (isMobile() ? 1 : 0);
	data += '&tx_powermail_pi1[referer]=' + encodeURIComponent(document.referrer);
	jQuery.ajax({
		url: getBaseUrl() + '/index.php?&eID=powermailEidMarketing',
		data: data,
		cache: false
	});
});

/**
 * Check if user device is mobile or not
 *
 * @return bool
 */
function isMobile() {
	var ua = navigator.userAgent;
	var checker = {
		iphone:ua.match(/(iPhone|iPod|iPad)/),
		blackberry:ua.match(/BlackBerry/),
		android:ua.match(/Android/)
	}

	if (checker.iphone || checker.blackberry || checker.android) {
		return true;
	}
	return false;
}

/**
 * Return BaseUrl as prefix
 *
 * @return	string	Base Url
 */
function getBaseUrl() {
	var baseurl;
	if (jQuery('base').length > 0) {
		baseurl = jQuery('base').prop('href');
	} else {
		if (window.location.protocol != "https:") {
			baseurl = 'http://' + window.location.hostname;
		} else {
			baseurl = 'https://' + window.location.hostname;
		}
	}
	return baseurl;
}