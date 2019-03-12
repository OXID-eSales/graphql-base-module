<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\Provider;

interface MutationProviderInterface
{

    public function getMutations();

    public function getMutationResolvers();
}