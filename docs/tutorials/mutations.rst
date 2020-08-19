Howto: implement a mutation
===========================

Let's consider the case that we'd like to change data through the GraphQL API, say the title of a product.
Having every anonymous user be able to change data is probably not a good idea. In fact we should make sure
only a logged in user is able to update a product's title.

.. important::  This is only an example. In production, only a logged in and authorized admin user should be able to mutate product data. Please see section `Authorization <authorization.html>`_ for a ``PermissionProvider`` example.

We start with the controller this time and (this is entirely done because of step by step tutorial reasons)
add a ``ProductMutation`` controller. There's no need for any changes in the ``Shared\Service\NamespaceMapper``
(see `Queries <tutorials/queries.html>`_), as the Controller directory should already be registered in there.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Controller/ProductMutation.php
   :language: php

In the product mutation service, we need some method that takes care of storing the product:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/ProductMutation.php
   :language: php

and as we can reuse the OXID eShop core functionality to save the application model, let's add some functionality to
the infrastructure layer to deal with this.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Infrastructure/ProductMutationRepository.php
   :language: php

We are not done yet. The request will probably contain the product id (we want to change exactly this product)
and the new title. If we look at the ``Controller\ProductMutation::productTitleUpdate()`` method, it expects
a ``ProductDataType`` as argument. Also we will need a check if the product in question even exists before it can be updated.
So we need some factory to create a ``ProductDataType`` from the input:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/ProductTitleInput.php
   :language: php

and this in turn needs some infrastructure layer part, because we need the OXID eShop model's ``BaseModel::assign()`` method.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Infrastructure/ProductMutation.php
   :language: php

To get a clearer view on what we need for the mutation, here's the relevant file structure

.. code:: shell

    ├── ..
    └── Product
        ├── Controller
        │   └── ProductMutation.php
        ├── DataType
        │   └── Product.php
        ├── Exception
        │   └── ProductNotFound.php
        ├── Infrastructure
        │   ├── ProductMutation.php
        │   └── ProductMutationRepository.php
        └── Service
            ├── ProductTitleInput.php
            └── ProductMutation.php

.. important:: As stated in section `Authorization <authorization.html>`_, when using the ``@Logged`` annotation as done here, we need to send the `token` in the HTTP `Authorization` header in order to see the mutation in the schema.


**Request:**

.. code:: graphql

    mutation {
        productTitleUpdate(
            product: {
                productId: "dc5ffdf380e15674b56dd562a7cb6aec"
                title: "my new product title"
            }
        ){
            id
            title
        }
    }


**Response:**

.. code:: graphql

    {
        "data": {
            "productTitleUpdate": {
                "id": "dc5ffdf380e15674b56dd562a7cb6aec",
                "title": "my new product title"
            }
        }
    }
