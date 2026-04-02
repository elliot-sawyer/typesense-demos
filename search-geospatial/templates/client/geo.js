
document.addEventListener('DOMContentLoaded', () => {
    const searchPageContainer = document.querySelector("body.SearchPage");
    const serverPath = searchPageContainer.getAttribute('data-path');
    const searchCollection = searchPageContainer.getAttribute('data-collection')
    const MAX_SEARCH_RESULTS = 250;
    let TYPESENSE_CONFIG = {
        apiKey: searchPageContainer.getAttribute('data-apikey') || '',
        nodes: [{
            protocol: searchPageContainer.getAttribute('data-protocol') || '',
            host: searchPageContainer.getAttribute('data-hostname') || '',
            port: searchPageContainer.getAttribute('data-port') || '',
        }],
        connectionTimeoutSeconds: 2,
    };
    if(serverPath && serverPath != 'null') {
        TYPESENSE_CONFIG.nodes.forEach(function(obj) {
            obj.path = serverPath;
        });
    }

    // Initialize Typesense InstantSearch Adapter
    const typesenseAdapter = new TypesenseInstantSearchAdapter({
        server: TYPESENSE_CONFIG,
        additionalSearchParameters: {
            query_by: 'FullAddress,Suburb,City,Council,Region'
        }
    });

    const searchClient = typesenseAdapter.searchClient;

    // Initialize Typesense Client for direct queries
    const typesenseClient = new Typesense.Client(TYPESENSE_CONFIG);

    // Initialize InstantSearch
    const search = instantsearch({
        searchClient,
        indexName: searchCollection,
    });

    // Global variables for map and markers
    let map;
    let markersLayer;
    let allMarkers = [];
    let suburbPolylineLayer;

    // Initialize Leaflet map
    function initMap() {
        // Initialize map centered on New Zealand
        map = L.map('map').setView([-41.2865, 174.7762], 6);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Create a layer group for markers
        markersLayer = L.layerGroup().addTo(map);

        // Create a layer group for suburb polylines
        suburbPolylineLayer = L.layerGroup().addTo(map);
    }

    // Update map markers based on search results
    function updateMapMarkers(hits) {
        // Clear existing markers
        markersLayer.clearLayers();
        allMarkers = [];

        // Add markers for each hit
        hits.forEach(hit => {
            if (hit.Location && Array.isArray(hit.Location) && hit.Location.length === 2) {
                const [lat, lng] = hit.Location;

                const marker = L.circleMarker([lat, lng], {
                    radius: 8,
                    fillColor: '#4854D3',
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).bindPopup(`
                    <div class="popup-address">${hit.FullAddress}</div>
                    <div class="popup-details">
                        ${hit.Suburb}, ${hit.City}<br>
                        ${hit.Council}<br>
                        ${hit.Region}
                    </div>
                `);

                markersLayer.addLayer(marker);
                allMarkers.push(marker);
            }
        });

        // Fit map to markers if there are any
        if (allMarkers.length > 0) {
            const group = new L.featureGroup(allMarkers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // Function to fetch and display suburb polyline
    async function displaySuburbPolyline(suburbName, cityName, councilName) {
        // Clear existing polylines
        suburbPolylineLayer.clearLayers();

        if (!suburbName || !cityName || !councilName) {
            return;
        }

        try {
            // Query the suburbs collection
            const searchParameters = {
                q: '*',
                query_by: 'suburb.name',
                filter_by: `suburb.name:=${suburbName} && city.name:=${cityName} && council.name:=${councilName}`,
                per_page: 1
            };

            const results = await typesenseClient.collections('suburbs').documents().search(searchParameters);

            if (results.hits && results.hits.length > 0) {
                const encodedPolyline = results.hits[0].document.polyline;
                if(encodedPolyline && typeof encodedPolyline === 'string') {
                    const decoded = polyline.decode(encodedPolyline);
                    const polygon = L.polygon(decoded, {
                        color: '#4854D3',
                        weight: 3,
                        opacity: 1,
                        fillColor: '#4854D3',
                        fillOpacity: 0.15,
                        interactive: false
                    });
                    // Add to the polyline layer
                    suburbPolylineLayer.addLayer(polygon);
                    map.fitBounds(polygon.getBounds().pad(0.1));
                }
            }
        } catch (error) {
            console.error('Error fetching suburb polyline:', error);
        }
    }

    // Configure InstantSearch widgets
    search.addWidgets([
        // Search box
        instantsearch.widgets.searchBox({
            container: '#searchbox',
            placeholder: 'Search for addresses...',
            showSubmit: false,
            showReset: false,
            autofocus: true
        }),

        // Stats
        instantsearch.widgets.stats({
            container: '#stats',
            templates: {
                text: '{{#hasNoResults}}No results{{/hasNoResults}}{{#hasOneResult}}1 result{{/hasOneResult}}{{#hasManyResults}}{{#helpers.formatNumber}}{{nbHits}}{{/helpers.formatNumber}} results{{/hasManyResults}} found in {{processingTimeMS}}ms'
            }
        }),

        // Refinement list for Region
        instantsearch.widgets.refinementList({
            container: '#facet-region',
            attribute: 'Region',
            searchable: false,
            showMore: false,
            limit: 25,
            cssClasses: {
                count: 'mx-4 badge rounded-pill text-bg-primary'
            },
            transformItems(items) {
                const container = document.getElementById('facet-region-container');
                if (container) {
                    container.style.display = items.length > 0 ? 'block' : 'none';
                }
                return items;
            }
        }),

        // Refinement list for Council
        instantsearch.widgets.refinementList({
            container: '#facet-council',
            attribute: 'Council',
            searchable: false,
            showMore: false,
            limit: 25,
            cssClasses: {
                count: 'mx-4 badge rounded-pill text-bg-primary'
            },
            transformItems(items) {
                const container = document.getElementById('facet-council-container');
                if (container) {
                    container.style.display = items.length > 0 ? 'block' : 'none';
                }
                return items;
            }
        }),

        // Refinement list for City
        instantsearch.widgets.refinementList({
            container: '#facet-city',
            attribute: 'City',
            searchable: false,
            showMore: false,
            limit: 25,
            cssClasses: {
                count: 'mx-4 badge rounded-pill text-bg-primary'
            },
            transformItems(items) {
                const container = document.getElementById('facet-city-container');
                if (container) {
                    container.style.display = items.length > 0 ? 'block' : 'none';
                }
                return items;
            }
        }),

        // Refinement list for Suburb
        instantsearch.widgets.refinementList({
            container: '#facet-suburb',
            attribute: 'Suburb',
            searchable: false,
            showMore: false,
            limit: 25,
            cssClasses: {
                count: 'mx-4 badge rounded-pill text-bg-primary'
            },
            transformItems(items) {
                const container = document.getElementById('facet-suburb-container');
                if (container) {
                    container.style.display = items.length > 0 ? 'block' : 'none';
                }
                return items;
            }
        }),

        // Configure hits per page
        instantsearch.widgets.configure({
            hitsPerPage: MAX_SEARCH_RESULTS
        }),

        // Infinite Hits (results list with infinite scroll)
        instantsearch.widgets.infiniteHits({
            container: '#hits',
            templates: {
                item(hit) {
                    const lat = hit.Location && hit.Location[0] ? hit.Location[0] : 0;
                    const lng = hit.Location && hit.Location[1] ? hit.Location[1] : 0;
                    return `
                        <div class="hit-item" data-lat="${lat}" data-lng="${lng}">
                            <div class="address-full"><strong>${hit.FullAddress}</strong></div>
                            <div class="address-details">
                            ${hit.Region} | ${hit.Council} | ${hit.City} | ${hit.Suburb}
                            </div>
                        </div>
                    `;
                },
                empty: 'No results found',
                showMoreText: 'Load more results'
            },
            showPrevious: false,
            transformItems(items) {
                // Update map with all loaded results
                updateMapMarkers(items);
                return items;
            }
        })
    ]);

    // Function to fly to a marker when clicking on a result
    function flyToMarker(lat, lng) {
        map.flyTo([lat, lng], 15);

        // Find and open the popup for this marker
        allMarkers.forEach(marker => {
            const markerLatLng = marker.getLatLng();
            if (markerLatLng.lat === lat && markerLatLng.lng === lng) {
                marker.openPopup();
            }
        });
    }


    // Initialize map
    initMap();

    // Start InstantSearch
    search.start();

    // Listen for refinement changes to display suburb polyline
    search.on('render', function() {
        const renderState = search.renderState[searchCollection];

        if (renderState && renderState.refinementList) {
            const suburbRefinements = renderState.refinementList.Suburb;
            const cityRefinements = renderState.refinementList.City;
            const councilRefinements = renderState.refinementList.Council;

            // Check if exactly one suburb is selected
            if (suburbRefinements && suburbRefinements.items) {
                const selectedSuburbs = suburbRefinements.items.filter(item => item.isRefined);

                if (selectedSuburbs.length === 1) {
                    const suburbName = selectedSuburbs[0].value;

                    // Get selected city and council
                    let cityName = null;
                    let councilName = null;

                    if (cityRefinements && cityRefinements.items) {
                        const selectedCities = cityRefinements.items.filter(item => item.isRefined);
                        if (selectedCities.length === 1) {
                            cityName = selectedCities[0].value;
                        }
                    }

                    if (councilRefinements && councilRefinements.items) {
                        const selectedCouncils = councilRefinements.items.filter(item => item.isRefined);
                        if (selectedCouncils.length === 1) {
                            councilName = selectedCouncils[0].value;
                        }
                    }

                    // Display the suburb polyline
                    if (cityName && councilName) {
                        displaySuburbPolyline(suburbName, cityName, councilName);
                    }
                } else {
                    // Clear polyline if no suburb or multiple suburbs selected
                    suburbPolylineLayer.clearLayers();
                }
            } else {
                // Clear polyline if no suburb facet
                suburbPolylineLayer.clearLayers();
            }
        }
    });

    // Add click event delegation for hit items
    document.addEventListener('click', function(e) {
        const hitItem = e.target.closest('.hit-item');
        if (hitItem) {
            const lat = parseFloat(hitItem.getAttribute('data-lat'));
            const lng = parseFloat(hitItem.getAttribute('data-lng'));
            if (!isNaN(lat) && !isNaN(lng)) {
                flyToMarker(lat, lng);
            }
        }
    });

});
