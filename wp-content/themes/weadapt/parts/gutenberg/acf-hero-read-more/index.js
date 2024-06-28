const readMoreHeroContainers = document?.querySelectorAll('.hero-read-more__more');

readMoreHeroContainers?.forEach(container => {
	const readMoreHeroBtn = container?.querySelector('.hero-read-more__more-btn');
	readMoreHeroBtn?.addEventListener('click', () => {
		container.classList?.toggle('active');
	});
});