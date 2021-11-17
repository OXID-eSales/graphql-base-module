.. _events-BasketAuthorization:

BasketAuthorization
===================

This event belongs to the `graphql-storefront module <https://github.com/OXID-eSales/graphql-storefront-module>`_.
It will be fired when basket authorization is done. You may call the
``setAuthorized(bool $authorized)`` method to oversteer the default basket authorization logic.

- ``true`` will succeed the authorization
- ``false`` will fail the authorization (default)

BasketAuthorizationEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BasketAuthorization.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BasketAuthorization`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/basketauthorizationservices.yaml
   :language: yaml
