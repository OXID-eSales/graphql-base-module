.. _events-AfterRemoveItem:

AfterRemoveItem
===============

This event belongs to the `graphql-storefront module <https://github.com/OXID-eSales/graphql-storefront-module>`_.
It will be fired after an item is remove from basket and holds the ID of the the user basket, ID of the basket item for remove and amount.

AfterRemoveItemEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/AfterRemoveItem.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``AfterRemoveItem`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/afterremoveitemservices.yaml
   :language: yaml
