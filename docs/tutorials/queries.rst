Implement a query
========================

Product Query
-------------

An example of a simple query would be asking for some product general information. In this
section we will implement the query for getting the product title like so.

**Query**

.. code-block:: graphql

    query {
        product (
            id: "dc5ffdf380e15674b56dd562a7cb6aec"
        ){
            title
        }
    }

**Response**

.. code-block:: json

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

As stated in the `Introduction <tutorials/introduction.html>`_, we need Controller,
DataType, Service, Infrastructure and maybe some Exception. Please also have a look
at the documentation section about the `Architecture <architecture.html>`_ of
our modules.

.. code-block:: bash

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

In the data type we describe all the fields of the product object available for retrieval.
In our case we are interested in product id and title:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/DataType/Product.php
   :caption: src/Product/DataType/Product.php
   :language: php

Repository
^^^^^^^^^^

The Repository class contains relations to the OXID eShop Core. Ideally this layer should be
the only one you need to change if something changes in the shop.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Infrastructure/ProductRepository.php
   :caption: src/Product/Infrastructure/ProductRepository.php
   :language: php

Service
^^^^^^^

The service layer contains the GraphQL module's business logic. In our case its simple found/not found
cases handling:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/Product.php
   :caption: src/Product/Service/Product.php
   :language: php

We'll throw the ``ProductNotFound`` Exception (with a 404 error code) in case the requested
product cannot be found in the shop. Example of Exception class:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Exception/ProductNotFound.php
   :caption: src/Product/Exception/ProductNotFound.php
   :language: php


Controller
^^^^^^^^^^

The Controller takes the request parameters and links it to the business logic located in the service:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Controller/Product.php
   :caption: src/Product/Controller/Product.php
   :language: php

Now we are ready to send a request (you might find more details in `Requests <requests.html>`_) against the API.
