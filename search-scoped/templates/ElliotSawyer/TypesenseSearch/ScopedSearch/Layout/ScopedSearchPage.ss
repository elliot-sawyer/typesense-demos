<% if CurrentUser %>
<div class="container">
    <div class="row m-4">
        <div class="col-12">
            <p>You are logged in as <strong>$CurrentUser.Name</strong>.
            A scoped key has been issued for your user for this page.  You are only able to search on content that you have access to.
            Only you can view your own documents.
            </p>
        </div>

    </div>
    <div class="row">
        <div class="col-12">
            <div id="scoped-searchbox">
            </div>
        </div>

        <div class="row my-4">
            <div class="col-3">
                <h3>Owners</h3>
                <div id="refinementList-Owners"></div>
            </div>
            <div class="col-9">
                <div id="scoped-hits">
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<% require javascript("search-scoped/templates/client/dist/scoped.js") %>
<% else  %>
<div class="alert alert-primary">You need to sign in to use this demo.  All speakers have an account.<br/>
    <ul>
        <li><strong>User:</strong>:firstname.lastname@example.com</li>
        <li><strong>Pass:</strong>:Test00!!</li>
    </ul>
</div>
<% end_if %>
