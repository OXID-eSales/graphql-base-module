# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.5.0] 2020-05-19

### Added
- Pagination input type

## [2.4.1] 2020-04-15

### Added
- [Contributing guidelines](CONTRIBUTING.md)

### Changed
- fixed double file level comments

## [2.4.0] 2020-04-01

### Changed
- [#9](https://github.com/OXID-eSales/graphql-base-module/pull/9) deprecate smurf naming and superflus interfaces
- Rewrite test to not use assertIsString

### Added
- [#10](https://github.com/OXID-eSales/graphql-base-module/pull/10) added infection mutation testing framework and improved tests
- PHP-CS-Fixer
- allow for PHPUnit ^7.5|^8.5|^9

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

[2.5.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v2.4.1...v2.5.0
[2.4.1]: https://github.com/OXID-eSales/graphql-base-module/compare/v2.4.0...v2.4.1
[2.4.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v2.3.0...v2.4.0
[2.3.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v2.1.0...v2.2.0
[2.1.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v1.3.3...v2.0.0
