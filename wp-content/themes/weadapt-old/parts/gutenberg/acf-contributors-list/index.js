/* globals vars */
const fetchContributorsData = (queryNode, isLoadMore = false) => {
	const rowNode      = queryNode.querySelector('.row--ajax');
	const loadMoreNode = queryNode.querySelector('.cpt-more__btn');
	const userQueryArgs   = queryNode.querySelector('input[name="user_query_args"]').value;
	const offsetUser = +rowNode.dataset.offsetUser;

	const formData = new FormData();
	formData.append('user_query_args', userQueryArgs);


	if (isLoadMore) {
		formData.append('offset_user', offsetUser);
	}

	[rowNode, loadMoreNode].forEach(item => item?.classList.add('loading'));

	fetch(vars.restLoadContributorUrl, {
		method: 'POST',
		body: formData,
	})
		.then(response => response.text())
		.then(data => {
			try {
				const response = JSON.parse(data);

				const { outputHtml, offsetUser: ajaxOffsetUser, allPost } = response;

				if (isLoadMore) {
					rowNode.setAttribute('data-offset-user', offsetUser + ajaxOffsetUser);
					rowNode.innerHTML += outputHtml;
				} else {
					rowNode.setAttribute('data-offset-user', ajaxOffsetUser);
					rowNode.innerHTML = outputHtml;
				}

				if (allPost <= +rowNode.dataset.offsetUser) {
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

const initQueryContributorsLoadMore = () => {
	const queryNodes = document.querySelectorAll('.contributors-list');

	[].forEach.call(queryNodes, queryNode => {
		const loadMoreNode = queryNode.querySelector('.cpt-more__btn');

		loadMoreNode?.addEventListener('click', () => {
			fetchContributorsData(queryNode, true);
		});
	});
};

initQueryContributorsLoadMore();