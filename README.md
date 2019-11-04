# oxid-esales/graphql-base

[![Build Status](https://img.shields.io/travis/com/OXID-eSales/graphql-base-module.svg?style=for-the-badge&logo=travis)](https://travis-ci.com/OXID-eSales/graphql-base-module) [![PHP Version](https://img.shields.io/packagist/php-v/oxid-esales/graphql-base.svg?style=for-the-badge)](https://github.com/oxid-esales/graphql-base-module) [![Stable Version](https://img.shields.io/packagist/v/oxid-esales/graphql-base.svg?style=for-the-badge&label=latest)](https://packagist.org/packages/oxid-esales/graphql-base-module)

This module provides:
- a basic [GraphQL](https://www.graphql.org) implementation for the [OXID eShop](https://www.oxid-esales.com/)
- authorization and authentication using [JWT](https://jwt.io)
- a query to log you in and get a JWT for further authentication

## Usage

This assumes you have the OXID eShop up and running.

### Install

```bash
$ composer require oxid-esales/graphql-base
```

After requiring the module, you need to head over to the OXID eShop admin and
activate the GraphQL Base module.

### How to use

You can use your favourite GraphQL client to explore the API, if you do not
already have one installed, you may use [Altair GraphQL Client](https://altair.sirmuel.design/) or
you could simply just fire up your terminal and use `curl` to do a basic check
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
{"data":{"token":"a-very-long-jwt"}}
```

This `token` is then to be send as your authorization with every request in the
HTTP `Authorization` header like this:

```
Authorization: Bearer a-very-long-jwt
```

## Testing

### Linting, Syntax and static analysis

```bash
$ composer test
```

### Unit tests

- install this module into a running OXID eShop
- change the `test_config.yml`
  - add `oe/graphql-base` to the `partial_module_paths`
  - set `activate_all_modules` to `true`

```bash
$ ./vendor/bin/runtests
```

## Troubleshooting

### Apache HTTP Authorization

php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default.
For this workaround to work, add these lines to your .htaccess file:

```
RewriteCond %{HTTP:Authorization} ^(.+)$
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```

## Build with

- [GraphQLite](https://graphqlite.thecodingmachine.io/)

## License

MIT, see [LICENSE file](LICENSE).
