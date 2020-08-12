Exception handling
==================

In GraphQL, when an error occurs, the server must add an ``errors`` entry in the response. This is the way it should look like:
::

    {
        "errors": [
            {
                "message": "Wished price was not found by id: some-id",
                "extensions": {
                    "category": "requesterror"
                },
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

To build our GraphQL modules we are using `GraphQLite <https://graphqlite.thecodingmachine.io/docs/3.0/features.html>`_, which requires `graphql-php <https://webonyx.github.io/graphql-php/>`_. `Here <https://webonyx.github.io/graphql-php/error-handling/>`_ you can see how graphql-php handles errors.

**Base GraphQL** module provides the following exception classes:

============  ===============  ====================================
Class         Category         Description
============  ===============  ====================================
InvalidLogin  permissionerror  Thrown when we have an invalid login
InvalidToken  permissionerror  Thrown when a token is invalid
NotFound      requesterror     Thrown when a record was not found
============  ===============  ====================================

If you want to change the http status code in your custom exception, you have to implement ``HttpErrorInterface``.

In the error response you will see a ``category`` entry, which describes the category of the error. You can use ``ErrorCategories`` class, which defines several error categories.

Custom exceptions
-----------------

Here is an example of a custom exception which tells us that a product was not found. It is part of the **Catalogue GraphQL** module.

.. code:: php

    <?php

    declare(strict_types=1);

    namespace OxidEsales\GraphQL\Catalogue\Product\Exception;

    use OxidEsales\GraphQL\Base\Exception\NotFound;

    use function sprintf;

    final class ProductNotFound extends NotFound
    {
        public static function byId(string $id): self
        {
            return new self(sprintf('Product was not found by id: %s', $id));
        }
    }

Here is an example of an exception when specific record already exists:

.. code:: php

    <?php

    declare(strict_types=1);

    namespace MyVendor\MyModule\Record\Exception;

    use Exception;
    use GraphQL\Error\ClientAware;
    use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
    use OxidEsales\GraphQL\Base\Exception\HttpErrorInterface;

    final class RecordExists extends Exception implements ClientAware, HttpErrorInterface
    {
        public function getHttpStatus(): int
        {
            return 400;
        }

        public function isClientSafe(): bool
        {
            return true;
        }

        public function getCategory(): string
        {
            return ErrorCategories::REQUESTERROR;
        }

        public static function byUniqueField(string $field): self
        {
            return new self(sprintf("Record with field '%s' already exists!", $field));
        }
    }

In this example you can see the usage of ``ClientAware``, ``HttpErrorInterface`` and ``ErrorCategories``.
