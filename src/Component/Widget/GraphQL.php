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
use OxidEsales\GraphQl\Framework\SchemaFactoryInterface;

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

    /**
     * Execute the GraphQL query
     *
     * @throws \Throwable
     */
    public function executeQuery(AppContext $context, $query)
    {
        $httpStatus = 200;
        $output = ['msg' => 'Controller is working'];
        $this->renderJsonResponse($output, $httpStatus);
    }

    /**
     * set the AppContext for use in passing down the Resolve Tree
     *
     * @return \OxidProfessionalServices\GraphQl\Core\AppContext
     */
    private function setAppContext()
    {
        $this->_oAppContext = oxNew(AppContext::class);

        $oAuth = oxNew(Auth::class);
        $aContext = $oAuth->authorize();
        $this->_oAppContext->viewer = $aContext->sub;
        $this->_oAppContext->rootUrl = $aContext->aud;

        $this->_oAppContext->request = !empty( $_REQUEST ) ? $_REQUEST : null;

        return $this->_oAppContext;
    }

    /**
     * Get the AppContext for use in passing down the Resolve Tree
     *
     * @return \OxidProfessionalServices\GraphQl\Core\AppContext
     */
    private function getAppContext()
    {
        $this->appContext = new \OxidEsales\GraphQl\Framework\AppContext();
        return $this->appContext;
    }


    /**
     * Returns the Schema as defined by static registrations
     *
     * @return GraphQL\Type\Schema
     */
    public function getSchema()
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
    protected function renderJsonResponse($result, $httpStatus)
    {
        /**
         * Force json content type by oxid framework
         */
        $_GET['renderPartial'] = 1;

        $utils = Registry::getUtils();
        $utils->setHeader('Content-Type: application/json', true, $httpStatus);

        $utils->showMessageAndExit(json_encode($result));

    }
}