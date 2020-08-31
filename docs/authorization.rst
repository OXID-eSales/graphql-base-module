Authorization
=============

Authorization and Authentication in the GraphQL API is handled via JSON Web
Tokens. Please keep in mind that not all queries need authorization. The
token is mandatory for all mutations though and some fields are only accessible
with a valid token.

.. important::
   There is no server side session!

Consumer usage
--------------

The `graphql-base` module provides you with a `token` query that returns a JWT
to be used in future requests.

**Request:**

.. code-block:: graphql

    query {
        token (
            username: "admin",
            password: "admin"
        )
    }

**Response:**

.. code-block:: json

    {
        "data": {
            "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"
        }
    }

This `token` is then to be send in the HTTP `Authorization` header as a bearer
token.

.. code-block:: yaml

   Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c

If you want to have a brief look into the JWT you received head over
to `jwt.io <https://jwt.io>`_.

Protect your queries/mutations/types
------------------------------------

In order to protect your own queries, mutations or types you may use GraphQLite's
build in `authentication and authorization <https://graphqlite.thecodingmachine.io/docs/3.0/authentication_authorization>`_
features.

The `graphql-base` module brings an authentication- and authorization service
implemented in ``OxidEsales\GraphQL\Base\Service\Authentication`` and
``OxidEsales\GraphQL\Base\Service\Authorization`` to connect the GraphQLite library
to OXID's security mechanism.

Authentication
^^^^^^^^^^^^^^

The authentication service is responsible for creating and validating the JSON
Web Token, as well as resolving the ``@Logged`` annoation.

.. literalinclude:: examples/ControllerWithLogged.php
   :language: php

Using the ``@Logged()`` annotations prevents consumers from seeing and using
your resolver without a valid JWT.

Authorization
^^^^^^^^^^^^^

For finer grained access control you may use the ``@Right()`` annotation to ask
if the token in use allows for a specific right. These rights are coupled to the
user group which will be stored in the token itself.

.. literalinclude:: examples/ControllerWithRights.php
   :language: php

In case you need to have more control on how the authorization service decides,
you may register a handler for the ``OxidEsales\GraphQL\Base\Event\BeforeAuthorization``
event and oversteer the result in your event subscriber, see :ref:`events-BeforeAuthorization`.

Map rights to groups
^^^^^^^^^^^^^^^^^^^^

In order to use the ``SEE_BASKET`` right as we have seen in the last example, we
need to map this right to a user group. For this to work we need to create a
``PermissionProvider`` in our module and register it with the ``graphql_permission_provider``
tag in our ``services.yaml`` file.

.. literalinclude:: examples/PermissionProvider.php
   :language: php
