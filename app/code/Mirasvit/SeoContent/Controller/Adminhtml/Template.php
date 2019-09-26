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
use Magento\Framework\Registry;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;
use Magento\Backend\App\Action\Context;

abstract class Template extends Action
{
    protected $templateRepository;

    private $registry;

    protected $context;

    protected $resultFactory;

    public function __construct(
        TemplateRepositoryInterface $templateRepository,
        Registry $registry,
        Context $context
    ) {
        $this->templateRepository = $templateRepository;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return TemplateInterface
     */
    protected function initModel()
    {
        $template = $this->templateRepository->create();

        if ($this->getRequest()->getParam(TemplateInterface::ID)) {
            $template = $this->templateRepository->get($this->getRequest()->getParam(TemplateInterface::ID));
        }

        $this->registry->register(TemplateInterface::class, $template);

        return $template;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_SeoContent::seo_content_template');
        $resultPage->getConfig()->getTitle()->prepend(__('Advanced SEO Suite'));
        $resultPage->getConfig()->getTitle()->prepend(__('Template Manager'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_SeoContent::seo_content_template');
    }
}
