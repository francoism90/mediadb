# Server Setup

See [documentation](https://francoism90.github.io/mediadb-docs) for details.

## Current Environment

OS: [Arch Linux](https://archlinux.org/)  
CPU: AMD Ryzen 5 3400G  
Memory: 16GB DDR4  
Storage: [mergerfs](https://github.com/trapexit/mergerfs)

### Packages

- [mariadb](https://wiki.archlinux.org/title/MariaDB)
- [meilisearch](https://wiki.archlinux.org/title/MeiliSearch)
- [nginx-mainline-mod-brotli-git](https://aur.archlinux.org/packages/nginx-mainline-mod-brotli-git/) (optional)
- [nginx-mainline-mod-secure-token-git](https://aur.archlinux.org/packages/nginx-mainline-mod-secure-token-git/)
- [nginx-mainline-mod-vod-git](https://aur.archlinux.org/packages/nginx-mainline-mod-vod-git/)
- [nginx](https://wiki.archlinux.org/title/Nginx)
- [php](https://wiki.archlinux.org/title/PHP) including [php-swoole](https://aur.archlinux.org/packages/php-swoole)
- [redis](https://wiki.archlinux.org/title/Redis)
- [soketi](https://aur.archlinux.org/packages/soketi/)
- [supervisor](https://archlinux.org/packages/supervisor/)

### Configuration

- OS: `crontab`, `hosts`
- Nginx: `nginx`
- Supervisor: `supervisor`

### Recommendations

- <https://wiki.archlinux.org/title/Improving_performance>
- <https://wiki.archlinux.org/title/Security>
