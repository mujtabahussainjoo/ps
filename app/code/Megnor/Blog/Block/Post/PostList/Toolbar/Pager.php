<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@megnor.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Megnor\Blog\Block\Post\PostList\Toolbar;

use Megnor\Blog\Model\Config\Source\LazyLoad;

/**
 * Blog posts list toolbar pager
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{

    /**
     * Retrieve url of all pages
     *
     * @return string
     */
    public function getPagesUrls()
    {
        $urls = [];
        for ($page = $this->getCurrentPage() + 1; $page <= $this->getLastPageNum(); $page++) {
            $urls[$page] = $this->getPageUrl($page);
        }

        return $urls;
    }

    /**
     * Retrieve true olny if can use lazyload
     *
     * @return bool
     */
    public function useLazyload()
    {
        $lastPage = $this->getLastPageNum();
        $currentPage = $this->getCurrentPage();

        return $this->getLazyloadMode()
            && $this->getCollection()->getSize()
            && $lastPage > 1
            && $currentPage < $lastPage;
    }

    /**
     * Retrieve lazyload json config string
     * @param array $config
     *
     * @return string
     */
    public function getLazyloadConfig(array $config = [])
    {
        $config = array_merge([
            'page_url' => $this->getPagesUrls(),
            'current_page' => $this->getCurrentPage(),
            'last_page' => $this->getLastPageNum(),
            'padding' => $this->getLazyloadPadding(),
            'list_wrapper' => $this->getListWrapper(),
            'auto_trigger' => $this->getLazyloadMode() == LazyLoad::ENABLED_WITH_AUTO_TRIGER,
        ], $config);

        return json_encode($config);
    }

    /**
     * Retrieve lazyload mod
     *
     * @return int
     */
    public function getLazyloadMode()
    {
        return (int) $this->_scopeConfig->getValue(
            'mfblog/post_list/lazyload_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve lazyload padding
     *
     * @return int
     */
    public function getLazyloadPadding()
    {
        return (int) $this->_scopeConfig->getValue(
            'mfblog/post_list/lazyload_padding',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
