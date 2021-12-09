.. _events-BeforeBasketRemoveOnPlaceOrder:

BeforeBasketRemoveOnPlaceOrder
==============================

This event belongs to the `graphql-storefront module <https://github.com/OXID-eSales/graphql-storefront-module>`_.
It will be fired before basket remove when an order is placed and holds the ID of the user basket which was ordered.
You may call the
``setPreserveBasketAfterOrder(bool $preserveBasketAfterOrder = true)`` method to preserve basket after order.

- ``true`` will preserve basket
- ``false`` will remove basket (default)

BeforeBasketRemoveOnPlaceOrderEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforeBasketRemoveOnPlaceOrder.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforeBasketRemoveOnPlaceOrder`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/beforebasketremoveonplaceorderservices.yaml
   :language: yaml
