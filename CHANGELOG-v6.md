# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [6.0.0] - Unreleased

### Added
- PHP 8 support
- Token service for accessing current token
- User DataType in place of UserData and AnonymousUserData classes
  - It tries to actually load shop user
  - Implements the ShopModelAwareInterface
- File upload handling
  - Used the special fork of Ecodev/graphql-upload, which supports PHP8 and webonyx/graphql-php ^0.13 to fit other components
- BelongsToShop token validation constraint
- JwtConfigurationBuilder to handle everything related to JWT token configuration.

### Changed
- Updated to thecodingmachine/graphqlite ^4.0 and lcobucci/jwt ^4.0
- Lcobucci\JWT\Token cannot be used directly anymore, changed to UnencryptedToken interface everywhere
- DataType directory contents have been splitted to several: Filter, Pagination, Sorting
- Authentication service improved
  - AuthenticationServiceInterface is now fully implemented
  - Extracted token creation to Token service
  - Extracted token validation to TokenValidator service
    - Token validation is done once during token reading procedure now
    - Checking if token user is in "blocked" group is done here as well
- There is no NullToken class anymore. Token may be available or not, no stable default states anymore.
- Tests readability improved greatly
- ModuleSetup class moved up from Framework directory

### Removed
- PHP 7.3 support
- UserData and AnonymousUserData Framework classes
- NullToken Framework classe
- Deprecated Legacy service

[6.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v5.2.0...b-6.4.x
