<?php

declare(strict_types=1);

namespace App\Controller\Ajax;

use App\Repository\Review\ReviewRepository;
use App\Services\FileStorage;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;

class IllustrationController extends AbstractController
{
    public function __construct(
        private FileStorage $fileStorage,
    ) {}

    #[Route(
        '/ajax/add_temp_illustration',
        name: 'add_temp_illustration',
        methods: ['POST'],
    )]
    public function addTempIllustration(Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('review_creator')['illustrations_input'][0];

        $res = $this->fileStorage->uploadTemporaryFile($file);

        if(!$res){
            return $this->json(
                [
                    'result' => false,
                ], 
                Response::HTTP_FAILED_DEPENDENCY
            );
        } else {
            return $this->json(
                [
                    'result' => true,
                    'name' => $res,
                ], 
                Response::HTTP_ACCEPTED
            );
        }
    }

    #[Route(
        '/ajax/remove_temp_illustration',
        name: 'remove_temp_illustration',
        methods: ['POST'],
    )]
    public function removeTempIllustration(Request $request) : Response
    {
        $filename = $request->request->get('key');
        
        if(!$this->fileStorage->removeTemporaryFile($filename)){
            return $this->json(
                [
                    'result' => false,
                ], 
                Response::HTTP_FAILED_DEPENDENCY
            );
        } else {
            return $this->json(
                [
                    'result' => true,
                    'name' => $filename,
                ], 
                Response::HTTP_ACCEPTED
            );
        }
    }

    #[Route(
        '/ajax/save-illustrations',
        name: 'save_illustrations',
        methods: ['POST'],
    )]
    public function saveIllustrations(Request $request, ReviewRepository $reviewRepository) : Response
    {
        $reviewId = $request->request->get('reviewId');
        /** @var string[] $illustrations */
        $illustrations = $request->request->get('illustrations');

        try {
            $this->fileStorage->updateReviewIllustrations(
                $reviewRepository->findByID($reviewId),
                $illustrations,
            );
        } catch(Exception $e){
            return $this->json(
                [
                    'result' => false,
                    'message' => $e->getMessage(),
                ],
                Response::HTTP_FAILED_DEPENDENCY,
            );
        }

        return $this->json(
            [
                'result' => true,
            ],
            Response::HTTP_ACCEPTED,
        );
    }
}
