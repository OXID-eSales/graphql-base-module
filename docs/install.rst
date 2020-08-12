Install and Setup
====================

Installation
------------

To start with `GraphQL <https://www.graphql.org>`_ you need `OXID eShop <https://www.oxid-esales.com/>`_ up and running (at least ``OXID-eSales/oxideshop_ce: v6.5.0`` component, which is part of the ``6.2.0`` compilation).

To install the available GraphQL modules, first you have to go to the shop's directory

.. code:: shell

    cd <shop_directory>

and to register the modules in project's composer.json file.

.. code:: shell

    composer require oxid-esales/graphql-base --no-update
    composer require oxid-esales/graphql-catalogue --no-update

Then install the defined dependencies executing

.. code:: shell

    composer update

After installing the modules you need to add their configurations into the project's configuration file. You can do it by running:

.. code:: shell

    ./bin/oe-console oe:module:install-configuration source/modules/oe/graphql-base/
    ./bin/oe-console oe:module:install-configuration source/modules/oe/graphql-catalogue/

This will overwrite the configuration of the modules if it was already present in the project's configuration file.

Activation
----------

Now you need to activate the modules, either via OXID eShop admin or CLI.

.. code:: shell

    ./bin/oe-console oe:module:activate oe/graphql-base
    ./bin/oe-console oe:module:activate oe/graphql-catalogue


.. important::
    Keep in mind that you have to activate the **GraphQL Base** module first.
