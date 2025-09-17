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
namespace Bydn\VirtualMirror\Block\Adminhtml\Config\Field;

class Prompts extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Bydn\VirtualMirror\Block\Adminhtml\Config\Field\Prompts\Renderer\AttributeSet
     */
    private $attributeSetRenderer;

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'attribute_set_id',
            [
                'label' => __('Attribute Set ID'),
                'renderer' => $this->getAttributeSetRenderer(),
                'class' => 'required-entry connection-code'
            ]
        );
        $this->addColumn(
            'prompt',
            [
                'label' => __('Prompt'),
                'class' => 'required-entry connection-host'
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Prompt');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->getAttributeSetRenderer()->calcOptionHash($row->getData('attribute_set_id'))] =
            'selected="selected"';
        $optionExtraAttr['option_' . $this->getAttributeSetRenderer()->calcOptionHash($row->getData('value'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }

    /**
     * Returns the renderer for the attribute set field
     *
     * @return Type|(Type&\Magento\Framework\View\Element\BlockInterface)|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributeSetRenderer()
    {
        if (!$this->attributeSetRenderer) {
            $this->attributeSetRenderer = $this->getLayout()->createBlock(
                \Bydn\VirtualMirror\Block\Adminhtml\Config\Field\Prompts\Renderer\AttributeSet::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->attributeSetRenderer->setClass('customer_group_select admin__control-select');
        }
        return $this->attributeSetRenderer;
    }
}