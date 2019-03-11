<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 04.03.19
 * Time: 10:24
 */

namespace OxidEsales\GraphQl\Type\Provider;

interface MutationProviderInterface
{

    public function getMutations();

    public function getMutationResolvers();
}