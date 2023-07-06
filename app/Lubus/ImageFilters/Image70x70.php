<?php

namespace App\Lubus\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Image70x70 implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(70, 70)->encode('jpg', 100);
    }
}
