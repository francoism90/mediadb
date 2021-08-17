# MediaDB (API)

**MediaDB** is a web-based media streaming service written in Laravel and Vue.

- The [nginx-vod-module](https://github.com/kaltura/nginx-vod-module) is used for on-the-fly repackaging of MP4 files to DASH.
- The [nginx-secure-token-module](https://github.com/kaltura/nginx-secure-token-module) is used to encrypt stream urls. CDN solutions may be preferred, `nginx-secure-token-module` provides support for several token providers (Akamai/CloudFront).

Note: MediaDB is very much in development and may not be suitable for production purposes. It is recommended to fork the project.

## Documentation

<https://francoism90.github.io/mediadb-docs/>

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
