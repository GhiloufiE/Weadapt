'use strict';

import siteMessageClear from '../../../assets/js/lib/site-message-clear';

/**
 * Popups
 */

// Set Last Focus Element
let lastFocusElement;

const setLastFocusElement = function (event) {
	if (event.key === 'Tab') {
		lastFocusElement = event.target;
	}
};

// Set Popup Focusable Elements
const setPopupFocusableElements = function (event) {
	const activePopup       = document.querySelector('[data-popup-content].active');
	const focusableElements = activePopup.querySelectorAll('button, [href], [tabindex]:not([tabindex="-1"])');

	if (! focusableElements || event.key !== 'Tab') {
		return;
	}

	if (event.shiftKey) {
		if (document.activeElement === focusableElements[0]) {
			focusableElements[focusableElements.length - 1].focus();

			event.preventDefault();
		}
	} else {
		if (document.activeElement === focusableElements[focusableElements.length - 1]) {
			focusableElements[0].focus();

			event.preventDefault();
		}
	}
};

// Close Active Popup Content
const closeActivePopupContent = function (event) {
	const currentPopup         = document.documentElement.dataset.popup;
	const currentPopupElements = document.querySelectorAll(`[data-popup="${currentPopup}"], [data-popup-content="${currentPopup}"]`);

	if (
		event.key === 'Escape' || (event.key === undefined && (
			event.target.closest('.popup__bg') === null && event.target.closest('.mb-popup-content') === null
		))
	) {
		document.documentElement.removeAttribute('data-popup');

		['click', 'keydown'].forEach(event => {
			document.documentElement.removeEventListener(event, closeActivePopupContent);
		});

		if (currentPopupElements) {
			[].forEach.call(currentPopupElements, currentPopupElement => {
				currentPopupElement.classList.remove('active');
			});
		}

		document.body.addEventListener('keyup', setLastFocusElement);

		if (lastFocusElement) {
			lastFocusElement.focus();
		}

		document.body.removeEventListener('keydown', setPopupFocusableElements);
	}
};

// Toggle Popups
const togglePopups = function (event) {
	event.preventDefault();
	event.stopImmediatePropagation();

	let currentPopup = event.currentTarget.dataset.popup;

	if (! currentPopup && event.target.tagName === 'A') {
		currentPopup = event.currentTarget.getAttribute('href').replace('#', '');
	}

	const activePopupElements  = document.querySelectorAll(`[data-popup]:not([data-popup="${currentPopup}"]), [data-popup-content]:not([data-popup-content="${currentPopup}"])`);
	const currentPopupElements = document.querySelectorAll(`[data-popup="${currentPopup}"], [data-popup-content="${currentPopup}"]`);

	if (activePopupElements) {
		[].forEach.call(activePopupElements, activePopupElement => {
			activePopupElement.classList.remove('active');
		});
	}

	if (event.currentTarget.classList.contains('active')) {
		document.documentElement.removeAttribute('data-popup');

		['click', 'keydown'].forEach(event => {
			document.documentElement.removeEventListener(event, closeActivePopupContent);
		});

		if (currentPopupElements) {
			[].forEach.call(currentPopupElements, currentPopupElement => {
				currentPopupElement.classList.remove('active');
			});
		}

		if (lastFocusElement) {
			lastFocusElement.focus();
		}

		document.body.addEventListener('keyup', setLastFocusElement);
		document.body.removeEventListener('keydown', setPopupFocusableElements);
	} else {
		if (currentPopup == 'form-popup') {
			siteMessageClear();
		}

		document.documentElement.setAttribute('data-popup', currentPopup);

		['click', 'keydown'].forEach(event => {
			document.documentElement.addEventListener(event, closeActivePopupContent);
		});

		document.body.removeEventListener('keyup', setLastFocusElement);

		if (currentPopupElements) {
			[].forEach.call(currentPopupElements, currentPopupElement => {
				currentPopupElement.classList.add('active');

				if (currentPopupElement.dataset.popupContent !== undefined) {
					currentPopupElement.focus();

					document.body.addEventListener('keydown', setPopupFocusableElements);
				}
			});
		}
	}
};

// Run Popups
const runPopups = () => {
	const popupElements = document.querySelectorAll('[data-popup]:not(html)');

	if (popupElements) {
		[].forEach.call(popupElements, popupElement => {
			popupElement.addEventListener('click', togglePopups);
		});
	}

	if (document.documentElement.hasAttribute('data-popup')) {
		['click', 'keydown'].forEach(event => {
			document.documentElement.addEventListener(event, closeActivePopupContent);
		});
	}

	document.body.addEventListener('keyup', setLastFocusElement);

	// Trigger Content with Popup Elements
	document.addEventListener('popupTrigger', function(event) {
		const popupElements = event.detail.querySelectorAll('[data-popup]:not(html)');

		if (popupElements) {
			[].forEach.call(popupElements, popupElement => {
				popupElement.addEventListener('click', togglePopups);
			});
		}
	});
};

runPopups();

export default runPopups;