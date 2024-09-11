const buttons = document.querySelectorAll('.share__button');

buttons.forEach(btn => {
	const btnLink  = btn.querySelector('.wp-block-button__link');
	const closeBtn = btn.nextElementSibling.querySelector('.share__close');

	if (closeBtn) {
		closeBtn.addEventListener('click', event => {
			event.preventDefault();

			btn.classList.remove('active');
		});
	}

	btnLink.addEventListener('click', event => {
		event.preventDefault();

		btn.classList.toggle('active');
	});
});