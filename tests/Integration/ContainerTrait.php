<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\GraphQl\Tests\Integration;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Application\Utility\GraphQlTypePass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

trait ContainerTrait
{

    /**
     * @var ContainerBuilder
     */
    private $symfonyContainer;

    public function createUncompiledContainer()
    {
        $this->symfonyContainer = new ContainerBuilder();
        $this->symfonyContainer->addCompilerPass(new GraphQlTypePass());
        $reflector = new \ReflectionClass(ContainerFactory::class);
        $loader = new YamlFileLoader($this->symfonyContainer, new FileLocator(dirname($reflector->getFileName())));
        $loader->load('services.yaml');

        $this->symfonyContainer = $this->setAllServicesAsPublic($this->symfonyContainer);

        return $this->symfonyContainer;
    }

    private function setAllServicesAsPublic(ContainerBuilder $container): ContainerBuilder
    {
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }

        return $container;
    }

}
