<?php

namespace Bydn\VirtualMirror\Model\Config\Source;

class AttributeSets implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    private \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private \Magento\Catalog\Model\ResourceModel\Product $productResource;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResource
    ) {
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
        $this->productResource = $productResource;
    }

    /**
     * Returns options for attribute set multiselect
     */
    public function toOptionArray(): array
    {
        $entityTypeId = (int)$this->productResource->getTypeId();
        $collection = $this->attributeSetCollectionFactory->create();
        $collection->setEntityTypeFilter($entityTypeId)
                   ->setOrder('attribute_set_name', 'ASC');

        $options = [];
        foreach ($collection as $set) {
            $options[] = [
                'value' => (int)$set->getAttributeSetId(),
                'label' => $set->getAttributeSetName()
            ];
        }

        return $options;
    }
}
