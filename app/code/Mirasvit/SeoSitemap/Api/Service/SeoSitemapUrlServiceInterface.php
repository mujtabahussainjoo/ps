<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.146
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Api\Service;

interface SeoSitemapUrlServiceInterface
{
    /**
     * @return string
     */
    public function getBaseRoute();

    /**
     * @return string
     */
    public function getConfigUrSuffix();

    /**
     * @return string
     */
    public function getBaseUrl();

    /**
     * @param $pathInfo
     * @return bool|array
     */
    public function match($pathInfo);
}