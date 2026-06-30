# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [14.0.0] - unreleased

### Added
- Clear the GraphQL schema cache when the shop cache is cleared (e.g. via `oe:cache:clear`)
- New Error-handling with payload which contains result and possible errors (including message and unique code) (https://docs.oxid-esales.com/interfaces/graphql/en/latest/exceptions/Error%20Handling.html)

### Changed
- Update module to work with OXID eShop 7.6
- Upgrade GraphQLite v7 -> v8
- Move SEO encoder and refresh token model factories to the `Shared` namespace for reuse outside their original domains
- Align the `oe:graphql:cache-clear` command with the streamlined 7.6 console conventions
- Queries and Mutations are now using payloads
