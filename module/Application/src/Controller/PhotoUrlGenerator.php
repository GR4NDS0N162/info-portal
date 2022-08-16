<?php

declare(strict_types=1);

namespace Application\Controller;

class PhotoUrlGenerator
{
    public static function generate(): string
    {
        return 'https://picsum.photos/' . random_int(100, 999);
    }
}