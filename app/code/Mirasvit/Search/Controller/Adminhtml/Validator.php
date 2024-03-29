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
 * @package   mirasvit/module-search
 * @version   1.0.121
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

abstract class Validator extends Action
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    
    /**
     * @var StopwordRepositoryInterface
     */
    protected $stopwordRepository;

    /**
     * @var StopwordServiceInterface
     */
    protected $stopwordService;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_Search::search');

        $resultPage->getConfig()->getTitle()->prepend(__('Validate Search'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Search::search');
    }
}
