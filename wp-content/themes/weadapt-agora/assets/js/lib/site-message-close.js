import siteMessageClear from './site-message-clear';

'use strict';

/**
 * Site Message Close
 */
const siteMessageClose = function () {
	const closeButton = document.querySelector('.main-footer__message__close');

	if (closeButton !== null) {
		closeButton.addEventListener('click', event => {
			event.preventDefault();

			siteMessageClear();
		});
	}
};


export default siteMessageClose;