<script>
    let prepopulatedFacets = [$JSPreselectedFacets.RAW];
    <% if $SearchAPIKey && $Host && $Port && $Protocol && $QuerySearchParameters && $CollectionName %>
    const typesenseInstantsearchAdapter = new TypesenseInstantSearchAdapter({
        server: {
            apiKey: "{$SearchAPIKey}", // Be sure to use an API key that only allows searches, in production
            nodes: [
                {
                    host: '$Host',
                    port: '$Port',
                    protocol: '$Protocol',
                },
            ],
        },
        // The following parameters are directly passed to Typesense's search API endpoint.
        //  So you can pass any parameters supported by the search endpoint below.
        //  queryBy is required.
        //  filterBy is managed and overridden by InstantSearch.js. To set it, you want to use one of the filter widgets like refinementList or use the `configure` widget.
        additionalSearchParameters: {
            queryBy: '$QuerySearchParameters'
        },
    });
    const searchClient = typesenseInstantsearchAdapter.searchClient;

    const search = instantsearch({
        searchClient,
        indexName: '$CollectionName',
    });
    <% else %>
        alert('search api key, host, post, protocol, queryfields, or collection name missing');
    <% end_if %>

// TODO: Hit count
//    async function getIndexSize() {
//        let results = await searchClient
//            .search({ q: '*' });
//
//        return results['found'];
//    }
//
//    let indexSize;
//
//    (async () => {
//        indexSize = await getIndexSize();
//    })();

    search.addWidgets([
        instantsearch.widgets.configure({
            facetFilters: prepopulatedFacets
        }),
        instantsearch.widgets.searchBox({
            container: '#searchbox',
            showSubmit: false,
            showReset: false,
            placeholder: 'type in a search term... ',
            autofocus: true,
            cssClasses: {
                input: 'form-control-lg w-100',
                loadingIcon: 'stroke-primary',
            },
//            queryHook(query, search) {
//                const modifiedQuery = queryWithoutStopWords(query);
//                search(modifiedQuery);
//            },
        }),
        instantsearch.widgets.infiniteHits({
            container: '#hits',
            cssClasses: {
              list: 'list-unstyled',
              item: 'd-flex flex-column mb-5',
              loadMore: 'btn btn-secondary d-block mt-4',
              disabledLoadMore: 'btn btn-light mx-auto d-block mt-4',
            },
            templates: {
                item(data, { html, components }) {
                  return `
<% include InstantSearchJS_results %>
                      `;
                },
                empty: 'No results found for <q>{{ query }}</q>. Try another search term.',
                showMoreText: 'Show more results',
            },
        }),

        <% if getSelectedFields(Facet) %>
        <% loop getSelectedFields(Facet) %>
        instantsearch.widgets.refinementList({
            container: '#refinementList-$Field',
            attribute: '$Field',
            searchable: true,
            searchablePlaceholder: 'Search {$Field.LowerCase}',
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
        <% end_loop %>
        <% end_if %>

        instantsearch.widgets.stats({
            container: '#stats',
            templates: {
                text: ({ nbHits, hasNoResults, hasOneResult, processingTimeMS }) => {
                    let statsText = '';
                    if (hasNoResults) {
                        statsText = 'no results';
                    } else if (hasOneResult) {
                        statsText = '1 result';
                    } else {
                        statsText = `${nbHits.toLocaleString()} results`;
                    }
                    return `<% include InstantSearchJS_stats %>`;
                },
            },
            cssClasses: {
                text: 'text-muted',
            },
        }),
        <% if getSelectedFields(Sort) %>
        instantsearch.widgets.sortBy({
            container: '#sort-by',
            items: [
              {label: 'relevancy', value: `$CollectionName/sort/_text_match(buckets: 10):desc,LastEdited:desc`,},
<% loop getSelectedFields(Sort) %>
              {label: '$Field asc', value: `$Top.CollectionName/sort/$Field:asc`},
              {label: '$Field desc', value: `$Top.CollectionName/sort/$Field:desc`},
<% end_loop %>
              {label: 'recent first',value: `$Top.CollectionName/sort/LastEdited:desc`},
              {label: 'oldest first', value: `$Top.CollectionName/sort/LastEdited:asc`},

            ],
            cssClasses: {
              select: 'form-select form-select-lg',
            },
        })
        <% end_if %>
    ]);
    search.start();
</script>
