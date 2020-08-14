<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Full\Quallified\Namespace\Tests\Codeception\Acceptance;

use Full\Quallified\Namespace\Tests\Codeception\AcceptanceTester;

class CategoryQueryCest
{
    public function testFetchSingleCategoryById(AcceptanceTester $I): void
    {
        $I->haveHTTPHeader('Content-Type', 'application/json');
        $I->sendPOST('/graphql/', [
            'query' => 'query {
                token (username: "admin", password: "admin")
            }'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        // assert that result is a valid JWT
    }
}
