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



namespace Mirasvit\SeoContent\Controller\Adminhtml\Rewrite;

use Mirasvit\SeoContent\Api\Data\RewriteInterface;
use Mirasvit\SeoContent\Controller\Adminhtml\Rewrite;

class Save extends Rewrite
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam(RewriteInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParams();

        if ($data) {
            $model          = $this->initModel();
            $resultRedirect = $this->resultRedirectFactory->create();

            if ($id && !$model) {
                $this->messageManager->addErrorMessage(__('This rewrite no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->setUrl($data[RewriteInterface::URL])
                ->setIsActive($data[RewriteInterface::IS_ACTIVE])
                ->setSortOrder($data[RewriteInterface::SORT_ORDER])
                ->setTitle($data[RewriteInterface::TITLE])
                ->setMetaTitle($data[RewriteInterface::META_TITLE])
                ->setMetaKeywords($data[RewriteInterface::META_KEYWORDS])
                ->setMetaDescription($data[RewriteInterface::META_DESCRIPTION])
                ->setDescription($data[RewriteInterface::DESCRIPTION])
                ->setDescriptionPosition($data[RewriteInterface::DESCRIPTION_POSITION])
                ->setDescriptionTemplate($data[RewriteInterface::DESCRIPTION_TEMPLATE])
                ->setStoreIds($data[RewriteInterface::STORE_IDS]);

            try {
                $this->rewriteRepository->save($model);

                $this->messageManager->addSuccessMessage(__('Rewrite was saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [RewriteInterface::ID => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [RewriteInterface::ID => $id]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }
}
