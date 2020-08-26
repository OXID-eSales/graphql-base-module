Introduction
============

In the following sections we'll show how to implement queries and mutations
for the OXID eShop GraphQL API from scratch, step by step.

To start with your own module, you will definitely need an up and running OXID eShop with the GraphQL base module installed.

Preparations
------------

We don't want to duplicate work like say setting up a brand new graphql module from scratch.
So we already prepared a little something: `Skeleton GraphQL OXID module <https://github.com/OXID-eSales/graphql-module-skeleton>`_

Let's try this out:

.. code-block:: bash

    composer create-project oxid-esales/graphql-module-skeleton

We get prompted for vendor and package name and end up with a ready to use module. Give it a repo, install
into OXID eShop via composer, activate.
The module skeleton comes with a Category controller as example, so at this point you can use a GraphQL
client like Altair to verify you see the category query in the schema.

The Namespace mapper
--------------------

Let's assume we want to fetch information for one specific item (let's take a product)
via the OXID eShop GraphQL API, which means we need to implement a new Controller and DataType.

So in our module's ``src`` directory, let's add the ``Product`` directory structure:

.. code-block:: bash

    ├── ..
    └── Product
        ├── Controller
        ├── DataType
        ├── Exception
        ├── Infrastructure
        └── Service

Have a look at the ``Shared/Service/NamespaceMapper.php``, this is the place to register
controller and type namespaces.

.. code-block:: bash

    ├── ..
        ├── Shared
        │   └── Service
        │       └── NamespaceMapper.php
        ..


.. literalinclude:: ../examples/tutorials/mygraph/src/Shared/Service/NamespaceMapper.php
   :language: php
