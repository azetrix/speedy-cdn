# Speedy CDN
**_the cdn without any fuss._**

Speedy is a self-hosted CDN web application.

Stop worrying about your website not being able to handle thousands of concurrent connections because Speedy can free most of it for you.

## Installation
```console
$ cd /var/www
$ git clone https://github.com/azetrix/speedy-cdn.git
```
#### NGINX server block
```nginx
    [...]

    root /var/www/speedy/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php;
    }

    location ~ ^/index\.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.2-fpm.sock;
    }

    [...]
```

## Usage
1. Rename `config.sample.php` to `config.php`
2. Edit `config.php` to your heart's desire
3. Deploy!

## CDN Structure
```
https://cdn.speedy.com/assets/img/logo.png  ->  https://www.origin.com/assets/img/logo.png
```

## Supported MIME Types
```php
const ALLOWED_CONTENT_TYPES = [
        '/^image\/(?:(?:(?:x-(?:citrix-)?)?png)|(?:x-(?:citrix-)?|p)?jpeg|gif|x-icon|bmp|psd|svg\+xml|webp)/i',
        '/^text\/(?:css|plain|yaml|xml|xsl)/i',
        '/^application\/(?:javascript|json|(?:rss\+|xhtml\+|atom\+)?xml)/i'
    ];
```
