import AlertBanner from './js/components/banner.src';

const initBanner = () => {
  const alert = new AlertBanner;
  alert.init();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initBanner);
} else {
  initBanner();
}

import './styles/app.scss';
