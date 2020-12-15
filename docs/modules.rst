Modules and use cases
=====================

Based on the capabilities the GraphQL-Base module, we at OXID eSales, built
modules to give access to the most frequently used features
of the eShop, through queries and mutations. We are striving to expose all shop features via the GraphQL
API in the end.

You may find all our `GraphQL modules for OXID eShop <https://github.com/OXID-eSales?q=graphql>`_ on GitHub

GraphQL-Base module
-------------------

`GraphQL-Base module <https://github.com/OXID-eSales/graphql-base-module>`_ is here
to provide the basic integration of GraphQL into the OXID eShop. You need this module
installed if you want to use GraphQL within OXID eShop.

GraphQL-Storefront module
-------------------------

The `GraphQL-Storefront module's <https://github.com/OXID-eSales/graphql-storefront-module>`_
mission is to

* provide you with all queries you need to create a view of the catalogue with categories, products
  and everything related.
* expose all queries and mutations for everything account based. It allows you to manage customers
  addresses, as well as newsletter subscription, order history and so on.
* support placing new orders. It is possible to manage multiple userbaskets, add/remove products
  from those baskets, query and chose delivery address, shipping method and payment and in the end
  place the order. See :doc:`Consuming the API - Place an Order <./consuming/PlaceOrder>`.
