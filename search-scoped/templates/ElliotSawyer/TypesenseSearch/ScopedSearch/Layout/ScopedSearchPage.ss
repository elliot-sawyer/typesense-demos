<div class="row m-4">
    <div class="col-10">
        You are logged in as <strong>$CurrentUser.FirstName $CurrentUser.Surname</strong>.  Only you can view your own documents.
    </div>

    <div class="col-2">
        <a class="btn btn-primary" href="/Security/logout?SecurityID=$SecurityID&BackURL=$Parent.Link">Log in as someone else</a>
    </div>
</div>
<div id="scopedsearch">
    <div id="scoped-searchbox">
    </div>

    <p>A scoped key has been issued for your user for this page.  You are only able to search on content that you have access to.</p>
    <div id="scoped-hits">
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(event) {

    let body = document.querySelector("body.ScopedSearchPage"),
        apiKey = body.getAttribute('data-apiKey');
        protocol = body.getAttribute('data-protocol'),
        hostname = body.getAttribute('data-hostname'),
        port = body.getAttribute('data-port'),
        path = body.getAttribute('data-path'),
        collection = body.getAttribute('data-collection'),
        queryfields = body.getAttribute('data-queryfields');

        const typesenseInstantsearchAdapter = new TypesenseInstantSearchAdapter({
            server: {
                apiKey: apiKey,
                nodes: [
                    {
                        host: hostname,
                        port: port,
                        protocol: protocol,
                        path: path
                    },
                ],
            },
            additionalSearchParameters: {
                queryBy: queryfields
            },
        });
        const searchClient = typesenseInstantsearchAdapter.searchClient;

        const search = instantsearch({
            searchClient,
            indexName: collection,
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
            instantsearch.widgets.infiniteHits({
                container: '#scoped-hits',
                cssClasses: {
                list: 'list-unstyled',
                item: 'd-flex flex-column mb-5',
                loadMore: 'btn btn-secondary d-block mt-4',
                disabledLoadMore: 'btn btn-light mx-auto d-block mt-4',
                },
                templates: {
                    item(data, { html, components }) {
                    return `
                        <article>
                            <a href="${data.Link}">
                            ${data.Title}
                            </a>
                        </article>
                        `;
                    },
                    empty: 'No results found for <q>{{ query }}</q>. Try another search term.',
                    showMoreText: 'Show more results',
                },
            }),
        ])

        search.start();
});
</script>
<body class="ScopedSearchPage"
    data-protocol="https"
    data-hostname="localhost"
    data-port="443"
    data-path="/_typesense"
    data-apikey="SFRvYjQ2QUF2QlhwcXlKQjQxNDdFaHRubzVTT29mbXI1QjlzQnVVYUQ3Zz1UY1pGeyJmaWx0ZXJfYnkiOiJPd25lcklEczoxMSIsImV4cGlyZXNfYXQiOjE3NzMxMzE3MDd9"
    data-collection="SharedDocuments"
    data-queryfields="Title,Content" cz-shortcut-listen="true">
