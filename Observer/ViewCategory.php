<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 */

namespace Facebook\BusinessExtension\Observer;

use Facebook\BusinessExtension\Helper\FBEHelper;
use Facebook\BusinessExtension\Helper\ServerSideHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

use Facebook\BusinessExtension\Helper\ServerEventFactory;

class ViewCategory implements ObserverInterface
{
  /**
   * @var FBEHelper
   */
    protected $_fbeHelper;

  /**
   * @var ServerSideHelper
   */
    protected $_serverSideHelper;

  /**
   * \Magento\Framework\Registry
   */
    protected $_registry;

    public function __construct(
        FBEHelper $fbeHelper,
        ServerSideHelper $serverSideHelper,
        \Magento\Framework\Registry $registry
    ) {
        $this->_fbeHelper = $fbeHelper;
        $this->_registry = $registry;
        $this->_serverSideHelper = $serverSideHelper;
    }

    public function execute(Observer $observer)
    {
        try {
            $eventId = $observer->getData('eventId');
            $customData = [];
            $category = $this->_registry->registry('current_category');
            if ($category) {
                $customData['content_category'] = addslashes($category->getName());
            }
            $event = ServerEventFactory::createEvent('ViewCategory', $customData, $eventId);
            $this->_serverSideHelper->sendEvent($event);
        } catch (Exception $e) {
            $this->_fbeHelper->log(json_encode($e));
        }
        return $this;
    }
}
