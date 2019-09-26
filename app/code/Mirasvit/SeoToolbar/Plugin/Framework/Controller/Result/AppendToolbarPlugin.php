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



namespace Mirasvit\SeoToolbar\Plugin\Framework\Controller\Result;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\LayoutInterface;

class AppendToolbarPlugin
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    private $layout;

    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        LayoutInterface $layout
    ) {
        $this->request  = $request;
        $this->response = $response;
        $this->layout   = $layout;
    }

    /**
     * @param \Magento\Framework\Controller\ResultInterface $subject
     * @param object                                        $result
     *
     * @return object
     */
    public function afterRenderResult($subject, $result)
    {
        if (preg_match('/checkout|customer/', $this->request->getUri())) {
            return $result;
        }

        if ($this->response->getStatusCode() !== 200) {
            return $result;
        }

        if ($this->request->getParam('_')
            || $this->request->getParam('is_ajax')
            || $this->request->getParam('isAjax')) {
            return $result;
        }

        /** @var \Mirasvit\SeoToolbar\Block\Toolbar $toolbar */
        if ($toolbar = $this->layout->createBlock(\Mirasvit\SeoToolbar\Block\Toolbar::class)) {
            $this->response->appendBody($toolbar->toHtml());
        }

        return $result;
    }
}
