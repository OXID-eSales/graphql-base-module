Partially Successfull
=====================

GraphQL allows for the response to have a ``data`` and ``errors`` key. If you
have a situation where you can recover from an error during GraphQL execution,
you may catch the Exception instead of letting it bubble all the way up and pass
it to the ``\OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler::addError()``
static method.

.. important::
   Keep in mind, that the response in the ``data`` key still has to be valid
   against your schema.

All exceptions given to the ``addError()`` static method will be delivered to
the consumer of the API in the ``errors`` key.

Use case
--------

Imaging you query a list of products with the following query, the ``ean`` field
is defined as ``String!`` but the ``ean`` field resolver returns ``null``. This
will throw an ``Exception`` that you may catch in the ``products`` resolver,
replace the ``product`` in this case with ``null`` in the list and add an error
response by forwarding the ``Exception`` to the
``\OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler::addError()`` method.

If you do not catch this ``Exception`` in the products resolver, the consumer of
your API will only get an error response without any data.


**Request:**

.. code-block:: graphql

    query {
        products (
            title
            ean
        )
    }

**Response:**

.. code-block:: json

    {
        "errors": [
            {
                "message": "EAN for product 1234 could not be fetched",
            }
        ],
        "data": {
            "products": [
                {
                    "title": "Article 1",
                    "ean": "some-ean"
                },
                null
            ]
        }
    }
