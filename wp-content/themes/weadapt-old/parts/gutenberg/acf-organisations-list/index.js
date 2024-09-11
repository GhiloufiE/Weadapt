/* globals vars */
const fetchOrganisationsData = (queryNode, isLoadMore = false) => {
	const rowNode      = queryNode.querySelector('.row--ajax');
	const loadMoreNode = queryNode.querySelector('.cpt-more__btn');
	const queryArgs   = queryNode.querySelector('input[name="query_args"]').value;
	const offsetOrganisation = +rowNode.dataset.offsetOrganisation;

	const formData = new FormData();
	formData.append('query_args', queryArgs);

	if (isLoadMore) {
		formData.append('offset_organisation', offsetOrganisation);
	}

	[rowNode, loadMoreNode].forEach(item => item?.classList.add('loading'));

	fetch(vars.restLoadOrganisationUrl, {
		method: 'POST',
		body: formData,
	})
		.then(response => response.text())
		.then(data => {
			try {
				const response = JSON.parse(data);

				const { outputHtml, offsetOrganisation: ajaxOffsetOrganisation, allPost } = response;

				if (isLoadMore) {
					rowNode.setAttribute('data-offset-organisation', offsetOrganisation + ajaxOffsetOrganisation);
					rowNode.innerHTML += outputHtml;
				} else {
					rowNode.setAttribute('data-offset-organisation', ajaxOffsetOrganisation);
					rowNode.innerHTML = outputHtml;
				}

				if (allPost <= +rowNode.dataset.offsetOrganisation) {
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
}

const initQueryOrganisationsLoadMore = () => {
	const queryNodes = document.querySelectorAll('.organisations-list');

	[].forEach.call(queryNodes, queryNode => {
		const loadMoreNode = queryNode.querySelector('.cpt-more__btn');

		loadMoreNode?.addEventListener('click', () => {
			fetchOrganisationsData(queryNode, true);
		});

	});
};

initQueryOrganisationsLoadMore();