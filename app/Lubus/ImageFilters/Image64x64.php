<?php

namespace App\Lubus\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Image64x64 implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(64, 64)->encode('jpg', 100);
    }
}
