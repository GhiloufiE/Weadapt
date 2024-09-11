import Cookies from 'js-cookie';
import siteMessage from '../../../assets/js/lib/site-message';
import siteMessageClear from '../../../assets/js/lib/site-message-clear';

const initLikesEvent = () => {
	const parentNodes = document.querySelectorAll('.single-references__actions, .map__content__wrap');

	if (parentNodes) {
		parentNodes.forEach(parentNode => {
			parentNode.addEventListener('click', event => {
				const button = event.target.parentElement;

				if (button.parentElement.hasAttribute('data-like')) {
					const parent = button.parentElement;
					const postID = parent.dataset.id;
					const event = button.classList.contains('liked') ? '-' : '+';
					const likesCount = document.querySelector('#likes-count');

					if (!button.classList.contains('loading')) {
						siteMessageClear();

						button.classList.add('loading');

						const formData = new FormData();

						formData.append('action', 'post_like');
						formData.append('event', event);
						formData.append('post_id', postID);
						formData.append('nonce', parent.dataset.nonce);

						// eslint-disable-next-line no-undef
						fetch(vars.ajaxUrl, {
							method: 'POST',
							body: formData,
						})
							.then(response => response.json())
							.then(data => {
								const { status, msg, like_count: likeCount } = data;
								try {
									siteMessage(status, msg);

									if (status === 'success') {
										button.classList.remove('loading');

										if (event === '+') {
											button.classList.add('liked');
											// eslint-disable-next-line no-undef
											Cookies.set(`like_${postID}`, 1, { expires: 2628000 });
											likesCount.innerText = likeCount;
										} else {
											button.classList.remove('liked');
											// eslint-disable-next-line no-undef
											Cookies.remove(`like_${postID}`);
											likesCount.innerText = likeCount;
										}
									}
								} catch (err) {
									siteMessage(status, msg);
								}
							})
							.catch(err => {
								siteMessage('error', err);
							});
					}
				}
			});
		});
	}
};

initLikesEvent();