<?php

/**
 * RQuadling/Reflection
 *
 * LICENSE
 *
 * This is free and unencumbered software released into the public domain.
 *
 * Anyone is free to copy, modify, publish, use, compile, sell, or distribute this software, either in source code form or
 * as a compiled binary, for any purpose, commercial or non-commercial, and by any means.
 *
 * In jurisdictions that recognize copyright laws, the author or authors of this software dedicate any and all copyright
 * interest in the software to the public domain. We make this dedication for the benefit of the public at large and to the
 * detriment of our heirs and successors. We intend this dedication to be an overt act of relinquishment in perpetuity of
 * all present and future rights to this software under copyright law.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT
 * OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * For more information, please refer to <https://unlicense.org>
 *
 */

namespace RQuadling\Reflection;

use ReflectionException;
use RQuadling\Reflection\Traits\GetConstantsTrait;
use RQuadling\Reflection\Traits\GetMethodsTrait;

/**
 * Extends the ReflectionClass to allow conversion of a ReflectionProperty and ReflectionMethod to a localised
 * ReflectionProperty and ReflectionMethod.
 *
 * @extends \ReflectionClass<ReflectionClass>
 */
class ReflectionClass extends \ReflectionClass
{
    use GetConstantsTrait;
    use GetMethodsTrait;

    /**
     * ReflectionClass constructor.
     *
     * @param class-string|object $argument
     *
     * @throws ReflectionException
     */
    public function __construct($argument)
    {
        parent::__construct($argument);
    }

    /**
     * Override the standard ReflectionClass::getProperties method to return a custom ReflectionProperty array.
     *
     * @param int $filter The optional filter bitmask made up from the following bit, with the default being all of them.
     *                    - \ReflectionProperty::IS_PRIVATE
     *                    - \ReflectionProperty::IS_PROTECTED
     *                    - \ReflectionProperty::IS_PUBLIC
     *                    - \ReflectionProperty::IS_STATIC
     *
     * @return ReflectionProperty[] a filtered list of properties for the reflected class
     *
     * @uses \ReflectionProperty
     */
    public function getProperties($filter = -1): array
    {
        return \array_map(
            function (\ReflectionProperty $property) {
                return new ReflectionProperty($property->class, $property->name);
            },
            parent::getProperties($filter)
        );
    }

    /**
     * Override the standard ReflectionClass::getProperty method to return a custom ReflectionProperty.
     *
     * @param string $name
     *
     * @throws ReflectionException
     */
    public function getProperty($name): ReflectionProperty
    {
        $property = parent::getProperty($name);

        return new ReflectionProperty($property->class, $property->name);
    }
}
