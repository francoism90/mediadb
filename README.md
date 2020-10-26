# MediaDB (API)

[![Build Status](https://travis-ci.com/francoism90/mediadb.svg?branch=master)](https://travis-ci.com/francoism90/mediadb)

**MediaDB** is a web-based media streaming service written in Laravel and Vue.

- The [nginx-vod-module](https://github.com/kaltura/nginx-vod-module) is used for on-the-fly repackaging of MP4 files to DASH.
- [Encryption URL](https://github.com/kaltura/nginx-secure-token-module) and [expire tokens](https://nginx.org/en/docs/http/ngx_http_secure_link_module.html) are used to prevent unwanted access and reading of streams. However a CDN may be preferred, `nginx-secure-token-module` provides support for several token providers.
- Generates sprites and thumbnails of video files.

MediaDB is very much in development and may not be suitable for production purposes.

## Installation

MediaDB requires a Laravel compatible development environment like [Homestead](https://laravel.com/docs/8.x/homestead).

- [nginx](https://nginx.org) with `--with-http_secure_link_module`
- [nginx-secure-token-module](https://github.com/kaltura/nginx-secure-token-module)
- [nginx-vod-module](https://github.com/kaltura/nginx-vod-module)
- [ffmpeg](https://www.ffmpeg.org/) including `ffprobe`
- [PHP](https://php.net/) 7.3 or later, with exif and GD support, including required extensions like `php-redis` and `php-imagick`.
- [Image optimizers](https://docs.spatie.be/laravel-medialibrary/v8/converting-images/optimizing-converted-images/)
- MariaDB/MySQL (with JSON support), Redis and Supervisor.
- [Elasticsearch](https://www.elastic.co/products/elasticsearch)
- [Samples](https://gist.github.com/jsturgis/3b19447b304616f18657) for testing.

Please consult the upstream documentation of used packages in `composer.json` for possible other missing (OS) dependencies and/or recommendations.

### Front-end/app

- <https://github.com/francoism90/mediadb-ui> - Optional front-end/app (Cordova) for MediaDB written in Vue and Quasar.

Note: it is recommend to clone/install MediaDB projects as subfolders, e.g. `/srv/http/mediadb/api` (mediadb-api) and `/srv/http/mediadb/ui` (mediadb-ui).

### Nginx

See `doc/nginx` for configuration examples.

| Site | Domain | Details |
| - | - | - |
| mediadb-api.conf | localhost:3000 | API endpoint: authentication, JSON mapping for vod-local, media manager, etc. |
| mediadb-ui.conf | mediadb.test:443 mediadb.test:80 | MediaDB Front-end (optional). |
| vod-json.conf | localhost:8081 | VOD: JSON mapping (media path, sequences, clips etc.). |
| vod-mapped.conf | localhost:1935 | VOD: add tokens, streaming (DASH), etc. |
| vod-stream.conf | stream.test:443 stream.test:80 | VOD: streaming endpoint, validate security tokens, vod-mapped proxy. |

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

It is advisable to checkout all configuration files and change them when necessary, especially `.env`, `config/library.php`, `config/vod.php`, `config/hashids.php` and `config/filesystems.php`.

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

`.env`:

```env
VOD_KEY=d5460ef7a5c2bece2d1b24e0d9959e5ea9beb9dd449080147bdba001e9106793
VOD_IV=722d4f9191c53d5e934e13719d02cced
```

`vod-mapped.conf`:

```bash
secure_token_encrypt_uri_key d5460ef7a5c2bece2d1b24e0d9959e5ea9beb9dd449080147bdba001e9106793;
secure_token_encrypt_uri_iv 722d4f9191c53d5e934e13719d02cced;
```

### Set VOD security

`.env`:

```env
VOD_SECRET=secret
```

`vod-stream.conf`:

```bash
secure_link_md5 "$secure_link_expires$arg_id secret";
```

### Set VOD path

`vod-json.conf`:

```bash
set $base /srv/http/mediadb/api/storage/app/streams;
```

## Usage

To import files (videos, subtitles/captions, ..) to the library:

```bash
cd /srv/http/mediadb/api
php artisan library:create 'My Title'
php artisan library:import /path/to/import <model-id>
```

Use [MediaDB UI](https://github.com/francoism90/mediadb-ui) or any other custom front-end to retrieve the streaming data/manage media.

### Notes

- Make sure files in the import and destination path are writeable by `http` (or the running user), the importer will skip non writable files.
- Make sure videos can be played in the browser/target device as they aren't being encoded (yet).
- Make sure there is enough space on the disk to import and process the media.
- See `app/Console/Commands/Library/Import.php` for more details.

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
- [Developers](composer.json) offering packages and support. :)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
