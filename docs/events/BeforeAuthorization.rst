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

.. literalinclude:: BeforeAuthorization.php
   :language: php

services.yaml
^^^^^^^^^^^^^

.. code:: yaml

    services:
        Full\Quallified\Namespace\\DeveloperAuthorizationEventSubscriber:
            class: Full\Quallified\Namespace\\DeveloperAuthorizationEventSubscriber
            tags: ['kernel.event_subscriber']
