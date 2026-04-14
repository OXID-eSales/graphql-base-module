# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [11.0.2] - 2026-04-14

### Fixed
- Composer throws security issues for concrete graphl-version

## [11.0.1] - 2026-03-18

### Fixed
- Graphqlite is incompatible with webonyx/graphql-php v15.31

## [11.0.0] - 2025-06-11
This is stable release for v11.0.0. No changes have been made since v11.0.0-rc.1.

## [11.0.0-rc.1] - 2025-04-23

### Added
- FilesystemAdapter as schema caching solution
- CacheClearCommand allowing to manually clear schema cache

### Changed
- Update module to work with OXID eShop 7.3
- Replaced `TokenFilterList::fromUserInput` and `TokenSorting::fromUserInput` with direct object instantiation
- Removed static access for `Legacy::createUniqueIdentifier()` and made it an instance method
- Updated `MissingSignatureKey` exception to use a constructor instead of a static factory method
- Upgrade GraphQLite to 7.0

[11.0.2]: https://github.com/OXID-eSales/graphql-base-module/compare/v11.0.1...v11.0.2
[11.0.1]: https://github.com/OXID-eSales/graphql-base-module/compare/v11.0.0...v11.0.1
[11.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v11.0.0-rc.1...v11.0.0
[11.0.0-rc.1]: https://github.com/OXID-eSales/graphql-base-module/compare/v10.0.0...v11.0.0-rc.1
