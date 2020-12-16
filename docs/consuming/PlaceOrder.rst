Place an Order
==============

.. important::
   To place an order you need the  `GraphQL Storefront module
   <https://github.com/OXID-eSales/graphql-storefront-module/>`_ installed which
   is not available in a stable release as of this writing

The big picture
---------------

In order to successfully place an order via the GraphQL API you need to first

- create a basket and fill with products
- set a delivery address (in case it is different from invoice address)
- set desired delivery option
- set desired payment option
- and finally place the order

.. important::
   Ordered Basket will be removed on order creation!

Keep in mind, that you will need to send a valid
JWT in Authorization header for any of the following queries or mutations.

.. uml::
   :align: center

   "Client (PWA)" -> "GraphQL API": basketCreate mutation
   "Client (PWA)" <- "GraphQL API": Basket datatype or error
   "Client (PWA)" -> "GraphQL API": basketAddProduct mutation
   "Client (PWA)" <- "GraphQL API": Basket datatype or error
   "Client (PWA)" -> "GraphQL API": basketDeliveryMethods query
   "Client (PWA)" <- "GraphQL API": array of DeliveryMethod data types
   "Client (PWA)" -> "GraphQL API": basketSetDeliveryMethod mutation
   "Client (PWA)" <- "GraphQL API": Basket datatype or error
   "Client (PWA)" -> "GraphQL API": basketPayments query
   "Client (PWA)" <- "GraphQL API": array of Payment data types
   "Client (PWA)" -> "GraphQL API": basketSetPayment mutation
   "Client (PWA)" <- "GraphQL API": Basket datatype or error
   "Client (PWA)" -> "GraphQL API": placeOrder mutation
   "Client (PWA)" <- "GraphQL API": Order datatype or error


Setup the basket
----------------

As there is no server side session available and the GraphQL API is as explicit
as possible, your first step towards placing an order is to create a basket via
the ``basketCreate`` mutation:

.. code-block:: graphql
   :caption: call to ``basketCreate`` mutation

    mutation {
        basketCreate(
            basket: {
                title: "myBasket",
                public: false
            }
        ){
            id
        }
    }

.. code-block:: json
   :caption: ``basketCreate`` mutation response

    {
        "data": {
            "basketCreate": {
                "id": "310e50a2b1be309b255d70462cd75507"
            }
        }
    }

It is your responsibility to store this ID locally, as you will need it to add
products to this basket as well as to do any other preparation and the checkout.

If you happen to "forget" the ID, you can fetch all baskets belonging to a user
via the ``baskets`` field in the ``customer`` query.

This newly created basket is empty, so let's add a product to it.

.. code-block:: graphql
   :caption: call to ``basketAddProduct`` mutation

   mutation {
        basketAddProduct(
            basketId: "310e50a2b1be309b255d70462cd75507",
            productId:"05848170643ab0deb9914566391c0c63",
            amount: 1
        ) {
            items {
                amount
                product {
                    id
                    title
                }
            }
        }
    }

.. code-block:: json
   :caption: ``basketAddProduct`` mutation response

    {
        "data": {
            "basketAddProduct": {
                "items": [
                    {
                        "amount": 1,
                        "product": {
                            "id": "05848170643ab0deb9914566391c0c63",
                            "title": "Trapez ION MADTRIXX"
                        }
                    }
                ]
            }
        }
    }

It is also possible for you to add a voucher to your basket. In order to do that,
you need to know the number of an existing and available voucher that you could use.
If the voucher does not exist or otherwise is not applicable, the API will return
an error with a proper message.

.. code-block:: graphql
   :caption: call to ``basketAddVoucher`` mutation

    mutation {
        basketAddVoucher(
            basketId: "310e50a2b1be309b255d70462cd75507",
            voucherNumber: "MyVoucher"
        )
        {
            id
            vouchers{
              number
            }
        }
    }

In case the voucher exists and is applicable, the following response will be returned:

.. code-block:: json
   :caption: ``basketAddVoucher`` mutation response

    {
        "data": {
            "basketAddVoucher": {
                "id": "e461fcdcda96b96b9a89a7d0fdc956eb",
                "vouchers": [
                    {
                      "number": "MyVoucher"
                    }
                ]
            }
        }
    }


Set the desired delivery option
-------------------------------

In order to set your desired delivery option, you need to know the available
delivery options for this basket. You may query those via the
``basketDeliveryMethods`` query.

.. code-block:: graphql
   :caption: call to ``basketDeliveryMethods`` query

    query {
        basketDeliveryMethods(
            basketId: "310e50a2b1be309b255d70462cd75507"
        ) {
            id
            title
        }
    }

.. code-block:: json
   :caption: ``basketDeliveryMethods`` query response

    {
        "data": {
            "basketDeliveryMethods": [
                {
                    "id": "oxidstandard",
                    "title": "Standard"
                }
            ]
        }
    }

Now that you know about the available options, you can set the desired delivery
option.

.. code-block:: graphql
   :caption: call to ``basketSetDeliveryMethod`` mutation

    mutation {
        basketSetDeliveryMethod(
            basketId: "310e50a2b1be309b255d70462cd75507",
            deliveryMethodId:"oxidstandard"
        ) {
            id
        }
    }

.. code-block:: json
   :caption: ``basketSetDeliveryMethod`` mutation response

    {
        "data": {
            "basketSetDeliveryMethod": {
                "id": "310e50a2b1be309b255d70462cd75507"
            }
        }
    }

Set the desired payment option
------------------------------

Orders need to be paid for, even in the case you place an order via
GraphQL. For choosing and setting a payment option, the workflow is the same as
with choosing the delivery option. Query available payment options for this
basket via the ``basketPayments`` query and set the desired one via the
``basketSetPayment`` mutation.

.. code-block:: graphql
   :caption: call to ``basketPayments`` query

   query {
        basketPayments(
            basketId: "310e50a2b1be309b255d70462cd75507"
        ) {
            id
            title
        }
    }

.. code-block:: json
   :caption: ``basketPayments`` query response

    {
        "data": {
            "basketPayments": [
                {
                    "id": "oxidpayadvance",
                    "title": "Vorauskasse"
                },
                {
                    "id": "oxiddebitnote",
                    "title": "Bankeinzug/Lastschrift"
                },
                {
                    "id": "oxidcashondel",
                    "title": "Nachnahme"
                }
            ]
        }
    }

.. code-block:: graphql
   :caption: call to ``basketSetPayment`` mutation

    mutation {
        basketSetPayment(
            basketId: "310e50a2b1be309b255d70462cd75507",
            paymentId:"oxidpayadvance"
        ) {
            payment {
                id
                title
            }
        }
    }

.. code-block:: json
   :caption: ``basketSetPayment`` mutation response

    {
        "data": {
            "basketSetPayment": {
                "payment": {
                    "id": "oxidpayadvance",
                    "title": "Vorauskasse"
                }
            }
        }
    }

Finally placing the order
-------------------------

Now that the stage is set up, all that needs to be done is to place the order via
the ``placeOrder`` mutation.

.. important::
   Ordered Basket will be removed on order creation!

.. code-block:: graphql
   :caption: final call to ``placeOrder`` mutation

    mutation {
        placeOrder(
            basketId:"310e50a2b1be309b255d70462cd75507"
        ) {
            id
            orderNumber
        }
    }

.. code-block:: json
   :caption: ``placeOrder`` mutation response

    {
        "data": {
            "placeOrder": {
              "id": "20804e7bef3ed3a1dda5b2506e914989",
              "orderNumber": 1
            }
        }
    }

You successfully placed your first order!

.. important::
   In case that **Users have to Confirm General Terms and Conditions during Check-Out** option is active, **placeOrder** will fail with an error if **confirmTermsAndConditions** input field is missing or its value is false

.. code-block:: graphql
   :caption: final call to ``placeOrder`` mutation plus ``confirmTermsAndConditions`` input field

    mutation {
        placeOrder(
            basketId:"310e50a2b1be309b255d70462cd75507"
            confirmTermsAndConditions: true
        ) {
            id
            orderNumber
        }
    }

.. code-block:: json
   :caption: ``placeOrder`` mutation response

    {
        "data": {
            "placeOrder": {
              "id": "20804e7bef3ed3a1dda5b2506e914989",
              "orderNumber": 1
            }
        }
    }
