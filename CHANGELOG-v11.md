# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [11.0.0-rc] - 2025-04-23

### Added
- FilesystemAdapter as schema caching solution
- CacheClearCommand allowing to manually clear schema cache

### Changed
- Update module to work with OXID eShop 7.3
- Replaced `TokenFilterList::fromUserInput` and `TokenSorting::fromUserInput` with direct object instantiation
- Removed static access for `Legacy::createUniqueIdentifier()` and made it an instance method
- Updated `MissingSignatureKey` exception to use a constructor instead of a static factory method
- Upgrade GraphQLite to 7.0

[11.0.0-rc]: https://github.com/OXID-eSales/graphql-base-module/compare/v10.0.0...v11.0.0-rc
