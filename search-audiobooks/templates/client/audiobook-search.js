document.addEventListener('DOMContentLoaded', () => {
    const searchPageContainer = document.querySelector("body.SearchableAudiobookPage");
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
            query_by: 'Body',
            sort_by: 'Start:asc'
        }
    });

    // Initialize InstantSearch
    search = instantsearch({
        searchClient: typesenseInstantsearchAdapter.searchClient,
        indexName: 'Audiobooks'
    });

    // Add widgets
    search.addWidgets([
        instantsearch.widgets.searchBox({
            container: '#audiobook-searchbox',
            placeholder: 'Search audiobook for spoken words...',
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

        instantsearch.widgets.stats({
            container: '#audiobook-stats'
        }),

        instantsearch.widgets.hits({
            container: '#audiobook-hits',
            cssClasses: {
                list: 'list-unstyled'
            },
            templates: {
                item(hit) {
                    const hex = hit.Hex || '#000000';
                    return `
                        <div class="row">
                            <div class="col-sm-4">
                            <strong>${hit.Start.toString().toHHMMSS()}</strong>
                            -
                            <strong>${hit.Stop.toString().toHHMMSS()}</strong>
                            </div>
                            <blockquote class="col-sm-8 blockquote">
                                ${hit._snippetResult.Body.value}
                            </blockquote>
                        </div>
                    `;
                },
                empty(results, { html }) {
                    return html`<div class="loading">No lines found</div>`;
                }
            }
        })
    ]);

    // Start the search
    search.start();

});
