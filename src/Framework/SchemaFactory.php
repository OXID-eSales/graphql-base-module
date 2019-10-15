<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

use Mouf\Composer\ClassNameMapper;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Service\AuthenticationServiceInterface;
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
    private $authService = null;

    private $namespaceMappers = null;

    public function __construct(iterable $namespaceMappers, AuthenticationServiceInterface $authService)
    {
        $this->namespaceMappers = $namespaceMappers;
        $this->authService = $authService;
    }

    public function getSchema(): Schema
    {
        if (null !== $this->schema) {
            return $this->schema;
        }

        $factory = new GraphQLiteSchemaFactory(
            new \Symfony\Component\Cache\Simple\NullCache(),
            ContainerFactory::getInstance()->getContainer()
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

        $factory->setAuthenticationService($this->authService)
                ->setAuthorizationService($this->authService);

        return $this->schema = $factory->createSchema();
    }
}
