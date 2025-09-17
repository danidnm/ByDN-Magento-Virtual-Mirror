<?php

namespace Bydn\VirtualMirror\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    private const PATH_VIRTUAL_MIRROR_BASE_CONFIG_PATH = 'bydn_virtualmirror/';

    private const PATH_VIRTUAL_MIRROR_ENABLED = 'bydn_virtualmirror/general/enable';
    private const PATH_VIRTUAL_MIRROR_SETS_ENABLE = 'bydn_virtualmirror/general/attribute_sets_enable';
    private const PATH_VIRTUAL_MIRROR_MODEL = 'bydn_virtualmirror/general/model';
    private const PATH_VIRTUAL_MIRROR_DEFAULT_PROMPT = 'bydn_virtualmirror/general/prompt';
    private const PATH_VIRTUAL_MIRROR_USE_SIMPLE = 'bydn_virtualmirror/general/use_simple';

    private const PATH_VIRTUAL_MIRROR_PROMPTS_BY_SET = 'bydn_virtualmirror/prompt_by_set/prompts';

    private const PATH_VIRTUAL_MIRROR_API_KEY_LEGACY = 'bydn_virtualmirror/general/api_key';

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
     * Checks if the module is enabled for a particular set
     *
     * @param int $setId
     * @param null|int|string $storeId
     * @return mixed
     */
    public function isEnabledForSet($setId, $storeId = null)
    {
        $setEnable = $this->scopeConfig->getValue(
            self::PATH_VIRTUAL_MIRROR_SETS_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $setEnable = explode(',', $setEnable ?? '');
        return (empty($setEnable) || in_array($setId, $setEnable));
    }

    /**
     * Returns the selected model
     *
     * @param null|int|string $storeId
     * @return mixed
     */
    public function getModel($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_VIRTUAL_MIRROR_MODEL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Returns if simple selected should be used in configurable products
     *
     * @param null|int|string $storeId
     * @return mixed
     */
    public function getUseSimple($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_VIRTUAL_MIRROR_USE_SIMPLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Returns prompt to be used
     */
    public function getPrompt($setId = null, $storeId = null)
    {
        // Default prompt
        $promptsDefault = $this->scopeConfig->getValue(
            self::PATH_VIRTUAL_MIRROR_DEFAULT_PROMPT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        // If set specified, try with it
        if ($setId) {
            $promptsBySet = $this->scopeConfig->getValue(
                self::PATH_VIRTUAL_MIRROR_PROMPTS_BY_SET,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $promptsBySet = json_decode($promptsBySet, true);
            $promptsBySet = array_column($promptsBySet, 'prompt', 'attribute_set_id');
            if (isset($promptsBySet[$setId])) {
                return $promptsBySet[$setId];
            }
        }

        return $promptsDefault;
    }

    /**
     * Returns the selected model configuration
     *
     * @param null|int|string $storeId
     * @return mixed
     */
    public function getModelConfig($storeId = null)
    {
        $model = $this->getModel();
        $groupConfigPath = self::PATH_VIRTUAL_MIRROR_BASE_CONFIG_PATH . $model = $this->getModel();
        $groupConfig = $this->scopeConfig->getValue(
            $groupConfigPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        // This is for legacy compatibility with first module version
        if (
            $model === \Bydn\VirtualMirror\Model\Config\Source\Model::GEMINI_NANO_BANANA && 
            empty($groupConfig['api_key'])
            ) {
            $groupConfig['api_key'] = $this->getApiKey();
        }

        return $groupConfig;
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
            self::PATH_VIRTUAL_MIRROR_API_KEY_LEGACY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}