<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;

class MovieCreateRequest extends BaseRequest {
  
  #[NotNull()]
  #[NotBlank()]
  private string $name;
  
  #[NotNull()]
  #[NotBlank()]
  private ?string $description;
  
  #[NotNull()]
  #[Image()]
  private ?UploadedFile $image;
  
  #[NotNull()]
  #[File()]
  private ?UploadedFile $movie;
  
  protected function validateMovie(): ?ConstraintViolationInterface {
    if(!str_starts_with($this->movie->getMimeType(), "video/")) {
      return new ConstraintViolation('Not a valid movie file', '', [], null, 'movie', $this->movie,);
    }
    
    return null;
  }
  
  public function getName(): string {
    return $this->name;
  }
  
  public function getDescription(): ?string {
    return $this->description;
  }
  
  public function getImage(): UploadedFile {
    return $this->image;
  }
  
  public function getMovie(): UploadedFile {
    return $this->movie;
  }
  
  protected function autoValidateRequest(): bool {
    return true;
  }
  
  protected function isApi(): bool {
    return true;
  }
}
