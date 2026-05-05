<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Seo\Infrastructure\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Category;

/*
 * This class is used to create proxy-methods for needed protected methods.
 * This class will be removed as far as the used methods are public.
 * Please avoid using this class in your code if possible!
 */

class SeoEncoderArticle extends SeoEncoderArticle_parent
{
    public function oegbGetCategoryUri(Article $article, Category $category, int $languageId): string
    {
        /** @phpstan-ignore method.notFound */
        return $this->createArticleCategoryUri($article, $category, $languageId);
    }
}
