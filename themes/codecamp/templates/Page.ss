<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>$SiteConfig.Title</title>
    <!-- Bootstrap CSS -->
    <% require themedCSS("dist/css/main") %>
  </head>
  <body class="$ClassName.ShortName" $DataAttributes.RAW>
    <main>
	$Layout
    </main>
    <% require themedJavascript("dist/js/main") %>

  </body>
</html>
