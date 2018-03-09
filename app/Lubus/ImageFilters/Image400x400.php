<?php

namespace App\Lubus\ImageFilters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Image400x400 implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->crop(400, 400)->encode('jpg', 100);
    }
}
