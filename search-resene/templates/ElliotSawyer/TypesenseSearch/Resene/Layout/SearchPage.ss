<div class="resene-search-container">
<% include SearchJumbotron %>
<div class="results-container">
    <div id="instantsearchResults">
        <div id="stats"></div>
        <div class="row">
            <div class="col-md-3">
                <h3>Charts</h3>
                <div id="refinementList-Charts"></div>

                <h3>Category</h3>
                <div id="refinementList-Category"></div>
            </div>
            <div class="col-md-9">
                <div id="hits"></div>
            </div>
        </div>
    </div>
    <div id="vectorResults">
        <div id="vectorStats"></div>
        <div id="vectorHits"></div>
    </div>

</div>
</div>
<div class="container-fluid">
<div class="row my-4">
    <div class="col">
        $Breadcrumbs
    </div>
</div>
</div>
<% require javascript(https://cdn.jsdelivr.net/npm/instantsearch.js@4) %>
<% require javascript(https://cdn.jsdelivr.net/npm/typesense-instantsearch-adapter@2/dist/typesense-instantsearch-adapter.min.js) %>
<% require javascript('search-resene/templates/client/resene-search.js') %>
<% require css('search-resene/templates/client/resene-search.css') %>
