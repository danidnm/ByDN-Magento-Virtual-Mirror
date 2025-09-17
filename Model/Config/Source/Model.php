<?php

namespace Bydn\VirtualMirror\Model\Config\Source;

class Model implements \Magento\Framework\Option\ArrayInterface
{
    const GEMINI_NANO_BANANA = 'nano_banana';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Not configured'),
                'value' => 'not_configured'
            ],
            [
                'label' => __('Gemini Nano Banana'),
                'value' => self::GEMINI_NANO_BANANA
            ]
        ];

        return $options;
    }
}
