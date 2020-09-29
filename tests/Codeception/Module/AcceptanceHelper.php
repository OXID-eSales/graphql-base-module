<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Module;

use Codeception\Module\REST;
use InvalidArgumentException;
use Lcobucci\JWT\Parser;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\AssertionFailedError;

class AcceptanceHelper extends \Codeception\Module
{
    public function _beforeSuite($settings = []): void // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        exec((new Facts())->getShopRootPath() . '/bin/oe-console oe:module:activate oe/graphql-base');
    }

    public function sendGQLQuery(string $query, ?array $variables = null, int $language = 0, int $shopId = 1): void
    {
        $rest = $this->getRest();

        $rest->haveHTTPHeader('Content-Type', 'application/json');
        $rest->sendPOST('/graphql?lang=' . $language . '&shp=' . $shopId, [
            'query'     => $query,
            'variables' => $variables,
        ]);
    }

    public function login(string $username, string $password, int $shopId = 1): void
    {
        $rest = $this->getRest();

        $this->logout();

        $query     = 'query ($username: String!, $password: String!) { token (username: $username, password: $password) }';
        $variables = [
            'username' => $username,
            'password' => $password,
        ];

        $this->sendGQLQuery($query, $variables, 0, $shopId);
        $rest->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $rest->seeResponseIsJson();
        $this->seeResponseContainsValidJWTToken();

        $rest->amBearerAuthenticated($this->grabTokenFromResponse());
    }

    public function logout(): void
    {
        $this->getRest()->deleteHeader('Authorization');
    }

    public function grabJsonResponseAsArray(): array
    {
        return json_decode($this->getRest()->grabResponse(), true);
    }

    public function grabTokenFromResponse(): string
    {
        return $this->grabJsonResponseAsArray()['data']['token'];
    }

    public function seeResponseContainsValidJWTToken(): void
    {
        $parser = new Parser();
        $token  = $this->grabTokenFromResponse();

        try {
            $parser->parse($token);
        } catch (InvalidArgumentException $e) {
            throw new AssertionFailedError(sprintf('Not a valid JWT token: %s', $token));
        }
    }

    protected function getRest(): REST
    {
        return $this->getModule('REST');
    }
}
