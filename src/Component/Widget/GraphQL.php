<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Component\Widget;

use GraphQL\Error\FormattedError;
use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidRequest;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler;
use OxidEsales\GraphQL\Base\Framework\TimerHandler;
use OxidEsales\GraphQL\Base\Service\Authentication as GraphQLAuthenticationService;
use Throwable;

/**
 * Class GraphQL
 *
 * Implements the GraphQL widget for the OXID eShop to make all
 * of this callable via a SEO Url or via widget.php?cl=graphql
 */
class GraphQL extends WidgetController
{
    public const SESSION_ERROR_MESSAGE = 'OXID eShop PHP session spotted. Ensure you have skipSession=1 '
        . 'parameter sent to the widget.php. For more information about the problem, check '
        . 'Troubleshooting section in documentation.';

    /**
     * Init function
     */
    public function init(): void
    {
        // handle preflight CORS request
        // https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS#Preflighted_requests
        if (
            strtoupper($_SERVER['REQUEST_METHOD']) === 'OPTIONS' &&
            isset(
                $_SERVER['HTTP_ORIGIN'],
                $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'],
                $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']
            )
        ) {
            $header = oxNew(\OxidEsales\Eshop\Core\Header::class);
            $header->setHeader('HTTP/1.1 204 No Content');
            $header->setHeader('Access-Control-Allow-Methods: POST, GET, OPTIONS');
            $header->setHeader('Access-Control-Allow-Headers: Content-Type, Authorization');
            $header->setHeader('Access-Control-Allow-Origin: *');
            $header->sendHeader();

            exit;
        }

        /** @var TimerHandler */
        $timerHandler = ContainerFactory::getInstance()->getContainer()->get(TimerHandler::class);
        $timerHandler->create('bootstrap')->startAt($_SERVER['REQUEST_TIME_FLOAT'])->stop();

        try {
            $this->handleShopSession();

            ContainerFactory::getInstance()
                ->getContainer()
                ->get(GraphQLQueryHandler::class)
                ->executeGraphQLQuery();
        } catch (Error $e) {
            $isAuthenticated = !($e instanceof InvalidLogin || $e instanceof InvalidToken);
            self::sendErrorResponse(FormattedError::createFromException($e), 200, $isAuthenticated);
        } catch (Throwable $e) {
            EshopRegistry::getLogger()->error($e->getMessage(), [$e]);
            self::sendErrorResponse(FormattedError::createFromException($e), 500);
        }
    }

    private function handleShopSession(): void
    {
        //if there's already a php session running, bail out to prevent inconsistent behaviour
        if (PHP_SESSION_NONE !== session_status()) {
            throw new InvalidRequest(self::SESSION_ERROR_MESSAGE);
        }

        $this->setShopUserFromToken();
    }

    private function setShopUserFromToken(): void
    {
        $session = EshopRegistry::getSession();
        $session->setUser(null);
        $session->setBasket(null);
        $session->setVariable('usr', null);

        try {
            $userId = ContainerFactory::getInstance()
                ->getContainer()
                ->get(GraphQLAuthenticationService::class)
                ->getUserId();

            if ($userId) {
                $session->setVariable('usr', $userId);
            }
        } catch (InvalidToken $exception) {
            //all is well so far
        }
    }

    public static function sendErrorResponse(array $message, int $status, bool $isAuthenticated = true): void
    {
        $body = [
            'errors' => [
                $message,
            ],
        ];

        header('Content-Type: application/json', true, $status);

        if (!$isAuthenticated) {
            header('WWW-Authenticate: Bearer', true, $status);
        }

        print json_encode($body);

        exit;
    }
}
