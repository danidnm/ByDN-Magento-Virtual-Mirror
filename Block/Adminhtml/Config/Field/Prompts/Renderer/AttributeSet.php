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
namespace Bydn\VirtualMirror\Block\Adminhtml\Config\Field\Prompts\Renderer;

class AttributeSet extends \Magento\Framework\View\Element\Html\Select
{
    /** @var AttributeSetCollectionFactory */
    private $attributeSetCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            
            $collection = $this->attributeSetCollectionFactory->create();
            $collection->setEntityTypeFilter(4);

            $options = [
                ['value' => '', 'label' => __('-- Select Attribute Set --')]
            ];

            foreach ($collection as $set) {
                $options[] = [
                    'value' => $set->getAttributeSetId(),
                    'label' => $set->getAttributeSetName()
                ];
            }
            foreach ($options as $opt) {
                $this->addOption($opt['value'], $this->escapeHtml($opt['label']));
            }
        }

        return parent::_toHtml();
    }
}