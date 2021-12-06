# Server Setup

## Current Environment

OS: [Arch Linux](https://archlinux.org/)
CPU: AMD Ryzen 5 3400G
Memory: 16GB DDR4
Storage: [mergerfs](https://github.com/trapexit/mergerfs)

### Packages

- [meilisearch](https://wiki.archlinux.org/title/MeiliSearch)
- [nginx](https://wiki.archlinux.org/title/Nginx)
- [nginx-mainline-mod-brotli-git](https://aur.archlinux.org/packages/nginx-mainline-mod-brotli-git/) (optional)
- [nginx-mainline-mod-secure-token-git](https://aur.archlinux.org/packages/nginx-mainline-mod-secure-token-git/)
- [nginx-mainline-mod-vod-git](https://aur.archlinux.org/packages/nginx-mainline-mod-vod-git/)
- [redis](https://wiki.archlinux.org/title/Redis)
- [soketi](https://aur.archlinux.org/packages/soketi/)
- [supervisor](https://archlinux.org/packages/community/any/supervisor/)

### Configuration

- os: `doc/crontab`, `doc/hosts`
- nginx: `doc/nginx`
- supervisor: `doc/supervisor`

### Recommendations

- <https://wiki.archlinux.org/title/Improving_performance>
- <https://wiki.archlinux.org/title/Security>
