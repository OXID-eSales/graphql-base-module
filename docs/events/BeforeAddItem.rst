.. _events-BeforeAddItem:

BeforeAddItem
=============

This event belongs to the `graphql-storefront module <https://github.com/OXID-eSales/graphql-storefront-module>`_.
It will be fired before an item is added to basket and holds the ID of the the user basket, ID of the product and the amount.

BeforeAddItemEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforeAddItem.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforeAddItem`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/beforeadditemservices.yaml
   :language: yaml
