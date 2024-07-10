/* globals vars */

const fetchData = (queryNode, isLoadMore = false) => {
	const rowNode      = queryNode.querySelector('.row--ajax');
	const loadMoreNode = queryNode.querySelector('.cpt-more__btn');
	const formNode     = queryNode.querySelector('.cpt-search-form');
	const queryArgs    = queryNode.querySelector('input[name="query_args"]').value;
	const queryType    = queryNode.querySelector('input[name="query_type"]').value;

	const formData = formNode ? new FormData(formNode) : new FormData();
	formData.append('args', queryArgs);
	formData.append('query_type', queryType);

	if (isLoadMore) {
		formData.append('paged', +rowNode.dataset.paged + 1);
	}

	[rowNode, loadMoreNode].forEach(item => item?.classList.add('loading'));


	fetch(vars.restLoadSearchPostsUrl, {
		method: 'POST',
		body: formData,
	})
		.then(response => response.text())
		.then(data => {
			try {
				const response = JSON.parse(data);

				const { paged, pages, output_html: outputHtml } = response;

				rowNode.setAttribute('data-paged', paged);
				rowNode.setAttribute('data-pages', pages);

				if (paged >= pages) {
					loadMoreNode?.parentElement.classList.add('hidden');
				} else {
					loadMoreNode?.parentElement.classList.remove('hidden');
				}

				if (isLoadMore) {
					rowNode.innerHTML += outputHtml;
				} else {
					rowNode.innerHTML = outputHtml;
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
	const queryNodes = document.querySelectorAll('.cpt-search-query');

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