<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Command;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends Command
{
    public function __construct(private readonly CacheInterface $cache)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('oe:graphql:cache-clear')
            ->setDescription('Clear schema cache');
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Clearing schema cache...</info>');
        $this->cache->clear();
        $output->writeln('<info>Schema cache cleared.</info>');

        return Command::SUCCESS;
    }
}
