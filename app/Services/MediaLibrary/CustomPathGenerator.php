<?php

namespace App\Services\MediaLibrary;

use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{

    public function getPath(Media $media): string
    {
        switch  ($media->model_type) {
            case User::class:
                return User::AWS_PROFILE_IMAGES_PATH;
                break;
            default:
                return $media->id . DIRECTORY_SEPARATOR;
        }
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'thumbnails/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'rs-images/';
    }
}
