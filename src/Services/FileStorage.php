<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Review\Review;
use App\Entity\Review\ReviewIllustration;
use Cloudinary\Cloudinary;

class FileStorage 
{
    private Cloudinary $cloudinary;

    public function __construct(string $url)
    {
        $this->cloudinary = new Cloudinary($url);
    }

    private function getPath(Review $review) : string
    {
        return 'reviews/' . $review->getId() . '/';
    }

    public function uploadIllustration(Review $review, string $fileName) : string
    {
        $response = $this->cloudinary->uploadApi()->upload($fileName, [
            'folder' => $this->getPath($review),
            'eager' => [
                'width' => 500,
                'height' => 500,
            ],
        ]);
        return $response->offsetGet('public_id');
    }

    public function removeIllustration(Review $review, string $fileName) : bool
    {
        $response = $this->cloudinary->uploadApi()->destroy($fileName);
        return $response->offsetGet('result');
    }

    public function updateReviewIllustrations(Review $review, array $newIllustrations) : void 
    {
        $oldIllustrations = $review->getIllustrations();
        
        foreach($oldIllustrations as $illustraion){
            $index = array_search($illustraion->getImg(), $newIllustrations);
            if($index !== false){
                unset($newIllustrations[$index]);
            } else {
                $this->removeIllustration($review, $illustraion->getImg());
                $review->removeIllustration($illustraion);
            }
        }

        foreach($newIllustrations as $name){
            $newIllustration = new ReviewIllustration();
            $newIllustration->setImg($name);
            
            $review->addIllustration($newIllustration);
        }
    }
}