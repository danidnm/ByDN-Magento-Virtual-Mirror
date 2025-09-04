<?php

namespace Bydn\VirtualMirror\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    private const PATH_VIRTUAL_MIRROR_ENABLED = 'bydn_virtualmirror/general/enable';
    private const PATH_VIRTUAL_MIRROR_API_KEY = 'bydn_virtualmirror/general/api_key';

    /**
     * Class constructor
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @return void
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Check if the module is enabled
     *
     * @param null|int|string $storeId
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_VIRTUAL_MIRROR_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Returns the API key
     *
     * @param null|int|string $storeId
     * @return mixed
     */
    public function getApiKey($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_VIRTUAL_MIRROR_API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}