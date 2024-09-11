/* eslint-disable no-nested-ternary */
/* eslint-disable no-mixed-operators */
/* eslint-disable indent */
/* globals vars */

/*const inputs = document.querySelectorAll("input[type='search']");*/
const mapInput = document.querySelector(".map__search input[type='search']");
const filtersInput = document.querySelector(".cpt-filters__form__search input[type='search']");
const submitBtn = document.querySelector("button.cpt-filters__form__button");
const resetBtn = document.querySelector("button[type='reset']");

mapInput?.addEventListener('change', () => {
    filtersInput.value = mapInput.value;
    submitBtn?.click();
});

resetBtn?.addEventListener('click', () => {
    mapInput.value = '';
    submitBtn?.click();
});


const getSearchParams = () => {
    const params = new URL(location.href).searchParams;

    const selectedCategories = params.get('categories') ? params.get('categories').split(',').map(category => +category).filter(category => typeof category == 'number' && !isNaN(category)) : [];
    const selectedSort = params.get('sort_by') ? params.get('sort_by') : '';
    const selectedPostTypes = params.get('post_types') ? params.get('post_types').split(',') : [];
    const selectedPostType = params.get('post_type') ? params.get('post_type').split(',') : [];
    const searchQuery = params.get('s') ? params.get('s') : '';

    return {selectedCategories, selectedSort, selectedPostTypes, searchQuery, selectedPostType};
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

const fetchData = (queryNode, isLoad = false) => {
    const form = queryNode.querySelector('.cpt-filters__form');
    const row = queryNode.querySelector('.row--ajax');
    const loadMoreBtn = queryNode.querySelector('.cpt-more__btn');
    const filters = queryNode.querySelector('.cpt-filters');
    const categoriesList = queryNode.querySelector('.cpt-filters__list');

    [row, loadMoreBtn, filters].forEach(item => item?.classList.add('loading'));

    const formData = new FormData(form);
    formData.append('action', 'ajax_posts');

    if (isLoad) {
        formData.append('paged', +row.dataset.page + 1);
    }

    formData.set('s', formData.get('search'));
    fetch(vars.restCaseStudiesUrl, {
        method: 'POST',
        body: formData,
    })
        .then(response => response.text())
        .then(data => {
            try {
                const response = JSON.parse(data);

                const {page, pages, output_html: outputHtml, output_filters_html: outputFiltersHtml} = response;

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

const calculateDropdownWidth = () => {
    const dropdown = document.querySelector('.dropdown-wrapper__inner.active');
    const dropdownMenu = dropdown?.querySelector('.dropdown-wrapper__menu');

    const {right} = dropdown.getBoundingClientRect();

    if (right <= dropdownMenu.clientWidth + 16) {
        dropdown.classList.add('left');
    } else {
        dropdown.classList.remove('left');
    }
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
    let {selectedCategories, selectedSort, selectedPostTypes, searchQuery, selectedPostType} = getSearchParams();

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
            const {target} = event;

            if (target.tagName !== 'INPUT') {
                return;
            }

            const {value} = target;

            switch (target.name) {
                case 'categories[]':
                    updateMiltipleDataValues(selectedCategories, +value);
                    break;

                case 'post_types[]':
                    updateMiltipleDataValues(selectedPostTypes, value);
                    break;

                case 'sort_by':
                    item.querySelector('.sort-by-label').innerText = value;
                    selectedSort = value;
                    break;

                default:
                    throw new Error('Unknown input name.');
            }

            const params = {
                's': searchQuery,
                'categories': selectedCategories,
                'post_types': selectedPostTypes,
                'sort_by': selectedSort,
                'post_type': selectedPostType,
            };

            updateSearchParams(params);
        });
    });
};

const initQuery = () => {
    const queryNodes = document.querySelectorAll('.cpt-query');

    if (queryNodes) {
        queryNodes.forEach(queryNode => {
            const loadMore = queryNode.querySelector('.cpt-more__btn');
            const filterForm = queryNode.querySelector('.cpt-filters__form');

            if (filterForm) {
                const filtersNode = filterForm.querySelectorAll('.cpt-filters__item, .cpt-filters__categories');
                const dropdownTriggers = filterForm.querySelectorAll('.dropdown-wrapper__icon');

                toggleItemsActiveState(filtersNode);
                toggleFilterPopup(dropdownTriggers);

                filterForm.addEventListener('change', event => {
                    event.preventDefault();

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
