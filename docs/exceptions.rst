Errors and Exceptions
=====================

When error occurs in GraphQL, the server adds an ``errors`` entry in the response. Here is an example

.. code-block:: json

    {
        "errors": [
            {
                "message": "Wished price was not found by id: some-id",
                "locations": [
                    {
                        "line": 2,
                        "column": 3
                    }
                ],
                "path": [
                    "wishedPrice"
                ]
            }
        ]
    }

You can read more about `GraphQL errors in the official spec <http://spec.graphql.org/June2018/#sec-Errors>`_.

In most cases the HTTP status code will also reflect the error condition. So for example it will be ``404`` in case something was not found.

Exceptions in your module
-------------------------

The ``graphql-base`` module provides the following exceptions you can use or extend from

============  ===============  ================================================================================== ================
Class         Category         Description                                                                        HTTP Status code
============  ===============  ================================================================================== ================
InvalidLogin  permissionerror  Thrown when we have an invalid login                                               401
InvalidToken  permissionerror  Thrown when a token is invalid                                                     403
NotFound      requesterror     Thrown when a record was not found                                                 404
Exists        requesterror     Thrown when a record exists (when we want to register already registered customer) 400
OutOfBounds   requesterror     Thrown when values are out of bounds                                               400
============  ===============  ================================================================================== ================

Exception to GraphQL Error
--------------------------

The GraphQL modules are build using `GraphQLite <https://graphqlite.thecodingmachine.io/docs/3.0/features.html>`_, which requires `graphql-php <https://webonyx.github.io/graphql-php/>`_. `Here <https://webonyx.github.io/graphql-php/error-handling/>`_ you can see how graphql-php handles errors.

In short: If you want the consumer to see the message of your exception, it needs to implement the ``GraphQL\Error\ClientAware`` interface.

HTTP Status code
----------------

If you want to change the HTTP status code in your custom exception, you have to implement ``OxidEsales\GraphQL\Base\Exception\HttpErrorInterface``

Error categories
----------------

In the error response you might want to see a ``category`` entry, which describes the category of the error. You can use ``OxidEsales\GraphQL\Base\Exception\ErrorCategories`` class, which defines several error categories.

Example
-------

Here is an example of a custom exception which tells us that a product was not found. It is part of the ``graphql-storefront`` module.

.. literalinclude:: examples/exceptions/ProductNotFoundException.php
   :language: php

Here is an example of an exception when customer's password does not match the requirements:

.. literalinclude:: examples/exceptions/PasswordMismatchException.php
   :language: php


In this example you can see the usage of ``ClientAware``, ``HttpErrorInterface`` and ``ErrorCategories``.
