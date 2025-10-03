<?php
namespace Bydn\VirtualMirror\Plugin;

use Magento\Customer\Model\FileProcessor;
use Magento\Customer\Model\FileProcessorFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Controller\Account\EditPost;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class CustomerEditPost
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @param RequestInterface $request
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(
        RequestInterface $request,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory
    ) {
        $this->request = $request;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * Handle file upload before customer save
     *
     * @param EditPost $subject
     * @return void
     */
    public function beforeExecute(EditPost $subject)
    {
        //$files = $this->request->getFiles()->toArray();
        $file = $this->request->getFiles('virtualmirror_user_image_1');
    
        if (isset($file['tmp_name'])) {

            $uploader = $this->uploaderFactory->create(['fileId' => 'virtualmirror_user_image_1']);
            
            // Set allowed extensions
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);

            // Define the destination directory
            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $destinationPath = $mediaDirectory->getAbsolutePath('user_images');

            // Move the file
            $result = $uploader->save($destinationPath);
            
            // Set the file path in the request params to be saved as a customer attribute
            $this->request->setParam('virtualmirror_user_image_1', $result['file']);
        }
    }
}
