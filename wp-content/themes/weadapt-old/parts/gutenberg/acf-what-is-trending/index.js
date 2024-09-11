/* globals Swiper, vars */

const initTabs = () => {
	const allSections = document.querySelectorAll('.what-is-trending');

	allSections.forEach(section => {
		const tabs = section.querySelector('.swiper');
		const tabsButton = section.querySelectorAll('.what-is-trending__nav-item');
		const tabsContent = section.querySelectorAll('.what-is-trending__list');

		new Swiper(tabs, {
			slidesPerView: 'auto',
			grabCursor: true,
			watchOverflow: true,
		});

		tabsButton.forEach(button => {
			button.addEventListener('click', event => {
				const tabIndex = button.dataset.tab;

				if (!button.classList.contains('active')) {
					tabsButton.forEach(button => {
						button.classList.remove('active');
					});

					tabsContent.forEach(button => {
						button.classList.remove('active');
					});

					tabsButton[tabIndex]?.classList.add('active');

					if (tabsContent[tabIndex]) {
						section.classList.add('loading');

						const contentNode = tabsContent[tabIndex];

						contentNode.classList.add('active');

						if (contentNode.querySelector('.row--ajax').innerHTML.length === 0) {
							const form           = contentNode.querySelector('.cpt-filters__form');
							const row            = contentNode.querySelector('.row--ajax');
							const loadMoreBtn    = contentNode.querySelector('.cpt-more__btn');

							[row, loadMoreBtn].forEach(item => item.classList.add('loading'));

							const formData = new FormData(form);
							formData.append('action', 'ajax_posts');

							fetch(vars.restLoadPostsUrl, {
								method: 'POST',
								body: formData,
							})
								.then(response => response.text())
								.then(data => {
									try {
										const response = JSON.parse(data);

										const { page, pages, output_html: outputHtml } = response;

										row.setAttribute('data-page', page);
										row.setAttribute('data-pages', pages);

										if (pages > page) {
											loadMoreBtn.parentElement.classList.remove('hidden');
										} else {
											loadMoreBtn.parentElement.classList.add('hidden');
										}

										row.innerHTML = outputHtml;

										[row, loadMoreBtn, section].forEach(item => item.classList.remove('loading'));
									} catch (error) {
										// eslint-disable-next-line
										console.error('Error: ', data);

										[row, loadMoreBtn, section].forEach(item => item.classList.remove('loading'));
									}
								})
								.catch(error => {
									[row, loadMoreBtn, section].forEach(item => item.classList.remove('loading'));

									// eslint-disable-next-line no-console
									console.log(error);
								});
						} else {
							section.classList.remove('loading');
						}

						// Set URL
						if (event.isTrusted) {
							const currentUrl = new URL(window.location.href);

							window.history.pushState(null, '', `${currentUrl.origin}${currentUrl.pathname}?section-tab=${button.dataset.key}`);
						}
					}
				}
			});
		});
	});


	// $_GET variables
	const handleCurrentTabUrl = function() {
		const currentUrl = new URL(window.location.href);

		if (currentUrl.searchParams.has('section-tab')) {
			const tabName   = currentUrl.searchParams.get('section-tab');
			const tabButton = document.querySelector(`.what-is-trending__nav-item[data-key="${tabName}"]`);

			if (tabButton) {
				tabButton.click();
			}
		}
	}

	window.addEventListener('popstate', handleCurrentTabUrl);
};

initTabs();

if (window.acf) {
	window.acf.addAction('render_block_preview/type=what-is-trending', initTabs);
}