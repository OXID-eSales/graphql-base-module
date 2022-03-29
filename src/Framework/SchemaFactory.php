<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use Mouf\Composer\ClassNameMapper;
use OxidEsales\GraphQL\Base\Event\SchemaFactory as SchemaFactoryEvent;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

    /** @var TimerHandler */
    private $timerHandler;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param NamespaceMapperInterface[] $namespaceMappers
     */
    public function __construct(
        iterable $namespaceMappers,
        Authentication $authentication,
        Authorization $authorization,
        ContainerInterface $container,
        TimerHandler $timerHandler,
        EventDispatcherInterface $eventDispatcher,
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
        $this->timerHandler      = $timerHandler;

        $this->eventDispatcher = $eventDispatcher;
    }

    public function getSchema(): Schema
    {
        if (null !== $this->schema) {
            return $this->schema;
        }

        $queryTimer = $this->timerHandler
                           ->create('schema-gen')
                           ->start();

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

        $this->eventDispatcher->dispatch(
            new SchemaFactoryEvent($factory),
            SchemaFactoryEvent::NAME
        );

        $this->schema = $factory->createSchema();
        $queryTimer->stop();

        return $this->schema;
    }
}
