# MediaDB (API)

**MediaDB** is a web-based media streaming service written in Laravel and Vue.

- The [nginx-vod-module](https://github.com/kaltura/nginx-vod-module) is used for on-the-fly repackaging of MP4 files to DASH.
- [Encryption URL](https://github.com/kaltura/nginx-secure-token-module) is used to prevent unwanted access and reading of streams. However CDN solutions may be preferred instead, `nginx-secure-token-module` provides support for several token providers.
- Generates sprites and thumbnails of video files.

Full size [screenshots](https://github.com/francoism90/.github/tree/master/screens/mediadb) are available on my Github repo.

MediaDB is very much in development and may not be suitable for production purposes.

## Installation

MediaDB requires a Laravel compatible development environment like [Homestead](https://laravel.com/docs/8.x/homestead).

- [nginx](https://nginx.org)
- [nginx-secure-token-module](https://github.com/kaltura/nginx-secure-token-module)
- [nginx-vod-module](https://github.com/kaltura/nginx-vod-module)
- [ffmpeg](https://www.ffmpeg.org/) including `ffprobe`
- [PHP](https://php.net/) 7.4 or later, with exif and GD support, including required extensions like `php-redis` and `php-imagick`.
- [Image optimizers](https://docs.spatie.be/laravel-medialibrary/v9/converting-images/optimizing-converted-images/)
- MariaDB/MySQL (with JSON support), Redis and Supervisor.
- [Elasticsearch](https://www.elastic.co/products/elasticsearch)
- [Samples](https://gist.github.com/jsturgis/3b19447b304616f18657) for testing.

Please consult the upstream documentation of used packages in `composer.json` for possible other missing (OS) dependencies and/or recommendations.

### Front-end/app

- <https://github.com/francoism90/mediadb-ui> - Optional front-end/app (Cordova) for MediaDB written in Vue and Quasar. Currently only tested on Android.

Note: it is recommend to clone/install MediaDB projects as subfolders, e.g. `/srv/http/mediadb/api` (mediadb-api) and `/srv/http/mediadb/ui` (mediadb-ui).

### Nginx

See `doc/nginx` for configuration examples.

| Site | Domain | Details |
| - | - | - |
| mediadb-api.conf | localhost:3000 | API endpoint: Laravel instance, authentication, media processing, etc. |
| mediadb-ui.conf | mediadb.test:443 mediadb.test:80 | MediaDB Front-end, MediaDB-API proxy. |
| mediadb-vod.conf | stream.test:443 stream.test:80 | VOD: streaming endpoint, local vod-mapping. |

### Laravel

See `doc` for configuration examples.

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

It is advisable to checkout all configuration files and change them when necessary, especially `.env`, `config/video.php`, `config/hashids.php`, `config/media-library.php` and `config/filesystems.php`.

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

`mediadb-vod.conf`:

```bash
secure_token_encrypt_uri_key d5460ef7a5c2bece2d1b24e0d9959e5ea9beb9dd449080147bdba001e9106793;
secure_token_encrypt_uri_iv 722d4f9191c53d5e934e13719d02cced;
```

### Set DASH encryption key

`mediadb-vod.conf`:

```bash
vod_secret_key "mysecret-$vod_filepath";
```

### Set VOD url

`.env`:

```env
VOD_URL=https://stream.test
```

`mediadb-vod.conf`:

```bash
vod_base_url "https://stream.test";
vod_segments_base_url "https://stream.test";
```

## Usage

To import files (videos, captions, ..) to the library:

```bash
cd /srv/http/mediadb/api
php artisan video:import /path/to/import
php artisan video:import-caption /path/to/import <video-id>
```

Use [MediaDB UI](https://github.com/francoism90/mediadb-ui) or any other custom front-end to retrieve the streaming data/manage media.

### Notes

- Make sure files in the import and destination path are writeable by `http` (or the running user), the importer will skip non writable files.
- Make sure videos can be played in the browser/target device as they aren't being encoded (yet).
- Make sure there is enough space on the disk to import and process the media.
- See `app/Console/Commands/Video/Import.php` and `app/Console/Commands/Video/ImportCaption.php` for more details.

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
- [Packagers](composer.json)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
