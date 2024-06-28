/* globals vars */
const fetchResourcesData = (queryNode, isLoadMore = false) => {
	const rowNode      = queryNode.querySelector('.row--ajax');
	const loadMoreNode = queryNode.querySelector('.cpt-more__btn');
	const queryArgs   = queryNode.querySelector('input[name="query_args"]').value;
	const offsetResources = +rowNode.dataset.offsetResource;

	const formData = new FormData();
	formData.append('query_args', queryArgs);

	if (isLoadMore) {
		formData.append('offset_resource', offsetResources);
	}

	[rowNode, loadMoreNode].forEach(item => item?.classList.add('loading'));

	fetch(vars.restLoadResourceUrl, {
		method: 'POST',
		body: formData,
	})
		.then(response => response.text())
		.then(data => {
			try {
				const response = JSON.parse(data);

				const { outputHtml, offsetResource: ajaxOffsetResource, allPost } = response;

				if (isLoadMore) {
					rowNode.setAttribute('data-offset-resource', offsetResources + ajaxOffsetResource);
					rowNode.innerHTML += outputHtml;
				} else {
					rowNode.setAttribute('data-offset-resource', ajaxOffsetResource);
					rowNode.innerHTML = outputHtml;
				}

				if (allPost <= +rowNode.dataset.offsetResource) {
					console.log(loadMoreNode?.parentElement);
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

const initQueryResourcesLoadMore = () => {
	const queryNodes = document.querySelectorAll('.single-organisation__resources');
	console.log(queryNodes);
	[].forEach.call(queryNodes, queryNode => {
		const loadMoreNode = queryNode.querySelector('.cpt-more__btn');

		loadMoreNode?.addEventListener('click', () => {
			fetchResourcesData(queryNode, true);
		});

	});
};

initQueryResourcesLoadMore();
