.. _events-BeforeBasketDeliveryMethods:

BeforeBasketDeliveryMethods
===========================

This event will be fired right before getting the list with available delivery methods for specific basket.

BeforeBasketDeliveryMethodsEventSubscriber
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/BeforeBasketDeliveryMethods.php
   :language: php

.. important::
     The code above is only an example. In case you need to handle the ``BeforeBasketDeliveryMethods`` event,
     please adapt to your needs.

services.yaml
^^^^^^^^^^^^^

.. literalinclude:: ../examples/events/beforebasketdeliverymethodsservices.yaml
   :language: yaml
