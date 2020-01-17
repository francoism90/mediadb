# MediaDB

## Build your own streaming service

### Intro

**MediaDB** is a web-based video streaming service written in Laravel and Vue.
It relies on [nginx-vod-module](https://github.com/kaltura/nginx-vod-module) for on-the-fly repackaging of MP4 files to DASH. [Encryption URL](https://github.com/kaltura/nginx-secure-token-module) and [expire tokens](https://nginx.org/en/docs/http/ngx_http_secure_link_module.html) are used to prevent unwanted access of streams.

MediaDB is very much in development and is not yet suitable for production purposes.

### Requirements

- [nginx](https://nginx.org) 1.17.7 (with `--with-http_secure_link_module`)
- [nginx-secure-token-module](https://github.com/kaltura/nginx-secure-token-module)
- [nginx-vod-module](https://github.com/kaltura/nginx-vod-module)
- [ngx_brotli](https://github.com/google/ngx_brotli)
- [Laravel](https://laravel.com/docs/6.x) development environment
- [Elasticsearch](https://www.elastic.co/products/elasticsearch)
- [Samples](https://gist.github.com/jsturgis/3b19447b304616f18657) for testing

### Install

#### nginx

See `doc/nginx` for configuration examples.

### Laravel

```bash
cd /path/to/html
composer install
php artisan key:generate
php artisan jwt:secret
php artisan storage:link
php artisan horizon:install
php artisan telescope:install
php artisan elastic:create-index "App\Support\Scout\MediaIndexConfigurator"
php artisan elastic:create-index "App\Support\Scout\UserIndexConfigurator"
php artisan elastic:update-mapping "App\Models\Media"
php artisan elastic:update-mapping "App\Models\User"
```

```bash
npm install
npm run dev
```

It is advisable to view all configuration files and change them when necessary, especially `config/vod.php` and `config/filesystems.php`.

### Production

#### Generate VOD key and IV

```bash
dd if=/dev/urandom bs=1 count=32 2> /dev/null | xxd -p -c32
dd if=/dev/urandom bs=1 count=16 2> /dev/null | xxd -p -c32
```

```env
VOD_KEY=
VOD_IV=
```

#### Set VOD secure link

```bash
"$secure_link_expires$arg_id$remote_addr my-secret";
```

```env
VOD_SECRET=
```

#### Optimize

```bash
composer install --optimize-autoloader --no-dev
npm run prod
php artisan optimize
```

Optimize modules:

- https://github.com/kaltura/nginx-vod-module#performance-recommendations