<% if $Link == '/home' %>
<div class="jumbotron">
    <div class="container-fluid">
<div class="row">
    <div class="col-3">
    <div class="list-group" id="list-tab" role="tablist">
        <a class="list-group-item list-group-item-action active" id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab" aria-controls="list-home">Find XKCD</a>
        <% loop Menu(1).Find('URLSegment', 'demos').Children %>
        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#list-$URLSegment" role="tab" aria-controls="list-$URLSegment">$MenuTitle</a>
        <% end_loop %>

    </div>
    </div>
    <div class="col-9">
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
            <h2><a href="https://findxkcd.com">Find XKCD</a></h2>
            The first demonstration is a website called FindXKCD. For those unaware, XKCD is an online webcomic about math, programming, and scientific in-jokes. The Typesense community created a tool for searching the entire xkcd comic library.
            </div>
            <% loop Menu(1).Find('URLSegment', 'demos').Children %>
                <div class="tab-pane fade" id="list-$URLSegment" role="tabpanel" aria-labelledby="list-$URLSegment-list">
                <h2><a href="$Link">$MenuTitle</a></h2>
                    $Content
                </div>
            <% end_loop %>
            <%-- <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
            <h2>Paint your website with Resene</h2>

            </div>
            <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">
            <h2>Geospatial search</h2>

            </div>
            <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
            <h2>Scoped search</h2>

            </div> --%>
        </div>
  </div>
</div>
    </div>

</div>
<% else  %>
<div class="container">
	<div class="row">
		<div class="col-12">
            <% if URLSegment != "home" %>
            <h1>$Title</h1>
            <% end_if %>
			$Content
		</div>
	</div>
</div>
<% end_if %>
