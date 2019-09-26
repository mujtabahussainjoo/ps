<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@megnor.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Megnor\Blog\Model\ResourceModel\Author;

/**
 * Blog author collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Megnor\Blog\Model\Author', 'Megnor\Blog\Model\ResourceModel\Author');
    }
}
