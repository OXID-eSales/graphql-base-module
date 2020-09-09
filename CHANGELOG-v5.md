# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [unreleased]

### Added

- send `Access-Control-Allow-Origin: *` response header with every response

### Changed

- **BC-BREAK!** fix wording of `lowerThen` / `greaterThen` to `lessThan` /
  `greaterThan` - OXDEV-3956, thanks [@Zockman](https://github.com/Zockman)

### Fixed

- Preflight CORS handling

[5.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v4.0.0...v5.0.0
