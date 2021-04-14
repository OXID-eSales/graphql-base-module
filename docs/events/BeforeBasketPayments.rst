.. _events-BeforeBasketPayments:

BeforeBasketPayments
====================

This event will be fired right before getting the list with available payments for specific basket.

BeforeBasketPaymentsEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforeBasketPayments.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforeBasketPayments`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/beforebasketpaymentsservices.yaml
   :language: yaml
