const runSlider = function () {
	const sliders = document.querySelectorAll('.slider');

	sliders.forEach(slider => {
		const swiper = slider.querySelector('.swiper');
		const swiperPagination = slider.querySelector('.swiper-pagination');
		const i18n = swiperPagination?.getAttribute('data-i18n');

		// eslint-disable-next-line
		new Swiper(swiper, {
			loop: true,
			slidesPerView: 'auto',
			pagination: {
				el: swiperPagination,
				type: 'bullets',
				clickable: true,
				renderBullet: (index, className) => {
					return `<button class="${className}"><span class="screen-reader-text">${i18n} ${index}</span></button>`;
				},
			},
		});
	});
};

document.addEventListener('DOMContentLoaded', () => {
	runSlider();
});

if (window.acf) {
	window.acf.addAction('render_block_preview/type=slider', runSlider);
}