<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Requests\MovieCreateRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class MovieController extends AbstractController
{
    const IMAGE_PATH = '/images';
    const MOVIE_PATH = '/movies';
    
    private function uploadFile(SluggerInterface $slugger, UploadedFile $file, string $path) {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename) . '-' . uniqid() . '.' . $file->guessExtension();
        
        try {
            $movedFile = $file->move(self::IMAGE_PATH, $safeFilename);
            return $movedFile->getPath();
        } catch(FileException $ex) {
            return false;
        }
    }
    
    #[Route('/movie', name: 'movie', methods: ['GET'])]
    public function show(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MovieController.php',
        ]);
    }
    
    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(MovieRepository $movieRepository): JsonResponse {
        return $this->json($movieRepository->findAll());
    }
    
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(SluggerInterface $slugger, MovieRepository $movieRepository, MovieCreateRequest $request) {
        $movie = new Movie();
        
        $imagePath = $this->uploadFile($slugger, $request->getImage(), self::IMAGE_PATH);
        $moviePath = $this->uploadFile($slugger, $request->getMovie(), self::MOVIE_PATH);
        
        $movie->setName($request->getName());
        $movie->setDescription($request->getDescription());
        $movie->setImage($imagePath);
        $movie->setFiles([$moviePath]);
        
        $movieRepository->save($movie, true);
        
        return $this->json([
            'success' => true
        ]);
    }
    
    public function delete(Movie $movie) {
        
    }
}
