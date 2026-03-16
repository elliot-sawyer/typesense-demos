$HeroImageCSS
<div class="jumbotron <% if HeroImage %>hero-image <% end_if %>p-0 mb-0 rounded-0" <% if HeroImage %>data-heroimage-id="$HeroImage.ID" <% end_if %>>
    <div class="container">
        <div class="row">
            <h1 class="display-5 fw-bold text-white mb-4">$MenuTitle</h1>
            <div class="search-modes">
                <button class="search-mode-btn active" data-mode="name">Search by Name</button>
                <button class="search-mode-btn" data-mode="hex">Find by Hex Code</button>
            </div>

            <div id="nameSearchContainer">
                <div id="searchbox"></div>
            </div>

            <div id="hexSearchContainer">
                <div class="hex-input-group">
                    <input
                        type="text"
                        id="hexInput"
                        placeholder="Enter hex code (e.g., #4854D3 or 4854D3)"
                        maxlength="7"
                        class="form-control form-control-lg my-4 mr-auto w-100"
                    >
                    <div id="hexPreview"></div>
                </div>
            </div>
        </div>
    </div>
</div>
