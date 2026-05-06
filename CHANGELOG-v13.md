# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [13.0.0] - 2026-05-06

### Changed
- Update module to work with OXID eShop 7.5
- Minimum PHP version is now 8.3, tested up to PHP 8.5
- Upgraded PHPUnit from 11 to 12
- Upgraded `lcobucci/jwt` from v4 to v5, added `lcobucci/clock` dependency
- `BeforeTokenCreation` event now provides `withClaim()` and `withHeader()` methods for adding custom token data. This abstracts the JWT library's immutable Builder pattern. The `getBuilder()` method is now marked as `@internal`.

### Added
- `RequestReaderInterface`
- `ResponseWriterInterface`
- `GraphQLQueryHandlerInterface`
- `SchemaFactoryInterface`
- `SeoEncoderArticle` with `oegbGetCategoryUri` method, including `SeoEncoderArticleFactory`

### Fixed
- Graphqlite is incompatible with webonyx/graphql-php v15.31
- Composer throws security issues for concrete graphl-version


[13.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v12.0.2...v13.0.0
