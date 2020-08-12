Sending GraphQL requests
========================

Tools
-----

You can use your favourite GraphQL client to explore the API. `Altair <https://altair.sirmuel.design/>`_ is a useful first choice, if you don't have one already installed. It has a `Chrome extension <https://chrome.google.com/webstore/detail/altair-graphql-client/flnheeellpciglgpaodhkhmapeljopja>`_ version as well. Of course, ``curl`` is a viable option on the command line as always.

Usage
-----

For each request you need to specify a method, address, and request body.

GET and POST requests can be used for queries, while mutations should use the POST method.

The URL for GraphQL requests is as follows:
::

    https://<your host address>/graphql

If you would like to query a specific subshop, use the ``shp`` parameter with your desired shop number:
::

    https://<your host address>/graphql?shp=2

A curl query would look something like this:
::

    curl \
        -X POST \
        -H "Content-Type: application/json" \
        --data '{"query": "{token(username: \"user\", password: \"pass\")}"}' \
        https://oxideshop.local/graphql

Headers
-------

- ``Content-Type`` should always be ``application/json``.
- ``Authorization`` enables usage of a token to authenticate with the shop, for example:
::

    curl \
        -X POST \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer <your jwt token>" \
        --data '{"query": "<your query>"} \
        https://<your host address>/graphql

.. note::

    In Altair, the option to set headers is topmost in the sidebar, as of writing.

Our API currently only uses the ``Bearer`` authorization type. To obtain a jwt login token use the ``token`` query, along with the respective shop credentials.
