<?php
namespace Salecto\Ordergrid\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Store\Model\StoreManagerInterface;

class Products extends Column
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteria;
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepository;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_requestInterfase;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;
    
    /**
     * Products constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\View\Asset\Repository $assetRepository
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param SearchCriteriaBuilder $criteria
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Magento\Framework\App\RequestInterface $requestInterface,
        SearchCriteriaBuilder $criteria,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $components = [],
        array $data = []
    ) {
    
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->_assetRepository = $assetRepository;
        $this->_requestInterfase= $requestInterface;
        $this->_orderFactory    = $orderFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
    	//echo "<pre>";print_r($dataSource);exit;    	
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $order  = $this->_orderRepository->get($item["entity_id"]);
				$products_info = $options = '';
				
				foreach ($order->getAllVisibleItems() as $o_items)
			    {
					 if($_options = $this->getSelectedOptions($o_items)) {
							$options .= '<dt class="options">';
								foreach ($_options as $_option) : 
									$options .= '<dd>'.$_option['label'].'</dd><dd>'.$_option['value'].'</dd>';
								endforeach; 
							$options .= '</dt>';
						}
			          $products_info .= "<b>".$o_items->getName()."</b><br/><b>".$o_items->getSku()."</b><br/><br/>";
			    }
				
				$item['product_name'] =
                        "<div style='width: 100%;margin: 0 auto;text-align: left'>$products_info.$options</div>";
				//echo "<pre>";print_r($item['product_name']);exit;
            }
        }
        return $dataSource;
    }
	
	/*
     * get Configurable/Bundle selected options from Item object
     */
    public function getSelectedOptions($item){
     $result = [];
        $options = $item->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
	}    
}
