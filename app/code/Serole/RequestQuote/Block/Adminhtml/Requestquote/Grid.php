<?php
namespace Serole\RequestQuote\Block\Adminhtml\Requestquote;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\RequestQuote\Model\requestquoteFactory
     */
    protected $_requestquoteFactory;

    /**
     * @var \Serole\RequestQuote\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\RequestQuote\Model\requestquoteFactory $requestquoteFactory
     * @param \Serole\RequestQuote\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\RequestQuote\Model\RequestquoteFactory $RequestquoteFactory,
        \Serole\RequestQuote\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_requestquoteFactory = $RequestquoteFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_requestquoteFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


		
				$this->addColumn(
					'name',
					[
						'header' => __('name'),
						'index' => 'name',
					]
				);
				
				$this->addColumn(
					'email',
					[
						'header' => __('email'),
						'index' => 'email',
					]
				);
				
				$this->addColumn(
					'mobile',
					[
						'header' => __('mobile'),
						'index' => 'mobile',
					]
				);
				
				$this->addColumn(
					'company',
					[
						'header' => __('company'),
						'index' => 'company',
					]
				);
				
				$this->addColumn(
					'industry',
					[
						'header' => __('industry'),
						'index' => 'industry',
					]
				);
				
				$this->addColumn(
					'requrement',
					[
						'header' => __('requirement'),
						'index' => 'requrement',
					]
				);
				
				$this->addColumn(
					'budget',
					[
						'header' => __('budget'),
						'index' => 'budget',
					]
				);
				
				$this->addColumn(
					'date',
					[
						'header' => __('date'),
						'index' => 'date',
						'type'      => 'datetime',
					]
				);
					
					


		
        //$this->addColumn(
            //'edit',
            //[
                //'header' => __('Edit'),
                //'type' => 'action',
                //'getter' => 'getId',
                //'actions' => [
                    //[
                        //'caption' => __('Edit'),
                        //'url' => [
                            //'base' => '*/*/edit'
                        //],
                        //'field' => 'id'
                    //]
                //],
                //'filter' => false,
                //'sortable' => false,
                //'index' => 'stores',
                //'header_css_class' => 'col-action',
                //'column_css_class' => 'col-action'
            //]
        //);
		

		
		   $this->addExportType($this->getUrl('requestquote/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('requestquote/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Serole_RequestQuote::requestquote/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('requestquote');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('requestquote/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('requestquote/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('requestquote/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\RequestQuote\Model\requestquote|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'requestquote/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	

}