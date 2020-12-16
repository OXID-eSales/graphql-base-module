Extending a type
=================

In case you need to add fields to data types provided by another module (even OXID's GraphQL modules) you can just use the `Extending a type feature in GraphQLite <https://graphqlite.thecodingmachine.io/docs/3.0/extend_type>`_.

.. note::
    Extending input types is not possible with GraphQLite ``v3``, this feature will be available after switching to GraphQLite ``v4``

Every data type can be extended via the ``@ExtendType`` annotation multiple times, even in the same module.

.. important::
    Types, Controllers, Services, ... can not be extended through OXID's module chain!

In case you wonder, see description about :ref:`why we made classes final<final-classes>`.

Extend the product type
-----------------------

Given that you have

- a custom module that

    - adds a custom ``subtitle`` field to the ``oxarticle`` database table
    - extends the ``OxidEsales\Eshop\Application\Model\Article`` model with a ``subtitle()`` method

- want this field exposed via the GraphQL API
- have installed the ``oxid-esales/graphql-storefront`` module

Create the extend type
^^^^^^^^^^^^^^^^^^^^^^

The ``@ExtendType`` annotation has a ``class`` argument which has to be the data type you want to extend. That being the PHP class with the ``@Type`` annotation.

.. literalinclude:: examples/extending/ProductExtendType.php
   :language: php

The first argument is always the class you are extending. When a client asks for your field, you'll get the original data type and can then fetch the OXID eShop model from it in case needed.

This results in the following GraphQL schema

.. literalinclude:: examples/extending/ProductExtendType.graphql
   :language: graphql

Register type
^^^^^^^^^^^^^

For GraphQLite to find your newly created extend type

- it must be registered via the namespace mappers ``getTypeNamespaceMapping()``
- needs to exist in the DI container and the container identifier MUST be the fully qualified class name

Namespace mapper
""""""""""""""""

.. literalinclude:: examples/extending/NamespaceMapper.php
   :language: php

services.yaml
"""""""""""""

.. literalinclude:: examples/extending/services.yaml
   :language: yaml

Best practice
-------------

Try to withstand the urge to create another module for this case and integrate the GraphQL type mapping into your existing module. That way you do not end up with an invalid state when activating/deactivating a module. Otherwise you might deactivate the module that adds the ``subtitle()`` method to the ``OxidEsales\Eshop\Application\Model\Article`` class. The ``subtitle`` field is still in your GraphQL schema, but requesting that field will result in a PHP Fatal Error.
