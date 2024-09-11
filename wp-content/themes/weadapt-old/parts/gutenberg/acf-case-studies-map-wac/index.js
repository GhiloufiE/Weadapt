/* global vars, googleMapsVarsWac, markerData, google, markerClusterer */

import Cookies from 'js-cookie';
import runPopups from '../../components/popup/index';

/**
 * Global Debounce
 */
let debounceTimer;

const darkStyles = [
    {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
    {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
    {elementType: 'labels.text.fill', stylers: [{color: '#746855'}]},
    {
        featureType: 'administrative.locality',
        elementType: 'labels.text.fill',
        stylers: [{color: '#d59563'}],
    },
    {
        featureType: 'poi',
        elementType: 'labels.text.fill',
        stylers: [{color: '#d59563'}],
    },
    {
        featureType: 'poi.park',
        elementType: 'geometry',
        stylers: [{color: '#263c3f'}],
    },
    {
        featureType: 'poi.park',
        elementType: 'labels.text.fill',
        stylers: [{color: '#6b9a76'}],
    },
    {
        featureType: 'road',
        elementType: 'geometry',
        stylers: [{color: '#38414e'}],
    },
    {
        featureType: 'road',
        elementType: 'geometry.stroke',
        stylers: [{color: '#212a37'}],
    },
    {
        featureType: 'road',
        elementType: 'labels.text.fill',
        stylers: [{color: '#9ca5b3'}],
    },
    {
        featureType: 'road.highway',
        elementType: 'geometry',
        stylers: [{color: '#746855'}],
    },
    {
        featureType: 'road.highway',
        elementType: 'geometry.stroke',
        stylers: [{color: '#1f2835'}],
    },
    {
        featureType: 'road.highway',
        elementType: 'labels.text.fill',
        stylers: [{color: '#f3d19c'}],
    },
    {
        featureType: 'transit',
        elementType: 'geometry',
        stylers: [{color: '#2f3948'}],
    },
    {
        featureType: 'transit.station',
        elementType: 'labels.text.fill',
        stylers: [{color: '#d59563'}],
    },
    {
        featureType: 'water',
        elementType: 'geometry',
        stylers: [{color: '#17263c'}],
    },
    {
        featureType: 'water',
        elementType: 'labels.text.fill',
        stylers: [{color: '#515c6d'}],
    },
    {
        featureType: 'water',
        elementType: 'labels.text.stroke',
        stylers: [{color: '#17263c'}],
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
        const {text, value} = option;

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


const initMap = function (mapBlockNode, mapNode, markerNodes, markerCipNodes) {
    let activeMarker;

    const infoTitleNode = mapBlockNode.querySelector('.map__info__title');
    const infoLatNode = mapBlockNode.querySelector('.map__info__position__value--lat');
    const infoLngNode = mapBlockNode.querySelector('.map__info__position__value--lng');
    const contentNode = mapBlockNode.querySelector('.map__content');

    // Init Map
    const map = new google.maps.Map(mapNode, {
        zoom: parseInt(mapNode.dataset.zoom) || 3,
        minZoom: 3,
        zoomControl: true,
        zoomControlOptions: {
            position: google.maps.ControlPosition.LEFT_TOP,
        },
        mapTypeControlOptions: {
            mapTypeIds: [],
        },
        streetViewControl: false,
        center: new google.maps.LatLng(0, 0),
        restriction: {
            latLngBounds: {north: 85, south: -85, west: -180, east: 180},
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
                '<?xml version="1.0" encoding="UTF-8"?>',
                '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="42" viewBox="0 0 49.3 64.001">',
                `<path d="M24.6 63C18.1 56.7 1 39.4 1 24.6a23.6 23.6 0 1 1 47.3 0c0 14.6-17 32.1-23.7 38.4Z" fill="${googleMapsVarsWac.markerBgColor}" stroke="${googleMapsVarsWac.markerColor}" stroke-linejoi="round" stroke-width="1"/>`,
                `<path d="M10.8 23.5h3v2.1h-3a1 1 0 1 1 0-2.1Zm24.6 0h3c.6 0 1 .5 1 1v.1c0 .6-.4 1-1 1h-3v-2ZM14 14h.1a1 1 0 0 1 1.4 0l2.3 2.2-1.5 1.5-2.3-2.3a1 1 0 0 1 0-1.4Zm10.6-4.2c.6 0 1 .4 1 1v3h-2v-3c0-.6.4-1 1-1Zm6.9 6.4 2.3-2.2a1 1 0 1 1 1.5 1.5L33 17.6l-1.5-1.5ZM14 33.7l2.3-2.3 1.5 1.5-2.3 2.3a1 1 0 0 1-1.4 0V35a1 1 0 0 1 0-1.4Zm17.5-.8 1.5-1.5 2.2 2.3c.4.4.4 1 0 1.4a1 1 0 0 1-1.4 0L31.5 33Zm-11.6 6.4c0 2.8 2.5 5 5.4 4.6 2.3-.3 4-2.4 4-4.8v-3H20v3.2Zm10-4.1H19.3v-1.5a3 3 0 0 0-1-2.2l-.8-.7a9.3 9.3 0 0 1 1-13.5c3.8-3 9.4-2.7 12.7.7a9.3 9.3 0 0 1-.4 13.5c-.7.6-1 1.5-1 2.4v1.3Zm-8-2.5h5.5c.3-1.2.9-2.2 1.8-3a6.8 6.8 0 0 0 .3-10 6.9 6.9 0 0 0-9.2-.5 6.8 6.8 0 0 0-.8 9.9l.5.5c1 .9 1.6 2 1.8 3Z" fill="${googleMapsVarsWac.markerColor}"/>`,
                '</svg>',
            ].join('\n');
            const markerImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(markerSvg)}`;

            const lat = parseFloat(markerNode.dataset.lat);
            const lng = parseFloat(markerNode.dataset.lng);
            const latLng = {lat, lng};

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
        render: ({count, position}) => {
            const clustererSvg = [
                '<?xml version="1.0" encoding="UTF-8"?>',
                '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">',
                `<circle cx="20" cy="20" r="15" fill="${googleMapsVarsWac.markerColor}" stroke="${googleMapsVarsWac.markerBgColor}" stroke-width="1" />`,
                '</svg>',
            ].join('\n');
            const clustererImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(clustererSvg)}`;

            return new google.maps.Marker({
                icon: {
                    url: clustererImage,
                },
                label: {
                    text: String(count),
                    color: googleMapsVarsWac.markerBgColor,
                    fontSize: '13px',
                },
                position,
                zIndex: Number(google.maps.Marker.MAX_ZINDEX) + count,
            });
        },
    };
    const markerClusters = new markerClusterer.MarkerClusterer({map, markers, renderer});


    // Init Cip Markers
    const markersCip = [];

    if (markerCipNodes) {
        markerCipNodes.forEach(markerNode => {
            const markerSvg = [
                '<?xml version="1.0" encoding="UTF-8"?>',
                '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="42" viewBox="0 0 49.3 64.001">',
                `<path d="M24.6 63C18.1 56.7 1 39.4 1 24.6a23.6 23.6 0 1 1 47.3 0c0 14.6-17 32.1-23.7 38.4Z" fill="${googleMapsVarsWac.markerCipBgColor}" stroke="${googleMapsVarsWac.markerCipColor}" stroke-linejoi="round" stroke-width="1"/>`,
                `<path d="M10.9,25.9h3V28h-3c-0.6,0-1.1-0.5-1.1-1.1C9.8,26.4,10.3,25.9,10.9,25.9z M35.5,25.9h3c0.6,0,1,0.5,1,1V27c0,0.6-0.4,1-1,1h-3v-2V25.9z M14.1,16.4h0.1c0.4-0.4,1-0.4,1.4,0l2.3,2.2l-1.5,1.5l-2.3-2.3C13.7,17.4,13.7,16.8,14.1,16.4z M24.7,12.2c0.6,0,1,0.4,1,1v3h-2v-3C23.7,12.6,24.1,12.2,24.7,12.2z M31.6,18.6l2.3-2.2c0.4-0.4,1.1-0.4,1.5,0s0.4,1.1,0,1.5L33.1,20l-1.5-1.5V18.6z M24.6,20.1c3.8,0,6.9,3.1,6.9,6.9S28.4,34,24.6,34s-6.9-3.1-6.9-6.9S20.8,20.1,24.6,20.1 M24.6,17.6c-5.2,0-9.4,4.2-9.4,9.4s4.2,9.4,9.4,9.4s9.4-4.2,9.4-9.4S29.8,17.6,24.6,17.6L24.6,17.6z M35.4,37.6h-0.1c-0.4,0.4-1,0.4-1.4,0l-2.3-2.2l1.5-1.5l2.3,2.3C35.8,36.6,35.8,37.2,35.4,37.6z M24.8,41.8c-0.6,0-1-0.4-1-1v-3h2v3C25.8,41.4,25.4,41.8,24.8,41.8z M17.9,35.4l-2.3,2.2c-0.4,0.4-1.1,0.4-1.5,0c-0.4-0.4-0.4-1.1,0-1.5l2.3-2.1l1.5,1.5V35.4z" fill="${googleMapsVarsWac.markerCipColor}"/>`,
                '</svg>',
            ].join('\n');
            const markerImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(markerSvg)}`;

            const lat = parseFloat(markerNode.dataset.lat);
            const lng = parseFloat(markerNode.dataset.lng);
            const latLng = {lat, lng};

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

    //map.fitBounds(boundsCip);


    // Marker Clusterer
    const rendererCip = {
        render: ({count, position}) => {
            const clustererSvg = [
                '<?xml version="1.0" encoding="UTF-8"?>',
                '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">',
                `<circle cx="20" cy="20" r="15" fill="${googleMapsVarsWac.markerCipBgColor}" stroke="${googleMapsVarsWac.markerCipColor}" stroke-width="1" />`,
                '</svg>',
            ].join('\n');
            const clustererImage = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(clustererSvg)}`;

            return new google.maps.Marker({
                icon: {
                    url: clustererImage,
                },
                label: {
                    text: String(count),
                    color: googleMapsVarsWac.markerCipColor,
                    fontSize: '13px',
                },
                position,
                zIndex: Number(google.maps.Marker.MAX_ZINDEX) + count,
            });
        },
    };

    const markerCipClusters = new markerClusterer.MarkerClusterer({map, markers: [], renderer: rendererCip});


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
        map.setOptions({styles: darkStyles});
    }
    darkModeCheckbox.addEventListener('change', () => {
        map.setOptions({
            styles: darkModeCheckbox.checked ? darkStyles : [],
        });
    });


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
                    const opationAll = selectNode.querySelector('.select-pure__option[data-value="all"]');

                    if (opationAll) {
                        // Fix reset selectPure
                        opationAll.click();
                        opationAll.click();
                    }
                });
            }
            setTimeout(() => {
                updateMap();
            });
        });
    }
};


/**
 * Filtered Map Markers
 */
const mapFilter = function (formNode, map, markers, markerClusters) {
    const formData = new FormData(formNode);

    formNode.classList.add('loading');

    fetch(vars.restSearchCaseStudyMarkerstUrl, {
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
                // eslint-disable-next-line
                console.error('Error: ', data);

                formNode.classList.remove('loading');
            }
        })
        .catch(() => {
            formNode.classList.remove('loading');
        });
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


const mapBlockNodes = document.querySelectorAll('.case-studies-map-wac');


if (mapBlockNodes) {
    mapBlockNodes.forEach(mapBlockNode => {
        const mapNode = mapBlockNode.querySelector('.acf-map');
        const markerNodes = mapNode.querySelectorAll('.marker');
        const markerCipNodes = mapNode.querySelectorAll('.marker-cip');
        const mapOverlayNode = mapBlockNode.querySelector('.map__overlay .map__info');
        const viewAsListButton = mapBlockNode.querySelector('button.show-list');

        viewAsListButton.addEventListener('click', () => {
            mapBlockNode.classList.toggle('list');
            if (mapBlockNode.classList.contains('list')) {
                viewAsListButton.querySelector('span').innerText = 'View as a Map';
            } else {
                viewAsListButton.querySelector('span').innerText = 'View as a List';

            }
        });

        initMap(mapBlockNode, mapNode, markerNodes, markerCipNodes);

        if (mapOverlayNode) {
            mapOverlayNode.addEventListener('click', () => {
                mapBlockNode.classList.remove('has-overlay', 'has-overlay--cip');
            });
        }
    });
}
