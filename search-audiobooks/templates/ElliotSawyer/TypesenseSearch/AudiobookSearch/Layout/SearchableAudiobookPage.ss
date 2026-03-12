$HeroImageCSS
<div class="jumbotron <% if HeroImage %>hero-image <% end_if %>p-0 mb-0 rounded-0" <% if HeroImage %>data-heroimage-id="$HeroImage.ID" <% end_if %>>
    <div class="container">
        <div class="row mb-4">
            <div class="col">
            <h1 class="display-5 fw-bold text-white">$MenuTitle</h1>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div id="audiobook-searchbox">
            </div>
        </div>
    </div>
</div>

<div id="audiobooksearch" class="mt-4">
    <div class="container">
        <div class="row">
            <div id="audiobook-stats"></div>
            <div id="audiobook-hits">
        </div>
    </div>
</div>
<% require javascript("search-audiobooks/templates/client/audiobook-search.js") %>
<% require css("search-audiobooks/templates/client/audiobook-search.css") %>
