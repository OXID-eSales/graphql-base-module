<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Dao;

use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\NoTokenFoundException;
use OxidEsales\GraphQl\Exception\TooManyTokensException;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;

class TokenDao implements TokenDaoInterface
{
    const TOKENTABLE = 'oxgraphqltokens';

    /** @var QueryBuilderFactoryInterface $queryBuilderFactory */
    private $queryBuilderFactory;

    /** @var KeyRegistryInterface $keyRegistry */
    private $keyRegistry;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        KeyRegistryInterface $keyRegistry
    )
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->keyRegistry = $keyRegistry;
    }

    public function saveOrUpdateToken(Token $token)
    {
        try {
            $this->loadToken($token->getSubject(), $token->getShopid());
        } catch (NoTokenFoundException $e) {
            $this->insertToken($token);
            return;
        }
        $this->updateToken($token);

    }

    private function insertToken(Token $token)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->insert($this::TOKENTABLE)
            ->values(
                array(
                    'username' => ':username',
                    'token' => ':token',
                    'shopid' => ':shopid'
                )
            )
            ->setParameter('username', $token->getSubject())
            ->setParameter('shopid', $token->getShopid())
            ->setParameter('token', $token->getJwt());

        $queryBuilder->execute();
    }

    private function updateToken(Token $token)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->update($this::TOKENTABLE)
            ->set('token', ':token')
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('username', ':username'),
                $queryBuilder->expr()->eq('shopid', ':shopid')
            ))
            ->setParameter('token', $token->getJwt())
            ->setParameter('username', $token->getSubject())
            ->setParameter('shopid', $token->getShopid());
        $queryBuilder->execute();

    }

    public function loadToken($username, $shopid): Token
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('token')
            ->from($this::TOKENTABLE)
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('username', ':username'),
                $queryBuilder->expr()->eq('shopid', ':shopid')
            ))
            ->setParameter('username', $username)
            ->setParameter('shopid', $shopid);
        $result = $queryBuilder->execute();
        $row = $result->fetchAll();
        if (! $row) {
            throw new NoTokenFoundException("The is no token for user $username and shop $shopid.");
        }
        if (sizeof($row) != 1) {
            throw new TooManyTokensException("There are too many tokens for user $username and shop $shopid.");
        }
        $token = new Token($this->keyRegistry->getSignatureKey());
        $token->setJwt($row[0]['token']);

        return $token;

    }

}
