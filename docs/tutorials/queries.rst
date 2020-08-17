Queries
=======


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

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/DataType/Product.php
   :language: php

In the product repositry is the place where we connect to the OXID eShop Core. This is the only place where
for example the eShop models are accessed:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Infrastructure/ProductRepository.php
   :language: php

The service layer the holds the GraphQL module's business logic:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/Product.php
   :language: php

Exception in case the requested product cannot be found in the database, we'll answer with a 404 in this case:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Exception/ProductNotFound.php
   :language: php

The Controller takes the request and relays it to the business logic located in the service:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Controller/Product.php
   :language: php

So now we are ready to send a request (you might find more details in `Requests <requests.html>`_) against the API

::

    https://<your host address>/graphql/?shp=1&lang=1

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

.. important:: Usually you'll need to protect some parts of your API against unauthorized access. This is described in section `Authorization. <authorization.html>`_


Relation to other data types
----------------------------

Let's try to extend the query a little and also fetch information about the product's manufacturer.
We might be tempted to simply extend the product DataType to have a field for the manufacturer id, but
please check the hints given in `Specification <specification.html>`_ section. The manufacturer should get
its own datatype and then get a relation to the product.
So let's add the data type:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/DataType/Manufacturer.php
   :language: php

Also we need the connection to OXID eShop's ``OxidEsales\Eshop\Application\Model\Article::getManufacturer()`` which
belongs in the infrastructure layer:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Infrastructure/Product.php
   :language: php

And then we relate it to the product by using the ``@ExtendType()`` notation.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/RelationService.php
   :language: php

.. important:: In this case, the relation service needs to be registered in ``NamsepaceMapper::getTypeNamespaceMapping()``.

So now we are ready for the next query:

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

