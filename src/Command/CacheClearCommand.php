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
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheClearCommand extends Command
{
    public function __construct(private readonly CacheInterface $cache)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Clear schema cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        if (!$this->cache->clear()) {
            $style->error('Failed to clear schema cache.');

            return Command::FAILURE;
        }

        $style->success('Schema cache cleared.');

        return Command::SUCCESS;
    }
}
