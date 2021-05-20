.. _events-BeforeRemoveItem:

BeforeRemoveItem
================

This event belongs to the `graphql-storefront module <https://github.com/OXID-eSales/graphql-storefront-module>`_.
It will be fired before an basket item is removed and holds the ID of the the user basket, ID of the basket item for remove and amount.

BeforeRemoveItemEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforeRemoveItem.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforeRemoveItem`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/beforeremoveitemservices.yaml
   :language: yaml
