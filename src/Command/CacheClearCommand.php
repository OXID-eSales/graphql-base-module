<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Command;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'oe:graphql:cache-clear',
    description: 'Clear schema cache'
)]
class CacheClearCommand extends Command
{
    public function __construct(private readonly CacheInterface $cache)
    {
        parent::__construct();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Clearing schema cache...</info>');
        $this->cache->clear();
        $output->writeln('<info>Schema cache cleared.</info>');

        return Command::SUCCESS;
    }
}
