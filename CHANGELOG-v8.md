# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [8.1.1] - 2024-05-28

### Fixed
- Updated the version in metadata.php file

## [8.1.0] - 2024-04-04

### Changed
- Replace webmozart/path-util usage with symfony/filesystem
- Order of DataType-methods for alphabetic order in schema
- Set DebugFlag for error output to default
- extract logging error formatter to new method
- extract generation and save of module signature from `OxidEsales\GraphQL\Base\Infrastructure\ModuleSetup` to `OxidEsales\GraphQL\Base\Service\ModuleConfiguration`

### Added
- Workflow trigger to update schema in documentation
- Descriptions for queries, mutations and DataType-fields
- Context (trace) for logged errors

## [8.0.1] - 2023-06-30

### Changed
- Update documentation for v8.0.0 changes

## [8.0.0] - 2023-05-25

### Added
- Module upgraded for eshop version 7
  - Add twig support

### Changed
- The type of `sJsonWebTokenUserQuota` setting was changed from `str` to `num`.
- License updated - now using OXID Module and Component License
- Refactored NotFound exception and children to create instance with constructor instead of static methods.
  - `OxidEsales\GraphQL\Base\Exception\NotFound::notFound()` removed
  - `OxidEsales\GraphQL\Base\Exception\UserNotFound::byId()` removed
- Module upgraded for eshop version 7
  - Assetspath updated
  - Migrations config structure updated
  - Getting module setting via new `OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface`
  - Moved the translation files to the correct directory

### Removed
- Constant `WRONG_TYPE_MESSAGE` from `OxidEsales\GraphQL\Base\Exception\MissingSignatureKey` exception.
- Method `wrongType` from `OxidEsales\GraphQL\Base\Exception\MissingSignatureKey` exception.
- Module upgraded for eshop version 7
  - NAME-constant removed from events
  - Support PHP 7.4


[8.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v7.0.2...v8.0.0
