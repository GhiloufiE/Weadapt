/* global pwsL10n*/

'use strict';

import siteMessage from '../../../assets/js/lib/site-message';
import siteMessageClear from '../../../assets/js/lib/site-message-clear';


/**
 * Password Strength
 */
const passStrength = function () {
	const passInputs = document.querySelectorAll('.user-pass');

	if (passInputs) {
		[].forEach.call(passInputs, passInput => {
			passInput.addEventListener('keyup', event => {
				event.preventDefault();

				const parentRowElement      = passInput.closest('form');
				const passwordInput         = parentRowElement.querySelector('input[name="user_pass"]');
				const passwordConfirmInput  = parentRowElement.querySelector('input[name="user_pass_confirm"]');
				const strengthLineElement   = parentRowElement.querySelector('.ajax-form__pass-strength__line span');
				const strengthStatusElement = parentRowElement.querySelector('.ajax-form__pass-strength__status');

				let message    = '';
				const strength = wp.passwordStrength.meter(passwordInput.value, wp.passwordStrength.userInputDisallowedList(), passwordConfirmInput.value);

				switch (strength) {
				case 2: message  = pwsL10n.bad; break;
				case 3: message  = pwsL10n.good; break;
				case 4: message  = pwsL10n.strong; break;
				case 5: message  = pwsL10n.mismatch; break;
				default: message = pwsL10n.short; break;
				}

				strengthLineElement.setAttribute('data-strength', strength);
				strengthStatusElement.innerHTML = message;
			});
		});
	}
};


/**
 * Ajax Sign Up
 */
const ajaxSignUp = function () {
	const ajaxForms = document.querySelectorAll('.ajax-form');

	if (ajaxForms) {
		[].forEach.call(ajaxForms, ajaxForm => {
			ajaxForm.addEventListener('submit', event => {
				event.preventDefault();

				siteMessageClear();

				ajaxForm.classList.add('loading');

				const formData = new FormData(ajaxForm);

				// eslint-disable-next-line no-undef
				fetch(vars.ajaxUrl, {
					method: 'POST',
					body: formData,
				})
					.then(response => {
						return response.text();
					})
					.then(data => {
						try {
							const response = JSON.parse(data);

							if (response.status && response.message) {
								const timeout = (response.timeout) ? response.timeout : 7500;

								siteMessage(response.status, response.message, timeout);
							}

							if (response.reload != undefined) {
								setTimeout(() => {
									window.location.replace(response.reload);
								}, 1000);
							} else {
								ajaxForm.classList.remove('loading');

								if (response.popupTrigger != undefined) {
									document.querySelector(`[data-popup="${response.popupTrigger}"]`).click();
								}
							}
						} catch (error) {
							// eslint-disable-next-line
							console.error('Error: ', data);

							siteMessage('error', error.message);

							ajaxForm.classList.remove('loading');
						}
					})
					.catch(error => {
						siteMessage('error', error.message);

						ajaxForm.classList.remove('loading');
					});
			});
		});
	}
};


/**
 * DOMContentLoaded
 */
document.addEventListener('DOMContentLoaded', () => {
	passStrength();
	ajaxSignUp();
});