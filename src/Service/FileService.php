<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileService
{
    private SluggerInterface $slugger;
    private ParameterBagInterface $params;

    public function __construct(SluggerInterface $slugger, ParameterBagInterface $params){
        $this->slugger = $slugger;
        $this->params = $params;
    }
    public function uploadPicture(UploadedFile $pictureFile, String $fileName): string {
        $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        if($fileName != ''){
            $newFilename = $fileName;
        }
        else{
            $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();
        }

        try{
            $pictureFile->move(
                $this->params->get('kernel.project_dir').'/public/upload',
                $newFilename
            );
        } catch (FileException $e){
            dd($e);
        }
        return $newFilename;
    }

    public function deletePicture(String $imagePath){
        $fileSystem = new Filesystem();
        $projetDir = $this->params->get('kernel.project_dir');

        $fileSystem->remove($projetDir.'/public/'.$imagePath);
    }
}