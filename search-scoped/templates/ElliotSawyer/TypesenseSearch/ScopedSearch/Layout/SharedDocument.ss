<div class="container">
    <div class="row m-4">
        <div class="col-12">
            <p>You are logged in as <strong>$CurrentUser.Name</strong>.
            A scoped key has been issued for your user for this page.  You are only able to search on content that you have access to.
            You can view your own documents and those that have been shared with you.
            </p>
        </div>
    </div>
    <hr />
    <div class="row m-4">
        <div class="col-12">
            <% if ViewerMembers %>
                <div class="alert alert-primary">
            <strong>Owned by: </strong>
            <% loop ViewerMembers %>
                $Surname, $FirstName
                <% if not IsLast %>
                    |
                <% end_if %>
            <% end_loop %>
                </div>
            <% end_if %>
            $Content
        </div>
    </div>
</div>
