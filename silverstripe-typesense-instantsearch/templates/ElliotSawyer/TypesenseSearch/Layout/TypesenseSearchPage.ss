
<% if not $CollectionName %>
    <div class="alert alert-warning">This search page has not specified a collection</div>
<% else %>
<% if getSelectedFields(Query) || $QuerySearchParameters %>
<div class="row py-3">
    <% if getSelectedFields(Sort) %>
    <div class="col-9 justify-content-md-center">
        <div id="searchbox"></div>
    </div>
    <div class="col-3">
        <div id="sort-by"></div>
    </div>
    <% else %>
    <div class="col col-md-12 justify-content-md-center">
        <div id="searchbox"></div>
    </div>
    <% end_if %>
</div>

<div class="row">
    <% if getSelectedFields(Facet) %>
    <div class="col col-md-3">
        <% loop getSelectedFields(Facet) %>
        <h3>$Field</h3>
        <div id="refinementList-$Field"></div>
        <% end_loop %>
    </div>
    <div class="col col-9">
        <div id="stats"></div>
        <div id="hits"></div>
    </div>
    <% else %>
    <div class="col col-12">
        <div id="stats"></div>
        <div id="hits"></div>
    </div>
    <% end_if %>
</div>


<div class="row">
    <div class="col col-12">
        <div id="pagination"></div>
    </div>
</div>
<% include InstantSearchJS %>
<% else %>
    <div class="alert alert-warning">No query fields have been defined</div>
<% end_if %>
<% end_if %>
