/* globals vars */

const fetchData = (queryNode, isLoadMore = false) => {
	const rowNode      = queryNode.querySelector('.row--ajax');
	const loadMoreNode = queryNode.querySelector('.cpt-more__btn');
	const formNode     = queryNode.querySelector('.cpt-search-form');

	const queryArgs       = queryNode.querySelector('input[name="query_args"]').value;
	const userQueryArgs   = queryNode.querySelector('input[name="user_query_args"]').value;
	const organisationIDs = queryNode.querySelector('input[name="organisation_IDs"]').value;
	const userIDs         = queryNode.querySelector('input[name="user_IDs"]').value;
	const loggedIn        = queryNode.querySelector('input[name="logged_in"]').value;

	const offsetOrganisation = +rowNode.dataset.offsetOrganisation;
	const offsetUser = +rowNode.dataset.offsetUser;

	const formData = new FormData(formNode);
	formData.append('query_args', queryArgs);
	formData.append('user_query_args', userQueryArgs);
	formData.append('organisation_IDs', organisationIDs);
	formData.append('user_IDs', userIDs);
	formData.append('logged_in', loggedIn);
	formData.append('is_load_more', isLoadMore);

	if (isLoadMore) {
		formData.append('offset_organisation', offsetOrganisation);
		formData.append('offset_user', offsetUser);
	}

	[rowNode, loadMoreNode].forEach(item => item?.classList.add('loading'));

	fetch(vars.restLoadPostUserUrl, {
		method: 'POST',
		body: formData,
	})
		.then(response => response.text())
		.then(data => {
			try {
				const response = JSON.parse(data);

				const { outputHtml, offsetOrganisation: ajaxOffsetOrganisation, offsetUser: ajaxOffsetUser, allPost } = response;

				if (isLoadMore) {
					rowNode.setAttribute('data-offset-organisation', offsetOrganisation + ajaxOffsetOrganisation);
					rowNode.setAttribute('data-offset-user', offsetUser + ajaxOffsetUser);

					rowNode.innerHTML += outputHtml;
				} else {
					rowNode.setAttribute('data-offset-organisation', ajaxOffsetOrganisation);
					rowNode.setAttribute('data-offset-user', ajaxOffsetUser);

					rowNode.innerHTML = outputHtml;
				}

				if (allPost <= +rowNode.dataset.offsetOrganisation + +rowNode.dataset.offsetUser) {
					loadMoreNode?.parentElement.classList.add('hidden');
				} else {
					loadMoreNode?.parentElement.classList.remove('hidden');
				}

				[rowNode, loadMoreNode].forEach(item => item?.classList.remove('loading'));
			} catch (error) {
				// eslint-disable-next-line
				console.error('Error: ', data);

				[rowNode, loadMoreNode].forEach(item => item?.classList.remove('loading'));
			}
		})
		.catch(() => {
			[rowNode, loadMoreNode].forEach(item => item?.classList.remove('loading'));
		});
};


const initQueryLoadMore = () => {
	const queryNodes = document.querySelectorAll('.contributors-organisations');

	[].forEach.call(queryNodes, queryNode => {
		const loadMoreNode = queryNode.querySelector('.cpt-more__btn');
		const form         = queryNode.querySelector('.cpt-search-form');

		loadMoreNode?.addEventListener('click', () => {
			fetchData(queryNode, true);
		});

		form?.addEventListener('submit', event => {
			event.preventDefault();
			fetchData(queryNode);
		});
	});
};

initQueryLoadMore();