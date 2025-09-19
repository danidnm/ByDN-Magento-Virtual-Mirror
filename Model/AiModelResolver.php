<?php

namespace Bydn\VirtualMirror\Model;

class AiModelResolver
{
    /**
     * @ var \Bydn\VirtualMirror\Helper\Config
     */
    private \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig;

    /**
     * @var \Bydn\VirtualMirror\Model\Google\NanoBananaFactory
     */
    private \Bydn\VirtualMirror\Model\Google\NanoBananaFactory $nanoBananaFactory;

    /**
     * @param \Bydn\VirtualMirror\Model\Gemini\Api $gemini
     */
    public function __construct(
        \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig,
        \Bydn\VirtualMirror\Model\Google\NanoBananaFactory $nanoBananaFactory
    ) {
        $this->virtualMirrorConfig = $virtualMirrorConfig;
        $this->nanoBananaFactory = $nanoBananaFactory;
    }

    /**
     * Returns AI model instance to be used
     */
    public function getAiModel()
    {
        return $this->nanoBananaFactory->create();
    }
}