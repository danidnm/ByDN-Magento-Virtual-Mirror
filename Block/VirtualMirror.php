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
     * \Magento\Customer\Model\Session $customerSession
     */
    private \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig 
     */
    private \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig;

    /**
     * Class constructor
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     * @return void
    */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->virtualMirrorConfig = $virtualMirrorConfig;
        parent::__construct($context, $data);
    }

    /**
     * Block is only visible if customer is logged in
     */
    public function isVisible()
    {
        // Check module enable
        if (!$this->virtualMirrorConfig->isEnabled()) {
            return false;
        }

        // No customer. Not visible.
        if (!$this->customerSession->isLoggedIn()) {
            return false;
        }

        // Not available on bundle, virtual or downloadable products
        if (!in_array(
                $this->getCurrentProduct()->getTypeId(), 
                array(
                    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, 
                    \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
                )
            )
        ) {
            return false;
        }

        // Check enable for attribute set on current product
        $attributeSetId = $this->getCurrentProduct()->getAttributeSetId();
        if (!$this->virtualMirrorConfig->isEnabledForSet($attributeSetId)) {
            return false;
        }

        return true;
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
     * Return if simple 
     */
    public function getUseSimpleSelected()
    {
        $productType = $this->getCurrentProduct()->getTypeId();
        $useSimple = $this->virtualMirrorConfig->getUseSimple();
        return (($productType == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) && $useSimple);
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
