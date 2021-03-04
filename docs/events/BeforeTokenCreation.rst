.. _events-BeforeTokenCreation:

BeforeTokenCreation
===================

This event will be fired right before authentication is ready to build the token and carries the
``Lcobucci\JWT\Builder`` and the user data (``OxidEsales\GraphQL\Base\Framework\UserDataInterface``) used to build
the token as payload. You may change the builder object as you see fit before token gets created.

BeforeTokenCreationEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforeTokenCreation.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforeTokenCreation`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/beforetokencreationservices.yaml
   :language: yaml
