Examples for Queries and Mutations
==================================

In the following section we'll show step by step how to implement queries and mutations
for the OXID eShop GraphQL API from scratch.

All we definitely need up and running is the OXID eShop and the GraphQL base module.

Preparations
------------

We don't want to duplicate work like say setting up a brand new graphql module from scratch.
So we already prepared a little something: `Skeleton GraphQL OXID module <https://github.com/OXID-eSales/graphql-module-skeleton>`_

Let's try this out:

    .. code:: shell

        composer create-project oxid-esales/graphql-module-skeleton

We get prompted for vendor and package name and end up with a ready to use module. Give it a repo, install
into OXID eShop via composer, activate.
The module skeleton comes with a Category controller as example, so at this point you can use a GraphQL
client like Altair to verify you see the category query in the schema.

The Namespace mapper
--------------------

Let's assume we want to fetch information for one specific item (let's take a product)
via the OXID eShop GraphQL API which means we need to implement a new Controller and DataType.

So in our module's ``src`` directory, let's add the ``Product`` directory structure:

.. code:: shell

    ├── ..
    └── Product
        ├── Controller
        ├── DataType
        ├── Exception
        ├── Infrastructure
        └── Service

Have a look at the ``Shared/Service/NamespaceMapper.php``, this is the place to register
controller and type namespaces.

.. code:: shell

    ├── ..
        ├── Shared
        │   └── Service
        │       └── NamespaceMapper.php
        ..


.. literalinclude:: examples/tutorials/mygraph/src/Shared/Service/NamespaceMapper.php
   :language: php

Without the namespace mapper, we'd have to update the module's services.yaml every time we add something new.


Very simple query
-----------------

A simple query would be to ask for the title of product.

.. important:: The products in OXID eShop are multilanguage models. The language id ist not given in the query itself but is set as parameter of the GraphQL endpoint url same as the shop id. So we usually we don't need to bother with shop id or language, shop and GraphQl base module are handling this for us.

As stated in the last section, we need Controller, DataType and maybe some exception, service and infrastructure.
Please also have a look at the documentation section about the `Architecture <architecture.html>`_ of our modules.

.. code:: shell

    ├── ..
    └── Product
        ├── Controller
        │   └── Product.php
        ├── DataType
        │   └── Product.php
        ├── Exception
        │   └── ProductNotFound.php
        ├── Infrastructure
        │   └── ProductRepository.php
        └── Service
            └── Product.php


First thing we need is the data type:

.. literalinclude:: examples/tutorials/mygraph/src/Product/DataType/Product.php
   :language: php

In the product repositry is the place where we connect to the OXID eShop Core. This is the only place where
for example the eShop models are accessed:

.. literalinclude:: examples/tutorials/mygraph/src/Product/Infrastructure/ProductRepository.php
   :language: php

The service layer the holds the GraphQL module's business logic:

.. literalinclude:: examples/tutorials/mygraph/src/Product/Service/Product.php
   :language: php

Exception in case the requested product cannot be found in the database, we'll answer with a 404 in this case:

.. literalinclude:: examples/tutorials/mygraph/src/Product/Exception/ProductNotFound.php
   :language: php

The Controller takes the request and relays it to the business logic located in the service:

.. literalinclude:: examples/tutorials/mygraph/src/Product/Controller/Product.php
   :language: php

So now we are ready to send a request (you might find more details in `Requests <requests.html>`_) against the API

::

    https://<your host address>/graphql/?shp=1&lang=1

**Request:**

        .. code:: graphql

            query {
                product (
                    id: "058e613db53d782adfc9f2ccb43c45fe"
                ){
                    title
                }
            }

**Response:**

        .. code:: graphql

            {
                "data": {
                    "product": {
                        "title": "Bindung O&#039;BRIEN DECADE CT 2010",
                    }
                }
            }

.. important:: Usually you'll need to protect some parts of your API against unauthorized access. This is described in section `Authorization. <authorization.html>`_


Relation to other data types
----------------------------

Let's try to extend the query a little and also fetch information about the product's manufacturer.
We might be tempted to simply extend the product DataType to have a field for the manufacturer id, but
please check the hints given in `Specification <specification.html>`_ section. The manufacturer should get
its own datatype and then get a relation to the product.
So let's add this:








Mutation
--------

TODO


Beware the OXID eShop Enterprise Edition
----------------------------------------

TODO


