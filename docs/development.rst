Development
===========

In case you are creating your own GraphQL module for OXID eShop, you
might want to consider using the `oxid-esales/graphql-module-skeleton
<https://github.com/OXID-eSales/graphql-module-skeleton>`_ which comes
with a lot of best practices already applied.

Coding guidelines
-----------------

To enforce coding standards in the project we use `PHP-CS-Fixer
<https://github.com/FriendsOfPHP/PHP-CS-Fixer>`_ with a very strict rule set that
you can find in the ``.php_cs.dist`` file in all our GraphQL modules. The rule
set not only enforces `PSR-12 <https://www.php-fig.org/psr/psr-12/>`_ but also
file level comments, ordering of methods, short array syntax and so on.

You can just take the distributed version and discuss with your team to fit the
rule set to your needs.

Static code analysis
--------------------

As it is a good thing ™ and GraphQL is fully typed anyway, we decided to also
go fully typed on all of our source code. All PHP files have ``declare(strict_types=1);``
set.

In order to proof that we are using all types correct, we can take advantage of
static code analyzers. We choose to use `PHPStan <https://phpstan.org>`_, so it comes
prepacked and configured, but please feel free to use anything that fits your needs.

For PHPStan to work, we added a preconfigured ``phpstan.neon`` configuration file,
as well as the OXID eShop as a dev dependency.

Dependencies between software layers
------------------------------------

As you might have already read in the :doc:`/architecture` docs, we are using a
layered architecture, aka Hexagonal or Ports and Adapters. To help us not violating
the boundaries of each layer, we use `Deptrac <https://github.com/sensiolabs-de/deptrac>`_
to validate how our layers are depending on each others. You can find a pre
configured ``depfile.yml`` in our GraphQL modules, also in the module skeleton.

To run it, just run ``deptrac`` in the root folder of the module.

Unit testing
------------

To write unit tests, we included `PHPUnit <https://phpunit.de/>`_ in the ``composer.json``'s
dev dependencies. As unit tests should not depend on anything outside, nothing special
applies here.

Integration testing
-------------------

You have two ways to write integration tests for your GraphQL module, see below.
Those tests need the Shop to be setup and your module alongside the GraphQL Base
module installed. You can run them via ``vendor/bin/runtests`` or (for Codeception)
via ``vendor/bin/runtests-codeception``.

Using PHPUnit
^^^^^^^^^^^^^

You can extend from the GraphQL base modules ``OxidEsales\GraphQL\Base\Tests\Integration\TestCase``
or ``OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase`` in order to build
integration tests for GraphQL queries you'd like to test.

.. literalinclude:: examples/development/IntegrationTestPHPUnit.php
   :language: php

The ``TestCase`` class exports the following methods for you to use:

``query``

    ``query(string $query, ?array $variables = null, ?string $operationName = null): array``

    Send the GraphQL query in ``$query`` to the ``OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler``
    implementation an returns the result as an array.

``beforeContainerCompile``

    ``protected static function beforeContainerCompile(): void``

    If you need to inject something into the container before it is compiled, you can
    override this method in you test case.

The ``TokenTestCase`` extends the ``TestCase`` and adds handling for auth tokens

``setAuthToken``

    ``protected function setAuthToken(string $token): void``

    Set the JWT in ``$token`` to be used with the next queries.

Using Codeception
^^^^^^^^^^^^^^^^^

In the GraphQL module skeleton you may also find a pre configured setup to
use Codeception to write your integration tests. As this testing is just against
and HTTP API, there is no need to run a headless browser, so the ``acceptance.suite.yml``
is pretty slim and tests are pretty fast.

.. code-block:: yaml

    # suite config
    actor: AcceptanceTester
    path: Acceptance
    modules:
        enabled:
            - \Full\Quallified\Namespace\Tests\Codeception\Helper\Acceptance
            - REST:
                url: '%SHOP_URL%'
                depends: PhpBrowser
                part: Json

.. literalinclude:: examples/development/IntegrationTestCodeception.php
   :language: php

Travis
------

The module skeleton comes with a ``.travsi.yml`` file which will run the syntax
check, PHP-CS-Fixer, PHPStan and the unit tests via PHPUnit in Travis-CI for
for PHP version 7.1 to 7.4. Feel free to change this to your needs, or adapt to
whatever CI service you are using.

Happy hacking!
