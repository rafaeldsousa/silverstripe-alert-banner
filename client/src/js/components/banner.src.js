import 'promise-polyfill/src/polyfill';
import elementClosest from 'element-closest';
elementClosest(window);

class AlertBanner {
  constructor() {

    this.init = this.init.bind(this);
  }

  async setBannerCookie(id) {
    return fetch(`alerts/dismissbanner?id=${id}`);
  }

  dismissBanner(banner) {
    banner.classList.toggle('active');
  }
  init() {

    // missing forEach on NodeList for IE11
    if (window.NodeList && !NodeList.prototype.forEach) {
      NodeList.prototype.forEach = Array.prototype.forEach;
    }

    const dismisses = document.querySelectorAll('[data-dismiss-banner]');

    if (dismisses !== null) {
      var self = this

      dismisses.forEach(function (input) {
        input.addEventListener('click', e => {
          e.preventDefault();
          const target = e.target;
          const id = target.dataset['bannerId'];
          const banner = target.closest('.alertBanner');

          self.setBannerCookie(id);
          self.dismissBanner(banner);
        });
      }
      )
    }
  }
}

export default AlertBanner;
