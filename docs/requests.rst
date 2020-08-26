Sending GraphQL requests
========================

Tooling
-------

You can use your favourite GraphQL client to explore the API. `Altair <https://altair.sirmuel.design/>`_ is a useful first choice, if you don't have one already installed. It can also be installed as a `Chrome <https://chrome.google.com/webstore/detail/altair-graphql-client/flnheeellpciglgpaodhkhmapeljopja>`_ or `Firefox <https://addons.mozilla.org/de/firefox/addon/altair-graphql-client/>`_ extension as well. Of course, ``curl`` is a viable option on the command line as always.

Usage
-----

For each request you need to specify a method, address, and request body.

.. important::

    - ``GET`` and ``POST`` requests can be used for queries
    - Mutations must use the ``POST`` method

The endpoint for the GraphQL API

::

    https://<your host address>/graphql/

Shop and language are no special parameters to GraphQL, but will be taken from the environment after the OXID eShop resolved your hostname and parameters. If you happen to have a domain per subshop, query that domain. If you have a domain per language, query that domain. You could as an alternative also give the ``shp`` and ``lang`` paramters in the query string.

::

    https://<your host address>/graphql/?shp=2&lang=1

A curl query would look something like this:

.. code-block:: bash

    curl \
        -X POST \
        -H "Content-Type: application/json" \
        --data '{"query": "{token(username: \"user\", password: \"pass\")}"}' \
        https://oxideshop.local/graphql/

Headers
-------

- ``Content-Type`` must be ``application/json``.
- ``Authorization`` enables usage of a token to authenticate with the shop, for example:

.. code-block:: bash

    curl \
        -X POST \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer <your jwt token>" \
        --data '{"query": "<your query>"}' \
        https://<your host address>/graphql/

.. note::

    In Altair, the option to set headers is topmost in the sidebar, as of writing.

Our API uses the ``Bearer`` authorization type. To obtain a JWT login token use the ``token`` query, along with the respective shop credentials.
