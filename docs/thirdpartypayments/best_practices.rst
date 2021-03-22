Best Practices
==============

.. important::
 Existing 3rd party modules might need a little refactoring to serve the shop frontend as well as
 the GraphQL API without code duplication. Best practice would be to refactor business logic out of
 the controllers or components into model classes. Check the ``\OxidEsales\PayPalModule\Model\PaymentManager``
 class for example.

.. important::
 Storing information in the session has to be handled with care. For the frontend, a lot of information is
 usually just dumped into the session and reused in the next requests. This is not possible with the GraphQL API.
 You can store variables in the shop's session object which are needed to process the current request but there is no
 session to come back to in the next request. If data is needed for later use, store it in a safe place far away
 from the session.

**Module dependencies**

In case the 3rd party payment module should serve the shop frontend as well as the GraphQL API, make sure there's
no hard dependencies introduced which require the GraphQL modules to be mandatory. This way, the graphQL modules
only need to be installed in case the GraphQL API will actually be used.
Please refer to `OXID eShop Extension PayPal <https://github.com/OXID-eSales/paypal>`_ services.yaml file and usage of
injected services.

**What if**

... I want to customize the delivery set or payments list:
    Have your module hook into the ``\OxidEsales\Eshop\Application\Model\DeliverySetList`` class to deliver
    customized delivery and payment methods. That's the central point where the decsion is made which delivery
    and payment methods are available for the current user and basket combination and it can serve frontend and
    GraphQL API alike.

... I want to run a bonicheck
    If the bonicheck should serve as a control mechanism for available delivery and payment options,
    hook it into the ``\OxidEsales\Eshop\Application\Model\DeliverySetList`` to serve both frontend and GraphQL API.
    If the check is needed in some other checkout step, try to find the places in code both APIs will use.
    Models most likely, controllers or components small chance.



