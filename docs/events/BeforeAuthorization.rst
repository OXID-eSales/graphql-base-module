.. _events-BeforeAuthorization:

BeforeAuthorization
===================

This event will be fired when authorization is done. You may call the
``setAuthorized(?bool $flag = null)`` method to oversteer the default authorization logic.

- ``true`` will succeed the authorization
- ``false`` will fail the authorization
- ``null`` will use the default implementation to decided (default)

AuthorizationEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforeAuthorization.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforeAuthorization`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/services.yaml
   :language: yaml
