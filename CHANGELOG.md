# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [unreleased]

### Added
- basic input filter for type `DateTime`

### Changed
- removed `Input` suffix from filter types

## [1.3.0] 2019-12-09

### Added
- `filter` to specification
- basic input filter types for `string`, `float`, `integer`, `boolean` and `ID`
- `use function` for all PHP functions used
- HTTP status codes to spec

### Changed
- updated PHPStan vom `0.11` to `0.12`

## [1.2.0] 2019-12-02

### Added
- `query()` method for easier integration tests
- [spec for the API](docs/SPECIFICATION.md)
- `createUniqueIdentifier` to `LegacyService`

### Changed
- Unit tests are real unit tests now and will be run in travis

### Deprecated
- `execQuery()` method

## [1.1.2] 2019-11-14

### Added
- missing `getLanguageId` in `LegacyService`
- missing `getLanguageId` in `LegacyServiceInterface`
- hint about `RewriteRule` problems with query string

## [1.1.1] 2019-11-08

### Added
- composer troubleshooting upon install
- try to get `Authorization` header via `apache_request_headers()` also
- comment to token query

### Changed
- install with `require --no-update` and `composer update` afterwards
- version number in `metadata.php` was wrong
- lower boundaries of dependency versions

## [1.1.0] 2019-11-06

### Added
- `setAuthToken` to OxidEsales\GraphQL\Base\Tests\Integration\TestCase to allow for
  authentication and authorization tests

## [1.0.0] 2019-11-05

### Added
- [GraphQLite](https://github.com/thecodingmachine/graphqlite) as the main dependency
- [PHPStan](https://github.com/phpstan/phpstan) for static analysis
- Unit and integration tests using PHPUnit
- Accaptance tests using Codeception

### Changed
- Namespace from \OxidEsales\GraphQL to \OxidEsales\GraphQL\Base
- PSR2 -> PSR12
