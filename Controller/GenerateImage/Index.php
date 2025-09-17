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
     * @var \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig
     */
    private \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    private \Magento\Framework\Filesystem\DirectoryList $directoryList;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private \Magento\Framework\Filesystem $filesystem;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Bydn\VirtualMirror\Model\Gemini\Api
     */
    private \Bydn\VirtualMirror\Model\Gemini\Api $geminiApi;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Bydn\VirtualMirror\Helper\Config $config
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Bydn\VirtualMirror\Model\Gemini\Api $gemini
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Filesystem $filesystem,
        \Bydn\VirtualMirror\Model\Gemini\Api $geminiApi
    ) {
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->productRepository = $productRepository;
        $this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
        $this->virtualMirrorConfig = $virtualMirrorConfig;
        $this->customerSession = $customerSession;
        $this->filesystem = $filesystem;
        $this->geminiApi = $geminiApi;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Get prompt
        $prompt = $this->getPrompt();

        // Get images
        $customerImage = $this->getCustomerImage();
        $productImage = $this->getProductBaseImage();

        // Generate the new image
        $newImagePath = $this->geminiApi->generate($prompt, $customerImage, $productImage);

        // Create the response
        $result = $this->jsonFactory->create();
        return $result->setData([
            'success' => true,
            'url' => $this->getBaseMediaUrl() . $newImagePath
        ]);
    }

    /**
     * Returns the prompt to be used
     */
    private function getPrompt()
    {
        $product = $this->getCurrentProduct();
        $attributeSetId = $product->getAttributeSetId();
        return $this->virtualMirrorConfig->getPrompt($attributeSetId);
    }

    /**
     * Returns the absolute path of a user image
     */
    private function getCustomerImage()
    {
        //$mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        //$mediaAbsolutePath = $mediaDirectory->getAbsolutePath();
        return $this->directoryList->getPath('media') . '/avatars/' . $this->customerSession->getCustomer()->getCustomerAvatar();
        //return '/Users/danielnavarro/Sites/magento248/src/pub/media/virtualmirror/customers/dani.png';
    }

    /**
    * Get base image absolute path of current product
    *
    * @return string|null
    */
    private function getProductBaseImage()
    {
        return $this->directoryList->getPath('media') . '/catalog/product' . $this->getCurrentProduct()->getImage();
    }

    /**
    * Get product by ID from request
    *
    * @return \Magento\Catalog\Api\Data\ProductInterface
    * @throws \Magento\Framework\Exception\NoSuchEntityException
    */
    private function getCurrentProduct()
    {
        $productId = (int) $this->request->getParam('product_id');
        return $this->productRepository->getById($productId);
    }

    /**
     * Returns the base URL of the media folder
     */
    private function getBaseMediaUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
