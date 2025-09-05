<?php

namespace Bydn\VirtualMirror\Block;

class VirtualMirror extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry $registry
     */
    private \Magento\Framework\Registry $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * Class constructor
     * 
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     * @return void
    */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get URL for image generation endpoint
     */
    public function getImageGenerationEndpoint()
    {
        return $this->getUrl('virtualmirror/generateimage/index');
    }
    
    /**
     * Get current product ID
     */
    public function getProductId()
    {
        return $this->getCurrentProduct()->getId();
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
