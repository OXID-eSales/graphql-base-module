<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use Mouf\Composer\ClassNameMapper;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TheCodingMachine\GraphQLite\Schema;
use TheCodingMachine\GraphQLite\SchemaFactory as GraphQLiteSchemaFactory;

/**
 * Class SchemaFactory
 */
class SchemaFactory
{
    /** @var Schema */
    private $schema;

    /** @var Authentication */
    private $authentication;

    /** @var Authorization */
    private $authorization;

    /** @var NamespaceMapperInterface[] */
    private $namespaceMappers;

    /** @var ContainerInterface */
    private $container;

    /** @var CacheInterface */
    private $cache;

    /**
     * @param NamespaceMapperInterface[] $namespaceMappers
     */
    public function __construct(
        iterable $namespaceMappers,
        Authentication $authentication,
        Authorization $authorization,
        ContainerInterface $container,
        ?CacheInterface $cache = null
    ) {
        foreach ($namespaceMappers as $namespaceMapper) {
            $this->namespaceMappers[] = $namespaceMapper;
        }
        $this->authentication = $authentication;
        $this->authorization  = $authorization;
        $this->container      = $container;
        $this->cache          = $cache ?? new Psr16Cache(
            new NullAdapter()
        );
    }

    public function getSchema(): Schema
    {
        if (null !== $this->schema) {
            return $this->schema;
        }

        $factory = new GraphQLiteSchemaFactory(
            $this->cache,
            $this->container
        );

        $classNameMapper = new ClassNameMapper();

        foreach ($this->namespaceMappers as $namespaceMapper) {
            foreach ($namespaceMapper->getControllerNamespaceMapping() as $namespace => $path) {
                $classNameMapper->registerPsr4Namespace(
                    $namespace,
                    $path
                );
                $factory->addControllerNameSpace($namespace);
            }

            foreach ($namespaceMapper->getTypeNamespaceMapping() as $namespace => $path) {
                $classNameMapper->registerPsr4Namespace(
                    $namespace,
                    $path
                );
                $factory->addTypeNameSpace($namespace);
            }
        }

        $factory->setClassNameMapper($classNameMapper);

        $factory->setAuthenticationService($this->authentication)
                ->setAuthorizationService($this->authorization);

        return $this->schema = $factory->createSchema();
    }
}
