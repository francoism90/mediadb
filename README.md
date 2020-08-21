# MediaDB (API)

[![Build Status](https://travis-ci.com/francoism90/mediadb.svg?branch=master)](https://travis-ci.com/francoism90/mediadb)

**MediaDB** is a web-based video streaming service written in Laravel and Vue.

- The [nginx-vod-module](https://github.com/kaltura/nginx-vod-module) is used for on-the-fly repackaging of MP4 files to DASH.
- [Encryption URL](https://github.com/kaltura/nginx-secure-token-module) and [expire tokens](https://nginx.org/en/docs/http/ngx_http_secure_link_module.html) are used to prevent unwanted access and reading of streams.
- Generate sprites and thumbnails of video files.

MediaDB is very much in development and is not yet suitable for production purposes.

## Installation

MediaDB requires a Laravel compatible development environment like [Homestead](https://laravel.com/docs/7.x/homestead#environment-variables).

- [nginx](https://nginx.org) with `--with-http_secure_link_module`
- [nginx-secure-token-module](https://github.com/kaltura/nginx-secure-token-module)
- [nginx-vod-module](https://github.com/kaltura/nginx-vod-module)
- [ngx_brotli](https://github.com/google/ngx_brotli)
- [ffmpeg](https://www.ffmpeg.org/) including `ffprobe`
- [PHP](https://php.net/) 7.2 or later, with exif and GD support, including required extensions like `php-redis` and `php-imagick`.
- [Image optimizers](https://docs.spatie.be/laravel-medialibrary/v8/converting-images/optimizing-converted-images/)
- MariaDB/MySQL (with JSON support), Redis and Supervisor.
- [Elasticsearch](https://www.elastic.co/products/elasticsearch)
- [Samples](https://gist.github.com/jsturgis/3b19447b304616f18657) for testing.

Please consult the upstream documentation of used packages in `composer.json` for possible other missing (OS) dependencies and/or recommendations.

### Front-end

- <https://github.com/francoism90/mediadb-ui> - Front-end for MediaDB written in Vue and Quasar.

Note: it is recommend to clone/install MediaDB projects as subfolders, e.g. `/srv/http/mediadb/api` (mediadb-api) and `/srv/http/mediadb/ui` (mediadb-ui).

### Nginx

See `doc/nginx` for configuration examples.

| Site             | Domain                         | Details                                                                                |
|------------------|--------------------------------|----------------------------------------------------------------------------------------|
| mediadb-api.conf | localhost:3000                 | API: generates JSON for vod-local, authentication, import videos, json-api/REST, etc.  |
| mediadb-ui.conf  | mediadb.dom:443 mediadb.dom:80 | Front-end: accessible for user, browse library, search, play, etc.                     |
| vod-local.conf   | localhost:8081                 | VOD: points to JSON mappings files (full media paths, sequences, etc.).                |
| vod-mapped.conf  | localhost:1935                 | VOD: processes streaming (DASH), thumbnails (optional) and add security tokens.        |
| vod-stream.conf  | stream.dom:443 stream.dom:80   | VOD: accessible for user, validate security tokens, retrieve upstream vod-mapped data. |

### Laravel

See `doc/supervisor` for configuration examples.

```bash
cd /srv/http/mediadb/api
cp .env.example .env
composer install
php artisan migrate
php artisan key:generate
php artisan storage:link
php artisan horizon:install
php artisan telescope:install
```

It is advisable to view all configuration files and change them when necessary, especially `.env`, `config/vod.php`, `config/hashids.php` and `config/filesystems.php`.

#### Indexes

Note: make sure Elasticsearch is up and running.

```bash
php artisan elastic:create-index "App\Support\Scout\CollectionIndexConfigurator"
php artisan elastic:create-index "App\Support\Scout\TagIndexConfigurator"
php artisan elastic:create-index "App\Support\Scout\UserIndexConfigurator"
php artisan elastic:create-index "App\Support\Scout\VideoIndexConfigurator"
php artisan elastic:update-mapping "App\Models\Collection"
php artisan elastic:update-mapping "App\Models\Tag"
php artisan elastic:update-mapping "App\Models\User"
php artisan elastic:update-mapping "App\Models\Video"
```

#### Seeders

```bash
php artisan db:seed
```

### Generating VOD key + IV

```bash
dd if=/dev/urandom bs=1 count=32 2> /dev/null | xxd -p -c32
dd if=/dev/urandom bs=1 count=16 2> /dev/null | xxd -p -c32
```

```env
VOD_KEY=d5460ef7a5c2bece2d1b24e0d9959e5ea9beb9dd449080147bdba001e9106793
VOD_IV=722d4f9191c53d5e934e13719d02cced
```

```bash
secure_token_encrypt_uri_key d5460ef7a5c2bece2d1b24e0d9959e5ea9beb9dd449080147bdba001e9106793;
secure_token_encrypt_uri_iv 722d4f9191c53d5e934e13719d02cced;
```

### Set VOD security

```env
VOD_SECRET=secret
```

```bash
"$secure_link_expires$arg_id$remote_addr secret";
```

### Set VOD path

```bash
set $base /srv/http/mediadb/api/storage/app/streams;
```

## Usage

Media files must be imported to a channel (which will be created if needed):

```bash
cd /srv/http/mediadb/api
php artisan video:import /path/to/import "Top Gear"
```

Use [MediaDB UI](https://github.com/francoism90/mediadb-ui) or custom front-end to retrieve the streaming data/manage media.

### Notes

- Make sure the import and destination path is owned by `http` (running user). The importer will skip non writable files.
- Make sure the videos can be played in the browser as they aren't being encoded (yet).
- Make sure there is enough space on the disk to import and process the media.
- See `app/Console/Commands/User/Import.php` for more details.

## Upgrade guide

### Elasticsearch

```bash
php artisan elastic:update-index "App\Support\Scout\CollectionIndexConfigurator"
php artisan elastic:update-index "App\Support\Scout\TagIndexConfigurator"
php artisan elastic:update-index "App\Support\Scout\UserIndexConfigurator"
php artisan elastic:update-index "App\Support\Scout\VideoIndexConfigurator"
php artisan elastic:update-mapping "App\Models\Collection"
php artisan elastic:update-mapping "App\Models\Tag"
php artisan elastic:update-mapping "App\Models\User"
php artisan elastic:update-mapping "App\Models\Video"
```

Optional (re-)index the models:

```bash
php artisan scout:import "App\Models\Collection"
php artisan scout:import "App\Models\Tag"
php artisan scout:import "App\Models\User"
php artisan scout:import "App\Models\Video"
```

## Optimizing

```bash
composer install --optimize-autoloader --no-dev
php artisan optimize
```

### Modules

- <https://github.com/kaltura/nginx-vod-module#performance-recommendations>

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## Credits

- [Kaltura](https://github.com/kaltura)
- [Koel](https://github.com/koel)
- [Spatie](https://github.com/spatie)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
