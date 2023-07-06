<?php

namespace App\Lubus\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Invoice implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->resize(150, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg', 100);
    }
}
