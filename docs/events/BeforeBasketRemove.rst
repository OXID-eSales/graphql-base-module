.. _events-BeforeBasketRemove:

BeforeBasketRemove
==================

This event belongs to the `graphql-storefront module <https://github.com/OXID-eSales/graphql-storefront-module>`_.
It will be fired before a basket is removed and holds the ID of the the user basket.

BeforeBasketRemoveEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforeBasketRemove.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforeBasketRemove`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/beforebasketremoveservices.yaml
   :language: yaml
