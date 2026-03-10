<div class="row m-4">
    <div class="col-10">
        You are logged in as <strong>$CurrentUser.FirstName $CurrentUser.Surname</strong>.  Only you can view your own documents.
    </div>

    <div class="col-2">
        <a class="btn btn-primary" href="/Security/logout?SecurityID=$SecurityID&BackURL=$Parent.Link">Log in as someone else</a>
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
