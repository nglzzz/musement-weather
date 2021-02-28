<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ApiAbstractType extends AbstractType
{
    /**
     * For api request we don't need to check csrf token.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('csrf_protection', false);
    }

    /**
     * Generate field names without a prefix.
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
