Install and Setup
=================

Installation
------------

To start with `GraphQL <https://www.graphql.org>`_ you need `OXID eShop <https://www.oxid-esales.com/>`_ up and running (at least ``OXID-eSales/oxideshop_ce: v7.0.0`` component, which is part of the ``7.0.0`` compilation).

First navigate to your shop's root directory where the project ``composer.json`` file is located:

.. code-block:: bash

    cd <shop_directory>

Then you can simply install the module(s) by using ``composer require`` command. In most cases you want the full GraphQL integration by OXID including the schema for the OXID eShop storefront. To achieve this you need to install the **OXID GraphQL Storefront** module:

.. code-block:: bash

    composer require oxid-esales/graphql-storefront

This will automatically install the **OXID GraphQL Base** module, which is needed for general GraphQL integration into the OXID eShop. In case you only need the basic GraphQL integration and want to implement your own queries and mutations, you can also install the **GraphQL Base** module exclusively:

.. code-block:: bash

    composer require oxid-esales/graphql-base

If you decided to go with the **GraphQL Storefront** module, you need to run migrations after the installation was successfully executed:

.. code-block:: bash

    vendor/bin/oe-eshop-doctrine_migration migration:migrate oe_graphql_storefront

Activation
----------

Now you need to activate the modules. You can do this in the OXID eShop administration area or by using the CLI.

.. code-block:: bash

    vendor/bin/oe-console oe:module:activate oe_graphql_base
    vendor/bin/oe-console oe:module:activate oe_graphql_storefront

.. important::

    Keep in mind the **GraphQL Base** module should be activated first, before activating dependent modules
    like GraphQL Storefront. Also before deactivating GraphQL Base module please ensure dependent modules are
    deactivated first.

    In case you are using the OXID eShop Enterprise Edition, it is important for **GraphQL Base** module to be active
    in each subshop that has other dependant on **GraphQL Base** modules.

Entry Point Configuration
-------------------------

The GraphQL API uses the single entry point ``/graphql/``. Usually this entry point is configured in the ``.htaccess`` file that is coming with the OXID eShop. However, if you encounter trouble with that, ensure you have the proper ``/graphql/`` entry point configuration in your ``.htaccess`` file or add it, if not present:

.. code-block:: bash

    RewriteRule ^graphql/?$    widget.php?cl=graphql&skipSession=1   [QSA,NC,L]
