<?php

namespace Species\App\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Reflection twig extension.
 */
final class ReflectionTwigExtension extends AbstractExtension
{

    /** @inheritDoc */
    public function getFunctions(): array
    {
        return [

            new TwigFunction('fqcn', function (object $object) {
                return get_class($object);
            }),

            new TwigFunction('className', function (object $object) {
                $className = get_class($object);
                $className = explode('\\', $className);

                return end($className);
            }),

            new TwigFunction('instanceOf', function (object $object, string $class) {
                return is_a($object, $class, true);
            }),

        ];
    }

}
