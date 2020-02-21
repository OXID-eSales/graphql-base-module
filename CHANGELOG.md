# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [unreleased]

### Changed
- deprecated `OxidEsales\GraphQL\Base\Service\LegacyService` and `OxidEsales\GraphQL\Base\Service\LegacyServiceInterface`

## [2.3.0] 2020-02-18

### Added
- abstract `OxidEsales\GraphQL\Base\Tests\Integration\EnterpriseTestCase` for EE integration tests

## [2.2.0] 2020-02-04

### Added
- unit tests for filter input data types
- PHPCompatibility to CodeSniffer

### Changed
- factory method for filter inupt data types from `createWhatEverType` to `fromUserInput`
- moved the `OxidEsales\GraphQL\Base\Tests` namespace back to the `autoload` key in `composer.json`

## [2.1.0] 2020-01-29

### Added
- point out that we are using `lcobucci/jwt` in `README.md`
- `OxidEsales\GraphQL\Base\Tests\Integration\MultishopTestCase` for EE integration tests
- `.gitattributes` to keep tests and developement stuff out of other peoples production

### Changed
- moved input type factory method from `OxidEsales\GraphQL\Base\DataType\*FilterFactory` to existing `OxidEsales\GraphQL\Base\DataType\*Filter`
- wording in `docs/SPECIFICATION.md` from `DataObject` to `DataType`

### Removed
- Classes
    - `OxidEsales\GraphQL\Base\DataType\BoolFilterFactory`
    - `OxidEsales\GraphQL\Base\DataType\DateFilterFactory`
    - `OxidEsales\GraphQL\Base\DataType\FloatFilterFactory`
    - `OxidEsales\GraphQL\Base\DataType\IDFilterFactory`
    - `OxidEsales\GraphQL\Base\DataType\IntegerFilterFactory`
    - `OxidEsales\GraphQL\Base\DataType\StringFilterFactory`

## [2.0.0] 2020-01-23

### Changed
- `DataObject` -> `DataType`. We do not use the term `DataObject` anymore, because from GraphQL as the point of view these are types

### Removed
- Classes
    - `OxidEsales\GraphQL\Base\Exception\InvalidLoginException`
    - `OxidEsales\GraphQL\Base\Exception\InvalidTokenException`
    - `OxidEsales\GraphQL\Base\Exception\NoSignatureKeyException`
    - `OxidEsales\GraphQL\Base\Exception\NotFoundException`
- Methods
    - `OxidEsales\GraphQL\Base\Tests\Integration::execQuery`

## [1.3.3] 2020-01-20

### Deprecated
- Smurf-Naming-Convention for Exceptions (the old ones are still there and will be removed in the next major)
    - `InvalidLoginException` -> `InvalidLogin`
    - `InvalidTokenException` -> `InvalidToken`
    - `NoSignatureKeyException` -> `MissingSignatureKey`
    - `NotFoundException` -> `NotFound`

## [1.3.2] 2020-01-16

### Added
- handling of preflight CORS requests
- handling of HTTP GET requests

## [1.3.1] 2019-12-18

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
