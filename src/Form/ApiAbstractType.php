<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ApiAbstractType extends AbstractType
{
    /**
     * Generate field names without a prefix.
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
