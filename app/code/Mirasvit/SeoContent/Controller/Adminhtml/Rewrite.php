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



namespace Mirasvit\SeoContent\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\SeoContent\Api\Data\RewriteInterface;
use Mirasvit\SeoContent\Api\Repository\RewriteRepositoryInterface;

abstract class Rewrite extends Action
{
    protected $rewriteRepository;

    private $context;

    protected $resultFactory;

    public function __construct(
        RewriteRepositoryInterface $rewriteRepository,
        Context $context
    ) {
        $this->rewriteRepository = $rewriteRepository;

        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return RewriteInterface
     */
    protected function initModel()
    {
        $rewrite = $this->rewriteRepository->create();

        if ($this->getRequest()->getParam(RewriteInterface::ID)) {
            $rewrite = $this->rewriteRepository->get($this->getRequest()->getParam(RewriteInterface::ID));
        }

        return $rewrite;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_SeoContent::seo_content_rewrite');
        $resultPage->getConfig()->getTitle()->prepend(__('Advanced SEO Suite'));
        $resultPage->getConfig()->getTitle()->prepend(__('Rewrite Manager'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_SeoContent::seo_content_rewrite');
    }
}
