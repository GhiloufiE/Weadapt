// Get all the necessary elements
const tabsNodes = document.querySelectorAll('.single-tabs-nav');

if (tabsNodes) {
	[].forEach.call(tabsNodes, tabsNode => {
		const tabs = tabsNode.querySelectorAll('.single-tabs-nav__btn');
		const panels = document.querySelectorAll('section[role="tabpanel"]');
		const tabsParent = tabsNode.querySelector('.single-tabs-nav .swiper');

		// Add event listeners to each tab button
		tabs.forEach(tab => {
			tab.addEventListener('click', event => {
				// Remove the 'selected' class from all tabs
				tabs.forEach(tab => {
					tab.classList.remove('selected');
					tab.setAttribute('aria-selected', false);
				});

				// Hide all panels
				panels.forEach(panel => {
					panel.hidden = true;
					panel.setAttribute('aria-hidden', true);
				});

				// Add the 'selected' class to the clicked tab
				tab.classList.add('selected');
				tab.setAttribute('aria-selected', true);

				// Show the corresponding panel
				const panelId = tab.getAttribute('aria-controls');
				const panel = document.getElementById(panelId);
				panel.hidden = false;
				panel.setAttribute('aria-hidden', false);

				// Fix if is subtab
				const panelParent = tab.closest('section[role="tabpanel"]');

				if (panelParent !== null) {
					panelParent.hidden = false;
					panelParent.setAttribute('aria-hidden', false);
				}

				// Fix if has child
				const selectedSubtab = panel.querySelector('.single-tabs-nav__btn[aria-selected="true"]');

				if (selectedSubtab) {
					selectedSubtab.classList.add('selected');
					selectedSubtab.setAttribute('aria-selected', true);

					const subtabPanelId = selectedSubtab.getAttribute('aria-controls');
					const subtabPanel   = document.getElementById(subtabPanelId);

					subtabPanel.hidden  = false;
					subtabPanel.setAttribute('aria-hidden', false);
				}

				// Set URL
				if (event.isTrusted) {
					const currentUrl = new URL(window.location.href);

					window.history.pushState(null, '', `${currentUrl.origin}${currentUrl.pathname}?tab=${panelId.replace('tab-', '').replace('-panel', '')}`);
				}
			});
		});

		if (tabsParent) {
			// eslint-disable-next-line no-undef
			new Swiper(tabsParent, {
				slidesPerView: 'auto',
				grabCursor: true,
				watchOverflow: true,
			});
		}
	});


	// $_GET variables
	const handleCurrentTabUrl = function() {
		const currentUrl = new URL(window.location.href);

		if (currentUrl.searchParams.has('tab')) {
			const tabName = currentUrl.searchParams.get('tab');
			const tabNode = document.getElementById(`tab-${tabName}`);

			if (tabNode) {
				tabNode.click();

				// Fix if has parent
				const panelParent = tabNode.closest('section[role="tabpanel"]');

				if (panelParent !== null) {
					const parentTabName = panelParent.getAttribute('id').replace('-panel', '');
					const parentTabNode = document.getElementById(parentTabName);

					if (parentTabNode) {
						parentTabNode.click();
					}
				}
			}
		}
	}

	handleCurrentTabUrl();

	window.addEventListener('popstate', handleCurrentTabUrl);
}