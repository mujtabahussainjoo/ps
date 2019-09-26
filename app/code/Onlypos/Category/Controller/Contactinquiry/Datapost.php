<?php

   namespace Onlypos\Category\Controller\Contactinquiry;

   use Magento\Framework\App\Action\Context;

   class Datapost extends \Magento\Framework\App\Action\Action{

       public function __construct(Context $context)
       {
           parent::__construct($context);
       }
       public function execute()
       {
           echo "Ram"; exit;
       }
   }