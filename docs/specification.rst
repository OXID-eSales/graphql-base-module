Specification
=============

This document adheres to [RFC2119](https://www.ietf.org/rfc/rfc2119.txt)

General
-------

- the API must be a [GraphQL API as specified](https://www.graphql.org)
- entrypoint must be ``/graphql/``
- default HTTP status code must be ``200``
- use OXID models and create DataTypes as facades for GraphQLite
- when the object queried does not exist, the API must respond with a ``404`` HTTP status code
- when the object exists, but is not accesable for the current user (``oxactive`` set to 0, ``oxhidden`` set to 1 or other reasons), the API must respond with a ``401`` HTTP status code
- when an inaccessible or non existing object is requested via a relation from another existing object it must be ignored, the resolver needs to return ``null`` in that case

Login/Auth
----------

- auth against the API must be done via a Bearer JWT in the ``Authorization`` HTTP header
- the ``token`` query must not have a token send with the HTTP request

Naming
------

Queries
^^^^^^^

- must not have ``get`` or ``has`` or similar prefix
- query name must be singular or plural object name
- valid examples:
    - user
    - users
    - article
    - articles
- invalid examples
    - getUser
    - fetchArticle
- must have proper input type definitions

Queries for lists
^^^^^^^^^^^^^^^^^

Additional to the rules applied for the queries, when querying for lists the query should have the following input parameter - all optional.

- ``filter`` which should be of type ``ObjectFilterInput`` (which you have to create and name according)

    - the fields of this ``FilterInput`` type should be of one of the provided filter input types

- ``limit`` which must be of type ``Int`` (if ommited must behave as no limit)
- ``start`` which must be of type ``Int`` (if ommited must behave as 0)

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

- every field from every model should be available as a GraphQL field and must have a correct type annotation (ID and not string for OXID fields)
- multilang fields must not be exposed separately, but in context of the language of the token as a normal field (no ``getTitle_1`` or ``getTitle_2`` methods, only ``getTitle`` for the ``oxarticles.oxtitle\*`` database fields)
- parent ids, object ids, foreign keys, etc. must be exposed via their correct type and not via an ID

    - example: an article has a ``oxvendorid`` and ``oxmanufacturerid``, which must not be exposed as ID or string fields, but as a relation to that specific type
