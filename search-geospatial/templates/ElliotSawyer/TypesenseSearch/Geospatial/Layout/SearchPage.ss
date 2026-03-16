    <div class="container-fluid  g-0">
        <div class="row g-0">
            <!-- Left column - Search and Results -->
            <div class="col-md-3">
            $HeroImageCSS
            <div class="jumbotron <% if HeroImage %>hero-image <% end_if %>p-0 mb-0 rounded-0" <% if HeroImage %>data-heroimage-id="$HeroImage.ID" <% end_if %>>
                <div class="container-fluid">
                    <div class="row">
                        <h1 class="display-5 fw-bold text-white mb-4">$MenuTitle</h1>
                        <p class="text-white">Explore New Zealand addresses with geospatial search powered by Typesense</p>
                        <div id="nameSearchContainer">
                            <div id="searchbox"></div>
                        </div>
                    </div>
                </div>
            </div>



                <div class="left-column-wrapper">
                    <div id="searchbox"></div>

                    <div style="margin: 1rem 0rem 1rem 0.5rem">
                        <h5>Filters</h5>

                        <div id="facet-region-container" class="mb-3">
                            <h6>Region</h6>
                            <div id="facet-region"></div>
                        </div>

                        <div id="facet-council-container" class="mb-3">
                            <h6>Council</h6>
                            <div id="facet-council"></div>
                        </div>

                        <div id="facet-city-container" class="mb-3">
                            <h6>City</h6>
                            <div id="facet-city"></div>
                        </div>

                        <div id="facet-suburb-container" class="mb-3">
                            <h6>Suburb</h6>
                            <div id="facet-suburb"></div>
                        </div>
                    </div>

                    <div id="stats"></div>

                    <div id="hits"></div>

                    <div id="pagination"></div>
                </div>
            </div>

            <!-- Right column - Map -->
            <div class="col-md-9">
                <div id="map"></div>
            </div>
        </div>
    </div>
<% require javascript('search-geospatial/templates/client/leaflet.js') %>
<% require javascript('search-geospatial/templates/client/typesense.js') %>
<% require javascript('search-geospatial/templates/client/polyline.js') %>
<% require javascript('search-geospatial/templates/client/geo.js') %>
<% require css('search-geospatial/templates/client/geo.css') %>
<% require css('search-geospatial/templates/client/leaflet.css') %>
