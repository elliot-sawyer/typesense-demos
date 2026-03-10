document.addEventListener('DOMContentLoaded', () => {
    const searchPageContainer = document.querySelector("body.SearchPage");
    const serverPath = searchPageContainer.getAttribute('data-path');
    const searchCollection = searchPageContainer.getAttribute('data-collection')
    let TYPESENSE_CONFIG = {
        apiKey: searchPageContainer.getAttribute('data-apikey') || '',
        nodes: [{
            protocol: searchPageContainer.getAttribute('data-protocol') || '',
            host: searchPageContainer.getAttribute('data-hostname') || '',
            port: searchPageContainer.getAttribute('data-port') || '',
        }],
        connectionTimeoutSeconds: 2
    };

    if(searchPageContainer.getAttribute('data-path')!= '') {
        TYPESENSE_CONFIG.nodes.forEach(function(obj) {
            obj.path = serverPath;
        });
    }

    let currentMode = 'name';
    let search;

    // Initialize Typesense adapter
    const typesenseInstantsearchAdapter = new TypesenseInstantSearchAdapter({
        server: TYPESENSE_CONFIG,
        additionalSearchParameters: {
            query_by: 'Name,Code',
            exclude_fields: 'rgb_vector'
        }
    });

    // Initialize InstantSearch
    search = instantsearch({
        searchClient: typesenseInstantsearchAdapter.searchClient,
        indexName: 'Colours'
    });

    // Add widgets
    search.addWidgets([
        instantsearch.widgets.searchBox({
            container: '#searchbox',
            placeholder: 'Search colours by name...',
            showReset: false,
            showSubmit: false,
            cssClasses: {
                input: 'form-control form-control-lg my-4 mr-auto w-100',
                loadingIcon: 'stroke-primary',
            },
        }),

        instantsearch.widgets.configure({
            hitsPerPage: 25
        }),
        instantsearch.widgets.refinementList({
            container: '#refinementList-Charts',
            attribute: 'Charts',
            searchable: true,
            searchablePlaceholder: 'Search charts',
            showMore: true,
            limit: 10,
            showMoreLimit: 100,
            operator: 'and',
            cssClasses: {
                searchableInput: 'form-control form-control-sm mb-2',
                searchableSubmit: 'd-none',
                searchableReset: 'd-none',
                showMore: 'btn btn-secondary btn-sm',
                list: 'list-unstyled',
                count: 'badge ml-2',
                label: 'd-flex align-items-center mb-1',
                checkbox: 'mr-2',
            },
        }),

        instantsearch.widgets.refinementList({
            container: '#refinementList-Category',
            attribute: 'Category',
            searchable: true,
            searchablePlaceholder: 'Search category',
            showMore: true,
            limit: 10,
            showMoreLimit: 100,
            operator: 'and',
            cssClasses: {
                searchableInput: 'form-control form-control-sm mb-2',
                searchableSubmit: 'd-none',
                searchableReset: 'd-none',
                showMore: 'btn btn-secondary btn-sm',
                list: 'list-unstyled',
                count: 'badge ml-2',
                label: 'd-flex align-items-center mb-1',
                checkbox: 'mr-2',
            },
        }),

        instantsearch.widgets.stats({
            container: '#stats'
        }),

        instantsearch.widgets.hits({
            container: '#hits',
            templates: {
                item(hit, { html, components }) {
                    const hex = hit.Hex || '#000000';
                    return html`
                        <div class="colour-hit">
                            <div class="colour-swatch" style="background-color: ${hex}"></div>
                            <div class="colour-info">
                                <div class="colour-name">${hit.Name}</div>
                                <div class="colour-code">Code: ${hit.Code}</div>
                                <div class="colour-hex">Hex: ${hex} | RGB: ${hit.Red}, ${hit.Green}, ${hit.Blue}</div>
                            </div>
                        </div>
                    `;
                },
                empty(results, { html }) {
                    return html`<div class="loading">No colours found</div>`;
                }
            }
        })
    ]);

    // Start the search
    search.start();

    // Mode switching
    document.querySelectorAll('.search-mode-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const mode = btn.dataset.mode;
            currentMode = mode;

            // Update active button
            document.querySelectorAll('.search-mode-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Toggle containers
            if (mode === 'name') {
                document.getElementById('nameSearchContainer').style.display = 'block';
                document.getElementById('hexSearchContainer').classList.remove('active');
                document.getElementById('instantsearchResults').classList.remove('hidden');
                document.getElementById('vectorResults').classList.remove('active');
                search.helper.setQuery('').search();
            } else {
                document.getElementById('nameSearchContainer').style.display = 'none';
                document.getElementById('hexSearchContainer').classList.add('active');
                document.getElementById('instantsearchResults').classList.add('hidden');
                document.getElementById('hexInput').focus();
                // Show all colours initially
                performVectorSearch('FFFFFF');
            }
        });
    });

    // Hex code search functions
    const hexInput = document.getElementById('hexInput');
    const hexPreview = document.getElementById('hexPreview');

    function hexToRgb(hex) {
        // Remove # if present
        hex = hex.replace(/^#/, '');

        // Validate hex
        if (!/^[0-9A-Fa-f]{6}$/.test(hex)) {
            return null;
        }

        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);

        return { r, g, b };
    }

    function rgbToVector(r, g, b) {
        return [
            (r / 255).toFixed(6),
            (g / 255).toFixed(6),
            (b / 255).toFixed(6)
        ];
    }

    async function performVectorSearch(hex) {
        const rgb = hexToRgb(hex);
        if (!rgb) {
            return;
        }
        const vector = rgbToVector(rgb.r, rgb.g, rgb.b);
        const vectorQuery = `rgb_vector:([${vector.join(',')}], k:10)`;

        // Update preview
        hexPreview.style.backgroundColor = `#${hex.replace(/^#/, '')}`;

        // Show vector results container
        document.getElementById('vectorResults').classList.add('active');

        // Perform direct Typesense API call for vector search
        try {
            let typesenseServer = TYPESENSE_CONFIG.nodes[0],
                typesenseUri = typesenseServer.protocol+'://'
                    +typesenseServer.host
                    +(typesenseServer.port != '' ? (':'+typesenseServer.port) : '')
                    +(typesenseServer.path != '' ? (typesenseServer.path) : '');
            const response = await fetch(
                `${typesenseUri}/collections/${searchCollection}/documents/search?q=*&vector_query=${encodeURIComponent(vectorQuery)}&exclude_fields=rgb_vector&sort_by=Blue:desc`,
                {
                    headers: {
                        'X-TYPESENSE-API-KEY': TYPESENSE_CONFIG.apiKey
                    }
                }
            );

            const data = await response.json();

            // Update stats
            document.getElementById('vectorStats').innerHTML =
                `<div class="ais-Stats"><span class="ais-Stats-text">${data.found} results found for hex #${hex.replace(/^#/, '').toUpperCase()}</span></div>`;

            // Render results
            const hitsHTML = data.hits.map(hit => {
                const doc = hit.document;
                const hexColor = doc.Hex || '#000000';
                const euDistance = 1-(Math.sqrt(
                    +Math.pow((rgb.r-doc.Red), 2)
                    +Math.pow((rgb.g-doc.Green), 2)
                    +Math.pow((rgb.b-doc.Blue), 2)
                )/255);
                return `
                    <div class="colour-hit">
                        <div class="colour-swatch" style="background-color: ${hexColor}"></div>
                        <div class="colour-info">
                            <div class="colour-name">${doc.Name}</div>
                            <div class="colour-code">Code: ${doc.Code}</div>
                            <div class="colour-hex">Hex: ${hexColor} | RGB: ${doc.Red}, ${doc.Green}, ${doc.Blue} | ${euDistance}</div>
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById('vectorHits').innerHTML = hitsHTML || '<div class="loading">No colours found</div>';

        } catch (error) {
            console.error('Vector search error:', error);
            document.getElementById('vectorHits').innerHTML =
                '<div class="error">Error performing search: ' + error.message + '</div>';
        }
    }

    let debounceTimer;
    hexInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        const value = e.target.value;

        // Update preview immediately
        const rgb = hexToRgb(value);
        if (rgb) {
            hexPreview.style.backgroundColor = `#${value.replace(/^#/, '')}`;
        }

        debounceTimer = setTimeout(() => {
            if (value.length >= 6) {
                performVectorSearch(value);
            }
        }, 300);
    });

    hexInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            clearTimeout(debounceTimer);
            performVectorSearch(e.target.value);
        }
    });
});
