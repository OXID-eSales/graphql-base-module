# oxid-esales/graphql-base

[![Build Status](https://img.shields.io/github/workflow/status/OXID-eSales/graphql-base-module/CI?logo=github-actions&style=for-the-badge)](https://github.com/OXID-eSales/graphql-base-module/actions)
[![Build
Status](https://img.shields.io/sonar/quality_gate/OXID-eSales_graphql-base-module?server=https%3A%2F%2Fsonarcloud.io&style=for-the-badge&logo=sonarcloud)](https://sonarcloud.io/dashboard?id=OXID-eSales_graphql-base-module)

[![Stable Version](https://img.shields.io/packagist/v/OXID-eSales/graphql-base?style=for-the-badge&logo=composer&label=stable)](https://packagist.org/packages/oxid-esales/graphql-base)
[![Latest Version](https://img.shields.io/packagist/v/OXID-eSales/graphql-base?style=for-the-badge&logo=composer&label=latest&include_prereleases&color=orange)](https://packagist.org/packages/oxid-esales/graphql-base)
[![PHP Version](https://img.shields.io/packagist/php-v/oxid-esales/graphql-base?style=for-the-badge)](https://github.com/oxid-esales/graphql-base-module)

This module provides:
- a basic [GraphQL](https://www.graphql.org) implementation for the [OXID eShop](https://www.oxid-esales.com/)
- authorization and authentication using [JWT](https://jwt.io)
- a query to log you in and get a JWT for further authentication


## Documentation

* Full documentation, including GraphQL schema, can be found [here](https://docs.oxid-esales.com/interfaces/graphql/en/7.0/).

## Usage

This assumes you have OXID eShop (at least `OXID-eSales/oxideshop_ce: v6.8.0` component, which is part of the `6.3.0` compilation) up and running.

## Branch Compatibility

* master branch is compatible with latest shop compilations: 7.0.x and master
* 7.x versions (or b-6.5.x branch) are compatible with latest shop compilations: 6.5.x resp. b-6.5.x shop compilation branches
* 6.x versions (or b-6.4.x branch) are compatible with latest shop compilations: 6.4.x resp. b-6.4.x shop compilation branches
* 5.x versions (or b-6.3.x branch) are compatible with latest shop compilations: 6.3.x resp. b-6.3.x shop compilation branches (NOTE: no support for PHP 8 yet)

### Install

```bash
# Install desired version of oxid-esales/graphql-base module, in this case - latest released 6.x version
$ composer require oxid-esales/graphql-base ^7.0.0
```

After requiring the module, you need to activate it, either via OXID eShop admin or CLI.

```bash
$ ./bin/oe-console oe:module:activate oe_graphql_base
```

### How to use

You can use your favourite GraphQL client to explore the API, if you do not
already have one installed, you may use [Altair GraphQL Client](https://altair.sirmuel.design/).

To login and retrieve a token send the following GraphQL query to the server

```graphql
query {
    token (
        username: "admin",
        password: "admin"
    )
}
```

You could simply just fire up your terminal and use `curl` to do a basic check
if the GraphQL base module is up and running as epxected. To retrieve a valid
token you need to replace the username and password below with valid login
credentials.

```bash
$ curl http://oxideshop.local/graphql/ \
  -H 'Content-Type: application/json' \
  --data-binary '{"query":"query {token(username: \"admin\", password: \"admin\")}"}'
```

You should see a response similar to this:

```json
{
    "data": {
        "token": "a-very-long-jwt"
    }
}
```

This `token` is then to be send as your authorization with every request in the
HTTP `Authorization` header like this:

```
Authorization: Bearer a-very-long-jwt
```

### How to extend

The information on extending any module can be found in the [OXID eSales documentation](https://docs.oxid-esales.com).

How to extend GraphQL module types and implement your new mutations and queries is shown in [OXID GraphQL API documentation](https://docs.oxid-esales.com/interfaces/graphql/en/7.0/tutorials/index.html).

## Testing

### Linting, syntax check, static analysis and unit tests

```bash
$ composer test
```

### Integration/Acceptance tests

- install this module into a running OXID eShop
- change the `test_config.yml`
  - add `oe/graphql-base` to the `partial_module_paths`
  - set `activate_all_modules` to `true`

```bash
$ ./vendor/bin/runtests
```

## Issues

To report issues with GraphQL module please use the [OXID eShop bugtracking system](https://bugs.oxid-esales.com/).

## Contributing

You like to contribute? 🙌 AWESOME 🙌\
Go and check the [contribution guidelines](CONTRIBUTING.md)

## Build with

- [GraphQLite](https://graphqlite.thecodingmachine.io/)
- [lcobucci/jwt](https://github.com/lcobucci/jwt)

## License

OXID Module and Component License, see [LICENSE file](LICENSE).
