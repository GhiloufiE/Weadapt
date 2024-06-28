'use strict';

/**
 * Transparent Bg
 */
const headerElement = document.querySelector('.main-header--desktop');
headerElement.classList.add('transparent-main');

const transparentBg = function () {

    if (window.scrollY <= 44) {
        headerElement.classList.add('transparent-main');
        headerElement.style.marginBottom = '-58px';
    } else {
        headerElement.classList.remove('transparent-main');
        headerElement.style.marginBottom = '0px';
    }
};

transparentBg();

window.addEventListener('scroll', () => {
    transparentBg();
});
