/* global vars, googleMapsVars, markerData, google, markerClusterer */

import Cookies from 'js-cookie';
import runPopups from '../../../../weadapt/parts/components/popup/index';

/**
 * Global Debounce
 */
let debounceTimer;

const darkStyles = [
	{ elementType: 'geometry', stylers: [{ color: '#242f3e' }] },
	{ elementType: 'labels.text.stroke', stylers: [{ color: '#242f3e' }] },
	{ elementType: 'labels.text.fill', stylers: [{ color: '#746855' }] },
	{
		featureType: 'administrative.locality',
		elementType: 'labels.text.fill',
		stylers: [{ color: '#d59563' }],
	},
	{
		featureType: 'poi',
		elementType: 'labels.text.fill',
		stylers: [{ color: '#d59563' }],
	},
	{
		featureType: 'poi.park',
		elementType: 'geometry',
		stylers: [{ color: '#263c3f' }],
	},
	{
		featureType: 'poi.park',
		elementType: 'labels.text.fill',
		stylers: [{ color: '#6b9a76' }],
	},
	{
		featureType: 'road',
		elementType: 'geometry',
		stylers: [{ color: '#38414e' }],
	},
	{
		featureType: 'road',
		elementType: 'geometry.stroke',
		stylers: [{ color: '#212a37' }],
	},
	{
		featureType: 'road',
		elementType: 'labels.text.fill',
		stylers: [{ color: '#9ca5b3' }],
	},
	{
		featureType: 'road.highway',
		elementType: 'geometry',
		stylers: [{ color: '#746855' }],
	},
	{
		featureType: 'road.highway',
		elementType: 'geometry.stroke',
		stylers: [{ color: '#1f2835' }],
	},
	{
		featureType: 'road.highway',
		elementType: 'labels.text.fill',
		stylers: [{ color: '#f3d19c' }],
	},
	{
		featureType: 'transit',
		elementType: 'geometry',
		stylers: [{ color: '#2f3948' }],
	},
	{
		featureType: 'transit.station',
		elementType: 'labels.text.fill',
		stylers: [{ color: '#d59563' }],
	},
	{
		featureType: 'water',
		elementType: 'geometry',
		stylers: [{ color: '#17263c' }],
	},
	{
		featureType: 'water',
		elementType: 'labels.text.fill',
		stylers: [{ color: '#515c6d' }],
	},
	{
		featureType: 'water',
		elementType: 'labels.text.stroke',
		stylers: [{ color: '#17263c' }],
	},
];


const initCustomSelect = function (selectWrap, updateMap) {
	const setOptionChecked = (selectedIds, selectWrap) => {
		selectWrap.querySelectorAll('select option[selected]').forEach(option => {
			option.removeAttribute('selected');
		});

		if (Array.isArray(selectedIds)) {
			selectedIds.forEach(id => {
				const option = selectWrap.querySelector(`option[value="${id}"]`);
				option.setAttribute('selected', true);
			});
		} else {
			const option = selectWrap.querySelector(`option[value="${selectedIds}"]`);
			option.setAttribute('selected', true);
		}
	};


	const select = selectWrap.querySelector('select');
	const selectOptions = selectWrap.querySelectorAll('select option');
	const customSelect = selectWrap.querySelector('.theme-select');

	const options = [];
	let selectedOptions;

	if (select.multiple) {
		selectedOptions = [];
	}

	Array.from(selectOptions).forEach(option => {
		const { text, value } = option;

		options.push({
			label: text,
			value,
		});

		if (option.hasAttribute('selected')) {
			if (select.multiple) {
				selectedOptions.push(value);
			} else {
				selectedOptions = value;
			}
		}
	});

	// eslint-disable-next-line no-undef
	new SelectPure(customSelect, {
		options,
		multiple: select.multiple,
		autocomplete: true,
		value: selectedOptions,
		onChange: values => {
			setOptionChecked(values, selectWrap);

			updateMap();
		},
	});
};


const initMap = function (mapBlockNode, mapNode, markerNodes, markerOrgNodes, markerMembersNodes, markerStakeholdersNodes, markersolutionNodes, markerCipNodes) {
	let activeMarker;

	const infoTitleNode = mapBlockNode.querySelector('.map__info__title');
	const infoLatNode = mapBlockNode.querySelector('.map__info__position__value--lat');
	const infoLngNode = mapBlockNode.querySelector('.map__info__position__value--lng');
	const contentNode = mapBlockNode.querySelector('.map__content');

	// Init Map
	const map = new google.maps.Map(mapNode, {
		zoom: parseInt(mapNode.dataset.zoom) || 3,
		minZoom: 3,
		center: new google.maps.LatLng(0, 0),
		restriction: {
			latLngBounds: { north: 85, south: -85, west: -180, east: 180 },
			strictBounds: false,
		},
	});

	// Init Bounds
	const bounds = new google.maps.LatLngBounds();
	const boundsCip = new google.maps.LatLngBounds();


	// Init Markers
	const markers = [];

	if (markerNodes) {
		markerNodes.forEach(markerNode => {
			const markerSvg = [
				'<svg width="30" height="30" viewBox="0 0 144 144" fill="none" xmlns="http://www.w3.org/2000/svg">',
				'<circle cx="72" cy="72" r="72" fill="#FF9829"/>',
				'<g clip-path="url(#clip0_64_12040)">',
				'<path d="M72 120C69.36 120 67.104 119.04 65.232 117.168C63.36 115.296 62.4 113.04 62.4 110.4H81.6C81.6 113.04 80.64 115.296 78.768 117.168C76.896 119.04 74.64 120 72 120ZM52.8 105.6V96H91.2V105.6H52.8ZM54 91.2C48.48 87.936 44.112 83.52 40.848 78C37.584 72.48 36 66.48 36 60C36 50.016 39.504 41.52 46.512 34.512C53.52 27.504 62.016 24 72 24C81.984 24 90.48 27.504 97.488 34.512C104.496 41.52 108 50.016 108 60C108 66.48 106.368 72.48 103.152 78C99.936 83.52 95.52 87.936 90 91.2H54Z" fill="#333333"/>',
				'</g>',
				'<defs>',
				'<clipPath id="clip0_64_12040">',
				'<rect width="72" height="96" fill="white" transform="translate(36 24)"/>',
				'</clipPath>',
				'</defs>',
				'</svg>',
			].join('\n');
			const markerImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(markerSvg)}`;

			const lat = parseFloat(markerNode.dataset.lat);
			const lng = parseFloat(markerNode.dataset.lng);
			const latLng = { lat, lng };
			// console.log("hhhhhhhhhhhhhhhhhhhhhhhhhhh",markerNode.dataset);

			const marker = new google.maps.Marker({
				position: latLng,
				title: markerNode.dataset.title,
				id: markerNode.dataset.id,
				icon: {
					url: markerImage,
				},
			});

			marker.addListener('click', () => {
				mapBlockNode.classList.add('has-overlay');

				// Update URL
				window.history.pushState({}, '', `${markerData.currentUrl}view/${markerNode.dataset.slug}/`);

				// Set Active Marker
				activeMarker = marker;

				// Update Marker Info
				infoTitleNode.innerHTML = markerNode.dataset.title;
				infoLatNode.innerHTML = lat;
				infoLngNode.innerHTML = lng;

				// Centered By Marker
				mapCenterByMarker(mapBlockNode, mapNode, map, marker);

				// Update Content
				contentNode.innerHTML = '';

				// Fetch Data
				const formData = new FormData();
				formData.append('post_id', markerNode.dataset.id);
				formData.append('post_type', 'case-study');
				formData.append('action', 'load_post_content');

				contentNode.classList.add('loading');

				fetch(vars.ajaxUrl, {
					method: 'POST',
					body: formData,
				})
					.then(response => response.text())
					.then(data => {
						try {
							const response = JSON.parse(data);

							if (response.output_html) {
								contentNode.innerHTML = response.output_html;

								runPopups();
							}

							contentNode.classList.remove('loading');
						} catch (error) {
							contentNode.classList.remove('loading');
						}
					})
					.catch(() => {
						contentNode.classList.remove('loading');
					});
			});

			markers.push(marker);
			bounds.extend(latLng);
		});
	}
	if (markerOrgNodes) {
		markerOrgNodes.forEach(markerNode => {
			// Create SVG marker image
			const markerSvg = [
				'<svg width="30" height="30" viewBox="0 0 144 144" fill="none" xmlns="http://www.w3.org/2000/svg">',
				'<circle cx="72" cy="72" r="72" fill="#091968"/>',
				'<g clip-path="url(#clip0_64_12054)">',
				'<path d="M24 120V45.3333H45.3333V24H98.6667V66.6667H120V120H77.3333V98.6667H66.6667V120H24ZM34.6667 109.333H45.3333V98.6667H34.6667V109.333ZM34.6667 88H45.3333V77.3333H34.6667V88ZM34.6667 66.6667H45.3333V56H34.6667V66.6667ZM56 88H66.6667V77.3333H56V88ZM56 66.6667H66.6667V56H56V66.6667ZM56 45.3333H66.6667V34.6667H56V45.3333ZM77.3333 88H88V77.3333H77.3333V88ZM77.3333 66.6667H88V56H77.3333V66.6667ZM77.3333 45.3333H88V34.6667H77.3333V45.3333ZM98.6667 109.333H109.333V98.6667H98.6667V109.333ZM98.6667 88H109.333V77.3333H98.6667V88Z" fill="white"/>',
				'</g>',
				'<defs>',
				'<clipPath id="clip0_64_12054">',
				'<rect width="96" height="96" fill="white" transform="translate(24 24)"/>',
				'</clipPath>',
				'</defs>',
				'</svg>',
			].join('\n');
			const markerImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(markerSvg)}`;
	
			// Extract latitude and longitude
			const lat = parseFloat(markerNode.dataset.lat);
			const lng = parseFloat(markerNode.dataset.lng);
			const latLng = { lat, lng };
	
			// Create Google Maps marker
			const marker = new google.maps.Marker({
				position: latLng,
				title: markerNode.dataset.title,
				id: markerNode.dataset.id,
				icon: {
					url: markerImage,
				},
			});
	
			// Add click event listener to marker
			marker.addListener('click', () => {
				mapBlockNode.classList.add('has-overlay');
	
				// Update URL
				window.history.pushState({}, '', `${markerData.currentUrl}view/organisation/${markerNode.dataset.slug}/`);
	
				// Set active marker
				activeMarker = marker;
	
				// Update marker info
				infoTitleNode.innerHTML = markerNode.dataset.title;
				infoLatNode.innerHTML = lat;
				infoLngNode.innerHTML = lng;
	
				// Center map by marker
				mapCenterByMarker(mapBlockNode, mapNode, map, marker);
	
				// Fetch additional data
				const formData = new FormData();
				formData.append('post_id', markerNode.dataset.id);
				formData.append('post_type', 'organisation');
				formData.append('action', 'load_post_content');
				contentNode.classList.add('loading');
	
				fetch(vars.ajaxUrl, {
					method: 'POST',
					body: formData,
				})
				.then(response => response.text())
				.then(data => {
					console.log('Data received:', data);
					try {
						const response = JSON.parse(data);
						console.log('Parsed response:', response);
	
						if (response.output_html) {
							contentNode.innerHTML = response.output_html;
	
							// Ensure the correct section is visible
							const sections = ['tab-about-panel', 'tab-latest-panel', 'tab-members-panel'];
							sections.forEach(sectionId => {
								const sectionNode = document.getElementById(sectionId);
								if (sectionNode) {
									if (response.active_tab === sectionId) {
										sectionNode.removeAttribute('hidden');
										sectionNode.setAttribute('aria-hidden', 'false');
									} else {
										sectionNode.setAttribute('hidden', '');
										sectionNode.setAttribute('aria-hidden', 'true');
									}
								}
							});
	
							runPopups();
	
							// Tab functionality
							const tabButtons = document.querySelectorAll('.single-tabs-nav__btn');
							const tabPanels = document.querySelectorAll('section[role="tabpanel"]');
	
							tabButtons.forEach(button => {
								button.addEventListener('click', () => {
									const targetPanelId = button.getAttribute('aria-controls');
									const targetPanel = document.getElementById(targetPanelId);
	
									// Update ARIA-selected attributes
									tabButtons.forEach(btn => {
										btn.setAttribute('aria-selected', 'false');
									});
									button.setAttribute('aria-selected', 'true');
	
									// Show/Hide tab panels
									tabPanels.forEach(panel => {
										panel.classList.remove('active');
										panel.setAttribute('aria-hidden', 'true');
										panel.setAttribute('hidden', '');
									});
									targetPanel.classList.add('active');
									targetPanel.setAttribute('aria-hidden', 'false');
									targetPanel.removeAttribute('hidden');
								});
							});
	
							// Optionally, activate the first tab by default
							if (tabButtons.length > 0) {
								tabButtons[0].click();
							}
						} else {
							console.error('No output_html in response:', response);
						}
	
						contentNode.classList.remove('loading');
					} catch (error) {
						console.error('Error parsing response:', error);
						contentNode.classList.remove('loading');
					}
				})
				.catch(error => {
					console.error('Fetch error:', error);
					contentNode.classList.remove('loading');
				});
			});
	
			markers.push(marker);
			bounds.extend(latLng);
		});
	}
	
	if (markerStakeholdersNodes) {
		markerStakeholdersNodes.forEach(markerNode => {
			const markerSvg = [
				'<svg width="30" height="30" viewBox="0 0 144 144" fill="none" xmlns="http://www.w3.org/2000/svg">',
				'<circle cx="72" cy="72" r="72" fill="#0F79D7"/>',
				'<path d="M72 72.0599C65.4 72.0599 59.76 69.7228 55.02 64.9888C50.34 60.3146 47.94 54.6217 47.94 48.03C47.94 41.4382 50.28 35.8052 55.02 31.0712C59.76 26.3371 65.4 24 72 24C78.6 24 84.24 26.3371 88.98 31.0712C93.72 35.8052 96.06 41.4382 96.06 48.03C96.06 54.6217 93.72 60.2547 88.98 64.9888C84.3 69.6629 78.6 72.0599 72 72.0599ZM24 120V103.221C24 99.8052 24.9 96.6891 26.64 93.8727C28.38 91.0562 30.72 88.839 33.6 87.3408C39.78 84.2247 46.08 81.9476 52.5 80.3895C58.92 78.8315 65.4 78.0524 72 78.0524C78.6 78.0524 85.08 78.8315 91.5 80.3895C97.92 81.9476 104.22 84.2846 110.4 87.3408C113.28 88.839 115.62 90.9963 117.36 93.8727C119.1 96.7491 120 99.8652 120 103.221V120H24Z" fill="white"/>',
				'</svg>',
			].join('\n');
			const markerImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(markerSvg)}`;

			const lat = parseFloat(markerNode.dataset.lat);
			const lng = parseFloat(markerNode.dataset.lng);
			const latLng = { lat, lng };

			const marker = new google.maps.Marker({
				position: latLng,
				title: markerNode.dataset.title,
				id: markerNode.dataset.id,
				icon: {
					url: markerImage,
				},
			});

			marker.addListener('click', () => {
				mapBlockNode.classList.add('has-overlay');

				// Update URL
				window.history.pushState({}, '', `${markerData.currentUrl}stakeholder/view/${markerNode.dataset.slug}/`);

				// Set Active Marker
				activeMarker = marker;

				// Update Marker Info
				infoTitleNode.innerHTML = markerNode.dataset.title;
				infoLatNode.innerHTML = lat;
				infoLngNode.innerHTML = lng;

				// Centered By Marker
				mapCenterByMarker(mapBlockNode, mapNode, map, marker);

				// Update Content
				contentNode.innerHTML = '';

				// Fetch Data
				const formData = new FormData();
				formData.append('post_id', markerNode.dataset.id);
				formData.append('post_type', 'stakeholders');
				formData.append('action', 'load_post_content');

				contentNode.classList.add('loading');

				fetch(vars.ajaxUrl, {
					method: 'POST',
					body: formData,
				})
					.then(response => response.text())
					.then(data => {
						try {
							const response = JSON.parse(data);

							if (response.output_html) {
								contentNode.innerHTML = response.output_html;

								runPopups();
							}

							contentNode.classList.remove('loading');
						} catch (error) {
							contentNode.classList.remove('loading');
						}
					})
					.catch(() => {
						contentNode.classList.remove('loading');
					});
			});

			markers.push(marker);
			bounds.extend(latLng);
		});
	}
	if (markerMembersNodes) {
		markerMembersNodes.forEach(markerNode => {
			const markerSvg = [
				'<svg width="30" height="30" viewBox="0 0 144 144" fill="none" xmlns="http://www.w3.org/2000/svg">',
				'<circle cx="72" cy="72" r="72" fill="#EE006D"/>',
				'<g clip-path="url(#clip0_64_12033)">',
				'<path d="M52.8 100.8L72 86.16L91.2 100.8L84 77.04L103.2 63.36H79.68L72 38.4L64.32 63.36H40.8L60 77.04L52.8 100.8ZM72 120C65.376 120 59.136 118.752 53.28 116.208C47.424 113.664 42.336 110.256 38.016 105.936C33.696 101.616 30.288 96.528 27.744 90.672C25.2 84.816 23.952 78.576 23.952 71.952C23.952 65.328 25.2 59.088 27.744 53.232C30.288 47.376 33.696 42.288 38.016 37.968C42.336 33.648 47.424 30.24 53.28 27.696C59.136 25.152 65.376 23.904 72 23.904C78.624 23.904 84.864 25.152 90.72 27.696C96.576 30.24 101.664 33.648 105.984 37.968C110.304 42.288 113.712 47.376 116.256 53.232C118.8 59.088 120.048 65.328 120.048 71.952C120.048 78.576 118.8 84.816 116.256 90.672C113.712 96.528 110.304 101.616 105.984 105.936C101.664 110.256 96.576 113.664 90.72 116.208C84.864 118.752 78.624 120 72 120Z" fill="white"/>',
				'</g>',
				'<defs>',
				'<clipPath id="clip0_64_12033">',
				'<rect width="96" height="96" fill="white" transform="translate(24 24)"/>',
				'</clipPath>',
				'</defs>',
				'</svg>',
			].join('\n');
			const markerImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(markerSvg)}`;

			const lat = parseFloat(markerNode.dataset.lat);
			const lng = parseFloat(markerNode.dataset.lng);
			const latLng = { lat, lng };
			console.log("hhhhhhhhhhhhhhhhhhhhhhhhhhh",markerNode.dataset);

			const marker = new google.maps.Marker({
				position: latLng,
				title: markerNode.dataset.title,
				id: markerNode.dataset.id,
				icon: {
					url: markerImage,
				},
			});

			marker.addListener('click', () => {
				mapBlockNode.classList.add('has-overlay');

				// Update URL
				window.history.pushState({}, '', `/member/${markerNode.dataset.slug}/`);

				// Set Active Marker
				activeMarker = marker;

				// Update Marker Info
				infoTitleNode.innerHTML = markerNode.dataset.title;
				infoLatNode.innerHTML = lat;
				infoLngNode.innerHTML = lng;

				// Centered By Marker
				mapCenterByMarker(mapBlockNode, mapNode, map, marker);

				// Update Content
				contentNode.innerHTML = '';

				// Fetch Data
				const formData = new FormData();
				formData.append('post_id', markerNode.dataset.id);
				formData.append('post_type', 'members');
				formData.append('action', 'load_post_content');

				contentNode.classList.add('loading');

				fetch(vars.ajaxUrl, {
					method: 'POST',
					body: formData,
				})
					.then(response => response.text())
					.then(data => {
						try {
							const response = JSON.parse(data);
		
							if (response.output_html) {
								contentNode.innerHTML = response.output_html;
		
								// Ensure the correct section is visible
								const sections = ['tab-about-panel', 'tab-latest-panel', 'tab-members-panel'];
								sections.forEach(sectionId => {
									const sectionNode = document.getElementById(sectionId);
									if (sectionNode) {
										if (response.active_tab === sectionId) {
											sectionNode.removeAttribute('hidden');
											sectionNode.setAttribute('aria-hidden', 'false');
										} else {
											sectionNode.setAttribute('hidden', '');
											sectionNode.setAttribute('aria-hidden', 'true');
										}
									}
								});
		
								runPopups();
		
								// Tab functionality
								const tabButtons = document.querySelectorAll('.single-tabs-nav__btn');
								const tabPanels = document.querySelectorAll('section[role="tabpanel"]');
		
								tabButtons.forEach(button => {
									button.addEventListener('click', () => {
										const targetPanelId = button.getAttribute('aria-controls');
										const targetPanel = document.getElementById(targetPanelId);
		
										// Update ARIA-selected attributes
										tabButtons.forEach(btn => {
											btn.setAttribute('aria-selected', 'false');
										});
										button.setAttribute('aria-selected', 'true');
		
										// Show/Hide tab panels
										tabPanels.forEach(panel => {
											panel.classList.remove('active');
											panel.setAttribute('aria-hidden', 'true');
											panel.setAttribute('hidden', '');
										});
										targetPanel.classList.add('active');
										targetPanel.setAttribute('aria-hidden', 'false');
										targetPanel.removeAttribute('hidden');
									});
								});
		
								// Optionally, activate the first tab by default
								if (tabButtons.length > 0) {
									tabButtons[0].click();
								}
							} else {
								console.error('No output_html in response:', response);
							}
		
							contentNode.classList.remove('loading');
						} catch (error) {
							console.error('Error parsing response:', error);
							contentNode.classList.remove('loading');
						}
					})
					.catch(() => {
						contentNode.classList.remove('loading');
					});
			});

			markers.push(marker);
			bounds.extend(latLng);
		});
	}
	if (markersolutionNodes) {
		markersolutionNodes.forEach(markerNode => {
			const markerSvg = [

				,].join('\n');
			const markerImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(markerSvg)}`;

			const lat = parseFloat(markerNode.dataset.lat);
			const lng = parseFloat(markerNode.dataset.lng);
			const latLng = { lat, lng };

			const marker = new google.maps.Marker({
				position: latLng,
				title: markerNode.dataset.title,
				id: markerNode.dataset.id,
				icon: {
					url: markerImage,
				},
			});

			marker.addListener('click', () => {
				mapBlockNode.classList.add('has-overlay');

				// Update URL
				window.history.pushState({}, '', `${markerData.currentUrl}solution/view/${markerNode.dataset.slug}/`);

				// Set Active Marker
				activeMarker = marker;

				// Update Marker Info
				infoTitleNode.innerHTML = markerNode.dataset.title;
				infoLatNode.innerHTML = lat;
				infoLngNode.innerHTML = lng;

				// Centered By Marker
				mapCenterByMarker(mapBlockNode, mapNode, map, marker);

				// Update Content
				contentNode.innerHTML = '';

				// Fetch Data
				const formData = new FormData();
				formData.append('post_id', markerNode.dataset.id);
				formData.append('post_type', 'solution');
				formData.append('action', 'load_post_content');

				contentNode.classList.add('loading');

				fetch(vars.ajaxUrl, {
					method: 'POST',
					body: formData,
				})
					.then(response => response.text())
					.then(data => {
						try {
							const response = JSON.parse(data);

							if (response.output_html) {
								contentNode.innerHTML = response.output_html;

								runPopups();
							}

							contentNode.classList.remove('loading');
						} catch (error) {
							contentNode.classList.remove('loading');
						}
					})
					.catch(() => {
						contentNode.classList.remove('loading');
					});
			});

			markers.push(marker);
			bounds.extend(latLng);
		});
	}


	// Centered Map
	map.fitBounds(bounds);


	// Current Marker on Load
	if (typeof markerData.currentMarker !== 'undefined') {
		const markerNode = markers.find(marker => {
			return parseInt(marker.id) === parseInt(markerData.currentMarker);
		});

		if (markerNode) {
			setTimeout(() => {
				map.setZoom(15);
				google.maps.event.trigger(markerNode, 'click');
			}, 500);
		}
	}


	// Marker Clusterer
	const renderer = {
		render: ({ count, position }) => {
			const clustererSvg = [
				'<?xml version="1.0" encoding="UTF-8"?>',
				'<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">',
				`<circle cx="20" cy="20" r="15" fill="${googleMapsVars.markerColor}" stroke="${googleMapsVars.markerBgColor}" stroke-width="1" />`,
				'</svg>',
			].join('\n');
			const clustererImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(clustererSvg)}`;

			return new google.maps.Marker({
				icon: {
					url: clustererImage,
				},
				label: {
					text: String(count),
					color: googleMapsVars.markerBgColor,
					fontSize: '13px',
				},
				position,
				zIndex: Number(google.maps.Marker.MAX_ZINDEX) + count,
			});
		},
	};
	const markerClusters = new markerClusterer.MarkerClusterer({ map, markers, renderer });


	// Init Cip Markers
	const markersCip = [];

	if (markerCipNodes) {
		markerCipNodes.forEach(markerNode => {
			const markerSvg = [
				'<?xml version="1.0" encoding="UTF-8"?>',
				'<svg xmlns="http://www.w3.org/2000/svg" width="32" height="42" viewBox="0 0 49.3 64.001">',
				`<path d="M24.6 63C18.1 56.7 1 39.4 1 24.6a23.6 23.6 0 1 1 47.3 0c0 14.6-17 32.1-23.7 38.4Z" fill="${googleMapsVars.markerCipBgColor}" stroke="${googleMapsVars.markerCipColor}" stroke-linejoi="round" stroke-width="1"/>`,
				`<path d="M10.9,25.9h3V28h-3c-0.6,0-1.1-0.5-1.1-1.1C9.8,26.4,10.3,25.9,10.9,25.9z M35.5,25.9h3c0.6,0,1,0.5,1,1V27c0,0.6-0.4,1-1,1h-3v-2V25.9z M14.1,16.4h0.1c0.4-0.4,1-0.4,1.4,0l2.3,2.2l-1.5,1.5l-2.3-2.3C13.7,17.4,13.7,16.8,14.1,16.4z M24.7,12.2c0.6,0,1,0.4,1,1v3h-2v-3C23.7,12.6,24.1,12.2,24.7,12.2z M31.6,18.6l2.3-2.2c0.4-0.4,1.1-0.4,1.5,0s0.4,1.1,0,1.5L33.1,20l-1.5-1.5V18.6z M24.6,20.1c3.8,0,6.9,3.1,6.9,6.9S28.4,34,24.6,34s-6.9-3.1-6.9-6.9S20.8,20.1,24.6,20.1 M24.6,17.6c-5.2,0-9.4,4.2-9.4,9.4s4.2,9.4,9.4,9.4s9.4-4.2,9.4-9.4S29.8,17.6,24.6,17.6L24.6,17.6z M35.4,37.6h-0.1c-0.4,0.4-1,0.4-1.4,0l-2.3-2.2l1.5-1.5l2.3,2.3C35.8,36.6,35.8,37.2,35.4,37.6z M24.8,41.8c-0.6,0-1-0.4-1-1v-3h2v3C25.8,41.4,25.4,41.8,24.8,41.8z M17.9,35.4l-2.3,2.2c-0.4,0.4-1.1,0.4-1.5,0c-0.4-0.4-0.4-1.1,0-1.5l2.3-2.1l1.5,1.5V35.4z" fill="${googleMapsVars.markerCipColor}"/>`,
				'</svg>',
			].join('\n');
			const markerImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(markerSvg)}`;

			const lat = parseFloat(markerNode.dataset.lat);
			const lng = parseFloat(markerNode.dataset.lng);
			const latLng = { lat, lng };

			const marker = new google.maps.Marker({
				position: latLng,
				title: markerNode.dataset.title,
				id: markerNode.dataset.id,
				icon: {
					url: markerImage,
				},
			});

			marker.addListener('click', () => {
				mapBlockNode.classList.add('has-overlay', 'has-overlay--cip');

				// Update URL
				window.history.pushState({}, '', `${markerData.currentUrl}weather-station/${markerNode.dataset.id}/`);

				// Set Active Marker
				activeMarker = marker;

				// Update Marker Info
				infoTitleNode.innerHTML = markerNode.dataset.title;
				infoLatNode.innerHTML = lat;
				infoLngNode.innerHTML = lng;

				// Centered By Marker
				mapCenterByMarker(mapBlockNode, mapNode, map, marker);

				// Update Content
				contentNode.innerHTML = '';

				// Fetch Data
				const formData = new FormData();
				formData.append('cip_id', markerNode.dataset.id);
				formData.append('cip_title', markerNode.dataset.title);
				formData.append('action', 'load_cip_content');

				contentNode.classList.add('loading');

				fetch(vars.ajaxUrl, {
					method: 'POST',
					body: formData,
				})
					.then(response => response.text())
					.then(data => {
						try {
							const response = JSON.parse(data);

							if (response.output_html) {
								contentNode.innerHTML = response.output_html;

								runPopups();
							}

							contentNode.classList.remove('loading');
						} catch (error) {
							contentNode.classList.remove('loading');
						}
					})
					.catch(() => {
						contentNode.classList.remove('loading');
					});
			});

			markersCip.push(marker);
			boundsCip.extend(latLng);
		});
	}

	// map.fitBounds(boundsCip);


	// Marker Clusterer
	const rendererCip = {
		render: ({ count, position }) => {
			const clustererSvg = [
				'<?xml version="1.0" encoding="UTF-8"?>',
				'<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">',
				`<circle cx="20" cy="20" r="15" fill="${googleMapsVars.markerCipBgColor}" stroke="${googleMapsVars.markerCipColor}" stroke-width="1" />`,
				'</svg>',
			].join('\n');
			const clustererImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(clustererSvg)}`;

			return new google.maps.Marker({
				icon: {
					url: clustererImage,
				},
				label: {
					text: String(count),
					color: googleMapsVars.markerCipColor,
					fontSize: '13px',
				},
				position,
				zIndex: Number(google.maps.Marker.MAX_ZINDEX) + count,
			});
		},
	};

	const markerCipClusters = new markerClusterer.MarkerClusterer({ map, markers: [], renderer: rendererCip });


	// CIP Button
	const cipButton = mapBlockNode.querySelector('.view-cip-data');

	if (cipButton) {
		cipButton.addEventListener('click', event => {
			event.preventDefault();

			const cipButtonText = cipButton.querySelector('span');

			cipButton.classList.toggle('active');

			if (cipButton.classList.contains('active')) {
				cipButtonText.innerText = cipButton.dataset.hideText;

				markersCip.forEach(marker => {
					marker.setMap(map);
				});
				markerCipClusters.addMarkers(markersCip);
			} else {
				cipButtonText.innerText = cipButton.dataset.showText;

				markersCip.forEach(marker => {
					marker.setMap(null);
				});
				markerCipClusters.clearMarkers();
			}
		});
	}



	// Current CIP Marker on Load
	if (typeof markerData.currentCipMarker !== 'undefined') {
		const markerNode = markersCip.find(marker => {
			return parseInt(marker.id) === parseInt(markerData.currentCipMarker);
		});

		if (markerNode) {
			setTimeout(() => {
				cipButton.click();
				map.setZoom(15);
				google.maps.event.trigger(markerNode, 'click');
			}, 500);
		}
	}


	// Dark Mode
	const darkMode = Cookies.get('weadapt-dark-mode');
	const darkModeCheckbox = document.getElementById('dark-mode');

	if (darkMode !== undefined && darkMode === 'true') {
		map.setOptions({ styles: darkStyles });
	}
	if (darkModeCheckbox) {
		darkModeCheckbox.addEventListener('change', () => {
			map.setOptions({
				styles: darkModeCheckbox.checked ? darkStyles : [],
			});
		});
	}


	// On Resize
	window.addEventListener('resize', () => {
		window.clearTimeout(debounceTimer);

		debounceTimer = window.setTimeout(() => {
			if (mapBlockNode.classList.contains('has-overlay')) {
				mapCenterByMarker(mapBlockNode, mapNode, map, activeMarker);
			}
		}, 50);
	});


	// Update Map
	const updateMap = () => {
		// Reset Map View
		map.setZoom(3);
		mapBlockNode.classList.remove('has-overlay', 'has-overlay--cip');

		mapFilter(formNode, map, markers, markerClusters);
	};


	// Filters
	const selectNodes = mapBlockNode.querySelectorAll('.map__select');

	if (selectNodes) {
		selectNodes.forEach(selectNode => {
			initCustomSelect(selectNode, updateMap);
		});
	}


	// Search
	const formNode = mapBlockNode.querySelector('.map__controls');

	if (formNode) {
		formNode.addEventListener('submit', event => {
			event.preventDefault();

			updateMap();
		});

		formNode.addEventListener('reset', () => {
			if (selectNodes) {
				selectNodes.forEach(selectNode => {
					const optionAll = selectNode.querySelector('.select-pure__option[data-value="all"]');

					if (optionAll) {
						// Fix reset selectPure
						optionAll.click();
						optionAll.click();
					}
				});
			}

			// Clear Search Input
			const searchNode = mapBlockNode.querySelector('input[type="search"]');

			if (searchNode) {
				searchNode.removeAttribute('value');
			}



			setTimeout(() => {
				updateMap();
			}, 50);
		});
	}

	// $_GET variables
	const currentUrl = new URL(window.location.href);

	if (currentUrl.searchParams.has('search') || currentUrl.searchParams.has('filter')) {
		updateMap();
	}
};

// custom content filter 
/* const mapFilterTheme = function (formNode, map, markers, markerClusters) {
	const formData = new FormData(formNode);

	// Update URL
	window.history.pushState({}, '', `${markerData.currentUrl}?search=${formData.get('search')}&filter=${formData.get('theme_network')}`);

	formNode.classList.add('loading');

	let fetchUrl;
	switch (formData.get('select_content')) {
		case 'case_study':
			fetchUrl = vars.restSearchCaseStudyMarkerstUrl;
			break;
		case 'organisation':
			fetchUrl = vars.restSearchOrganisationMarkerstUrl;
			break;
		case 'members':
			fetchUrl = vars.restSearchMembersMarkerstUrl;
			break;
		case 'stakeholders':
			fetchUrl = vars.restSearchStakeholdersMarkerstUrl;
			break;
		default:
			fetchUrl = null; // Handle other cases as needed
	}

	if (fetchUrl) {
		fetch(fetchUrl, {
			method: 'POST',
			body: formData,
		})
			.then(response => response.text())
			.then(data => {
				try {
					const response = JSON.parse(data);

					if (response.ids !== undefined) {
						const filteredMarkers = markers.filter(marker => {
							return response.ids.includes(parseInt(marker.id));
						});

						// Hide All
						markers.forEach(marker => {
							marker.setMap(null);
						});
						markerClusters.clearMarkers();

						// Show Filtered
						filteredMarkers.forEach(marker => {
							marker.setMap(map);
						});
						markerClusters.addMarkers(filteredMarkers);
					}

					formNode.classList.remove('loading');
				} catch (error) {
					console.error('Error: ', error);
					formNode.classList.remove('loading');
				}
			})
			.catch(() => {
				formNode.classList.remove('loading');
			});
	} else {
		// Handle other cases or fallback
		// In this example, we're simply showing all markers if content type is not recognized
		markerClusters.addMarkers(markers);
		formNode.classList.remove('loading');
	}
};
 */
/**
 * Filtered Map Markers
 */

const buildUrlParams = (formData, params) => {
    return params
        .map(param => formData.get(param) ? `${encodeURIComponent(param)}=${encodeURIComponent(formData.get(param))}` : '')
        .filter(param => param)
        .join('&');
};


const getFetchUrl = (formData) => {
    const contentType = formData.get('select_content');
    if (contentType) {
        switch (contentType) {
            case 'case_study':
                return vars.restSearchCaseStudyMarkerstUrl;
            case 'organisation':
                return vars.restSearchOrganisationMarkerstUrl;
            case 'members':
                return vars.restSearchMembersMarkerstUrl;
            case 'solution':
                return vars.restSearchSolutionMarkerstUrl;
            case 'stakeholders':
                return vars.restSearchStakeholdersMarkerstUrl;
            default:
                return null;
        }
    }
    if (formData.get('select_country')) return vars.restSearchCountryMarkerstUrl;
    if (formData.get('theme_network')) return vars.restSearchThemeMarkerstUrl;
    if (formData.get('search')) return vars.restSearchMarkerstUrl;
    return null;
};


const mapFilter = async function (formNode, map, markers, markerClusters) {
    const formData = new FormData(formNode);

    const urlParams = buildUrlParams(formData, ['search', 'theme_network', 'select_country', 'select_content']);
    const url = `${markerData.currentUrl}${urlParams ? `?${urlParams}` : ''}`;

    // Update the browser URL without reloading the page
    window.history.pushState({}, '', url);
    formNode.classList.add('loading');

    const fetchUrl = getFetchUrl(formData);

    if (fetchUrl) {
        try {
            const response = await fetch(fetchUrl, {
			
                method: 'POST',
                body: formData,
            });
			console.log(response);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (data.ids) {
                const filteredMarkers = markers.filter(marker => data.ids.includes(parseInt(marker.id)));

                markers.forEach(marker => marker.setMap(null));
                markerClusters.clearMarkers();

                filteredMarkers.forEach(marker => marker.setMap(map));
                markerClusters.addMarkers(filteredMarkers);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            formNode.classList.remove('loading');
        }
    } else {
        markerClusters.addMarkers(markers);
        formNode.classList.remove('loading');
    }
};



/**
 * Centered Map by Marker
 */
const mapCenterByMarker = function (mapBlockNode, mapNode, map, marker) {
	const mapOverlayNode = mapBlockNode.querySelector('.map__info');

	const overlayWidth = mapOverlayNode.offsetWidth;
	const mapHeight = mapNode.offsetHeight;
	const mapWidth = mapNode.offsetWidth;

	// 190 = (1/2 .map__info__bg width) + (40px .map__info padding-top) + (1/2 .marker height)
	const offsetY = ((mapHeight / 2) - 190);

	// > Tablet
	let offsetX = 0;

	if (overlayWidth < mapWidth) {
		offsetX = ((overlayWidth - mapWidth) / 2 * -1);
	}

	if (map.getZoom() < 4) {
		map.setZoom(4);
	}
	map.setCenter(marker.getPosition());
	map.panBy(offsetX, offsetY);
};


const mapBlockNodes = document.querySelectorAll('.case-studies-map');

if (mapBlockNodes) {
	mapBlockNodes.forEach(mapBlockNode => {
		const mapNode = mapBlockNode.querySelector('.acf-map');
		const markerNodes = mapNode.querySelectorAll('.marker');
		const markerOrgNodes = mapNode.querySelectorAll('.marker-org');
		const markerMembersNodes = mapNode.querySelectorAll('.marker-members');
		const markersolutionNodes = mapNode.querySelectorAll('.marker-solution');
		const markerStakeholdersNodes = mapNode.querySelectorAll('.marker-stakeholders');
		const markerCipNodes = mapNode.querySelectorAll('.marker-cip');
		const mapOverlayNode = mapBlockNode.querySelector('.map__overlay .map__info');

		initMap(mapBlockNode, mapNode, markerNodes, markerOrgNodes, markerMembersNodes, markersolutionNodes, markerStakeholdersNodes, markerCipNodes);

		if (mapOverlayNode) {
			mapOverlayNode.addEventListener('click', () => {
				mapBlockNode.classList.remove('has-overlay', 'has-overlay--cip');
			});
		}
	});
}

document.addEventListener('DOMContentLoaded', function () {
	var select = document.querySelector('.map__select select');

	select.addEventListener('change', function () {
		var selectedOption = this.value;

		var data = new FormData();
		data.append('select_content', selectedOption);

		var xhr = new XMLHttpRequest();
		xhr.open('POST', window.location.href, true);
		xhr.onload = function () {
			if (xhr.status === 200) {
				// Remove existing markers
				var existingMarkers = document.querySelectorAll('.marker');
				existingMarkers.forEach(function (marker) {
					marker.parentNode.removeChild(marker);
				});

				var existingOrgMarkers = document.querySelectorAll('.marker-org');
				existingOrgMarkers.forEach(function (marker) {
					marker.parentNode.removeChild(marker);
				});

				// Add new markers
				var response = JSON.parse(xhr.responseText);
				if (response.organisation) {
					response.organisation.forEach(function (org) {
						var marker = createMarker(org, 'marker-org');
						document.querySelector('.acf-map').appendChild(marker);
					});
				}
				if (response.caseStudies) {
					response.caseStudies.forEach(function (caseStudy) {
						var marker = createMarker(caseStudy, 'marker');
						document.querySelector('.acf-map').appendChild(marker);
					});
				}
			}
		};
		xhr.send(data);
	});
});

function createMarker(data, markerClass) {
	var marker = document.createElement('div');
	marker.classList.add(markerClass);
	marker.setAttribute('data-id', data.id);
	marker.setAttribute('data-title', data.title);
	marker.setAttribute('data-lat', data.lat);
	marker.setAttribute('data-lng', data.lng);
	marker.setAttribute('data-slug', data.slug);
	return marker;
}


document.addEventListener('DOMContentLoaded', function () {
	const closeButtons = document.querySelectorAll('.map__info .close');
	const overlays = document.querySelectorAll('.map__overlay');

	closeButtons.forEach(closeButton => {
		closeButton.addEventListener('click', closeOverlay);
	});

	overlays.forEach(overlay => {
		overlay.addEventListener('click', function (event) {
			// Check if the click is directly on the overlay, not on its children
			if (event.target === this) {
				closeOverlay();
				event.stopPropagation(); // Stop the event from bubbling up
			}
		});
	});

	function closeOverlay() {
		const mapBlockNode = document.querySelector('.case-studies-map');
		// Remove overlay classes
		mapBlockNode.classList.remove('has-overlay', 'has-overlay--cip');
		// Revert the URL
		const initialUrl = markerData.currentUrl;
		window.history.pushState({}, '', initialUrl);
	}
});