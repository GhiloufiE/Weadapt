import siteMessageClose from './lib/site-message-close';
import contactForm from './lib/contact-form-7';

'use strict';

/**
 * Site Message Events
 */
siteMessageClose();

/**
 * Contact Form 7 Init
 */
contactForm();


/**
 * Theme Body Classes
 */
document.body.addEventListener('mousedown', () => {
	document.body.classList.add('using-mouse');
});

document.body.addEventListener('keydown', event => {
	if (event.key === 'Tab') {
		document.body.classList.remove('using-mouse');
	}
});

window.addEventListener('load', () => {
	document.body.classList.add('page-has-loaded');
});

