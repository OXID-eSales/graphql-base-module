# oxid-esales/graphql-base

This module provides:
- a basic GraphQL implementation for OXID eShop
- authorization and authentication using JWT
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

## Tests

```bash
$ composer install
$ composer test
```

## Build with

- [GraphQLite](https://graphqlite.thecodingmachine.io/)

## License

MIT, see [LICENSE file](LICENSE).
