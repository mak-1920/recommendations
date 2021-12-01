<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Review\Review;
use App\Entity\Review\ReviewIllustration;
use Cloudinary\Cloudinary;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStorage 
{
    private Cloudinary $cloudinary;

    public function __construct(
        string $url, 
        private string $directory)
    {
        $this->cloudinary = new Cloudinary($url);
    }

    private function getTempFolderPath() : string
    {
        return 'temp/';
    }
    private function getPath(string $folder) : string
    {
        return 'reviews/' . $folder . '/';
    }

    public function uploadIllustration(string $folder, string $filePath) : string
    {
        $response = $this->cloudinary->uploadApi()->upload($filePath, [
            'folder' => $this->getPath($folder),
            'eager' => [
                'width' => 500,
                'height' => 500,
            ],
        ]);
        return $response->offsetGet('public_id');
    }

    public function removeIllustration(string $fileName) : bool
    {
        $response = $this->cloudinary->uploadApi()->destroy($fileName);
        return (bool) $response->offsetGet('result');
    }

    public function updateReviewIllustrations(Review $review, array $newIllustrations) : void 
    {
        $oldIllustrations = $review->getIllustrations();
        
        foreach($oldIllustrations as $illustraion){
            $index = array_search($illustraion->getImg(), $newIllustrations);
            if($index !== false){
                unset($newIllustrations[$index]);
            } else {
                $this->removeIllustration((string)$review->getId(), $illustraion->getImg());
                $review->removeIllustration($illustraion);
            }
        }

        foreach($newIllustrations as $name){
            $newIllustration = new ReviewIllustration();
            $newIllustration->setImg($name);
            
            $review->addIllustration($newIllustration);
        }
    }

    public function uploadTemporaryFile(UploadedFile $file) : string|false
    {
        if($file){
            return $this->uploadIllustration($this->getTempFolderPath(), $file->getPathname());
        }
        return false;
    }

    public function removeTemporaryFile(string $fileName) : bool 
    {
        return $this->removeIllustration($fileName);
    }
}