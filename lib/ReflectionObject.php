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
 * ReflectionObject.
 *
 * Extends the ReflectionObject to allow conversion of a ReflectionProperty and ReflectionMethod to a localised
 * ReflectionProperty and ReflectionMethod.
 */
class ReflectionObject extends \ReflectionObject
{
    use GetConstantsTrait;
    use GetMethodsTrait;

    /**
     * Required to track dynamic objects.
     *
     * @var object
     */
    private $object;

    public function __construct($argument)
    {
        parent::__construct($argument);
        $this->object = $argument;
    }

    /**
     * Override the standard ReflectionObject::getProperties method to return a custom ReflectionProperty array.
     *
     * @param int $filter The optional filter bitmask made up from the following bit, with the default being all of them.
     *                    - \ReflectionProperty::IS_PRIVATE
     *                    - \ReflectionProperty::IS_PROTECTED
     *                    - \ReflectionProperty::IS_PUBLIC
     *                    - \ReflectionProperty::IS_STATIC
     *
     * @return ReflectionProperty[] a filtered list of properties for the reflected object
     *
     * @uses \ReflectionProperty
     */
    public function getProperties($filter = -1)
    {
        return \array_map(
            function ($property) {
                return $this->getProperty($property->name);
            },
            parent::getProperties($filter)
        );
    }

    /**
     * Override the standard ReflectionObject::getProperty method to return a custom ReflectionProperty.
     *
     * @param string $name
     *
     * @return ReflectionProperty a custom ReflectionProperty
     *
     * @throws ReflectionException
     */
    public function getProperty($name): ReflectionProperty
    {
        $property = parent::getProperty($name);

        return new ReflectionProperty($this->object, $property->name);
    }
}
