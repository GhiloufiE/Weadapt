import siteMessage from '../../../assets/js/lib/site-message';
import siteMessageClear from '../../../assets/js/lib/site-message-clear';

const cptQueries = document.querySelectorAll('.cpt-query');

if (cptQueries) {
	cptQueries.forEach(cptQuery => {
		cptQuery.addEventListener('click', event => {
			if (event.target.matches('.wp-block-button__duplicate') && ! event.target.classList.contains('loading')) {
				siteMessageClear();

				event.target.classList.add('loading');

				const formData = new FormData();

				formData.append('action', 'post_duplicate');
				formData.append('post_id', event.target.dataset.id);
				formData.append('nonce', event.target.dataset.nonce);

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

							siteMessage(response.status, response.message);

							if (response.redirect != undefined) {
								setTimeout(() => {
									window.location.replace(response.redirect.replace('&amp;', '&'));
								}, 1000);
							} else {
								event.target.classList.remove('loading');
							}
						} catch (error) {
							// eslint-disable-next-line
							console.error('Error: ', data);

							siteMessage('error', error.message);

							event.target.classList.remove('loading');
						}
					})
					.catch(error => {
						siteMessage('error', error);

						event.target.classList.remove('loading');
					});
			}
		});
	});
}