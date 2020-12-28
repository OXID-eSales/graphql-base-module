Install and Setup
=================

Installation
------------

To start with `GraphQL <https://www.graphql.org>`_ you need `OXID eShop <https://www.oxid-esales.com/>`_ up and running (at least ``OXID-eSales/oxideshop_ce: v6.5.0`` component, which is part of the ``6.2.0`` compilation).

To install the available GraphQL modules, first you have to go to the shop's directory

.. code-block:: bash

    cd <shop_directory>

and to require the modules via ``composer``:

.. code-block:: bash

    composer require oxid-esales/graphql-base
    composer require oxid-esales/graphql-storefront

After installing the modules you need to add their configurations into the project's configuration file. You can do it by running:

.. code-block:: bash

    ./bin/oe-console oe:module:install-configuration source/modules/oe/graphql-base/
    ./bin/oe-console oe:module:install-configuration source/modules/oe/graphql-storefront/

This will overwrite the configuration of the modules if it was already present in the project's configuration file.

Activation
----------

Now you need to activate the modules, either via OXID eShop admin or CLI.

.. code-block:: bash

    ./bin/oe-console oe:module:activate oe_graphql_base
    ./bin/oe-console oe:module:activate oe_graphql_storefront


.. important::
    Keep in mind that you have to activate the **GraphQL Base** module first.
