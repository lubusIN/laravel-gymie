<?php

namespace App\Lubus\ImageFilters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Image70x70 implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(70, 70)->encode('jpg', 100);
    }
}
