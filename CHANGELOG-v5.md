# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.1.1] - 2020-12-16

### Changed
- Updated documentation after catalogue, account and checkout module have been merged as
  storefront graphql module.
- deployment for GraphQL schema docs has been polished
- update `lcobucci/jwt` from `^3.3.1` to `^3.4.1` and fixed deprecation warnings

## [5.1.0] - 2020-11-23

### Added
- documentation on placing an order via GraphQL

### Deprecated
- `OxidEsales\GraphQL\Base\Service\Legacy`

## [5.0.0] - 2020-10-29

### Added

- Send `Access-Control-Allow-Origin: *` response header with every response
- Schema documentation available at https://oxid-esales.github.io/graphql-base-module
- New methods:
    - `Service/Legacy::getEmail`

### Changed

- Module id changed from **oe/graphql-base** to **oe_graphql_base** for being compatible with shop documentation
- **BC-BREAK!** fix wording of `lowerThen` / `greaterThen` to `lessThan` /
  `greaterThan` - OXDEV-3956, thanks [@Zockman](https://github.com/Zockman)
- Codeception acceptance tests configured and used for testing actual module workflow

### Fixed

- Preflight CORS handling
- Fixed module activation/deactivation event pointers

[5.1.1]: https://github.com/OXID-eSales/graphql-base-module/compare/v5.1.0...v5.1.1
[5.1.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v5.0.0...v5.1.0
[5.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v4.0.0...v5.0.0
