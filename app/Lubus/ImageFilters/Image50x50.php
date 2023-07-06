<?php

namespace App\Lubus\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Image50x50 implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(50, 50)->encode('jpg', 100);
    }
}
