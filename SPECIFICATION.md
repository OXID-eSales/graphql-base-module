# Specification

This document adheres to [RFC2119](https://www.ietf.org/rfc/rfc2119.txt)

## General

- the API must be a GraphQL API as specified on graphql.org
- Entrypoint must be: /graphql/
- if possible and faster we should create our own DataObjects and DataAccessObjects

## Login/Auth

- auth against the API must be done via a Bearer JWT in the Authorization HTTP Header
- every request must have a token, because the token reflects the shop id and language id of the request and if authorized, it also reflects the user
  - the token query may have a token

## Naming

### Queries

- must not have get or has or similar in its name
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

### Mutations

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

### Fields
- every field from every model should be available as a GraphQL Field and must have a correct type annotation (ID and not string for OXID fields)
- multilang fields must not be exposed separately, but in context of the language of the token as a normal field (no getTitle\_1 or getTitle\_2 methods, only getTitle for the oxarticles.oxtitle\* database fields)
- parent ids, object ids, foreign keys, etc. must be exposed via their correct type and not via an ID
  - example: an article has a oxvendorid and oxmanufacturerid, which must not be exposed as ID or string fields, but as a relation to that specific type
