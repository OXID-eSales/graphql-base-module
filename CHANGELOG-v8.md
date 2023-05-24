# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [8.0.0] - 2023-05-24

### Changed
- The type of `sJsonWebTokenUserQuota` setting was changed from `str` to `num`.

### Removed
- Support PHP 7.4
- Constant `WRONG_TYPE_MESSAGE` from `OxidEsales\GraphQL\Base\Exception\MissingSignatureKey` exception.
- Method `wrongType` from `OxidEsales\GraphQL\Base\Exception\MissingSignatureKey` exception.

[8.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v7.0.2...v8.0.0
