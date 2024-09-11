'use strict';

import Cookies from 'js-cookie';
import siteMessage from '../../../assets/js/lib/site-message';
import siteMessageClear from '../../../assets/js/lib/site-message-clear';

/* global vars */


/**
 * Global Debounce
 */
let debounceTimer;

export const debounce = (callback, time) => {
	window.clearTimeout(debounceTimer);
	debounceTimer = window.setTimeout(callback, time);
};


/**
 * Menu
 */

// Close SubMenu
const closeSubMenu = function (e) {
	const openedSubMenuElement = document.querySelector('.main-header__nav .menu-item.open');

	if (openedSubMenuElement) {
		openedSubMenuElement.classList.remove('open');

		openedSubMenuElement.querySelector('.menu-item__wrap a, .menu-item__wrap button').setAttribute('aria-expanded', 'false');
	}
};

// Close SubMenu Event
const closeSubMenuEscEvent = function (event) {
	if (event.key === 'Escape') {
		const menuButtonElement = document.querySelector('.main-header__nav .menu-item.open > button');

		if (menuButtonElement !== null) {
			document.querySelector('.main-header__nav .menu-item.open > button').focus();
		}

		closeSubMenu();

		document.documentElement.removeEventListener('keyup', closeSubMenuEscEvent);
	}
};

// Close SubMenu onClick outside
const closeSubMenuOnClick = e => {
	const {target} = e;
	const openedSubMenuElement = document.querySelector('.main-header__nav .menu-item.open');

	if (openedSubMenuElement && (!target.closest('.main-header') || target.classList.contains('search-form__field'))) {
		closeSubMenu();
	}
};

// Check if touch available
let isTouchAvailable;

const checkTouchForWindowResize = () => {
	isTouchAvailable = window.matchMedia('(pointer: coarse)').matches;

	if (isTouchAvailable && window.matchMedia('(min-width: 768px)').matches) {
		window.addEventListener('click', closeSubMenuOnClick);
	} else {
		window.removeEventListener('click', closeSubMenuOnClick);
	}
};

// Focusable Menu
const menuDropdownElements = document.querySelectorAll('.main-header__nav .menu-item__dropdown');

if (menuDropdownElements.length !== 0) {
	checkTouchForWindowResize();

	[].forEach.call(menuDropdownElements, menuDropdownElement => {
		let subMenuTimer;

		menuDropdownElement.addEventListener('click', function (event) {
			const menuItem = this.parentNode.parentNode;

			menuDropdownElements.forEach(item => {
				const element = item.parentNode.parentNode;

				if (menuItem !== element) {
					element.classList.remove('open');
					element.querySelector('.menu-item__wrap a, .menu-item__wrap button').setAttribute('aria-expanded', 'false');
				}
			});

			if (menuItem.classList.contains('open')) {
				menuItem.classList.remove('open');

				menuItem.querySelector('.menu-item__wrap a, .menu-item__wrap button').setAttribute('aria-expanded', 'false');

				document.documentElement.removeEventListener('keyup', closeSubMenuEscEvent);
			} else {
				menuItem.classList.add('open');

				menuItem.querySelector('.menu-item__wrap a, .menu-item__wrap button').setAttribute('aria-expanded', 'true');

				document.documentElement.addEventListener('keyup', closeSubMenuEscEvent);
			}

			event.preventDefault();
		});

		// Sub/Mega Menu Focusable Items
		const focusableElements = menuDropdownElement.parentElement.parentElement.querySelectorAll('button, [href], [tabindex]:not([tabindex="-1"])');

		if (focusableElements) {
			[].forEach.call(focusableElements, focusableElement => {
				focusableElement.addEventListener('focus', () => {
					if (subMenuTimer) {
						clearTimeout(subMenuTimer);

						subMenuTimer = null;
					}
				});
				focusableElement.addEventListener('blur', () => {
					subMenuTimer = setTimeout(() => {
						if (window.matchMedia('(min-width: 768px)').matches && !isTouchAvailable) {
							closeSubMenu();
						}
					}, 10);
				});
			});
		}
	});
}


// Grid Columns Helper
const subMenuElements = document.querySelectorAll('.mega-menu__col--sub-menu .sub-menu');

if (subMenuElements) {
	[].forEach.call(subMenuElements, subMenuElement => {
		subMenuElement.style.setProperty('--sub-menu-items', subMenuElement.querySelectorAll(':scope > li').length);
	});
}


// Message Popup
const messagePopup = document.querySelector('.message-popup');
const messagePopupClose = messagePopup ? document.querySelector('.message-popup__close') : '';

const closeMessagePopup = () => {
	if (messagePopup && !messagePopup.classList.contains('hidden')) {
		messagePopup.classList.add('hidden');
		Cookies.set('message-popup', 1);
	}
};

if (messagePopup) {
	messagePopupClose.addEventListener('click', closeMessagePopup);
}

// Checkbox Click
const menuCheckboxElements = document.querySelectorAll('.main-header__nav .true-false-button');
const transitionDurationSpeed = parseFloat(getComputedStyle(document.body).getPropertyValue('--transition-speed')) * 1000;

const toggleTransition = trigger => {
	document.body.classList.add('no-transition');

	const clickControll = event => event.preventDefault();

	trigger.addEventListener('click', clickControll);
	closeMessagePopup();

	setTimeout(() => {
		trigger.removeEventListener('click', clickControll);
		document.body.classList.remove('no-transition');
	}, transitionDurationSpeed);
};

if (menuCheckboxElements) {
	[].forEach.call(menuCheckboxElements, menuCheckboxElement => {
		menuCheckboxElement.addEventListener('keypress', event => {
			if (event.key === 'Enter') {
				const checkbox = menuCheckboxElement.querySelector('input[type="checkbox"]');

				checkbox.checked ^= 1;

				closeMessagePopup();

				if (menuCheckboxElement.getAttribute('for') === 'dark-mode') {
					if (transitionDurationSpeed) {
						toggleTransition(checkbox);
					}

					Cookies.set('weadapt-dark-mode', checkbox.checked);
					document.body.classList.toggle('theme--dark');
				}
			}
		});
	});
}

// Dark Mode
const darkModeCheckbox = document.getElementById('dark-mode');

if (darkModeCheckbox !== null) {
	darkModeCheckbox.addEventListener('change', event => {
		if (transitionDurationSpeed) {
			toggleTransition(darkModeCheckbox);
		}

		Cookies.set('weadapt-dark-mode', event.currentTarget.checked, { expires: 7 });
		document.body.classList.toggle('theme--dark');
	});
}
// Low res Images
const lowResImagesCheckbox = document.getElementById('low-quality-images');

if(lowResImagesCheckbox !== null) {
	lowResImagesCheckbox.addEventListener('change', event => {
		if(transitionDurationSpeed) {
			toggleTransition(lowResImagesCheckbox);
		}

		Cookies.set('weadapt-low-quality-images', event.currentTarget.checked, { expires: 7 });
		document.body.classList.toggle('low-quality-images');
		location.reload();
	});
}

/**
 * Search Form
 */
const searchForm = document.querySelector('.search-form');
const searchFormField = document.querySelector('.search-form__field');
const searchFormContent = document.querySelector('.search-form__content');
const searchFormContentWrap = document.querySelector('.search-form__content__wrap');

// Close Active Search Form
const closeActiveSearchForm = function (event) {
	if (
		event.key === 'Escape' || (event.key === undefined && (
			event.target.closest('.search-form') === null && event.target.closest('.search-form__content') === null
		))
	) {
		if (searchFormField) {
			searchFormField.blur();
		}
		if (searchFormContentWrap) {
			searchFormContentWrap.classList.remove('active');
		}

		['click', 'keydown', 'focus'].forEach(event => {
			document.documentElement.removeEventListener(event, closeActiveSearchForm);
		});
	}
};

if (searchFormField !== null) {
	searchFormField.addEventListener('focus', event => {
		event.preventDefault();

		searchFormContentWrap.classList.add('active');

		['click', 'keydown', 'focus'].forEach(event => {
			document.documentElement.addEventListener(event, closeActiveSearchForm);
		});
	});

	searchForm.addEventListener('reset', () => {
		searchFormContent.innerHTML = '';
		searchFormField.classList.remove('loading');
		searchFormContentWrap.classList.remove('active');
		searchFormField.setAttribute('value', '');
	});
}

// Search Rest Data
const searchRestData = function () {
	fetch(vars.restSearchUrl, {
		method: 'POST',
		body: JSON.stringify({
			query: searchFormField.value,
		}),
	})
		.then(response => {
			return response.text();
		})
		.then(data => {
			try {
				const response = JSON.parse(data);

				if (searchFormField.value.length > 2) {
					searchFormContent.innerHTML = response;
				}

				searchFormField.classList.remove('loading');
			} catch (error) {
				// eslint-disable-next-line
				console.error('Error: ', data);

				siteMessage('error', error.message);

				searchFormField.classList.remove('loading');
			}
		})
		.catch(error => {
			siteMessage('error', error.message);

			searchFormField.classList.remove('loading');
		});
};

// Input Event
searchFormField.addEventListener('input', () => {
	searchFormContent.innerHTML = '';
	siteMessageClear();

	if (searchFormField.value.length > 2) {
		searchFormField.classList.add('loading');
		debounce(searchRestData, 500);
	} else {
		searchFormField.classList.remove('loading');
	}
}, false);


/**
 * Sticky Header
 */
const headerElement = document.querySelector('.main-header--desktop');
const stickyElement = headerElement.querySelector('.main-header__main-area');

let headerHeight = 0;
let lastScrollTop = 0;

const stickyHeader = function () {
	const windowScrollTop = window.scrollY;
	const htmlMargin = parseInt(window.getComputedStyle(document.documentElement, null).marginTop, 10);
	const headeBoxOffsetTop = parseInt(headerElement.offsetTop - htmlMargin, 10);

	// Disable on Mobile
	if (window.matchMedia('(max-width: 768px)').matches) {
		headerElement.style.height = 'auto';
		stickyElement.style.marginTop = 0;

		stickyElement.classList.remove('sticky', 'pinned', 'no-transition');

		return;
	}

	// Set Height
	if (headerHeight == 0) {
		headerHeight = headerElement.clientHeight;
	}

	// Stick
	if (windowScrollTop > (headerHeight + htmlMargin + headeBoxOffsetTop)) {
		if (!stickyElement.classList.contains('sticky')) {
			headerElement.style.height = `${headerHeight}px`;
			stickyElement.style.marginTop = `${htmlMargin}px`;

			stickyElement.classList.add('sticky', 'no-transition');
		}
	}

	// Unstick
	if (windowScrollTop <= headerHeight - stickyElement.clientHeight + headeBoxOffsetTop + htmlMargin) {
		headerElement.style.height = 'auto';
		stickyElement.style.marginTop = 0;

		stickyElement.classList.remove('sticky', 'pinned', 'no-transition');
	}

	// Pinned
	if (stickyElement.classList.contains('sticky')) {
		stickyElement.classList.remove('no-transition');

		if (lastScrollTop > windowScrollTop) {
			stickyElement.classList.add('pinned');
		} else if (lastScrollTop < windowScrollTop) {
			stickyElement.classList.remove('pinned');
		}

		lastScrollTop = windowScrollTop;
	}
};

stickyHeader();

window.addEventListener('scroll', () => {
	stickyHeader();
});

window.addEventListener('resize', () => {
	debounce(stickyHeader, 50);
	debounce(closeSubMenu, 50);
	debounce(checkTouchForWindowResize, 50);
});


/**
 * Google Translate
 */
const googleTranslateConfig = {
	lang: 'en',
};

window.addEventListener('load', () => {
	if (!document.querySelector('.menu-item--lang')) {
		return;
	}

	translateInit(googleTranslateConfig);
});

const translateInit = function (config) {
	const code = translateGetCode(config);

	translateHtmlHandler(code);

	if (code == config.lang) {
		// If the default language is the same as the language we are translating into, then we clear the cookies
		translateCookieHandler(null);
	}

	// Initialize the widget with the default language
	// eslint-disable-next-line
	new google.translate.TranslateElement({
		pageLanguage: config.lang,
	});

	const googleLangElements = document.querySelectorAll('[data-google-lang]');

	if (googleLangElements) {
		[].forEach.call(googleLangElements, googleLangElement => {
			googleLangElement.addEventListener('click', function () {
				translateCookieHandler(
					`/${config.lang}/${this.getAttribute('data-google-lang')}`
				);

				window.location.reload();
			});
			googleLangElement.addEventListener('keypress', function (event) {
				if (event.key === 'Enter') {
					translateCookieHandler(
						`/${config.lang}/${this.getAttribute('data-google-lang')}`
					);

					window.location.reload();
				}
			});
		});
	}
};
const translateGetCode = function (config) {
	// If there are no cookies, then we pass the default language
	const lang = Cookies.get('googtrans') != undefined && Cookies.get('googtrans') != 'null' ? Cookies.get('googtrans') : config.lang;
	return lang.match(/(?!^\/)[^/]*$/gm)[0];
};

const translateCookieHandler = function (val) {
	// Writing down cookies /language_for_translation/the_language_we_are_translating_into
	Cookies.set('googtrans', val);

	let domain     = '';
	const hostname = document.location.hostname;
	const dotCount = (hostname.match(/\./g) || []).length;

	if (dotCount === 2) {
		const subdomain = hostname.substring(hostname.indexOf('.') + 1);

		domain = `.${subdomain}`;
	} else {
		domain = `.${hostname}`;
	}

	Cookies.set('googtrans', val, {
		domain: domain,
	});
};

const translateHtmlHandler = function (code) {
	const currentMenuListElement = document.querySelector(`span[data-google-lang="${code}"]`);
	const currentMenuElement = document.querySelector('.menu-item--lang');
	const langSelectElement = document.querySelector('.mega-menu--lang select');

	if (currentMenuListElement !== null) {
		currentMenuListElement.parentElement.classList.add('active');
	}

	if (currentMenuElement !== null) {
		currentMenuElement.innerHTML = code;
	}

	if (langSelectElement !== null) {
		langSelectElement.value = code;
	}
};


/**
 * Update Messages Notification
 */
const handleFepNotification = function (event, response) {
	// Update counts.
	if ( ! response || response.message_unread_count === undefined || response.message_unread_count_prev === undefined ) {
		return;
	}

	if (response.message_unread_count !== response.message_unread_count_prev) {
		const countNodes = document.querySelectorAll('[data-messages]');

		if (countNodes) {
			[].forEach.call(countNodes, countNode => {
				countNode.dataset.messages = parseInt( response.message_unread_count );
			});
		}
	}


	// Update unread messages.
	if ( response.message_unread_ids === undefined || response.message_unread_ids.length === 0 ) {
		return;
	}

	[].forEach.call(response.message_unread_ids, unreadId => {
		const messageNode = document.getElementById(`fep-message-${unreadId}`);

		if (messageNode && ! messageNode.classList.contains('fep-table-row-unread')) {
			messageNode.classList.add('fep-table-row-unread');
			messageNode.classList.remove('fep-table-row-read');
		}
	});
};

(function($) {
	$(document).on('fep_notification', handleFepNotification);
})(jQuery);
