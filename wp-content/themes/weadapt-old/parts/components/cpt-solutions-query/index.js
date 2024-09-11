function isNotEmptyTrimmed(str) {
	if(!str) return false;
	return str?.trim()?.length > 0;
}
const getSearchParams = () => {
	const params = new URL(location.href).searchParams;

	const selectedScales = params.get('solution-scale') ? params.get('solution-scale').split(',').map(scale => +scale).filter(scale => typeof scale == 'number' && !isNaN(scale)) : [];
	const selectedEcosystems = params.get('solution-ecosystem-type') ? params.get('solution-ecosystem-type').split(',').map(ecosystem => +ecosystem).filter(ecosystem => typeof ecosystem == 'number' && !isNaN(ecosystem)) : [];
	const selectedTypes = params.get('solution-type') ? params.get('solution-type').split(',').map(type => +type).filter(type => typeof type == 'number' && !isNaN(type)) : [];
	const selectedSectors = params.get('solution-sector') ? params.get('solution-sector').split(',').map(sector => +sector).filter(sector => typeof sector == 'number' && !isNaN(sector)) : [];
	const selectedImpacts = params.get('solution-climate-impact') ? params.get('solution-climate-impact').split(',').map(impact => +impact).filter(impact => typeof impact == 'number' && !isNaN(impact)) : [];
	const selectedStatuses = params.get('status') ? params.get('status').split(',').map(status => status).filter(status => typeof status == 'string' && isNotEmptyTrimmed(status)) : [];

	const selectedSort = params.get('sort_by') ? params.get('sort_by') : '';
	const selectedPostType = params.get('post_type') ? params.get('post_type').split(',') : [];
	const searchQuery = params.get('search') ? params.get('search') : '';

	return { selectedScales, selectedEcosystems, selectedTypes, selectedSectors, selectedImpacts, selectedStatuses, selectedSort, searchQuery, selectedPostType };
};

const updateSearchParams = params => {
	const queryParams = [];

	for (const param in params) {
		if (params[param] && Array.isArray(params[param]) && params[param].length > 0) {
			queryParams.push(`${param}=${params[param].join(',')}`);
		} else if (params[param] && typeof params[param] === 'string') {
			queryParams.push(`${param}=${params[param]}`);
		}
	}
	const newUrl = `${window.location.pathname}${queryParams.length > 0 ? '?' : ''}${queryParams.join('&')}`;

	window.history.pushState(null, '', newUrl);
};

const calculateDropdownWidth = () => {
	const dropdown = document.querySelector('.dropdown-wrapper__inner.active');
	const dropdownMenu = dropdown?.querySelector('.dropdown-wrapper__menu');

	const { right } = dropdown.getBoundingClientRect();

	if (right <= dropdownMenu.clientWidth + 16) {
		dropdown.classList.add('left');
	} else {
		dropdown.classList.remove('left');
	}
};

const fetchData = (queryNode, isLoad = false) => {
	const form           = queryNode.querySelector('.cpt-filters__form');
	const row            = queryNode.querySelector('.row--ajax');
	const loadMoreBtn    = queryNode.querySelector('.cpt-more__btn');
	const filters        = queryNode.querySelector('.cpt-filters');
	const categoriesList = queryNode.querySelector('.cpt-filters__list');

	[row, loadMoreBtn, filters].forEach(item => item?.classList.add('loading'));

	const formData = new FormData(form);
	formData.append('action', 'ajax_posts');

	if (isLoad) {
		formData.append('paged', +row.dataset.page + 1);
	}

	fetch(vars.restLoadPostsUrlAlt, {
		method: 'POST',
		body: formData,
	})
		.then(response => response.text())
		.then(data => {
			try {
				const response = JSON.parse(data);

				const { page, pages, output_html: outputHtml, output_filters_html: outputFiltersHtml } = response;

				row.setAttribute('data-page', page);
				row.setAttribute('data-pages', pages);

				if (pages > page) {
					loadMoreBtn.parentElement.classList.remove('hidden');
				} else {
					loadMoreBtn.parentElement.classList.add('hidden');
				}

				if (isLoad) {
					row.innerHTML += outputHtml;
				} else {
					row.innerHTML = outputHtml;
				}

				if (categoriesList) {
					outputFiltersHtml.trim() ? categoriesList.closest('.cpt-filters__terms').classList.remove('hidden') : categoriesList.closest('.cpt-filters__terms').classList.add('hidden');
					categoriesList.innerHTML = outputFiltersHtml.trim();
				}

				[row, loadMoreBtn, filters].forEach(item => item?.classList.remove('loading'));
			} catch (error) {
				// eslint-disable-next-line
				console.error('Error: ', data);

				[row, loadMoreBtn, filters].forEach(item => item?.classList.remove('loading'));
			}
		})
		.catch(error => {
			[row, loadMoreBtn, filters].forEach(item => item?.classList.remove('loading'));

			// eslint-disable-next-line no-console
			console.log(error);
		});
};

const toggleFilterPopup = triggers => {
	if (triggers.length === 0) return;

	const handleClickOutside = event => {
		if (event.target.className !== 'dropdown-wrapper__icon') {
			window.removeEventListener('click', handleClickOutside);
		}

		triggers.forEach(trigger => {
			const dropDown = trigger.closest('.dropdown-wrapper__inner');

			if (dropDown.classList.contains('active') && !trigger.contains(event.target)) {
				closeDropdown(dropDown);
			}
		});
	};

	const closeDropdown = dropdown => {
		dropdown.classList.remove('active');
		dropdown.querySelector('.dropdown-wrapper__menu').style.minWidth = '';
		dropdown.querySelector('.dropdown-wrapper__dropdown').classList.remove('left');

		window.removeEventListener('resize', calculateDropdownWidth);
	};

	const openDropdown = dropdown => {
		if (!dropdown.classList.contains('active')) {
			dropdown.classList.add('active');
			calculateDropdownWidth(dropdown);

			window.addEventListener('resize', calculateDropdownWidth);

			window.addEventListener('click', handleClickOutside);
		} else {
			closeDropdown(dropdown);
			window.removeEventListener('click', handleClickOutside);
		}
	};

	triggers.forEach(trigger => {
		trigger.addEventListener('click', () => {
			openDropdown(trigger.closest('.dropdown-wrapper__inner'));
		});
	});
};

const toggleItemsActiveState = filtersNode => {
	// eslint-disable-next-line prefer-const
	let { selectedScales, selectedEcosystems, selectedTypes, selectedSectors, selectedImpacts, selectedStatuses, selectedSort, searchQuery, selectedPostType } = getSearchParams();

	const updateMiltipleDataValues = (data, value) => {
		if (!data.includes(value)) {
			data.push(value);
		} else {
			const index = data.indexOf(value);
			data.splice(index, 1);
		}
	};

	filtersNode.forEach(item => {
		item.addEventListener('click', event => {
			const { target } = event;

			if (target.tagName !== 'INPUT' && target.tagName !== 'BUTTON') {
				return;
			}

			let value = null;
			let name = '';
			if (target.tagName === 'INPUT') {
				value = target.value;
				name = target.name;
			} else if (target.tagName === 'BUTTON') {
				value = target.parentElement.querySelector('input').value;
				name = target.parentElement.querySelector('input').name;
			}

			switch (name) {
				case 'solution-scale[]':
					updateMiltipleDataValues(selectedScales, +value);
					break;

				case 'solution-ecosystem-type[]':
					updateMiltipleDataValues(selectedEcosystems, +value);
					break;

				case 'solution-type[]':
					updateMiltipleDataValues(selectedTypes, +value);
					break;

				case 'solution-sector[]':
					updateMiltipleDataValues(selectedSectors, +value);
					break;

				case 'solution-climate-impact[]':
					updateMiltipleDataValues(selectedImpacts, +value);
					break;

				case 'status_full':
				case 'status_pilot':
					updateMiltipleDataValues(selectedStatuses, value);
					break;

				case 'sort_by':
					item.querySelector('.sort-by-label').innerText = value;
					selectedSort = value;
					break;

				case 'search':
					searchQuery = value;
					break;

				default:
					throw new Error('Unknown input name.');
			}

			const params = {
				'search': searchQuery,
				'solution-scale': selectedScales,
				'solution-ecosystem-type': selectedEcosystems,
				'solution-type': selectedTypes,
				'solution-sector': selectedSectors,
				'solution-climate-impact': selectedImpacts,
				'status': selectedStatuses,
				'sort_by': selectedSort,
				'post_type': selectedPostType,
			};

			updateSearchParams(params);
		});
	});
};

const initQuery = () => {
	const queryNodes = document.querySelectorAll('.cpt-solutions-query');

	if (queryNodes) {
		queryNodes.forEach(queryNode => {
			const loadMore   = queryNode.querySelector('.cpt-more__btn');
			const filterForm = queryNode.querySelector('.cpt-filters__form');

			if (filterForm) {
				const filtersNode = filterForm.querySelectorAll('.cpt-filters__item, .cpt-filters__categories, .status-filters__item-input');
				const dropdownTriggers = filterForm.querySelectorAll('.dropdown-wrapper__icon');

				toggleItemsActiveState(filtersNode);
				toggleFilterPopup(dropdownTriggers);

				filterForm.addEventListener('change', event => {
					event.preventDefault();
					if(event.target.name === 'search') return;
					fetchData(queryNode);
				});

				filterForm.addEventListener('submit', event => {
					event.preventDefault();

					fetchData(queryNode);
				});
			}

			if (loadMore) {
				loadMore.addEventListener('click', () => {
					fetchData(queryNode, true);
				});
			}
		});
	}
};

initQuery();