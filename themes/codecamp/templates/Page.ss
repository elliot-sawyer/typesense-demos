<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>$SiteConfig.Title</title>
    <!-- Bootstrap CSS -->
    $SiteConfig.BootswatchTheme
    <% require themedCSS("dist/css/codecamp") %>
  </head>
  <body class="$ClassName.ShortName" $DataAttributes.RAW>
    <% include Navbar %>
    <main>
	$Layout
    </main>
    <% require themedJavascript("dist/js/codecamp") %>

  </body>
</html>
