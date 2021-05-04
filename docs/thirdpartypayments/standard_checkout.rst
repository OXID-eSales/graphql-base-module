Standard Checkout
=================

During what we call the 'standard checkout' process, a user is logged in to the GraphQL API
(JWT token with existing oxuser.oxid), prepares a basket for checkout and then places the order
as described in :doc:`Consuming the API - Place an Order <../consuming/PlaceOrder>`.
In case the chosen payment method requires some extra action (like approval with a 3rd party),
this needs to be built into the module's GraphQL API.

.. uml::
   :align: center

   "Client (PWA)" -> "Storefront GraphQL API": basketCreate mutation
   "Client (PWA)" <- "Storefront GraphQL API": Basket datatype or error
   "Client (PWA)" -> "Storefront GraphQL API": basketAddProduct mutation
   "Client (PWA)" <- "Storefront GraphQL API": Basket datatype or error
   "Client (PWA)" -> "Storefront GraphQL API": basketDeliveryMethods query
   "Client (PWA)" <- "Storefront GraphQL API": array of DeliveryMethod data types
   "Client (PWA)" -> "Storefront GraphQL API": basketSetDeliveryMethod mutation
   "Client (PWA)" <- "Storefront GraphQL API": Basket datatype or error
   "Client (PWA)" -> "Storefront GraphQL API": basketPayments query
   "Client (PWA)" <- "Storefront GraphQL API": array of Payment data types
   "Client (PWA)" -> "Storefront GraphQL API": basketSetPayment mutation
   "Client (PWA)" <- "Storefront GraphQL API": Basket datatype or error
   "Client (PWA)" -> "3rd party module GraphQL API": 3rdPartyStandardApprovalProcess query
   "Client (PWA)" <- "3rd party module GraphQL API": 3rdPartyCommunicationInformation or error
   "Client (PWA)" -> "Storefront GraphQL API": placeOrder mutation
   "Client (PWA)" <- "Storefront GraphQL API": Order datatype or error


The GraphQL API checkout logic centers around from the ``OxidEsales\Eshop\Application\Model\UserBasket (EshopUserBasketModel)`` object,
as we don't have a session and therefore need a way to store a basket between requests. This
``EshopUserBasketModel`` will get converted into
an ``OxidEsales\Eshop\Application\Model\Basket``  (``EshopBasketModel``  or session basket) depending on the
query or mutations being processed. When the shop finalizes an order, it does this with an ``EshopBasketModel``.
The GraphQL API part of your module needs to prepare that ``EshopBasketModel`` session basket object for the shop to consume in
``OxidEsales\Eshop\Application\Model\Order::finalizeOrder()``. The payment (the actual transaction) can be handled
by the ``\OxidEsales\Eshop\Application\Model\PaymentGateway`` same as for calls coming in via shop frontend.

.. important::
 During the ``3rdPartyStandardApprovalProcess`` call, 3rd party module specific information should be stored in some suitable
 place (additional fields for oxuserbaskets table in case of PayPal).

Additional logic will have to be hooked in right before the order gets finalized. For that purpose the
``BeforePlaceOrder`` event is dispatched right at the start of the placeOrder call. Please refer to
:doc:`Events - BeforePlaceOrder <../events/BeforePlaceOrder>` for details.
In the case of the PayPal Standard Checkout, the PayPal module is subscribed to this event. The PayPal module will
use the earlier stored additional fields from oxuserbaskets table together with information it will fetch from the
PayPal API, to prepare the basket with delivery method and eventual delivery address.
