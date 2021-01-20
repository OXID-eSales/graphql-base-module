.. _events-BeforePlaceOrder:

BeforePlaceOrder
================

This event belongs to the `graphql-storefront module <https://github.com/OXID-eSales/graphql-storefront-module>`_.
It will be fired before an order is placed and holds the ID of the the user basket which is to be ordered.
A module can then do final preparations on that saved user basket.

PlaceOrderEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforePlaceOrder.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforePlaceOrder`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/beforeplaceorderservices.yaml
   :language: yaml
