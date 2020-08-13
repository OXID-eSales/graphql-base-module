Architecture
============

For the GraphQL modules we build we choose to use a hexagonal architecture, also known as ports and adapters. It might not be the perfect implementation, so when you see something, feel free to ping us or make a pull request.

If you are interessted in understading the overall picture, Zvonimir Spajic created a `blog post demystifing hexagonal architecture <https://madewithlove.com/hexagonal-architecture-demystified/>`_.

The filesystem hirachy in the ``src`` directory looks something like this

.. code:: shell

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

The folders in the ``src`` direcotory are the contexts in which we are working. All models currently have a `Shared` context and then a context for every entity we need to expose via the GraphQL API.

Controller
----------

The sole purpose of the controller is to be a slim layer to translate from the incomming HTTP request to our business logic. As we are using `GraphQLite <https://graphqlite.thecodingmachine.io/docs/3.0/features.html>`_ and `graphql-php <https://webonyx.github.io/graphql-php/>`_ our controller don't need any kind of validation, as all this is done in those libraries. This on the other hand is based on the controller methods using the correct type hints.

So most controllers look like this example

.. literalinclude:: examples/architecture/Controller.php
   :language: php

Business
--------

The data types in `DataType` directory and services in `Service` directory belong to the business layer. This is where the business rules are implemented, business rules as in "is the current authenticated user allowed to see this entity" for example.

Data types
^^^^^^^^^^

Classes in `DataType` are a facade to the OXID eShop models in our case and wrap them, also they define the fields of our data types in the GraphQL API. They are allowed to call methods on OXID eShop models directly, althought not part of the infrastructure layer. We are aware, that this violates the hexagonal approach, but yet struggle to come up with a good way to deal with this.

A sample data type might look like this

.. literalinclude:: examples/architecture/DataType.php
    :language: php

Services
^^^^^^^^

Services are to be called from the controllers to retrieve the entities in question. It might be tempting, but they are not allowed to talk to the persistence (e.g. Database) directly. A service can use the infrastructure layer to fetch an entity and decide whether it can be returned to the caller or not based on rules to be implemented in the service itself.

A sample service might look like this

.. literalinclude:: examples/architecture/Service.php
   :language: php

Infrastructure
--------------

The purpose of the infrastructure layer is to fetch entities from the persistence, in our case mostly the database itself. For most of our cases we can just use the `repository from the graphql-catalogue module <https://github.com/OXID-eSales/graphql-catalogue-module/blob/master/src/Shared/Infrastructure/Repository.php>`_.

You are also free to create your own repository or infrastructure service to communicate with the OXID eShop.

Reasons
-------

for Hexagonal approach
----------------------

When ever something in the OXID eShop changes we only need to adapt in the infrastructure layer (and in this case the data types). But there is no need to search through a lot of files, we know pretty fast where things are going wrong and need to be changed.

for wrapping OXID eShop models
------------------------------

We did not want to reinvent the wheel and re-implement everything that is already there in the core. Also it would lead a lot of confusion if the GraphQL API would behave different from the rest of the systems.

One of the main reasons to use OXID eShop models through the ``oxNew()`` call is that the GraphQL API then also reflects the changes your modules introduce. This is also the reason, why we use the specific getters and only fall back to using ``getFieldData()`` if none is available.


