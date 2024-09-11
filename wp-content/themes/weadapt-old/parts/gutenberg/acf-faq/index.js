function debounce(func, wait, immediate) {
	let timeout;
	return function() {
		const context = this,
			args = arguments;
		const later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};

		const callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);

		if (callNow) func.apply(context, args);
	};
}

const toggleAccordion = event => {
	event.preventDefault();

	const faqList = event.currentTarget.closest('.faq__col');
	const faqItems = faqList.querySelectorAll('.faq__item');
	const faqItemsVisible = faqList.querySelectorAll('.faq__item:not(.search-hidden) a');

	let faqVisible = 0;

	if (faqList.classList.contains('active')) {
		faqItems.forEach(item => {
			if (!item.classList.contains('search-hidden')) {
				faqVisible += 1;
			}

			if (faqVisible > 3) {
				item.classList.add('hidden');
			}
		});

		faqList.classList.remove('active');

		return;
	}

	if (faqItemsVisible[3]) {
		setTimeout(() => {
			faqItemsVisible[3].focus();
		}, 0);
	}

	faqList.classList.add('active');

	faqItems.forEach(item => {
		if (!item.classList.contains('search-hidden')) {
			item.classList.remove('hidden');
		}
	});
};

const searchLocation = {
	FORM: 'form',
	BLOCKS: 'blocks'
}

const faqSearch = (section, value = '', location= searchLocation.FORM) => {
	const faqColumns = section.querySelectorAll('.faq__col');
	const contributeColumns = document.querySelectorAll('section.contribute .row');

	if(location === searchLocation.FORM) {
		faqColumns.forEach(column => {
			const button = column.querySelector('.faq__button');
			const items = column.querySelectorAll('.faq__item');
			const message = column.querySelector('.faq__list__message');
			const queryString = value.toLowerCase();

			let searchCount = 0;

			items.forEach(item => {
				const title = item.innerText.trim().toLowerCase();

				if (title.includes(queryString)) {
					item.classList.remove('search-hidden');

					searchCount += 1;

					if (searchCount <= 3) {
						item.classList.remove('hidden');
					} else {
						item.classList.add('hidden');
					}
				} else {
					item.classList.add('search-hidden');
					item.classList.remove('hidden');
				}

				if (column.classList.contains('active')) {
					item.classList.remove('hidden');
				}
			});

			if (searchCount === 0) {
				message.classList.add('active');
			} else {
				message.classList.remove('active');
			}

			if (button) {
				if (searchCount <= 3) {
					button.style.display = 'none';
				} else {
					button.style.display = '';
				}
			}
		});
	} else {
		let scrollRef = null;
		const queryString = value.toLowerCase();

		contributeColumns?.forEach(contributeColumn => {
			const contributeColumnsMessage =  contributeColumn?.querySelector('.contribute__card-wrap__message');
			const contributeCards = contributeColumn?.querySelectorAll('.contribute__card-wrap:not(.contribute__card-wrap__message)');
			let contributeCardsSearchCount = 0;

			contributeCards?.forEach(contributeCard => {
				const contributeCardTitle = contributeCard?.querySelector('.icon-text-card__title')?.innerText?.trim()?.toLowerCase();

				if (contributeCardTitle?.includes(queryString)) {
					contributeCard?.classList?.remove('search-hidden');
					contributeCardsSearchCount += 1;
					if(scrollRef === null) {
						scrollRef = contributeColumn.parentElement;
					}
				} else {
					contributeCard?.classList?.add('search-hidden');
				}
			});

			if (contributeCardsSearchCount === 0) {
				contributeColumnsMessage?.classList?.add('active');
			} else {
				contributeColumnsMessage?.classList?.remove('active');
			}
		});
		if(scrollRef !== null && queryString?.length > 0) {
			window.scroll({ top: scrollRef.offsetTop, left: 0, behavior: 'smooth' });
		}
		scrollRef = null;
	}
};
const debouncedFaqSearch = debounce(faqSearch, 500); // Debounce interval of 500 milliseconds

const initFaq = () => {
	const allSections = document.querySelectorAll('.faq');

	allSections.forEach(section => {
		const elements = section.querySelectorAll('.faq__button .wp-block-button__link');
		const form = section.querySelector('.faq-form');
		const resetBtn = form?.querySelector('.faq-form__reset');
		const searchLocation = form?.getAttribute('data-location');

		Array.from(elements).forEach(element => {
			element.addEventListener('click', toggleAccordion);
		});

		form?.addEventListener('submit', event => event.preventDefault());

		form?.addEventListener('input', event => {
			const { value } = event.target;

			if (value.length > 0) {
				resetBtn.classList.add('active');
			} else {
				resetBtn.classList.remove('active');
			}

			debouncedFaqSearch(section, value, searchLocation);
		});

		resetBtn?.addEventListener('click', () => {
			resetBtn.classList.remove('active');
			faqSearch(section, '', searchLocation);
		});
	});
};

initFaq();

if (window.acf) {
	window.acf.addAction('render_block_preview/type=faq', initFaq);
}