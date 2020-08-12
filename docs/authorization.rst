Authorization
=============

Authorization and Authentication in the GraphQL API is handled via JSON Web Tokens. Please keep in mind that not all queries need authorization. The token is mandatory for all mutations though and some fields are only accesable with a valid token.

.. important::
   There is no server side session!

The `graphql-base` module provides you with a `token` query that returns a JWT to be used in future requests.

**Request:**

.. code:: graphql

    query {
        token (
            username: "admin",
            password: "admin"
        )
    }

**Response:**

.. code:: json

    {
        "data": {
            "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"
        }
    }

This `token` is then to be send in the HTTP `Authorization` header as a bearer token.

.. code:: apache

   Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c

If you want to have a brief look into the JWT you received head over to jwt.io_.

.. _jwt.io: https://jwt.io
