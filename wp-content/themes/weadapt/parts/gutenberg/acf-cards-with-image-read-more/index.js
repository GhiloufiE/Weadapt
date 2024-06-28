const cardContainers = document?.querySelectorAll('.cards-with-image-read-more__more');
cardContainers?.forEach(container => {
	const readMoreBtn = container?.querySelector('.cards-with-image-read-more__more-btn');
	readMoreBtn?.addEventListener('click', () => {
		container.classList?.toggle('active');
	});
});