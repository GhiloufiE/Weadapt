import siteMessage from './site-message';
import siteMessageClear from './site-message-clear';


/**
 * Contact Form 7
 */
const contactForm = () => {
	const wpcf7 = document.querySelectorAll('.wpcf7-form');

	wpcf7.forEach(form => {
		form?.removeAttribute('novalidate');

		['wpcf7mailsent', 'wpcf7invalid', 'wpcf7mailfailed'].forEach(action => {
			form.addEventListener(action, event => {
				const { status, apiResponse: { message, invalid_fields: invalidFields } } = event.detail;

				const statusClass = status == 'mail_sent' ? 'success' : 'error';

				let popupMessage = message;

				if (invalidFields.length) {
					popupMessage = '';

					invalidFields.forEach(item => {
						const { message } = item;
						popupMessage += `${message} <br>`;
					});
				}

				siteMessage(statusClass, popupMessage);

				if (action == 'wpcf7mailsent') {
					document.documentElement.click();
				}
			}, false);
		});

		form.addEventListener('submit', event => {
			event.preventDefault();
			siteMessageClear();

			const i18n = form.closest('.cf7-form')?.getAttribute('data-i18n');
			const requiredFields = form.querySelectorAll('.weadapt-required');
			let error = false;
			let message = '';

			requiredFields?.forEach(field => {
				const value = field.value.trim();
				const label = field.closest('p')?.querySelector('label')?.textContent?.replace('*', '')?.trim();

				if (!value) {
					error = true;

					if (label) {
						message += `"${label}" ${i18n} <br />`;
					} else {
						message = `"${field.getAttribute('name')}" ${i18n} <br/>`;
					}
				}
			});

			if (error) {
				event.stopImmediatePropagation();

				setTimeout(() => {
					siteMessage('error', message);
				}, 50);
			}
		});
	});
};

export default contactForm;