# MediaDB

## Install

```bash
composer install
npm install & npm run dev
php artisan storage:link
php artisan horizon:install
php artisan telescope:install
php artisan elastic:create-index "App\Support\Scout\CollectionIndexConfigurator"
php artisan elastic:create-index "App\Support\Scout\MediaIndexConfigurator"
php artisan elastic:create-index "App\Support\Scout\UserIndexConfigurator"
php artisan elastic:update-mapping "App\Models\Collection"
php artisan elastic:update-mapping "App\Models\Media"
php artisan elastic:update-mapping "App\Models\User"
```

## Generating VOD key + IV

```bash
dd if=/dev/urandom bs=1 count=32 2> /dev/null | xxd -p -c32
dd if=/dev/urandom bs=1 count=16 2> /dev/null | xxd -p -c32
```

## Optimize

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run prod
php artisan optimize
```
