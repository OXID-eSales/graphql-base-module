# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.0] 2020-06-23

### Added

- `$cache` parameter to `OxidEsales\GraphQL\Base\Framework\SchemaFactory::__construct`
- Cache flush on module events
- `OxidEsales\GraphQL\Base\Service\Authentication::getUserName()`
- `OxidEsales\GraphQL\Base\Service\Authentication::getUserId()`
- `OxidEsales\GraphQL\Base\Service\Legacy::login()`
- `OxidEsales\GraphQL\Base\Framework\UserData`

### Deprecated

- `OxidEsales\GraphQL\Base\Service\Legacy::checkCredentials()`
- `OxidEsales\GraphQL\Base\Service\Legacy::getUserGroup()`

## [3.0.0] 2020-05-19

### Added

- `OxidEsales\GraphQL\Base\Framework\NullToken`

### Changed

- `OxidEsales\GraphQL\Base\Framework\RequestReader::getAuthToken` now always returns with a Token object

### Removed

- Interfaces
    - `OxidEsales\GraphQL\Base\Framework\SchemaFactoryInterface`
    - `OxidEsales\GraphQL\Base\Service\LegacyServiceInterface`
    - `OxidEsales\GraphQL\Base\Service\KeyRegistryInterface`
    - `OxidEsales\GraphQL\Base\Framework\ErrorCodeProviderInterface`
    - `OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandlerInterface`
    - `OxidEsales\GraphQL\Base\Framework\RequestReaderInterface`
    - `OxidEsales\GraphQL\Base\Framework\ResponseWriterInterface`
    - `OxidEsales\GraphQL\Base\Service\AuthenticationServiceInterface`
    - `OxidEsales\GraphQL\Base\Service\AuthorizationServiceInterface`
- Classes
    - `OxidEsales\GraphQL\Base\Service\AuthorizationService`
    - `OxidEsales\GraphQL\Base\Service\AuthenticationService`
    - `OxidEsales\GraphQL\Base\Service\LegacyService`
    - `OxidEsales\GraphQL\Base\Event\BeforeAuthorizationEvent`
- Methods
    - `OxidEsales\GraphQL\Base\Service\Authorization::setToken`
    - `OxidEsales\GraphQL\Base\Service\Authentication::setToken`

[3.1.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v3.0.0...v3.1.0
[3.0.0]: https://github.com/OXID-eSales/graphql-base-module/compare/v2.5.0...v3.0.0
