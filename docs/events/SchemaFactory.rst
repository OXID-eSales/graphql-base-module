.. _events-SchemaFactory:

SchemaFactory
=============

This event will be fired on schema introspection, every request and gives you access to ``TheCodingMachine\GraphQLite\SchemaFactory``.

SchemaFactorySubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/SchemaFactory.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``SchemaFactory`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/schemafactoryservices.yaml
   :language: yaml
