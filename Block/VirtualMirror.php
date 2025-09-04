<?php

namespace Bydn\VirtualMirror\Block;

class VirtualMirror extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Helper\Image $imageHelper
     */
    private \Magento\Catalog\Helper\Image $imageHelper;

    /**
     * @var \Magento\Framework\Registry $registry
     */
    private \Magento\Framework\Registry $registry;

    /**
     * @var \Bydn\VirtualMirror\Helper\Config $config
     */
    private \Bydn\VirtualMirror\Helper\Config $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * Class constructor
     * 
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     * @return void
    */
    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Registry $registry,
        \Bydn\VirtualMirror\Helper\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->imageHelper = $imageHelper;
        $this->registry = $registry;
        $this->config = $config;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getGenerateImageEndpoint()
    {
        return $this->getUrl('virtualmirror/api/generateImage');
    }
    
    /**
    * Get base image URL of current product
    *
    * @return string|null
    */
    public function getProductBaseImage()
    {
        return $this->getBaseMediaUrl() . 'catalog/product/' . $this->getCurrentProduct()->getImage();
    }

    /**
    * Get current product from registry
    *
    * @return \Magento\Catalog\Model\Product|null
    */
    protected function getCurrentProduct()
    {
       return $this->registry->registry('current_product');
    }

    /**
     * Returns the base URL of the media folder
     */
    protected function getBaseMediaUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
