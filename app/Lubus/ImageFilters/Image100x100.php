<?php

namespace App\Lubus\ImageFilters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Image100x100 implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(100, 100)->encode('jpg', 100);
    }
}
