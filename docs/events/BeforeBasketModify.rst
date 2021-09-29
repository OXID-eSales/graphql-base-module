.. _events-BeforeBasketModify:

BeforeBasketModify
==================

This event belongs to the `graphql-storefront module <https://github.com/OXID-eSales/graphql-storefront-module>`_.
It will be fired before:

- delivery address ID
- delivery method ID
- payment ID

is set to a basket. It holds the ID of the the user basket and the event type.

BeforeBasketModifyEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforeBasketModify.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforeBasketModify`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/beforebasketmodifyservices.yaml
   :language: yaml
