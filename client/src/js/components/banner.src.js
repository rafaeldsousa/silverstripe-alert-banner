import 'promise-polyfill/src/polyfill';
import elementClosest from 'element-closest';
elementClosest(window);

class AlertBanner {
  constructor() {

    this.init = this.init.bind(this);
  }

  async setBannerCookie(id) {
    const formData = new FormData();
    formData.append('id', id);

    const thisPage = location.pathname === '/' ? '/home/' : location.pathname,
      url = [location.protocol, '//', location.host, thisPage].join(''),
      result = await fetch(url + 'setBannerApplies', {
        method: 'POST',
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin,
        referrer: 'no-referrer', // no-referrer, *client
        body: formData
      });

    return result;
  }

  dismissBanner(banner) {
    banner.classList.toggle('active');
  }
  init() {

    // missing forEach on NodeList for IE11
    if (window.NodeList && !NodeList.prototype.forEach) {
      NodeList.prototype.forEach = Array.prototype.forEach;
    }

    const dismiss = document.querySelector('[data-dismiss-banner]');

    if (dismiss !== null) {
      dismiss.addEventListener('click', e => {
        e.preventDefault();
        const target = e.target;
        const id = target.dataset['bannerId'];
        const banner = target.closest('.alertBanner');

        this.setBannerCookie(id);
        this.dismissBanner(banner);
      });
    }
  }
}

export default AlertBanner;
