<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

use Mouf\Composer\ClassNameMapper;
use OxidEsales\GraphQL\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQL\Service\AuthorizationServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TheCodingMachine\GraphQLite\Schema;
use TheCodingMachine\GraphQLite\SchemaFactory as GraphQLiteSchemaFactory;

/**
 * Class SchemaFactory
 *
 * @package OxidProfessionalServices\GraphQL\Core\Schema
 */
class SchemaFactory implements SchemaFactoryInterface
{
    /** @var Schema */
    private $schema = null;

    /** @var AuthenticationServiceInterface */
    private $authenticationService = null;

    /** @var AuthorizationServiceInterface */
    private $authorizationService = null;

    /** @var NamespaceMapperInterface[] */
    private $namespaceMappers = null;

    /** @var ContainerInterface */
    private $container = null;

    public function __construct(
        iterable $namespaceMappers,
        AuthenticationServiceInterface $authenticationService,
        AuthorizationServiceInterface $authorizationService,
        ContainerInterface $container
    ) {
        $this->namespaceMappers = $namespaceMappers;
        $this->authenticationService = $authenticationService;
        $this->authorizationService = $authorizationService;
        $this->container = $container;
    }

    public function getSchema(): Schema
    {
        if (null !== $this->schema) {
            return $this->schema;
        }

        $factory = new GraphQLiteSchemaFactory(
            new \Symfony\Component\Cache\Simple\NullCache(),
            $this->container
        );

        $classNameMapper = new ClassNameMapper();

        foreach ($this->namespaceMappers as $namespaceMapper) {
            /** @var $namespaceMapper NamespaceMapperInterface */
            foreach ($namespaceMapper->getControllerNamespaceMapping() as $namespace=>$path) {
                $classNameMapper->registerPsr4Namespace(
                    $namespace,
                    $path
                );
                $factory->addControllerNameSpace($namespace);
            }
            /** @var $namespaceMapper NamespaceMapperInterface */
            foreach ($namespaceMapper->getTypeNamespaceMapping() as $namespace=>$path) {
                $classNameMapper->registerPsr4Namespace(
                    $namespace,
                    $path
                );
                $factory->addTypeNameSpace($namespace);
            }
        }

        $factory->setClassNameMapper($classNameMapper);

        $factory->setAuthenticationService($this->authenticationService)
                ->setAuthorizationService($this->authorizationService);

        return $this->schema = $factory->createSchema();
    }
}
