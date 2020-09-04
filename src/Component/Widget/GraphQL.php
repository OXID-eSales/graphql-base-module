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
use OxidEsales\GraphQL\Base\Exception\HttpErrorInterface;
use OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler;
use Throwable;

/**
 * Class GraphQL
 *
 * Implements the GraphQL widget for the OXID eShop to make all
 * of this callable via a SEO Url or via widget.php?cl=graphql
 */
class GraphQL extends WidgetController
{
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

        try {
            ContainerFactory::getInstance()
                ->getContainer()
                ->get(GraphQLQueryHandler::class)
                ->executeGraphQLQuery();
        } catch (HttpErrorInterface $e) {
            self::sendErrorResponse(FormattedError::createFromException($e), $e->getHttpStatus());
        } catch (Throwable $e) {
            EshopRegistry::getLogger()->error($e->getMessage(), [$e]);
            self::sendErrorResponse(FormattedError::createFromException($e), 500);
        }
    }

    public static function sendErrorResponse(array $message, int $status): void
    {
        $body = [
            'errors' => [
                $message,
            ],
        ];

        header('Content-Type: application/json', true, $status);

        if (401 == $status) {
            header('WWW-Authenticate: Bearer', true, $status);
        }

        print json_encode($body);

        exit;
    }
}
