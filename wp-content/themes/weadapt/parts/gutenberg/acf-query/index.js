/* globals vars */

const fetchData = queryNode => {
	const rowNode      = queryNode.querySelector('.row--ajax');
	const loadMoreNode = queryNode.querySelector('.cpt-more__btn');
	const queryArgs    = queryNode.querySelector('input[name="query_args"]').value;

	[rowNode, loadMoreNode].forEach(item => item.classList.add('loading'));

	const formData = new FormData();

	formData.append('paged', +rowNode.dataset.paged + 1);
	formData.append('args', queryArgs);

	fetch(vars.restLoadQueryPostsUrl, {
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
					loadMoreNode.parentElement.remove();
				}
				rowNode.innerHTML += outputHtml;

				[rowNode, loadMoreNode].forEach(item => item.classList.remove('loading'));
			} catch (error) {
				// eslint-disable-next-line
				console.error('Error: ', data);

				[rowNode, loadMoreNode].forEach(item => item.classList.remove('loading'));
			}
		})
		.catch(() => {
			[rowNode, loadMoreNode].forEach(item => item.classList.remove('loading'));
		});
};


const initQueryLoadMore = () => {
	const queryNodes = document.querySelectorAll('.query__container');

	[].forEach.call(queryNodes, queryNode => {
		const loadMoreNode = queryNode.querySelector('.cpt-more__btn');

		if (loadMoreNode) {
			loadMoreNode.addEventListener('click', () => {
				fetchData(queryNode);
			});
		}
	});
};

initQueryLoadMore();