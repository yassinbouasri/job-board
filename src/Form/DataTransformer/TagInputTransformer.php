<?php

declare(strict_types=1);


namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class TagInputTransformer implements DataTransformerInterface
{

    public function transform(mixed $value): mixed
    {
       return implode(', ', $value ?? []);
    }

    public function reverseTransform(mixed $value): mixed
    {
        return array_filter(array_map('trim', explode(',', $value)));

    }
}