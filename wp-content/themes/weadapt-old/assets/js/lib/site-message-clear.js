'use strict';


/**
 * Site Message Clear
 */
const siteMessageClear = function () {
	const messageBox = document.querySelector('.main-footer__message');

	if (messageBox !== null) {
		messageBox.classList.remove('show', 'style--success', 'style--error');
		messageBox.style.removeProperty('--hide-timeout');

		messageBox.querySelector('.main-footer__message__text').innerHTML = '';
	}
};


export default siteMessageClear;