<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">$SiteConfig.Title</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <%-- intentionally blank --%>
                </li>
            </ul>
            <div class="d-flex">
                <div id="federated-search"></div>
                <% if CurrentUser %>
                <a role="button" class="btn btn-warning" href="/Security/logout?SecurityID=$SecurityID&BackURL=$Link">Logout</a>
                <% else %>
                <a role="button" class="btn btn-primary" href="/Security/login?SecurityID=$SecurityID&BackURL=$Link">Sign in</a>
                <% end_if %>
            </div>
        </div>
    </div>
</nav>
