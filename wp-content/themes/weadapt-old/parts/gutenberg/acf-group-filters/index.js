/* eslint-disable no-nested-ternary */
/* eslint-disable no-mixed-operators */
/* eslint-disable indent */
/* globals vars */

if (typeof groupsData !== 'undefined') {
    if (groupsData.length === 0) {
        groupsData = groupsDataCategories;
    }

    const tabbedSections = document.querySelectorAll('.cpt-query[data-tab]');
    const tabs = document.querySelectorAll('.tabs li');
    const url = window.location.search;
    const urlParams = new URLSearchParams(url);
    let currentTab = tabs.length > 1 ? tabs[0] : '';

    if (tabs.length > 1) {

        if (!urlParams.get('tab')) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', currentTab.innerText);
            window.history.replaceState({}, '', url.href);
        } else {
            tabs.forEach(tab => {
                if (tab.innerText === urlParams.get('tab')) {
                    currentTab = tab;
                }
            });
        }
        currentTab.classList.add('selected');

        tabbedSections.forEach(section => {
            if (section.dataset.tab !== currentTab.innerText) {
                section.classList.add('hidden-tab');
            } else {
                section.classList.remove('hidden-tab');
            }
        });


        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                currentTab.classList.remove('selected');
                currentTab = tab;
                const url = new URL(window.location.href);
                url.searchParams.set('tab', currentTab.innerText);
                window.history.replaceState({}, '', url.href);
                currentTab.classList.add('selected');
                tabbedSections.forEach(section => {
                    if (section.dataset.tab !== tab.innerText) {
                        section.classList.add('hidden-tab');
                    } else {
                        section.classList.remove('hidden-tab');
                    }
                });
            })
        });
    }

    const getSearchParams = () => {
        const params = new URL(location.href).searchParams;

        const selectedCategories = params.get('categories') ? params.get('categories').split(',').map(category => +category).filter(category => typeof category == 'number' && !isNaN(category)) : [];
        const selectedSort = params.get('sort_by') ? params.get('sort_by') : '';
        const selectedPostTypes = params.get('post_types') ? params.get('post_types').split(',') : [];
        const selectedPostType = params.get('post_type') ? params.get('post_type').split(',') : [];
        const searchQuery = params.get('search') ? params.get('search') : '';
        const tabQuery = params.get('tab') ? params.get('tab') : '';

        return {selectedCategories, selectedSort, selectedPostTypes, searchQuery, selectedPostType, tabQuery};
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

    const fetchData = (queryNode, filtersNode, isLoad = false, eventTarget = {}) => {
        const mainForm = filtersNode.querySelector('.cpt-filters__form');
        const currentForm = queryNode.querySelector('.cpt-filters__form');
        const row = queryNode.querySelector('.row--ajax');
        const loadMoreBtn = queryNode.querySelector('.cpt-more__btn');
        const filters = filtersNode.querySelector('.cpt-filters');
        const categoriesList = filtersNode.querySelector('.cpt-filters__list');

        [row, loadMoreBtn, filters].forEach(item => item?.classList.add('loading'));

        const queryNodeIdx = queryNode.getAttribute('data-idx');

        const formData = new FormData(currentForm);
        const mainFormData = new FormData(mainForm);

        const filterPosts = [];
        for (let pair of mainFormData.entries()) {
            if (pair[0] === 'sort_by' || pair[0] === 'search') {
                formData.set(pair[0], mainFormData.get(pair[0]));
            }
            if (pair[0] === 'post_types[]') {
                filterPosts.push(pair);
            }
            if (pair[0] === 'categories[]') {
                formData.append(pair[0], pair[1]);
            }
        }

        let selectedPosts = [];

        filterPosts.forEach(post => {
            selectedPosts.push(post[1]);
        });

        formData.set('selected_posts', selectedPosts.join(','));
        formData.set('section_nr', selectedPosts.join(','));
        formData.delete("post_types[]");

        for (let post of filterPosts) {

            if (groupsData[queryNodeIdx].includes(post[1])) {
                formData.append(post[0], post[1]);
            }
        }

        if (!formData.has("post_types[]") && eventTarget.name === 'post_types[]') {
            formData.append("post_types[]", 'null');
        }


        formData.append('action', 'ajax_posts');
        currentTab ? formData.append('tab', currentTab.innerText) : null;

        if (isLoad) {
            formData.append('paged', +row.dataset.page + 1);
        }

        fetch(vars.restLoadPostsUrlAwb, {
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
        let {
            selectedCategories,
            selectedSort,
            selectedPostTypes,
            searchQuery,
            selectedPostType,
            tabQuery
        } = getSearchParams();

        const updateMultipleDataValues = (data, value) => {
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
                    case 'categories[]':
                        updateMultipleDataValues(selectedCategories, +value);
                        break;

                    case 'post_types[]':
                        updateMultipleDataValues(selectedPostTypes, value);
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
                    'categories': selectedCategories,
                    'post_types': selectedPostTypes,
                    'sort_by': selectedSort,
                    'post_type': selectedPostType,
                    'tab': tabQuery,
                };

                updateSearchParams(params);
            });
        });
    };

    const initQuery = () => {
        const queryNodes = document.querySelectorAll('.group-filters .cpt-query');
        const filtersMainNode = queryNodes[0];
        const filterForm = filtersMainNode.querySelector('.cpt-filters__form');

        if (filterForm) {
            const filtersNode = filterForm.querySelectorAll('.cpt-filters__item, .cpt-filters__categories');
            const dropdownTriggers = filterForm.querySelectorAll('.dropdown-wrapper__icon');

            toggleItemsActiveState(filtersNode);
            toggleFilterPopup(dropdownTriggers);

        }

        if (queryNodes) {

            queryNodes.forEach(queryNode => {
                const loadMore = queryNode.querySelector('.cpt-more__btn');

                if (loadMore) {
                    loadMore.addEventListener('click', () => {
                        fetchData(queryNode, filtersMainNode, true);
                    });
                }

                filterForm.addEventListener('change', event => {
                    event.preventDefault();
                    if (event.target.name === 'search') return;
                    fetchData(queryNode, filtersMainNode, false, event.target);
                });

                filterForm.addEventListener('submit', event => {
                    event.preventDefault();
                    fetchData(queryNode, filtersMainNode);
                });

            });
        }
    };

    initQuery();
}

