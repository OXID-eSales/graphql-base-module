# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [15.0.0] - unreleased

### Changed
- Update module to work with OXID eShop 7.7
- Run module migrations with `oe-console oe:database:migrate` instead of the doctrine migration wrapper

### Removed
- `oxid-esales/oxideshop-doctrine-migration-wrapper` dev dependency

[15.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v14.0.1...b-7.7.x
