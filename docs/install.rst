Install and Setup
=================

Installation
------------

To start with `GraphQL <https://www.graphql.org>`_ you need `OXID eShop <https://www.oxid-esales.com/>`_ up and running (at least ``OXID-eSales/oxideshop_ce: v6.5.0`` component, which is part of the ``6.2.0`` compilation).

.. attention::

    Depending on your current shop version, you need to install a suitable version of the **OXID GraphQL API**. This installation manual is compatible with OXID eShop 6.4. For newer or older versions, please see the corresponding documentations.

First navigate to your shop's root directory where the project ``composer.json`` file is located:

.. code-block:: bash

    cd <shop_directory>

Then you can simply install the module(s) by using ``composer require`` command. In most cases you want the full GraphQL integration by OXID including the schema for the shop's storefront. To achieve this you need to install the **OXID GraphQL Storefront** module:

.. code-block:: bash

    composer require oxid-esales/graphql-storefront:~2.0.0

This will automatically install the **OXID GraphQL Base** module, which is needed for general GraphQL integration. In case you only need the basic GraphQL integration and want to implement your own queries and mutations, you can also install the **GraphQL Base** module exclusively:

.. code-block:: bash

    composer require oxid-esales/graphql-base:^6.0

If you decided to go with the **GraphQL Storefront** module, you need to run migrations after the installation was successfully executed:

.. code-block:: bash

    ./vendor/bin/oe-eshop-doctrine_migration migration:migrate oe_graphql_storefront

Activation
----------

Now you need to activate the modules, either via OXID eShop admin or CLI.

.. code-block:: bash

    ./vendor/bin/oe-console oe:module:activate oe_graphql_base
    ./vendor/bin/oe-console oe:module:activate oe_graphql_storefront


.. important::
    Keep in mind that you have to activate the **GraphQL Base** module first.
