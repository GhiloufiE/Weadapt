const readMoreContainers = document?.querySelectorAll('.image-text-read-more__more');
readMoreContainers?.forEach(container => {
	const readMoreBtn = container?.querySelector('.image-text-read-more__more-btn');
	readMoreBtn?.addEventListener('click', () => {
		container.classList?.toggle('active');
	});
});