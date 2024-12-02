class Marketing {
  'use strict';

  initialize = function () {
    const that = this;
    that.#sendMarketingInformation();
  };

  #sendMarketingInformation() {
    const marketingInformation = document.querySelector('#powermail_marketing_information');
    const url = marketingInformation.getAttribute('data-url');

    let data = new URLSearchParams();
    data.append('tx_powermail_pi5[language]', marketingInformation.getAttribute('data-language'));
    data.append('id', marketingInformation.getAttribute('data-pid'));
    data.append('tx_powermail_pi5[pid]', marketingInformation.getAttribute('data-pid'));
    data.append('tx_powermail_pi5[mobileDevice]', (this.#isMobile() ? 1 : 0));
    data.append('tx_powermail_pi5[referer]', document.referrer);

    fetch(url, {method: 'POST', body: data, cache: 'no-cache'});
  };

  #isMobile() {
    var ua = navigator.userAgent;
    var checker = {
      iphone:ua.match(/(iPhone|iPod|iPad)/),
      blackberry:ua.match(/BlackBerry/),
      android:ua.match(/Android/)
    };
    return (checker.iphone || checker.blackberry || checker.android);
  };
}

let marketing = new Marketing();
marketing.initialize();
