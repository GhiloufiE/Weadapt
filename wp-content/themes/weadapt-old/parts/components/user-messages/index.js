import siteMessage from '../../../assets/js/lib/site-message';
import siteMessageClear from '../../../assets/js/lib/site-message-clear';


/**
 * Global Debounce
 */
let debounceTimer;

export const debounce = (callback, time) => {
	window.clearTimeout(debounceTimer);
	debounceTimer = window.setTimeout(callback, time);
};


/**
 * Update Messages Content
 */
const triggerFepNotification = function (event, response) {
	if ( ! response || response.message_unread_ids === undefined || response.message_unread_ids.length === 0 ) {
		return;
	}

	// Update Messages
	[].forEach.call(response.message_unread_ids, unreadId => {
		const messageNode = document.getElementById(`fep-message-${unreadId}`);

		if (!messageNode) {
			const filterNode = document.querySelector('select.fep-filter');

			if ( 'show-all' === filterNode.value || 'inbox' === filterNode.value || 'unread' === filterNode.value ) {
				updateMessages();
			}
		}
	} );


	// Update Single Message Popup
	if (!document.documentElement.hasAttribute('data-popup') || document.documentElement.getAttribute('data-popup') !== 'messages') {
		return
	}

	[].forEach.call(response.message_unread_ids, unreadId => {
		const parentIdNode = document.querySelector(`input[id="fep_parent_id"][value="${parseInt(unreadId)}"]`);

		if (parentIdNode) {
			updateMessagePopup(parseInt(unreadId), true);
		}
	});
};

const triggerFepFormSend = function (event, response, form) {
	siteMessageClear();

	setTimeout(() => {
		siteMessage(response.fep_return, response.info);
	});

	if (document.documentElement.hasAttribute('data-popup')) {
		if (document.documentElement.getAttribute('data-popup') === 'messages-new') {
			updateMessages();
		} else {
			const parentIdNode = document.getElementById('fep_parent_id');

			if (parentIdNode) {
				updateMessagePopup(parseInt(parentIdNode.value), true);
			}
		}
	}
};

(function($) {
	$(document).on('fep_notification', triggerFepNotification);
	$(document).on('fep_form_submit_done', triggerFepFormSend);
})(jQuery);



/**
 * Update Counts
 */
const updateMessageCounts = (unread) => {
	// Fix unread messages count.
	if (typeof localStorage !== 'undefined') {
		localStorage.removeItem( 'fep_notification_time' );
		localStorage.removeItem( 'fep_notification_response' );
	}

	// Update counts.
	const countNodes = document.querySelectorAll('[data-messages]');

	if (countNodes) {
		[].forEach.call(countNodes, countNode => {
			countNode.dataset.messages = parseInt( unread );
		});
	}

	// Update document title
	if ( '1' == fep_notification_script.show_in_title ) {
		let title = document.title;

		// this will test if the document title already has a notification count in it, e.g. (1) website title
		if ( '(' === title.charAt(0) && -1 !== title.indexOf( ') ' ) ) {
			// we will split the title after the first bracket
			title = title.split( ') ' );

			if ( unread ) {
				document.title = '(' + unread + ') ' + title[1];
			} else {
				document.title = title[1];
			}
		} else {
			if ( unread ) {
				document.title = '(' + unread + ') ' + title;
			}
		}
	}
}



/**
 * Single Message Popup
 */
const updateMessagePopup = function( fepId = 0, isUpdate = false ) {
	const messagesPopup        = document.querySelector('[data-popup-content="messages"]');
	const messagesPopupButton  = messagesPopup.querySelector('[data-popup="messages"]');
	const messagesPopupContent = messagesPopup.querySelector('.popup__content');

	siteMessageClear();

	// Trigger popup.
	if (! isUpdate) {
		messagesPopupButton.click();
		messagesPopupContent.innerHTML = '';
	}

	messagesPopup.classList.add('loading');

	const formData = new FormData();

	formData.append('action', 'messages_view');
	formData.append('is_update', isUpdate);
	formData.append('fep_id', fepId);
	formData.append('nonce', messagesPopupContent.dataset.nonce);

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
				const { status, message, content, unread } = response;

				if ('success' === status) {
					if (isUpdate) {
						const messagesTabNode = messagesPopupContent.querySelector('.fep-message');

						messagesTabNode.innerHTML = content;
					} else {
						messagesPopupContent.innerHTML = content;
					}

					messagesPopup.scrollTop = messagesPopup.scrollHeight;

					// Remove unread classes.
					const messageNode = document.getElementById(`fep-message-${fepId}`);

					if (messageNode) {
						messageNode.classList.remove('fep-table-row-unread');
						messageNode.classList.add('fep-table-row-read');
					}

					updateMessageCounts(unread);
				} else {
					siteMessage(status, message);

					// Trigger popup.
					if (! isUpdate) {
						messagesPopupButton.click();
					}
				}

				messagesPopup.classList.remove('loading');
			} catch (error) {
				// eslint-disable-next-line
				console.error('Error: ', data);

				siteMessage('error', error.message);

				messagesPopup.classList.remove('loading');
			}
		})
		.catch(error => {
			siteMessage('error', error);

			messagesPopup.classList.remove('loading');
		});
}


/**
 * Single Message Popup Click
 */
const messagesTabNode = document.getElementById('tab-messages-panel');

if (messagesTabNode) {
	messagesTabNode.addEventListener('click', event => {
		if (event.target.matches('.fep-message') ) {
			event.preventDefault();
			event.stopImmediatePropagation();

			const fepId = event.target.getAttribute('fep-id');

			updateMessagePopup(fepId);
		}
	});
}


/**
 * Update Messages
 */
const messagesNode = document.querySelector('#tab-messages-panel .messages');

const updateMessages = function (currentPage = 0, isUpdate = false) {
	const searchNode = document.querySelector('input.fep-messagebox-search-form-field');
	const filterNode = document.querySelector('select.fep-filter');
	const tableNode  = document.getElementById('fep-box-content-content');
	const nextPage   = currentPage ? parseInt(currentPage) + 1 : 1;

	if (!isUpdate) {
		messagesNode.classList.add('loading');

		siteMessageClear();
	}

	fetch(`${vars.ajaxUrl}?action=messages_filter&fep-search=${searchNode.value}&fep-filter=${filterNode.value}&feppage=${nextPage}`, {
		method: 'GET',
	})
		.then(response => {
			return response.text();
		})
		.then(data => {
			try {
				const response = JSON.parse(data);

				if (response.content) {
					if (currentPage) {
						const tableRowsNode  = document.getElementById('fep-table');
						const contentDOM     = new DOMParser().parseFromString(response.content, 'text/html');
						const messagesDOM    = contentDOM.querySelectorAll('.fep-table-row');
						const buttonMoreDOM  = contentDOM.querySelector('.messages__more');
						const buttonMoreNode = tableNode.querySelector('.messages__more__button');

						if ( messagesDOM ) {
							messagesDOM.forEach(message => {
								tableRowsNode.appendChild(message.cloneNode(true));
							});
						}

						if (buttonMoreNode) {
							buttonMoreNode.dataset.page = nextPage;

							if (!buttonMoreDOM || buttonMoreDOM.classList.contains('hidden')) {
								buttonMoreNode.parentElement.remove();
							}
						}

					} else {
						messagesNode.innerHTML = response.content;

						updateMessageCounts(response.unread);

						// Trigger Content with Popup Elements
						const event = new CustomEvent('popupTrigger', { detail: messagesNode });

						document.dispatchEvent(event);
					}
				}

				messagesNode.classList.remove('loading');
			} catch (error) {
				// eslint-disable-next-line
				console.error('Error: ', data);

				siteMessage('error', error.message);

				messagesNode.classList.remove('loading');
			}
		})
		.catch(error => {
			siteMessage('error', error.message);

			messagesNode.classList.remove('loading');
		});
};


/**
 * Filter Messages Click
 */
if (messagesNode) {
	const messagesTabNode = document.getElementById('tab-messages-panel');

	if (messagesTabNode) {
		messagesTabNode.addEventListener('change', event => {
			// Check All
			if (event.target.matches('.fep-cb-check-uncheck-all') ) {
				const inputNodes = messagesNode.querySelectorAll('input.fep-cb');

				if (inputNodes) {
					[].forEach.call(inputNodes, inputNode => {
						inputNode.checked = event.target.checked;
					});
				}
			}

			// Form Filter
			if (event.target.matches('select.fep-filter') ) {
				updateMessages();
			}
		});

		// Loadmore
		messagesTabNode.addEventListener('click', event => {
			if (event.target.matches('.messages__more__button') ) {
				updateMessages(event.target.dataset.page);
			}
		});

		// Form Search
		messagesTabNode.addEventListener('input', event => {
			if (event.target.matches('.fep-messagebox-search-form-field') ) {
				debounce(updateMessages, 500);
			}
		}, false);

		// Form Submit
		messagesTabNode.addEventListener('submit', event => {
			if (event.target.matches('.fep-message-table') && event.submitter.matches('.fep-button') ) {
				event.preventDefault();

				messagesNode.classList.add('loading');

				siteMessageClear();

				const formData = new FormData(event.target);

				formData.append('action', 'messages_action');
				formData.append('fep_action', 'bulk_action');

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
							const { fep_return: status, info: message } = response;

							if (status && message) {
								siteMessage(status, message);
							}

							updateMessages( 0, true );
						} catch (error) {
							// eslint-disable-next-line
							console.error('Error: ', data);

							siteMessage('error', error.message);

							messagesPopup.classList.remove('loading');
						}
					})
					.catch(error => {
						siteMessage('error', error);

						messagesNode.classList.remove('loading');
					});
			}
		});
	}
}