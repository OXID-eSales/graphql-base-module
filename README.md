# graph-ql-base-module

The main framework for using GraphQL in OXID

<aside class="warning">
This code is still in the `alpha` state, so things
may still be changing before release.
</aside>

## The basics

This framework provides the registration of new
GraphQL queries and mutations within the OXID eshop.
This is all you need to do:

* Create a new OXID module

* To create a new query or mutation, implement either the
  `OxidEsales\GraphQl\Type\Provider\QueryProviderInterface`
  or the
  `OxidEsales\GraphQl\Type\Provider\MutationProviderInterface`.
  You may implement both interfaces in one class, if you
  provide queries and mutations that complement each others.

* For each interface you have to implement two methods.
  The first method `getQueries()` or `getMutations()`
  need to return an array of graphQL field definitions
  that will be added to the fields of the `query` or
  the `mutation` graphQL type.
  
  The second method `getQueryResolvers()` or `getMutationResolvers()`
  needs to return resolvers for the query or mutation fields
  defined in the field methods. Each handler is just a
  closure that will be called when a query or a mutation is
  requested. You may
  look into the `LoginQueryProvider` class or the
  `UserMutationProvider` for examples.
  
* Create a `services.yaml` file in your module. This is
  a Symfony DI container configuration file. Here you
  can register your types, for example:
  
  ```yaml
  OxidEsales\GraphQl\Type\LoginType:
      OxidEsales\GraphQl\Type\Provider\LoginQueryProvider
      tags: ['graphql_query_provider']
  ```
  
  For mutations choose the `graphql_mutation_provider` as
  tag. And if you have queries and mutations in one class,
  put both tags on the service.
  
That's it. After activating you module, your type should
be available through the `<shopurl>\graphql` path.

## Defining a query / mutation field

GraphQL queries or mutations are just fields on the
`query` respective `mutation` node in the GraphQL schema
tree. These basic GraphQL nodes will created automatically by the
framework when the schema is build. The different
queries and mutations that will be attached to these
nodes are defined in so called provider classes.

This allows of completely independetly implemented
queries and mutations that finally are strung together
by the DI container. So there may be an arbitrary
number of modules that provide some GraphQL interface
to the OXID eShop.

The `getQueries()` method to provide two login queries
might for example look like this:

```php
public function getQueries()
{
  $loginType = new LoginType();

  return [
    'login'  => [
      'type'        => $loginType,
      'description' => 'Returns a jason web token according to the provide credentials. ' .
      'If no credentials are given, a token for anonymous login is returned.',
      'args'        => [
        'username' => Type::string(),
        'password' => Type::string(),
        'lang' => Type::string(),
        'shopid' => Type::int()
      ],
    ],
    'setlanguage'  => [
      'type'        => $loginType,
      'description' => 'Changes the language in the current auth token, signs it again ' .
      'and returns the changed token.',
      'args'        => [
        'lang' => Type::nonNull(Type::string())
      ],
    ]
  ];
}
```

## Writing a query / mutation handler

As we said above, a query or mutation resolver is just
a closure. The following is an example from the
`UserMutationProvider`:

```php
['createUser' => function ($value, $args, $context, ResolveInfo $info) {
   /** @var AppContext $context */
   if ($args['user']['usergroup'] == AuthConstants::USER_GROUP_CUSTOMERS) {
      $this->permissionService->checkPermission($context->getAuthToken(), ['maycreatecustomer', 'maycreateanyuser']);
   }
   else {
      $this->permissionService->checkPermission($context->getAuthToken(), 'maycreateanyuser');
   }
   return $this->userService->saveUser($args, $context->getAuthToken()->getShopid());
}]
```

The handler closure should be as lean as possible. Think of
it as some sort of controller. So no business logic should go
to this handler. The only thing that really should happen here is the
permissions check. Then everything else should be delegated to
some service, in the case of the example the `userService` (this
service is actually injected into the provider class through
the DI container; we advise you to do the same with your
services).

Every handler should perform a permissions check. The one and
only query that does not check any permission is the login
query, because on login you do not have any permissions at all - 
except the implicit permission to log in.

When logging in, you will receive a token back that from now
on always should be provided in the `Authorization` header of
your request. This token determines (among other things) your
permissions.

As a logged in user you belong to one of (currently) four user
groups:

- anonymous
- customer
- shopadmin
- admin 

And these groups may have different permissions. Which group
has which permission is completely configurable. The
`PermissionService` (that you should
inject via DI into your query or mutation provider) provides
the `checkPermisson` method which takes an authorization
token and the requested permission (just an arbitrary string)
or an array of permissions. In the latter case the user needs
at least one of
the provided permissions. If the permission check fails, an
exception is thrown. An appropriate error message will show
up in the errors section of the GraphQL answer provided to
the client.

Permissions are configured via the DI container. Since every
module should be capable to define its own permissions,
there is a special service named `PermissionProvider`.
This provider class may be used multiple times in the DI
container, it is just necessary to use unique keys for
every instance.
Then tag the permission provider with `graphql_permission_provider`
and add the permissions you need for your query or mutation.

This is an example for the permissions above:

```yaml
  OxidEsales\GraphQl\Service\UserPermissionsProvider:
    class: OxidEsales\GraphQl\Service\PermissionsProvider
    calls:
      - ['addPermission', ['anonymous', 'maycreatecustomer']]
      - ['addPermission', ['admin', 'maycreateanyuser']]
      - ['addPermission', ['customer', 'mayupdateself']]
      - ['addPermission', ['shopadmin', 'mayupdateself']]
      - ['addPermission', ['admin', 'mayupdateanyuser']]
    tags: ['graphql_permissions_provider']
``` 

## The OXID GraphQL framework authorization

Authorization is implemented by using Jason Web Tokens (JWT). So
the first step for a consumer of the GraphQL API is to
obtain such a token. It is just a simple query named
login that requests a token:

```yaml
query InitialLogin {
    login {
        token       
    }
}
```

Since no parameters are provided, just an anonymous token
is returned. But this token has, apart from the default
JWT properties, some OXID eShop specific properties.
Crucial are the properties for shopid and language. All
queries that are authorized by a certain token will query
the database with the given shop id and will return the
result in the given language.

In the example above, where no shopid and language were
given as parameters, the token contains the default shopid
and the default language. You can choose differently
by logging in with the appropriate parameters:

```yaml
query InitialLogin {
    login (shopid: 2, lang: "en") {
        token       
    }
}
```

Now you will get a token for the shop with id 2 and
the language english (there is no check on these
input parameters; just subsequent queries / mutations
will fail when you use nonexisting values).

It is also possible to change the language on the token.
For this purpose another query named `setlanguage` is
defined:

```yaml
query LanguageChange {
    setlanguage (lang: "en") {
        token       
    }
}
```

With this query you get an updated token with a
different language. The crucial point is: It is the
same token with the same token id. This token
id may be used, for example, to identify a
user basket. And since the different language does
not change the token id, the basket stays the same,
but may now be shown in a different language.

It is not possible to change the shop id in such
a way. The reason for this is exactly the same:
If you change the shop (which you can easily do
by requesting a new token), then for example
a basket, that is tied to the old token, should not
be referenced by the new token, because the
items in the basket may not be available in the
other shop. So if you want to switch shops,
request another token.

All these logins and language changes where
anonymous. So the token identified the user
as being in group 'anonymous', one of the
four possible groups defined by the OXID
GraphQL base module.

The 'anonymous' group has quite restricted
permissions. So for doing more complex stuff,
like ordering, for example, you need a
not anonymous user.

So the `login` query allows you also to
log in as a specific user known by the shop
database. You only need to provide the
username and passeword:


```yaml
query UserLogin {
    login (username: "baerchen", password: "supersecure") {
        token       
    }
}
```

If username, password and shopid match (if no
shopid is given, again the default shopid is
used), you get back a token for this registered
user. And depending on the user rights in the
`OXUSER` table this token identifies the user
as being part of one of the other user groups:
`customer`, `shopadmin` or `admin`.

There is a special bonus, if an anonymous token
is provided when logging in: The new token will
have the same token id as the anonymous token.
This then allows for example to keep a basket,
that already has been created while being logged
in anonymously.

And the OXID GraphQL base module also allows
creation and modification of users. An anonymous
user for example may create a new customer and
then log in as this customer. And an admin user
may create any user for any group.

TODO: Currently the user object does not have
all the user fields or the possibility to add
more than one address etc.

## Installation

Use composer to install this module. It needs the currently
unreleased OXID eShop from the `b-6.x` branch.

Additionally you need to insert the following line into you
`.htaccess` file before the line with 
`RewriteCond %{REQUEST_URI} config\.inc\.php [NC]`:

```
RewriteRule ^(graphql/) widget.php?cl=graphql   [NC,L]
```
## Testing

To run the tests, change the `test_config.yml` file of your
project. Add `'oe/graphql-base'` to the `partial_module_paths`
and set `activate_all_modules` to `true`.