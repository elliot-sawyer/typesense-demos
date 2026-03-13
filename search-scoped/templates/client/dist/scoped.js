document.addEventListener("DOMContentLoaded", function(event) {

    const searchPageContainer = document.querySelector("body.ScopedSearchPage");
    const serverPath = searchPageContainer.getAttribute('data-path');
    const searchCollection = searchPageContainer.getAttribute('data-collection')
    let TYPESENSE_CONFIG = {
        apiKey: searchPageContainer.getAttribute('data-apikey') || '',
        nodes: [{
            protocol: searchPageContainer.getAttribute('data-protocol') || '',
            host: searchPageContainer.getAttribute('data-hostname') || '',
            port: searchPageContainer.getAttribute('data-port') || '',
        }],
        connectionTimeoutSeconds: 2,
    };
    if(searchPageContainer.getAttribute('data-path') != '') {
        TYPESENSE_CONFIG.nodes.forEach(function(obj) {
            obj.path = serverPath;
        });
    }

    // Initialize Typesense InstantSearch Adapter
    const typesenseAdapter = new TypesenseInstantSearchAdapter({
        server: TYPESENSE_CONFIG,
        additionalSearchParameters: {
            query_by: 'Title,Content'
        }
    });

    const search = instantsearch({
        searchClient: typesenseAdapter.searchClient,
        indexName: searchCollection,
    });

    search.addWidgets([
        instantsearch.widgets.searchBox({
            container: '#scoped-searchbox',
            showSubmit: false,
            showReset: false,
            placeholder: 'type in a search term... ',
            autofocus: true,
            cssClasses: {
                input: 'form-control-lg w-100',
                loadingIcon: 'stroke-primary',
            },
        }),
        instantsearch.widgets.refinementList({
            container: '#refinementList-Owners',
            attribute: 'Owner.Name',
            searchable: false,
            showMore: false,
            limit: 33,
            operator: 'and',
            cssClasses: {
                searchableInput: 'form-control form-control-sm mb-2',
                searchableSubmit: 'd-none',
                searchableReset: 'd-none',
                showMore: 'btn btn-secondary btn-sm',
                list: 'list-unstyled',
                count: 'mx-2',
                label: 'd-flex align-items-center m-1',
                checkbox: 'mx-2',
            },
        }),
        instantsearch.widgets.infiniteHits({
            container: '#scoped-hits',
            cssClasses: {
                list: 'list-unstyled',
                item: 'd-flex flex-column mb-5',
                loadMore: 'btn btn-secondary d-block mt-4',
                disabledLoadMore: 'btn btn-light mx-auto d-block mt-4',
            },
            templates: {
                item(data) {
                    return `
                    <article>
                        <h2>
                            <a href="${data.Link}">${data.Title}</a>
                        </h2>
                        <section>
                            <div>
                                <strong>Owners:</strong>
                                ${data['Owner.Name'].sort().join(", ")}
                            </div>
                            <main>${data.Content.substring(0, 255)}</main>
                        </section>
                    </article>`;
                },
                empty: 'No results found. Try another search term.',
                showMoreText: 'Show more results',
            },
        }),
    ])

    search.start();
});
