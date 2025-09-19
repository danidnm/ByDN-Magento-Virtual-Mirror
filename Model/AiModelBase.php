<?php

namespace Bydn\VirtualMirror\Model;

abstract class AiModelBase extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Generates content (text and images) from the Nano Banana API using a streaming request.
     * @return string The relative path of the generated image file
     */
    abstract function generate($prompt, $customerImage, $productImage): string;
}
