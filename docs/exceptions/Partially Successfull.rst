Partially Successfull
=====================

GraphQL allows for the response to have a ``data`` and ``errors`` key. If you
have a situation where you can recover from an error during GraphQL execution,
you may catch the Exception instead of letting it bubble all the way up and pass
it to the ``\OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler::addError()``
static method.

.. important::
   Keep in mind, that the response in the ``data`` key still has to be valid
   against your schema!
