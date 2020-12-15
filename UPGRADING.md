# Upgrading

Because there are many breaking changes an upgrade is not that easy. There are many edge cases this guide does not cover. We accept PRs to improve this guide.

## From v0 to v1

### Scout

Optional (re-)index the models:

```bash
php artisan scout:import "App\Models\Collection"
php artisan scout:import "App\Models\Tag"
php artisan scout:import "App\Models\User"
php artisan scout:import "App\Models\Video"
```
