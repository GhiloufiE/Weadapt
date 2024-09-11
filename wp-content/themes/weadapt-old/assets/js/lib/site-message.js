import siteMessageClear from './site-message-clear';

'use strict';

/**
 * Variables
 */
let siteMessageTimeout;


/**
 * Site Message
 */
const siteMessage = function (status = '', message = '', timeout = 7500) {
	const messageBox = document.querySelector('.main-footer__message');

	if (messageBox !== null) {
		messageBox.classList.add('show', `style--${status}`);
		messageBox.style.setProperty('--hide-timeout', `${parseFloat(timeout / 1000)}s`);

		messageBox.querySelector('.main-footer__message__text').innerHTML = message;

		clearTimeout(siteMessageTimeout);

		siteMessageTimeout = setTimeout(() => {
			siteMessageClear();
		}, timeout);
	}
};


export default siteMessage;