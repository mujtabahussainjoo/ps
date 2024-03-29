<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@megnor.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Megnor\Blog\Controller\Adminhtml\Post;

/**
 * Blog post related posts controller
 */
class RelatedPosts extends \Megnor\Blog\Controller\Adminhtml\Post
{
    /**
     * View related posts action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $model = $this->_getModel();
        $this->_getRegistry()->register('current_model', $model);

        $this->_view->loadLayout()
            ->getLayout()
            ->getBlock('blog.post.edit.tab.relatedposts')
            ->setPostsRelated($this->getRequest()->getPost('posts_related', null));

        $this->_view->renderLayout();
    }
}
