# MediaDB

## Build your own streaming service

### Intro

**MediaDB** is a web-based video streaming service written in Laravel and Vue.
It relies on [nginx-vod-module](https://github.com/kaltura/nginx-vod-module) for on-the-fly repackaging of MP4 files to DASH. [Encryption URL](https://github.com/kaltura/nginx-secure-token-module) and [expire tokens](https://nginx.org/en/docs/http/ngx_http_secure_link_module.html) are used to prevent unwanted access of streams.

MediaDB is very much in development and is not yet suitable for production purposes.

### Requirements

- [nginx](https://nginx.org) with `--with-http_secure_link_module`
- [nginx-secure-token-module](https://github.com/kaltura/nginx-secure-token-module)
- [nginx-vod-module](https://github.com/kaltura/nginx-vod-module)
- [ngx_brotli](https://github.com/google/ngx_brotli)
- [ffmpeg](https://www.ffmpeg.org/) including `ffprobe`
- [PHP](https://php.net/) 7.2 or later, with exif and GD support.
- [Image optimizers](https://docs.spatie.be/laravel-medialibrary/v7/converting-images/optimizing-converted-images/)
- [Laravel](https://laravel.com/docs/6.x) development environment with MySQL/MariaDB (with JSON support), Redis , Supervisor
- [Elasticsearch](https://www.elastic.co/products/elasticsearch)
- [Samples](https://gist.github.com/jsturgis/3b19447b304616f18657) for testing

For the time being please consult the upstream documentation of used packages in `composer.json` for possible other missing dependencies or recommendations.

## Install

### Nginx

See `doc/nginx` for configuration examples.

### Laravel

See `doc/supervisor` for configuration examples.

```bash
cd /path/to/html
composer install
php artisan migrate
php artisan db:seed
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

It is advisable to view all configuration files and change them when necessary, especially `.env`, `config/vod.php`, `config/hashids.php` and `config/filesystems.php`.

#### Compile Assets

```bash
cd /path/to/html
npm install
npm run dev
```

### Generating VOD key + IV

```bash
dd if=/dev/urandom bs=1 count=32 2> /dev/null | xxd -p -c32
dd if=/dev/urandom bs=1 count=16 2> /dev/null | xxd -p -c32
```

```env
VOD_KEY=
VOD_IV=
```

### Set VOD secure link

```bash
"$secure_link_expires$arg_id$remote_addr my-secret";
```

```env
VOD_SECRET=
```

## Upgrade

### Elasticsearch

```bash
php artisan elastic:update-index "App\Support\Scout\MediaIndexConfigurator"
php artisan elastic:update-index "App\Support\Scout\UserIndexConfigurator"
php artisan elastic:update-mapping "App\Models\Media"
php artisan elastic:update-mapping "App\Models\User"
```

## Optimize

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run prod
php artisan optimize
```

### Modules

- <https://github.com/kaltura/nginx-vod-module#performance-recommendations>
