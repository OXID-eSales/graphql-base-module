# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [unrelease]

### Added
- composer troubleshooting upon install
- try to get `Authorization` header via `apache_request_headers()` also
- comment to token query

### Changed
- install with `require --no-update` and `composer update` afterwards
- version number in `metadata.php` was wrong

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
