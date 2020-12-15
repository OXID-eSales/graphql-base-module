Architecture
============

For the GraphQL modules we choose a hexagonal architecture, also known as ports and adapters. In case you see some way to improve this implementation, feel free to ping us or make a pull request any time.

If you are interested in understanding the overall picture, Zvonimir Spajic created a blog post about `demystifing hexagonal architecture <https://madewithlove.com/hexagonal-architecture-demystified/>`_.

The file system hierarchy in the ``src`` directory looks similar to this

.. code-block:: bash

    ├── ..
    ├── Shared
    │   └── Infrastructure
    │       └── Repository.php
    └── Category
        ├── Controller
        │   └── Category.php
        ├── DataType
        │   ├── CategoryFilterList.php
        │   └── Category.php
        ├── Exception
        │   └── CategoryNotFound.php
        └── Service
            ├── Category.php
            └── RelationService.php

The folders in the ``src`` directory are the contexts in which we are working. All models currently have a `Shared` context and then a context for every entity we need to expose via the GraphQL API.

Controller
----------

The sole purpose of the controller is to be a slim layer which translates from the incoming HTTP request to our business logic. As we are using `GraphQLite <https://graphqlite.thecodingmachine.io/docs/3.0/features.html>`_ and `graphql-php <https://webonyx.github.io/graphql-php/>`_ our controllers don't need any kind of validation, as all this is done in those libraries. This on the other hand is relying on the controller methods to use the correct type hints.

So most controllers look like this example

.. literalinclude:: examples/architecture/Controller.php
   :language: php

Business
--------

The data types in `DataType` directory and services in `Service` directory belong to the business layer. This is where the business rules are implemented, business rules as for example in "is the currently authenticated user allowed to see this entity".

Data types
^^^^^^^^^^

Classes in `DataType` are a facade to the OXID eShop models in our case and wrap them, also they define the fields of our data types in the GraphQL API. They are allowed to call methods on OXID eShop models directly, although not part of the infrastructure layer. We are aware, that this violates the hexagonal approach, but yet struggle to come up with a good way to deal with this.

A sample data type might look like this

.. literalinclude:: examples/architecture/DataType.php
    :language: php

Services
^^^^^^^^

Services are to be called from the controllers to retrieve the entities in question. It might be tempting, but they are not allowed to talk to the persistence (e.g. Database) directly. A service can use the infrastructure layer to fetch an entity and decide whether it can be returned to the caller or not based on rules to be implemented in the service itself.

A sample service might look like this

.. literalinclude:: examples/architecture/Service.php
   :language: php

Additionally to this, we put the relation services for the data types into the ``Service`` directory. Those are using the ``@ExtendType`` annotation an need to exist in the DI container under their fully qualified class name. Those relation services do not call OXID models directly, but only through the infrastructure layer.

Infrastructure
--------------

The purpose of the infrastructure layer is to fetch entities from the persistence, in our case mostly the database itself. For most of our cases we can just use the `repository from the graphql-storefront module <https://github.com/OXID-eSales/graphql-storefront-module/blob/master/src/Shared/Infrastructure/Repository.php>`_.

You are also free to create your own repository or infrastructure service to communicate with the OXID eShop.

Reasons
-------

for Hexagonal approach
^^^^^^^^^^^^^^^^^^^^^^

Whenever something in the OXID eShop changes we only need to adapt it in the infrastructure layer (and in this case the data types). But there is no need to search through a lot of files, we know pretty fast where things are going wrong and need to be changed.

for wrapping OXID eShop models
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

We wanted to have the GraphQL API behave consistent to the (well known) existing system. The OXID eShop models are the central point which standard system and the GraphQL modules can share.

One of the main advantages to use OXID eShop models through the ``oxNew()`` call is that the GraphQL API then also reflects the changes your modules introduce. This is also the reason, why we use the specific getters and only fall back to using ``getFieldData()`` if no specific getter is available.

.. _final-classes:

for making classes final
^^^^^^^^^^^^^^^^^^^^^^^^

You might have noticed, that all classes in the GraphQL modules are ``final``. Therefore they can not be extended through OXID's module chain. It is technically not possible to extend data types by extending the class in the usuall PHP way anyway and we favour `composition over inheritance <https://en.wikipedia.org/wiki/Composition_over_inheritance>`_.

The ``final`` class declaration makes this concept explicit!
