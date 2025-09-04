<?php

namespace Bydn\VirtualMirror\Model\Gemini;

class Api extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Bydn\VirtualMirror\Helper\Config
     */
    protected \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected \Magento\Framework\Filesystem $filesystem;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected \Magento\Framework\App\Filesystem\DirectoryList $directoryList;

    /**
     * @var int
     */
    protected $fileIndex = 0;

    /**
     * @var string
     */
    protected $buffer = '';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Bydn\VirtualMirror\Helper\Config $virtualMirrorConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->virtualMirrorConfig = $virtualMirrorConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Generates content (text and images) from the Gemini API using a streaming request.
    */
    function generate(): void
    {
        // Model to be used and api key
        $model = 'gemini-2.5-flash-image-preview';
        $apiKey = $this->virtualMirrorConfig->getApiKey();
        if (!$apiKey) {
            throw new \Magento\Framework\Exception\LocalizedException("Error: GEMINI_API_KEY not set.");
        }

        // Setup URL
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Make the call
        try {
            $response = $this->makeCall(
                $url, 
                $this->getPayload()
            );
        } catch (\JsonException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid JSON response from API'));
        }

        // Process the response
        if (isset($response['candidates'][0]['content']['parts'])) {
            foreach ($response['candidates'][0]['content']['parts'] as $part) {
                
                // Process image part. Discard the text part.
                if (isset($part['inlineData']['data'])) {
                    $inlineData = $part['inlineData'];
                    $dataBuffer = base64_decode($inlineData['data']);
                    $fileExtension = $this->mimeTypeToExtension($inlineData['mimeType']);
                    
                    // Get media directory
                    $mediaDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    
                    // Create virtualmirror directory if it doesn't exist
                    $relativePath = 'virtualmirror';
                    $mediaDirectory->create($relativePath);
                    
                    // Generate random unique filename
                    $fileName = uniqid('img_', true) . $fileExtension;
                    $mediaDirectory->writeFile(
                        $relativePath . '/' . $fileName,
                        $dataBuffer
                    );
                } 
            }
        }
    }

    /**
     * Returns the payload for the Gemini API request.
     */
    private function getPayload()
    {
        return [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => 'Design a custom birthday card for a friend who loves space and cats.'],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseModalities' => ['IMAGE']
            ] 
        ];
    }

    /**
     * Makes a cURL POST request to the specified URL with the given payload and returns the decoded JSON response.
     */
    private function makeCall($url, $payload)
    {
        // cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Exec the request
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('cURL Error: ') . curl_error($ch));
        }
        curl_close($ch);

        // Decode the response
        return json_decode($response, true);
    }

    /**
     * Returns the file extension for a given MIME type.
     */
    private function mimeTypeToExtension(string $mimeType): string
    {
        $map = [
            'image/png' => '.png',
            'image/jpeg' => '.jpeg',
            'image/jpg' => '.jpg',
            'image/webp' => '.webp',
            'image/gif' => '.gif',
        ];
        return $map[$mimeType] ?? '';
    }
}

