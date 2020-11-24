# oxid-esales/graphql-base

[![Build Status](https://flat.badgen.net/travis/OXID-eSales/graphql-base-module/?icon=travis&label=build&cache=300&scale=1.1)](https://travis-ci.com/OXID-eSales/graphql-base-module)
[![PHP Version](https://flat.badgen.net/packagist/php/OXID-eSales/graphql-base/?cache=300&scale=1.1)](https://github.com/oxid-esales/graphql-base-module)
[![Stable Version](https://flat.badgen.net/packagist/v/OXID-eSales/graphql-base/latest/?label=latest&cache=300&scale=1.1)](https://packagist.org/packages/oxid-esales/graphql-base)

This module provides:
- a basic [GraphQL](https://www.graphql.org) implementation for the [OXID eShop](https://www.oxid-esales.com/)
- authorization and authentication using [JWT](https://jwt.io)
- a query to log you in and get a JWT for further authentication


## Documentation

* Full documentation can be found [here](https://docs.oxid-esales.com/modules/graphql/en/5.1/).
* Schema documentation available [here](https://oxid-esales.github.io/graphql-base-module).

## Usage

This assumes you have OXID eShop (at least `OXID-eSales/oxideshop_ce: v6.5.0` component, which is part of the `6.2.0` compilation) up and running.

### Install

```bash
$ composer require oxid-esales/graphql-base
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
$ curl http://oxideshop.local/widget.php?cl=graphql \
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

How to extend GraphQL module types and implement your new mutations and queries is shown in [OXID GraphQL API documentation](https://docs.oxid-esales.com/modules/graphql/en/5.1/tutorials/index.html).

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

## Build with

- [GraphQLite](https://graphqlite.thecodingmachine.io/)
- [lcobucci/jwt](https://github.com/lcobucci/jwt)

## License

GPLv3, see [LICENSE file](LICENSE).
