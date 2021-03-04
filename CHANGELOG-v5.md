# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Update module to use the new API from [lcobucci/jwt](https://lcobucci-jwt.readthedocs.io/en/latest/upgrading/#v3x-to-v4x) 4.0.0
- Prevent accidental PHP session usage
- Move Schema documentation to https://oxid-esales.github.io/graphql-storefront-module
- Changed `OxidEsales\GraphQL\Base\Infrastructure\Legacy::createUniqueIdentifier` method to static.
- Token does not contain information for customer's groups as changes to the groups should take immediate effect.
- Update `thecodingmachine/graphqlite` from `^3.1.2` to `^4.1.2` and fixed compatibility issues

### Added
- [`Server-Timing`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Server-Timing) response header
- Allow `errors` in response with `data`
- Introduce anonymous token with claim to a randomly generated userid and group `oxidanonymous`.
- Event `BeforeTokenCreation` - using it you may change the builder object before token gets created.

### Removed
- Removed CORS headers (https://docs.oxid-esales.com/interfaces/graphql/en/latest/troubleshooting.html)
- Interfaces
    - `OxidEsales\GraphQL\Base\Framework\ErrorCodeProvider`
    - `OxidEsales\GraphQL\Base\Exception\HttpErrorInterface`
- Methods
    - `OxidEsales\GraphQL\Base\Exception\Exists::getHttpStatus`
    - `OxidEsales\GraphQL\Base\Exception\InvalidLogin::getHttpStatus`
    - `OxidEsales\GraphQL\Base\Exception\InvalidRequest::getHttpStatus`
    - `OxidEsales\GraphQL\Base\Exception\InvalidToken::getHttpStatus`
    - `OxidEsales\GraphQL\Base\Exception\NotFound::getHttpStatus`
    - `OxidEsales\GraphQL\Base\Exception\OutOfBounds::getHttpStatus`

## [5.1.1] - 2020-12-16

### Changed
- Updated documentation after catalogue, account and checkout module have been merged as
  storefront graphql module
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
