Specification
=============

This document adheres to `RFC2119 <https://tools.ietf.org/html/rfc2119>`_

General
-------

- the API must be a `GraphQL API as specified <https://www.graphql.org>`_
- entrypoint must be ``/graphql/``
- default HTTP status code must be ``200``
- use OXID models and create DataTypes as facades for GraphQLite
- when the object queried does not exist, the API must respond with a ``404`` HTTP status code
- when the object exists, but is not accessible for the current user (``oxactive`` set to 0, ``oxhidden`` set to 1 or other reasons), the API must respond with a ``401`` HTTP status code
- when an inaccessible or non existing object is requested via a relation from another existing object it must be ignored, the resolver needs to return ``null`` in that case

Login/Auth
----------

- auth against the API must be done via a Bearer JWT in the ``Authorization`` HTTP header
- the ``token`` query must not have a token sent with the HTTP request

Naming
------

Queries
^^^^^^^

- must not have ``get`` or ``has`` or similar prefix
- query name must be singular or plural object name
- valid examples:
    - user
    - users
    - product
    - products
- invalid examples
    - getUser
    - fetchProduct
- must have proper input type definitions

Queries for lists
^^^^^^^^^^^^^^^^^

Additional to the rules applied for the queries, when querying for lists the query should have the following input parameters - all optional.

- ``filter`` which should be of type ``ObjectFilterInput`` (which you have to create and name accordingly)

    - the fields of this ``FilterInput`` type should be of one of the provided filter input types

- ``sort`` which should be of type ``ObjectSorting`` (which you have to create and name accordingly)
- ``limit`` which must be of type ``Int`` (if ommitted must behave as no limit)
- ``offset`` which must be of type ``Int`` (if ommitted must behave as 0)

Mutations
^^^^^^^^^

- must start with the object name, then the action verb (camelCase)
- valid examples
    - userRegister
    - categoryCreate
    - categoryUpdate
    - categoryDelete
- invalid
    - createuser
- should not use generics (create, update, delete), but the correct domain verbs where possible (register user vs. create / add, place order vs. create, ...)
- must have proper input type definitions

Fields
^^^^^^

- every field from every model should be available as a GraphQL field and must have a correct type annotation (``ID`` for ``oxid`` database field, not ``String``)
- multilanguage fields must not be exposed separately, but in context of the language of the token as a normal field (no ``title_1`` or ``title_2`` fields, only ``title`` for the ``oxarticles.oxtitle\*`` database fields)
- parent ids, object ids, foreign keys (relations), etc. must be exposed via their correct type

    - example: a product has an ``oxvendorid``, which should not be exposed as ``ID`` or ``String`` field, but as a relation to that specific type
    - you may additionally add the field with the ``ID`` type when necessary

.. code-block:: graphql

    type Product {
        # do
        category: Category!
        # don't
        categoryId: ID!
    }
