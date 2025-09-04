<?php
declare(strict_types=1);

namespace Bydn\VirtualMirror\Controller\GenerateImage;

class Index implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private \Magento\Framework\Controller\Result\JsonFactory $jsonFactory;

    /**
     * @var \Bydn\VirtualMirror\Model\Gemini\Api
     */
    private \Bydn\VirtualMirror\Model\Gemini\Api $geminiApi;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Bydn\VirtualMirror\Model\Gemini\Api $gemini
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Bydn\VirtualMirror\Model\Gemini\Api $geminiApi
    ) {
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->geminiApi = $geminiApi;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->geminiApi->generate();
        $result = $this->jsonFactory->create();
        return $result->setData(['success' => true]);
    }
}
