Place an Order
==============

.. important::
   To place an order you need the  `GraphQL Storefront module
   <https://github.com/OXID-eSales/graphql-storefront-module/>`_ installed and activated.

The big picture
---------------

Please keep in mind that other than what you know from shop with frontend, the
headless OXID eShop does not use a session. Means we have no session cookies
around and no session to come back to between requests.

What we do have is the possibility to 'log in' a user once to receive a JsonWebToken (JWT)
which can then be sent in each request requiring a 'known user'.

In Frontend for example usually a user start browsing then shop anonymously, he will at first
not receive a session (cookie). But as soon as the first item for a not logged in customer
ends up in the cart, we need a way to identify this user. He gets a session (id) and a cookie
and on the shopping goes.

With our Storefront module, only a user with existing shop account will be able to
create a basket (which because of a lack of session will be saved into database oxuserbaskets table).

Of course the GraphQL Storefront also offers the possibility to fully create and manage a customer account.
For now let's just assume we have an account to give you a run through of the checkout process.

In order to successfully place an order via the GraphQL API you need to first

- create a basket and fill with products
- set a delivery address (in case it is different from invoice address)
- set desired delivery option
- set desired payment option
- and finally place the order

.. important::
   Ordered Basket will be removed on order creation (unless prevented by another module like B2B)!

Keep in mind, that you will need to send a valid
JWT in Authorization header for any of the following queries or mutations.

.. uml::
   :align: center

   "Client (PWA)" -> "GraphQL API": basketCreate mutation
   "Client (PWA)" <- "GraphQL API": Basket datatype or error
   "Client (PWA)" -> "GraphQL API": basketAddItem mutation
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

Retrieve and send the JWT
-------------------------

You need to 'log in' the customer to graphLQ APi once to get a JWT with a predefined lifetime
(see GraphQL Base module settings for details).

.. code-block:: graphql
   :caption: call to ``token`` query

        query user {
            token(
                username: "user@oxid-esales.com"
                password: "useruser"
            )
        }

.. code-block:: json
   :caption: ``token`` query response

        {
            "data": {
                "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QubG9jYWwvIn0.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0LmxvY2FsLyIsImF1ZCI6Imh0dHA6Ly9sb2NhbGhvc3QubG9jYWwvIiwiaWF0IjoxNjUzNDc2MjU5LjU2NTgxNiwibmJmIjoxNjUzNDc2MjU5LjU2NTgxNiwiZXhwIjoxNjUzNTA1MDU5LjU3MTMyMSwic2hvcGlkIjoxLCJ1c2VybmFtZSI6InVzZXJAb3hpZC1lc2FsZXMuY29tIiwidXNlcmlkIjoiZTdhZjFjM2I3ODZmZDAyOTA2Y2NkNzU2OThmNGU2YjkiLCJ1c2VyYW5vbnltb3VzIjpmYWxzZSwidG9rZW5pZCI6ImZkODM2NWZkNDY3ZjJkOTAxNDJiYWFhODAwNjE1MDQ4In0.Q_rih628tTBan9_Dl03htix-c9G_EpqtwPGoiDjq8nab6BdwOVbEVfPRt7zbJlAnJn5_x49dZUxovZZ81aFVlg"
            }
        }

This token needs to be sent in an Authorization header, header key needs to be ``"Authorization"``
header value needs to be prefixed with ``"Bearer"`` followd by your token, in ou example ``"Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."``.

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


.. important:: Currently the GraphQL Stroefront module requires the userid-basketname to be unique, so one user cannot have two
     baskets with the same name at a time. Other than in frontend, with GraphQL a customer can have multiple prepared
     baskets.



This newly created basket is empty, so let's add a product to it.

.. code-block:: graphql
   :caption: call to ``basketAddItem`` mutation

   mutation {
        basketAddItem(
            basketId: "310e50a2b1be309b255d70462cd75507",
            productId:"05848170643ab0deb9914566391c0c63",
            amount: 2
        ) {
            id
            items {
                id
                amount
                product {
                    id
                    title
                }
            }
        }
    }

.. code-block:: json
   :caption: ``basketAddItem`` mutation response

    {
        "data": {
            "basketAddItem": {
                "id": "310e50a2b1be309b255d70462cd75507",
                "items": [
                    {
                        "id":  "d2317afe6d97d07563a7fe0965935f2f"
                        "amount": 2,
                        "product": {
                            "id": "05848170643ab0deb9914566391c0c63",
                            "title": "Trapez ION MADTRIXX"
                        }
                    }
                ]
            }
        }
    }

What you now see in the basket is not the product but what we call a ``basket item`` which
contains the information of the product plus additional information like the amount.

A given amount of products can be removed from the basket item. If the amount of zero
is reached, the item itself will be removed. Please note that we need the basket item
id for this mutation, not the product id.

.. code-block:: graphql
   :caption: call to ``basketRemoveItem`` mutation

    mutation basketRemoveItem {
        basketRemoveItem(
            basketId: "310e50a2b1be309b255d70462cd75507"
            basketItemId: "d2317afe6d97d07563a7fe0965935f2f"
            amount: 1
        ) {
            id
            items {
                id
                amount
                product {
                    id
                    title
                }
            }
        }
    }

.. code-block:: json
   :caption: ``basketRemoveItem`` mutation response

    {
        "data": {
            "basketRemoveItem": {
                "id": "310e50a2b1be309b255d70462cd75507",
                "items": [
                    {
                        "id": "d2317afe6d97d07563a7fe0965935f2f"
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


Set the desired delivery option
-------------------------------

We do offer als kinds of possibilities to create and set a delivery address
as well as query for the available shipping and payment methods for the current basket state.
For a quick demonstration, we can just set delivery and payment method. The customer's invoice
address will be used for delivery in that case.

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
   Ordered Basket will be removed on order creation! This is GraphQL Storefront module default behaviour
   which can be overruled by other modules like e.g. B2B.

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


Small note about Third party payments, for example PayPal Express checkout.
Which means a not logged in customer browses the shop, adds items to cart and then proceeds
to checkout via a third party never bothering to supply the shop with information where
and how to deliver beforehand. That customer acount may not even exist in the shop at this time.
Still with GraphQL, this unknown user needs to be identified with a JWT. So we added the
possiblity to identify an anonymous user by JWT.

It is then up to the third party payment module to implement all necessary queries and mutations
to allow the checkout.


Special cases for basket preparation
------------------------------------

It is also possible for you to add a voucher to your basket. In order to do that,
you need to know the number of an existing and available voucher that you could use.
If the voucher does not exist or otherwise is not applicable, the API will return
an error with a proper message. Of courese this needs to be done before the place
order mutation gets called.

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
