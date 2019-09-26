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


namespace Mirasvit\SeoAutolink\Plugin\Mirasvit\Blog\Block\Post\View;

use Mirasvit\SeoAutolink\Model\Config;
use Mirasvit\SeoAutolink\Helper\Replace as ReplaceHelper;
use Mirasvit\SeoAutolink\Model\Config\Source\Target;

class BlogPostOutput
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ReplaceHelper
     */
    protected $replaceHelper;

    /**
     * @param Config        $config
     * @param ReplaceHelper $replaceHelper
     */
    public function __construct(
        Config $config,
        ReplaceHelper $replaceHelper
    ) {
        $this->config = $config;
        $this->replaceHelper = $replaceHelper;
    }

    /**
     * @param \Mirasvit\Blog\Block\Post\View $block
     * @param string                         $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPostContent($block, $result)
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        if ($this->config->isAllowedTarget(Target::MIRASVIT_BLOG_POST)) {
            $result = $this->replaceHelper->addLinks($result);
        }
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $result;
    }
}
