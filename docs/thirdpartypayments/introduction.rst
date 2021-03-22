Introduction
============

This section will explain how to integrate a third party payment (module) into the GraphQL checkout workflow.

From what we've seen in existing modules, there's the following possibilities

 - the payment method is used in the regular (standard) checkout process as described in
   :doc:`Consuming the API - Place an Order <../consuming/PlaceOrder>`.

 - the payment method bypasses parts of the regular checkout process which we  will call
   an 'express checkout' workflow. The basket will be prepared, the payment approval etc.
   will be handled by a third party, the order will be placed.
   Information about where and how to ship will be provided by the 3rd party.
   This might require additional queries and mutation, which have to be be provided by the third
   party payment module.

As we implemented both ways in the `OXID eShop Extension PayPal <https://github.com/OXID-eSales/paypal>`_, this will
serve as an example.

