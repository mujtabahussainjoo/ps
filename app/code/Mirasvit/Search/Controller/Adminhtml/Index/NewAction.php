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


namespace Mirasvit\Search\Controller\Adminhtml\Index;

use Mirasvit\Search\Controller\Adminhtml\Index as ParentIndex;

class NewAction extends ParentIndex
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
