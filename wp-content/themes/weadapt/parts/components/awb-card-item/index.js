const cardItems = document.querySelectorAll('.awb-card-item');

cardItems.forEach(item => {
	const showMoreBtn = item.querySelector('.awb-card-item__more-btn');

	showMoreBtn?.addEventListener('click', () => {
		showMoreBtn.closest('.awb-card-item__more')?.classList.toggle('active');
	});
});
