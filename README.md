# Code Camp Wellington 2026

Demos:
    * Paint your website with Resene
    * NZ Address Search
    * Scoped Search
    * Audiobook Search

## Secret sauce!

I'm using Apache for this demo project.  You can set up a reverse proxy rule to serve Typesense under the same domain and port as your webserver, i.e. https://localhost/_typesense/.  Don't forget the trailing slash!

```
# setup with SSL snakeoil, and typesense reverse proxy
<VirtualHost *:443>
  DocumentRoot /var/www/html/
  ServerName localhost
  SSLEngine on
  SSLCertificateFile /etc/apache2/ssl/server.crt
  SSLCertificateKeyFile /etc/apache2/ssl/server.key
  # --- Typesense reverse proxy ---
  ProxyPreserveHost On
  ProxyPass        /_typesense/ http://typesense:8108/
  ProxyPassReverse /_typesense/ http://typesense:8108/
</VirtualHost>

# you need proxy and proxy_http for this
a2enmod ssl rewrite headers proxy proxy_http

#make SSL certs and restart
mkdir -p /etc/apache2/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/apache2/ssl/server.key -out /etc/apache2/ssl/server.crt --batch && \
    a2ensite ssl && \
    service apache2 restart
```
