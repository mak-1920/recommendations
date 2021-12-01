<?php

namespace App\Controller\Ajax;

use App\Services\FileStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
        $filename = $request->request->get('fileId');

        if(!$this->fileStorage->uploadTemporaryFile($file, $filename)){
            return $this->json(
                [
                    'result' => false,
                ], 
                Response::HTTP_FORBIDDEN
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
        '/ajax/remove_temp_illustration',
        name: 'remove_temp_illustration',
        methods: ['POST'],
    )]
    public function removeTempIllustration(Request $request) : Response
    {
        $filename = $request->request->get('fileId');
        
        if(!$this->fileStorage->removeTemporaryFile($filename)){
            return $this->json(
                [
                    'result' => false,
                ], 
                Response::HTTP_FORBIDDEN
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
}
