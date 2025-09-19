<?php
/**
 * @package     Bydn_VirtualMirror
 * @author      Daniel Navarro <https://github.com/danidnm>
 * @license     GPL-3.0-or-later
 * @copyright   Copyright (c) 2025 Daniel Navarro
 *
 * This file is part of a free software package licensed under the
 * GNU General Public License v3.0.
 * You may redistribute and/or modify it under the same license.
 */
namespace Bydn\VirtualMirror\Model\Config\Source;

class Model implements \Magento\Framework\Option\ArrayInterface
{
    const GOOGLE_NANO_BANANA = 'nano_banana';
    const BYTEDANCE_SEE_DREAM = 'see_dream';

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
                'label' => __('Google Nano Banana'),
                'value' => self::GOOGLE_NANO_BANANA
            ],
            [
                'label' => __('See Dream'),
                'value' => self::BYTEDANCE_SEE_DREAM
            ]
        ];

        return $options;
    }
}
