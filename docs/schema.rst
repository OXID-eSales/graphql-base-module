Extending the schema
====================

In case you want to extend/modify the behaviour of a field/query/mutation or to use own annotation you can use `middlwares <https://graphqlite.thecodingmachine.io/docs/field-middlewares>`_.

First you have to subscribe to SchemaFactory event, see event `SchemaFactory <events/SchemaFactory.html>`_, then you can register your field middleware service.

.. literalinclude:: examples/extending/FieldMiddleware.php
   :language: php

.. important::
     The code above is only an example. In case you need to add middleware,
     please adapt to your needs.
