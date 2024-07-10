/* globals vars */

import siteMessage from '../../../assets/js/lib/site-message';
import siteMessageClear from '../../../assets/js/lib/site-message-clear';

const initJoinEvent = () => {
	const parentNodes = document.querySelectorAll('.cpt-latest, .cpt-widget, .info-widget-cpt, .info-widget-user, .single-hero, .map__content__wrap');

	if (parentNodes) {
		parentNodes.forEach(parentNode => {
			parentNode.addEventListener('click', event => {
				if (event.target.classList.contains('button-join')) {
					event.preventDefault();

					const button = event.target;

					if (!button.classList.contains('loading')) {
						siteMessageClear();

						button.classList.add('loading');

						const isJoined = button.classList.contains('is-joined');
						const dataId   = button.dataset.id;
						const dataType = button.dataset.type;

						const formData = new FormData();

						formData.append('action', 'ajax_join');
						formData.append('post_id', dataId);
						formData.append('type', dataType);
						formData.append('is_joined', isJoined);
						formData.append('nonce', vars.ajaxJoinNonce);

						fetch(vars.ajaxUrl, {
							method: 'POST',
							body: formData,
						})
							.then(response => response.json())
							.then(data => {
								const { status, message, count_html: countHtml } = data;

								try {
									siteMessage(status, message);

									if (status === 'success') {
										const allSameButtons = document.querySelectorAll(`.button-join[data-id="${dataId}"][data-type="${dataType}"]`);

										if (allSameButtons) {
											if (isJoined) {
												allSameButtons.forEach(sameButton => {
													const sameButtonSpan = sameButton.querySelector('span');

													sameButton.classList.remove('is-joined');
													sameButtonSpan.innerText = sameButton.dataset.joinTitle;
												});
											} else {
												allSameButtons.forEach(sameButton => {
													const sameButtonSpan = sameButton.querySelector('span');

													sameButton.classList.add('is-joined');
													sameButtonSpan.innerText = sameButton.dataset.unjoinTitle;
												});
											}
										}

										if (countHtml) {
											const countNodes = document.querySelectorAll(`.join-count[data-id="${dataId}"][data-type="${dataType}"]`);

											if (countNodes) {
												countNodes.forEach(countNode => {
													countNode.innerText = countHtml;
												});
											}
										}
									}
								} catch (err) {
									siteMessage(status, message);
								}

								button.classList.remove('loading');
							})
							.catch(err => {
								siteMessage('error', err);

								button.classList.remove('loading');
							});
					}
				}
			});
		});
	}
};

initJoinEvent();