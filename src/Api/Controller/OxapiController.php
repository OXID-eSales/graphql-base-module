<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Api\Controller;

use GraphQL\Error\FormattedError;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\InvalidRequest;
use OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler;
use OxidEsales\GraphQL\Base\Framework\RequestReader;
use OxidEsales\GraphQL\Base\Framework\TimerHandler;
use OxidEsales\GraphQL\Base\Service\Authentication as GraphQLAuthenticationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

readonly class OxapiController
{
    public const SESSION_ERROR_MESSAGE = 'OXID eShop PHP session spotted. Ensure you have skipSession=1 '
    . 'parameter sent to the widget.php. For more information about the problem, check '
    . 'Troubleshooting section in documentation.';

    public function __construct(
        private readonly TimerHandler $timerHandler,
        private readonly GraphQLQueryHandler $queryHandler
    )
    {
    }

    #[Route('/oxapi/{shp}/{lang}/', requirements: ['shp' => Requirement::DIGITS, 'lang' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function process(int $shp, int $lang): Response
    {
        $_POST['shp'] = $shp;
        $_POST['lang'] = $lang;

        /** @var TimerHandler $timerHandler */
        $this->timerHandler
            ->create('bootstrap')
            ->startAt($_SERVER['REQUEST_TIME_FLOAT'])
            ->stop();

        $headers = [
            'Content-Type: application/json',
            $this->generateServerTimingHeader()
        ];
        $status = 200;

        try {
            $this->handleShopSession();
            $result = $this->queryHandler
                ->executeGraphQLQuery();
        } catch (Error $e) {
            $result = [
                'errors' => [
                    FormattedError::createFromException($e)
                ]
            ];
            if (!$this->isAuthenticated($e)) {
                $headers[] = 'WWW-Authenticate: Bearer';
            }
        } catch (\Throwable $e) {
            EshopRegistry::getLogger()->error($e->getMessage(), [$e]);
            $result = [
                'errors' => [
                    FormattedError::createFromException($e)
                ]
            ];
            $status = 500;
        }

        return new JsonResponse($result, $status, $headers);
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

        $authentication = ContainerFacade::get(GraphQLAuthenticationService::class);
        $userId = $authentication->getUser()->id()->val();

        if ($userId) {
            $session->setVariable('usr', $userId);
        }
    }

    private function generateServerTimingHeader(): string
    {
        $timings = [];

        foreach ($this->timerHandler->getTimers() as $name => $timer) {
            $timings[] = sprintf('%s;dur=%.3f', $name, $timer->getDuration() * 1000);
        }

        return 'Server-Timing: ' . implode(',', $timings);
    }

    private function isAuthenticated(Error $error): bool
    {
        return $error->getCategory() !== ErrorCategories::PERMISSIONERRORS;
    }
}
