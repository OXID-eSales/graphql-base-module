Troubleshooting
===============

Apache HTTP Authorization
-------------------------

``php-cgi`` under Apache does not pass HTTP Basic user/pass to PHP by default. For this workaround to work, add these lines to your .htaccess file:

.. code-block:: apache

    RewriteCond %{HTTP:Authorization} ^(.+)$
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

or

.. code-block:: apache

    SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1

Query String gets swallowed
---------------------------

When you call the API endpoint with a query string, for example `/graphql/?lang=1` and that `lang` parameter gets swallowed by apache, it is due to the missing `QSA`-`RewriteRule`-Flag. Find the `RewriteRule` that looks like this:

.. code-block:: apache

    RewriteRule ^(graphql/)    widget.php?cl=graphql   [NC,L]


and make it look like this:

.. code-block:: apache

    RewriteRule ^(graphql/)    widget.php?cl=graphql   [QSA,NC,L]


Graphql schema appears incomplete
---------------------------------

Your client's introspection requests get the available schema based upon your access rights. Make sure you are logged in and using the correct token in the ``Authorization`` header.

If you're having trouble finding admin panel requests, this could also be caused by insufficient account rights,

.. note::

    You may want to doublecheck this in the database, as the administrative dashboard setting name could be different. E.g. it could say a user has been granted ``admin`` rights, but actually they are a ``malladmin``. It is not the same and does not give enough access to query, as an example, the shop version

Installation issues for dev environment
---------------------------------------

In case you'd like to contribute, installing the modules as described in the `Oxid documentation <https://docs.oxid-esales.com/developer/en/6.0/modules/good_practices/module_setup.html>`_ might lead to your changes not being reflected or errors when activating the modules.

A more reliable first step in setting things up would be to clone the desired repository in the ``oxideshop`` directory, for example, and symlink it into its respective place in ``source\modules`` like this:

.. code-block:: bash

    cd /var/www/oxideshop
    git clone <url-to-module-repository>
    ln -s <module-directory-path> ./source/modules/<target-directory>

.. important::

    The <target-directory> should be the same as the ``target-directory`` value in the module's ``composer.json`` file, so for ``graphql-base-module`` it's ``source/modules/oe/graphql-base``.

After that, you can continue from step ``2. Register module package in project composer.json`` in the `docs <https://docs.oxid-esales.com/developer/en/6.0/modules/good_practices/module_setup.html>`_.
