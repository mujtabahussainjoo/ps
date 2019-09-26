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



namespace Mirasvit\Seo\Plugin\Frontend\Theme\Block\Html\Pager;

use Magento\Framework\App\RequestInterface;

class RemoveFirstPageParamPlugin
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Theme\Block\Html\Pager $subject
     * @param string                          $url
     * @return string
     */
    public function afterGetPageUrl($subject, $url)
    {
        if ($this->request->isAjax()) {
            // return original url (Sm_ShopBy compatibility)
            return $url;
        }
        \Magento\Framework\Profiler::start(__METHOD__);
        $url = $this->removeFirstPage($url);
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $url;
    }

    /**
     * @param string $url
     * @return string $url
     */
    private function removeFirstPage($url)
    {
        if (preg_match('/p=1/', $url)) {
            $url = trim(str_replace('&amp;', '&', $url));
        } else {
            return $url;
        }

        if (preg_match('/\?p=1$/', $url)) {
            $url = str_replace('?p=1', '', $url);
        } elseif (preg_match('/&p=1$/', $url)) {
            $url = str_replace('&p=1', '', $url);
        } elseif (preg_match('/\?p=1&/', $url) || preg_match('/&p=1&/', $url)) {
            $url = str_replace('p=1&', '', $url);
        }

        return $url;
    }
}
