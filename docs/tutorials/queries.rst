Implementing the Query
======================

Simple Query
------------

Example of a simple query would be - asking for some product general information. In this
section we will implement the query for getting product title:

**Request:**

    .. code:: graphql

        query {
            product (
                id: "dc5ffdf380e15674b56dd562a7cb6aec"
            ){
                title
            }
        }

**Response:**

    .. code:: graphql

        {
            "data": {
                "product": {
                    "title": "Kuyichi Ledergürtel JEVER",
                }
            }
        }

.. important::
    Products in OXID eShop are multilanguage models. The language id
    is not given in the query itself but is set as parameter of the GraphQL endpoint
    url same as the shop id. So we usually don't need to think much about handling the
    shop id or language parameters - shop and GraphQl base module are handling this for us.


File structure
^^^^^^^^^^^^^^

As stated in the Introduction: we need Controller, DataType, Service, Infrastructure and maybe some Exception.
Please also have a look at the documentation section about the `Architecture <architecture.html>`_ of our modules.

.. code:: shell

    ├── ..
    └── Product
        ├── Controller
        │   └── Product.php
        ├── DataType
        │   └── Product.php
        ├── Exception
        │   └── ProductNotFound.php
        ├── Infrastructure
        │   └── ProductRepository.php
        └── Service
            └── Product.php


Data type
^^^^^^^^^

In data type we should describe all the fields of product object available for the retrieval.
In our case we are interested in product id and title:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/DataType/Product.php
   :language: php

.. note::
    ``getEshopModel`` method will be used later in examples of data type relations. Feel free
    to ignore that one for now.

Repository
^^^^^^^^^^

The Repositry class contains relations to the OXID eShop Core. Ideally this layer should be
the only one you need to change if something changes in the shop.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Infrastructure/ProductRepository.php
   :language: php


Service
^^^^^^^

The service layer contains the GraphQL module's business logic. In our case its simple found/not found
cases handling:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/Product.php
   :language: php

We'll throw the ProductNotFound Exception (with a 404 error code) in case the requested
product cannot be found in the shop. Example of Exception class:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Exception/ProductNotFound.php
   :language: php


Controller
^^^^^^^^^^

The Controller takes the request parameters and links it to the business logic located in the service:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Controller/Product.php
   :language: php

So now we are ready to send a request (you might find more details in `Requests <requests.html>`_) against the API

::

    https://<your host address>/graphql/?shp=1&lang=1

.. important::
    Usually you'll need to protect some parts of your API against unauthorized access. This
    is described in an `Authorization. <authorization.html>`_ section.


Relation with other Data Types
------------------------------

Let's try to extend the query a little and also fetch information about the product's manufacturer:

**Request:**

.. code:: graphql

    query {
        product (
            id: "dc5ffdf380e15674b56dd562a7cb6aec"
        ){
            title
            manufacturer {
                id
                title
            }
        }
    }

**Response:**

.. code:: graphql

    {
        "data": {
            "product": {
                "title": "Kuyichi Ledergürtel JEVER",
                "manufacturer": {
                    "id": "9434afb379a46d6c141de9c9e5b94fcf",
                    "title": "Kuyichi"
                }
            }
        }
    }

.. important:: As stated in the `Specification <specification.html>`_ section, if no manufacturer can be found for the product, we get a null.

Extending the product DataType
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

We might be tempted to simply extend the product DataType to have a field for the manufacturer id, but
please check the hints given in `Specification <specification.html>`_ section. The manufacturer should get
its own datatype and then get a relation to the product.
So let's add the Manufacturer Data Type:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/DataType/Manufacturer.php
   :language: php

Also we need the connection to OXID eShop's ``OxidEsales\Eshop\Application\Model\Article::getManufacturer()`` which
belongs in the infrastructure layer:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Infrastructure/Product.php
   :language: php

And then we relate it to the product by creating RelationService and using the ``@ExtendType()`` notation.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/RelationService.php
   :language: php

.. important:: In this case, the relation service needs to be registered in ``NamsepaceMapper::getTypeNamespaceMapping()``.

At this point, running our extended query should be possible.
