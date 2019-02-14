<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @category    module
 *
 * @package     GraphQL
 * @link        http://www.oxid-esales.com
 * @copyright   (C) OXID eSales AG 2003-2018
 * @version     OXID eSales GraphQL
 */

namespace OxidEsales\GraphQl\Component\Widget;

use GraphQL\Error\FormattedError;
use GraphQL\Error\Debug;
use GraphQL\Type\Schema;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Framework\AppContext;
use OxidEsales\GraphQl\Framework\SchemaFactoryInterface;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;

/**
 * Class GraphQL
 *
 * @package OxidEsales\GraphQl\Component\Widget
 */
class GraphQL extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    private $schemaFactory;

    /**
     * @var \OxidEsales\GraphQl\Framework\AppContext
     */
    protected $appContext;

    /**
     * Init function
     *
     * @return void
     */
    public function init()
    {

        $context = $this->initializeAppContext();
        $query = $this->getGraphQLRequest();
        $this->executeQuery($context, $query);

    }

    public function initializeAppContext()
    {
        $container = ContainerFactory::getInstance()->getContainer();
        /** @var EnvironmentServiceInterface $environmentService */
        $environmentService = $container->get(EnvironmentServiceInterface::class);
        /** @var KeyRegistryInterface $keyRegistry */
        $keyRegistry = $container->get(KeyRegistryInterface::class);
        $appContext = new AppContext();
        $appContext->setShopUrl($environmentService->getShopUrl());
        $appContext->setDefaultShopId($environmentService->getDefaultShopId());
        $appContext->setDefaultShopLanguage($environmentService->getDefaultLanguage());
        try {
            $jwt = $this->getAuthToken();
            $token = new Token($keyRegistry->getSignatureKey());
            $token->setJwt($jwt);
            $appContext->setAuthToken($token);
        }
        catch (NoAuthHeaderException $e)
        {
            // pass
        }
        return $appContext;
    }

    private function getAuthToken()
    {
        $authHeader = $this->getAuthorizationHeader();
        if (! $authHeader) {
            throw new NoAuthHeaderException();
        }
        list($jwt) = sscanf( $authHeader, 'Bearer %s');
        return $jwt;
    }
    /**
     *  Get header Authorization
     *
     *  @return $aHeaders array
     */
    private function getAuthorizationHeader(){

        $authHeader = null;

        if (isset($_SERVER['Authorization'])) {
            $authHeader = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            //Nginx or fast CGI
            $authHeader = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix
            //(a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if (isset($aRequestHeaders['Authorization'])) {
                $authHeader = trim($requestHeaders['Authorization']);
            }
        }

        return $authHeader;
    }

    /**
     * Execute the GraphQL query
     *
     * @throws \Throwable
     */
    private function executeQuery(AppContext $context, $query)
    {
        $httpStatus = 200;
        $output = ['msg' => 'Controller is working'];
        $this->renderJsonResponse($output, $httpStatus);
    }

    /**
     * Returns the Schema as defined by static registrations
     *
     * @return Schema
     */
    private function getSchema(): Schema
    {
        if (! $this->schemaFactory) {
            $this->schemaFactory = ContainerFactory::getInstance()->getContainer()->get(SchemaFactoryInterface::class);
        }
        return $this->schemaFactory->getSchema();
    }

    /**
     * Get the Request data
     *
     * @return array
     */
    private function getGraphQLRequest()
    {
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $sRaw = file_get_contents('php://input') ?: '';
            $aData = json_decode($sRaw, true) ?: [];
        } else {
            $aData = $_REQUEST;
        }

        $aData += ['query' => null, 'variables' => null, 'operationName' => null];

        if (null === $aData['query']) {
            $aData['query'] = '{welcome}';
        }

        return $aData;
    }

    /**
     * Return a JSON Object with the graphql results
     *
     * @param $aResult
     */
    private function renderJsonResponse($result, $httpStatus)
    {
        $_GET['renderPartial'] = 1;
        header('Content-Type: application/json', true, $httpStatus);
        exit(json_encode($result));

    }
}