# graph-ql-base-module

The main framework for using GraphQL in OXID

<aside class="warning">
This code is still in the `alpha` state, so things
may still be changing before release.
</aside>

## Usage

This framework provides the registration of new
GraphQL types within the OXID eshop.

* Write a new OXID module
* To create a new type, inherit from the abstract
  class `OxidEsales\GraphQl\Type\BaseType`
* Implement the two abstract methods
  
  `getQueriesOrMutations()`: returns an array
    of fields for the `query` or `mutation` type
  
  `getQueryOrMutationHandlers()`: Return an associative
    array of runnables that are to be executed when
    on of the registered fields is queried/changed.
    The keys of this array need to be exactly the
    name of the fields defined in the previous method
    
  Look up the `LoginType` class for an example how
  to implement this.
* Create a `services.yaml` file in your module. This is
  a Symfony DI container configuration file. Here you
  can register your types, for example:
  
  ```yaml
  OxidEsales\GraphQl\Type\LoginType:
      class: OxidEsales\GraphQl\Type\LoginType
      tags: ['graphql_query_type']
  ```
  
  For mutations choose the `graphql_mutation_type` as
  tag.
  
That's it. After activating you module, your type should
be available through the `<shopurl>\graphql` path.

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