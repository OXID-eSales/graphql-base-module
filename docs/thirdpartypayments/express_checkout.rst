Express Checkout
================

In order to use express checkout, the customer does not need to be logged in to or
even already have an account in that shop. It's just fill the basket, approve the payment
with the 3rd party and all necessary information to place the order is provided to the shop.

.. uml::
   :align: center

   "Client (PWA)" -> "Base GraphQL API": token query (JWT for anonymous or existing user)
   "Client (PWA)" -> "Storefront GraphQL API": basketCreate mutation
   "Client (PWA)" <- "Storefront GraphQL API": Basket datatype or error
   "Client (PWA)" -> "Storefront GraphQL API": basketAddProduct mutation
   "Client (PWA)" <- "Storefront GraphQL API": Basket datatype or error
   "Client (PWA)" -> "3rd party module GraphQL API": 3rdPartyExpressApprovalProcess query
   "Client (PWA)" <- "3rd party module GraphQL API": 3rdPartyCommunicationInformation or error
   "Client (PWA)" -> "Storefront GraphQL API": placeOrder mutation
   "Client (PWA)" <- "Storefront GraphQL API": Order datatype or error

As we have no session in the graphQL storefront to help us identify a possibly anonymous user,
we added the possibility to the `GraphQL-Base module <https://github.com/OXID-eSales/graphql-base-module>`_
to provide a what we call 'anonymous' token. If no username and/or password is present in the token query,
the user gets a random userid which is only present in the token but not in the shop's oxuser table.
In the token groups claim, he will be put into (virtual group) 'oxidanonymous' and the rights to manipulate a basket
can be given to that virtual user group.

.. important:: If such an anonymous user loses his JWT token, he will lose his basket.

**Request for an anonymous token:**

.. code-block:: graphql

    query {
        token
    }

Please refer to :doc:`Authorization <../authorization>` for more details.

Provide permissions
^^^^^^^^^^^^^^^^^^^

In the `GraphQL Storefront module <https://github.com/OXID-eSales/graphql-storefront-module/>`_ only
special shop user groups are given the rights to prepare a basket and place an order.
A 3rd party payment module can have its own PermissionProvider, opening up the rights of existing
queries to additional user groups or adding own rights for module specific queries and mutations.

.. literalinclude:: ../examples/thirdpartypayments/PermissionProvider.php
   :language: php


Place the (express checkout) order
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Of course, a 3rd party payment module could come with its completely own implementation
of checkout workflow. Here we will explain how to hook into the already existing Storefront ``placeOrder`` mutation.

At the point where the express checkout customer starts the placeOrder mutation, all information needs to be available
to successfully finish the checkout. During the ``3rdPartyExpressApprovalProcess`` call, 3rd party module specific
information should be stored in some suitable place (additional fields for oxuserbaskets table in case of PayPal).
Keep in mind that there's no session.

The ``placeOrder`` mutation gets called as usual, the user is identified by his JWT token and we need the information,
for which basket the order will be placed.

.. code-block:: graphql

        mutation {
             placeOrder(
                 basketId:"bab15f075f10f62937d178866e4f791b"
             ) {
                 id
                 orderNumber
             }
         }

Additional logic will have to be hooked in right before the order gets finalized. For that purpose the
``BeforePlaceOrder`` event is dispatched right at the start of the placeOrder call. Please refer to
:doc:`Events - BeforePlaceOrder <../events/BeforePlaceOrder>` for details. In the case of the PayPal Express Checkout,
the PayPal module is subscribed to this event. The PayPal module will use the earlier stored additional fields
from oxuserbaskets table together with information it will fetch from the PayPal API, to prepare the basket with
delivery method and eventual delivery address. The user is either matched to an existing account or a new user account
gets created in the shop. When the event handling is done, all has to be ready for the order to be finalized.

Please check :doc:`Third Party Payments - Best Practices <./best_practices>` for further hints.


