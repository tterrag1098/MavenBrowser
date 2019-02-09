MavenBrowser
============

A simple single-file browser for maven repositories. Will sort version folders in reverse date order, rather than alphabetically like apache/nginx autoindex. Also a bit nicer to look at. You can see an example at [my maven](https://maven.tterrag.com).

How To Use
==========

Just put `index.php` at the root of your maven structure. To work properly your http server must rewrite URLs to this file. An simple nginx example:

```nginx
server {
  root /var/www/maven; # change to match your maven root
  index index.php;

  server_name maven.tterrag.com; # change to match your URL

  location / {
    try_files $uri @router;
  }

  location @router {
    if (-d $request_filename) {
      rewrite ^(.*)$ /index.php;
    }
  }

  # Below this is just the usual way to pass to php-fpm
  location ~ \.php$ {
    try_files $uri =404;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
  }
}
```

For apache, a `.htaccess` file could be used. If you have an example of this, please submit a PR!
