<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQl\Exception\NoSignatureKeyException;
use OxidEsales\GraphQl\Exception\TooManySignatureKeysException;

/**
 * Class KeyRegistry
 *
 * The current implementation stores the signature key in
 * the config table. This should be changed eventually.
 *
 * @package OxidEsales\GraphQl\Service
 */
class KeyRegistry implements KeyRegistryInterface
{

    private $tableName = 'graphqlsignaturekey';

    private $columnName = 'signaturekey';

    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    public function __construct(QueryBuilderFactoryInterface $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    public function createSignatureKey()
    {
        $this->createTableIfNecessary();
        try {
            $this->getSignatureKey();
        } catch (NoSignatureKeyException $e) {
            $key = base64_encode(openssl_random_pseudo_bytes(64));
            $this->queryBuilderFactory
                ->create()
                ->insert($this->tableName)
                ->values([$this->columnName => '?'])
                ->setParameter(0, $key)
                ->execute();
        }
    }

    public function getSignatureKey()
    {
        try {
            $result = $this->queryBuilderFactory->create()->select($this->columnName)->from($this->tableName)->execute();
        } catch (\Exception $e) {
            throw new NoSignatureKeyException();
        }
        $rows = $result->fetchAll();
        if (sizeof($rows) === 0) {
            throw new NoSignatureKeyException();
        }
        if (sizeof($rows) > 1) {
            throw new TooManySignatureKeysException();
        }
        return $rows[0][$this->columnName];

    }

    private function createTableIfNecessary()
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $connection = $queryBuilder->getConnection();
        $connection->exec('CREATE TABLE IF NOT EXISTS ' . $this->tableName .
            ' (' . $this->columnName . ' VARCHAR(128))');
    }

}
