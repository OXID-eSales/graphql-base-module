Implement a mutation
===========================

Having the query for the products it is time to create a mutation to change
products. Let's keep it simple and just change the product title.

Anonymous users being able to change data is probably not a good idea. In fact
we should make sure only a logged in user is able to update a product's title.

.. important::  This is only an example. In production, only a logged in and authorized admin user should be able to mutate product data. Please see section `Authorization <authorization.html>`_ for a ``PermissionProvider`` example.

We start with the controller this time and (this is entirely done because of step by step tutorial reasons)
add the mutation to the ``Product`` controller we build earlier.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Controller/ProductMutation.php
   :caption: src/Product/Controller/Product.php
   :language: php

In the product service, we need to add the method that takes care of storing the product

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/ProductMutation.php
   :caption: src/Product/Service/Product.php
   :language: php

As we can reuse the OXID eShop core functionality to save the application model, let's add some functionality to
the infrastructure layers ``ProductRepository`` from the query tutorial to deal with this.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Infrastructure/ProductMutationRepository.php
   :caption: src/Product/Infrastructure/ProductRepository.php
   :language: php

We are not done yet. The request will probably contain the product id (we want to change exactly this product)
and the new title. If we look at the ``Controller\Product::productTitleUpdate()`` method, it expects
a ``ProductDataType`` as argument. Also we will need a check if the product in question even exists before it can be updated.
So we need some factory to create a ``ProductDataType`` from the input:

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/ProductTitleInput.php
   :caption: src/Product/Service/ProductTitleInput.php
   :language: php

This in turn needs some infrastructure layer part, because we need the OXID eShop model's ``BaseModel::assign()`` method.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Infrastructure/ProductMutation.php
   :caption: src/Product/Infrastructure/ProductMutation.php
   :language: php

To get a clearer view on what we need for the mutation, here's the relevant
file structure after the changes from this tutorial applied.

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
        │   ├── ProductMutation.php
        │   └── ProductRepository.php
        └── Service
            ├── ProductTitleInput.php
            └── Product.php

.. important:: As stated in section `Authorization <authorization.html>`_, when using the ``@Logged`` annotation as done here, we need to send the `token` in the HTTP `Authorization` header in order to see the mutation in the schema.


**Mutation**

.. code-block:: graphql

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


**Response**

.. code-block:: json

    {
        "data": {
            "productTitleUpdate": {
                "id": "dc5ffdf380e15674b56dd562a7cb6aec",
                "title": "my new product title"
            }
        }
    }
