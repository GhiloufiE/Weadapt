const bookmarkInit = () => {
	const button = document.querySelector('[href="#bookmark-event"]');

	button?.addEventListener('click', event => {
		event.preventDefault();
	});
};

bookmarkInit();