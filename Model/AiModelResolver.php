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
     * @var \Bydn\VirtualMirror\Model\ByteDance\SeeDreamFactory
     */
    private \Bydn\VirtualMirror\Model\ByteDance\SeeDreamFactory $seeDreamFactory;

    /**
     * @param \Bydn\VirtualMirror\Model\Gemini\Api $gemini
     */
    public function __construct(
        \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig,
        \Bydn\VirtualMirror\Model\Google\NanoBananaFactory $nanoBananaFactory,
        \Bydn\VirtualMirror\Model\ByteDance\SeeDreamFactory $seeDreamFactory
    ) {
        $this->virtualMirrorConfig = $virtualMirrorConfig;
        $this->nanoBananaFactory = $nanoBananaFactory;
        $this->seeDreamFactory = $seeDreamFactory;
    }

    /**
     * Returns AI model instance to be used
     */
    public function getAiModel()
    {
        if ($this->virtualMirrorConfig->getModel() == \Bydn\VirtualMirror\Model\Config\Source\Model::BYTEDANCE_SEE_DREAM) {
            return $this->seeDreamFactory->create();
        }
        else {
            return $this->nanoBananaFactory->create();
        }
    }
}