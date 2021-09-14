# Upgrading

See the [mediadb-docs](https://francoism90.github.io/mediadb-docs/guide/upgrading.html) for more details.

## From v0 to v1

- Set media containing `collection_name` `clip` to `clips`.
- Set media containing `collection_name` `caption` to `captions`.
- Replace `VOD_URL`, `VOD_KEY` and `VOD_IV` with `DASH_URL`, `DASH_KEY` and `DASH_IV`.
- The `media:regenerate` command has been replaced by `video:regenerate`.
- The import disk has been removed.
