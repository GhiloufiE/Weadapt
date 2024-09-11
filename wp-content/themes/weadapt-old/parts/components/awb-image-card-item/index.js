const cardItems = document.querySelectorAll('.awb-image-card-item');

cardItems.forEach(item => {
    const showMoreBtn = item.querySelector('.awb-image-card-item__more-btn');

    showMoreBtn?.addEventListener('click', () => {
        showMoreBtn.closest('.awb-image-card-item__more')?.classList.toggle('active');
    });
});
