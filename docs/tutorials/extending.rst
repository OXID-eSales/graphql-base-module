Extend the data type
====================

Let's try to extend the GraphQL schema a little so we are able to fetch
information about the manufacturer assigned to a product.

**Query**

.. code-block:: graphql

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

**Response**

.. code-block:: json

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

.. important::
   As stated in the `Specification <specification.html>`_ section, if no manufacturer can be found for the product, we get a null.

We might be tempted to simply extend the product DataType to have a field for the manufacturer id, but
please check the hints given in `Specification <specification.html>`_ section. The manufacturer should get
its own datatype and then get a relation to the product. So let's add the Manufacturer Data Type:

.. literalinclude:: ../examples/tutorials/mygraph/src/Manufacturer/DataType/Manufacturer.php
   :caption: src/Manufacturer/DataType/Manufacturer.php
   :language: php

Also we need the connection to OXID eShop's ``OxidEsales\Eshop\Application\Model\Article::getManufacturer()`` which
belongs in the infrastructure layer:

.. literalinclude:: ../examples/tutorials/mygraph/src/Manufacturer/Infrastructure/Manufacturer.php
   :caption: src/Manufacturer/Infrastructure/Manufacturer.php
   :language: php

And then we relate it to the product by implementing a RelationService and using the ``@ExtendType()`` notation.

.. literalinclude:: ../examples/tutorials/mygraph/src/Product/Service/RelationService.php
   :caption: src/Product/Service/RelationService.php
   :language: php

Please remember as this is a new type for GraphQL, it needs to be
registered in the ``NamespaceMapper::getTypeNamespaceMapping()`` method!

At this point, running our extended query will be possible.
