<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;

/**
 * Class LoginType
 *
 * @package OxidEsales\GraphQl\Type
 */
class LoginType extends BaseType
{

    /** @var AuthenticationServiceInterface $authService */
    private $authService;

    /** @var  EnvironmentServiceInterface $environmentService */
    private $environmentService;

    public function __construct(
        AuthenticationServiceInterface $authService,
        EnvironmentServiceInterface $environmentService)
    {
        $this->authService = $authService;
        $this->environmentService = $environmentService;

        $config = [
            'name' => 'Token',
            'description' => 'Authentification token',
            'fields' => ['token' => Type::string()],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                return $value;
            }
        ];
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function getQueriesOrMutations()
    {
        return [
            'login'  => [
                'type'        => $this,
                'description' => 'Returns a jason web token according to the provide credentials. ' .
                'If no credentials are given, a token for anonymous login is returned.',
                'args'        => [
                    'username' => Type::string(),
                    'password' => Type::string(),
                    'lang' => Type::string(),
                    'shopid' => Type::int()
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function getQueryOrMutationHandlers()
    {
        return [
            'login' => function ($value, $args, $context, ResolveInfo $info) {
                $tokenRequest = new TokenRequest();
                $tokenRequest->setUsername($args['username']);
                $tokenRequest->setPassword($args['password']);
                if ($args['lang']) {
                    $tokenRequest->setLang($args['lang']);
                }
                else {
                    $tokenRequest->setLang($this->environmentService->getDefaultLanguage());
                }
                if ($args['shopid']) {
                    $tokenRequest->setShopid($args['shopid']);
                }
                else {
                    $tokenRequest->setShopid($this->environmentService->getDefaultShopId());
                }
                $tokenRequest->setCurrentToken($context->getToken());
                return $this->authService->getToken($tokenRequest);
            }
        ];
    }
}
