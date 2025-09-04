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
        // Get API Key
        $apiKey = $this->virtualMirrorConfig->getApiKey();
        if (!$apiKey) {
            throw new \Magento\Framework\Exception\LocalizedException("Error: GEMINI_API_KEY not set.");
        }

        // Setup model
        $model = 'gemini-2.5-flash-image-preview';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Payload
        $payload = [
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
        try {
            $dataReturned = json_decode($response, true);
        } catch (\JsonException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid JSON response from API'));
        }

        // Data returned should be an array
        if (!is_array($dataReturned)) {
            $dataReturned = [$dataReturned];
        }

        // Process the response
        if (isset($dataReturned['candidates'][0]['content']['parts'])) {
            foreach ($dataReturned['candidates'][0]['content']['parts'] as $part) {
                
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
                    
                    // Save file
                    $fileName = "generated_image_{$this->fileIndex}{$fileExtension}";
                    $mediaDirectory->writeFile(
                        $relativePath . '/' . $fileName,
                        $dataBuffer
                    );
                    $this->fileIndex++;
                } 
            }
        }
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

