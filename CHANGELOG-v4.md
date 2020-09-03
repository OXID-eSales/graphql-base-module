# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.0.0] 2020-09-03

### Added

- Documentation!

### Changed

- Permissions via user group relation (`oxobject2group`) instead of `oxuser.oxrights`
- Claim `group` renamed to `groups` in JWT

### Removed

- `\OxidEsales\GraphQL\Base\Framework\UserData::getUserGroup()`
- `\OxidEsales\GraphQL\Base\Service\Legacy::checkCredentials()`
- `\OxidEsales\GraphQL\Base\Service\Legacy::getUserGroup()`


[4.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v3.2.0...v4.0.0
