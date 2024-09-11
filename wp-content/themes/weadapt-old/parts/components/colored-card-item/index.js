const coloredItems = document.querySelectorAll('.colored-card-item');

coloredItems.forEach(item => {
	const showMoreBtn = item.querySelector('.colored-card-item__more-btn');

	showMoreBtn?.addEventListener('click', () => {
		showMoreBtn.closest('.colored-card-item__more')?.classList.toggle('active');
	});
});
