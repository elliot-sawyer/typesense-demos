<% if CurrentUser %>
<div class="container">
    <div class="row m-4">
        <div class="col-10">
            You are logged in as <strong>$CurrentUser.FirstName $CurrentUser.Surname</strong>.  Only you can view your own documents.
        </div>

        <div class="col-2">
            <a class="btn btn-primary" href="/Security/logout?SecurityID=$SecurityID&BackURL=$Parent.Link">Log in as someone else</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div id="scoped-searchbox">
            </div>
        </div>

        <div class="row">
            <div class="col-3">
                <h3>Owners</h3>
                <div id="refinementList-Owners"></div>
            </div>
            <div class="col-9">
                <p>A scoped key has been issued for your user for this page.  You are only able to search on content that you have access to.</p>
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
