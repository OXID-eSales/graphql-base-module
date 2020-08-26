Caching
=======

.. important::
    As of this writing resolvers are not cached!

As stated in other occurrences already, we are heavily relying on GraphQLite for our modules. GraphQLite creates the GraphQL schema by using PHP's reflection to scan and find controllers and data types. The result of this step can be `cached by GraphQLite itself <https://graphqlite.thecodingmachine.io/docs/3.0/other-frameworks#requirements>`_.

The ``TheCodingMachine\GraphQLite\SchemaFactory`` needs the DI container and a PSR-16 cache. By default we pass it a null cache.

.. code-block:: yaml

    oxidesales.graphqlbase.cacheadapter:
        class: Symfony\Component\Cache\Adapter\NullAdapter

    oxidesales.graphqlbase.cache:
        class: Symfony\Component\Cache\Psr16Cache
        arguments:
            $pool: '@oxidesales.graphqlbase.cacheadapter'

Change the cache
----------------

There are two ways for you to change the caching backend, first is to provide another cache adapter, the second is to provide another PSR-16 cache implementation.

In both cases you need to override the corresponding key in the DI container through the ``var/configuration/configurable_services.yaml`` file.

Custom cache adapter
^^^^^^^^^^^^^^^^^^^^

You can choose from the ``symfony/cache`` components `adapters <https://symfony.com/doc/current/components/cache.html#available-cache-adapters>`_ or create your own adapter implementing the ``Symfony\Component\Cache\Adapter\AdapterInterface``.

.. code-block:: yaml

    services:

        oxidesales.graphqlbase.cacheadapter:
            class: Symfony\Component\Cache\Adapter\FilesystemAdapter
            arguments:
                $namespace: 'graphql'
                $defaultLifetime: 1200
                $directory: '/var/www/oxideshop/source/cache'

Custom cache
^^^^^^^^^^^^

In case you already have another PSR-16 cache, or want to build your own implementation, instead of overriding the ``oxidesales.graphqlbase.cacheadapter`` key, you can use the ``oxidesales.graphqlbase.cache`` key to inject your PSR-16 cache.

.. code-block:: yaml

    services:

        oxidesales.graphqlbase.cache:
            class: Symfony\Component\Cache\Simple\FilesystemCache
            arguments:
                $namespace: 'graphql_simple'
                $defaultLifetime: 1200
                $directory: '/var/www/oxideshop/source/cache'

Symfony itself comes with some PSR-16 cache implementations, but `strives towards cache contracts <https://symfony.com/doc/current/components/cache.html#cache-contracts-versus-psr-6>`_.
