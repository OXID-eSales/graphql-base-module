# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [9.0.0] - unreleased

### Changed
- Extract `sendUnauthenticatedErrorResponse` from `Component\Widget\GraphQL::sendErrorResponse`
- Change `Exception\ErrorCategories` class with interface
- Upgrade Codeception and PHPUnit versions
- Logo
- Update GraphQLite version to ^6.2

### Added
- Missing type signatures in
  - `Exception\InvalidArgumentMultiplePossible` class
  - `Infrastructure\Legacy` class
  - `Service\Authorization::isAllowed` method
  - `Service\Token::getTokenClaim` method

### Fixed
- Issues reported by PHP MD

### Removed
- Migration trigger on module activation

[9.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v8.0.1...b-7.1.x
