<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12.02.19
 * Time: 15:41
 */

namespace OxidEsales\GraphQl\Service;


/**
 * Class KeyRegistry
 *
 * The current implementation stores the signature key in
 * the config table. This should be changed eventually.
 *
 * @package OxidEsales\GraphQl\Service
 */
interface KeyRegistryInterface
{

    public function createSignatureKey();

    public function getSignatureKey();
}