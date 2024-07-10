import Cookies from 'js-cookie';
import siteMessage from './../../../assets/js/lib/site-message';
import siteMessageClear from './../../../assets/js/lib/site-message-clear';

/**
 * Reply & Cansel Reply
 */
const commentTextarea = document.getElementById('comment');
const commentSubmit   = document?.getElementById('submit');
const replyTitle      = document?.getElementById('reply-title')?.textContent;

if (commentTextarea && replyTitle) {
	const replyButtons = document.querySelectorAll('.comment-reply-link');

	replyButtons?.forEach(button => {
		button.addEventListener('click', event => {
			event.preventDefault();

			commentTextarea.placeholder = button.dataset.replyto;
			commentSubmit.value = commentSubmit.dataset.respondText;

			const canselReplyButton = document.getElementById('cancel-comment-reply-link');

			canselReplyButton.addEventListener('click', event => {
				event.preventDefault();

				commentTextarea.placeholder = replyTitle;
				commentSubmit.value = commentSubmit.dataset.postText;
			});
		});
	});
}


/**
 * Show Children Comments
 */
const childrenButtons = document.querySelectorAll('.comment__link--children');

childrenButtons?.forEach(button => {
	button.addEventListener('click', event => {
		event.preventDefault();

		button.closest('.comment__footer').nextElementSibling.classList.toggle('active');
	});
});


/**
 * Hash Comments Open Popup
 */
let target = window.location.hash;

if (target.includes('#comment-')) {
	const popupCommentsButton = document.querySelector('button[data-popup="comments"]');

	if (popupCommentsButton) {
		history.replaceState(null, null, ' ');

		popupCommentsButton.click();
	}
}


/**
 * Comment Like
 */
const likeButtons = document.querySelectorAll('.comment__link--like');

likeButtons?.forEach(button => {
	button.addEventListener('click', event => {
		event.preventDefault();

		siteMessageClear();

		button.classList.add('loading');

		const formData  = new FormData();
		const likeEvent = button.classList.contains('liked') ? '-' : '+';
		const commentID = button.dataset.id

		formData.append('action', 'comment_like');
		formData.append('event', likeEvent);
		formData.append('comment_id', commentID);

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

						if (likeEvent === '+') {
							button.classList.add('liked');
							// eslint-disable-next-line no-undef
							Cookies.set(`like_comment_${commentID}`, 1, { expires: 2628000 });
							button.querySelector('span').innerText = likeCount;
						} else {
							button.classList.remove('liked');
							// eslint-disable-next-line no-undef
							Cookies.remove(`like_comment_${commentID}`);
							button.querySelector('span').innerText = likeCount;
						}
					}
				} catch (err) {
					siteMessage(status, msg);
				}
			})
			.catch(err => {
				siteMessage('error', err);
			});
	});
});