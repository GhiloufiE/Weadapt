import Cookies from 'js-cookie';
import siteMessage from '../../../assets/js/lib/site-message';
import siteMessageClear from '../../../assets/js/lib/site-message-clear';

const initDonwloadEvent = () => {
	const buttonNodes = document.querySelectorAll('.wp-block-button.download .wp-block-button__link');

	if (buttonNodes) {
		buttonNodes.forEach(button => {
			button.addEventListener('click', event => {
				if (button.dataset.fileId) {
					const fileID = parseInt(button.dataset.fileId);

					if (!button.classList.contains('loading')) {
						siteMessageClear();

						button.classList.add('loading');

						const formData = new FormData();

						formData.append('action', 'file_download');
						formData.append('event', event);
						formData.append('file_id', fileID);

						// eslint-disable-next-line no-undef
						fetch(vars.ajaxUrl, {
							method: 'POST',
							body: formData,
						})
							.then(response => response.json())
							.then(data => {
								const { download_count: downloadCount } = data;

								button.classList.remove('loading');

								try {
									button.classList.add('liked');
									// eslint-disable-next-line no-undef
									Cookies.set(`download_${fileID}`, 1, { expires: 2628000 });

									const downloadsCountNode = document.querySelector(`[data-resource-id="${fileID}"]`);

									if (downloadsCountNode) {
										downloadsCountNode.innerText = downloadCount;
									}
								} catch (err) {
									siteMessage('error', msg);
								}
							})
							.catch(err => {
								siteMessage('error', err);
							});
					}


					return false;
				}
			});
		});
	}
};

initDonwloadEvent();