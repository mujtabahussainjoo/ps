<?php

namespace Serole\RequestQuote\Block\Adminhtml\Requestquote\Edit\Tab;

/**
 * Requestquote edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Serole\RequestQuote\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Serole\RequestQuote\Model\Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Serole\RequestQuote\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('requestquote');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

		
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('name'),
                'title' => __('name'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('email'),
                'title' => __('email'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'mobile',
            'text',
            [
                'name' => 'mobile',
                'label' => __('mobile'),
                'title' => __('mobile'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'company',
            'text',
            [
                'name' => 'company',
                'label' => __('company'),
                'title' => __('company'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'industry',
            'text',
            [
                'name' => 'industry',
                'label' => __('industry'),
                'title' => __('industry'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'requrement',
            'text',
            [
                'name' => 'requrement',
                'label' => __('requirement'),
                'title' => __('requirement'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('description'),
                'title' => __('description'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'budget',
            'text',
            [
                'name' => 'budget',
                'label' => __('budget'),
                'title' => __('budget'),
				
                'disabled' => $isElementDisabled
            ]
        );
					

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::MEDIUM
        );

        $fieldset->addField(
            'date',
            'date',
            [
                'name' => 'date',
                'label' => __('date'),
                'title' => __('date'),
                    'date_format' => $dateFormat,
                    //'time_format' => $timeFormat,
				
                'disabled' => $isElementDisabled
            ]
        );
						
						
						

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
		
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    
    public function getTargetOptionArray(){
    	return array(
    				'_self' => "Self",
					'_blank' => "New Page",
    				);
    }
}
