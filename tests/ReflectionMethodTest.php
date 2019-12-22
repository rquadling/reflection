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

namespace RQuadlingTests\Reflection;

use PHPUnit\Framework\TestCase;
use RQuadling\Reflection\ReflectionMethod;
use RQuadlingTests\Reflection\Fixtures\ReflectionFixture;

class ReflectionMethodTest extends TestCase
{
    /**
     * @dataProvider provideDataForGetReturnTypeFromDocblock
     *
     * @throws \ReflectionException
     */
    public function testGetReturnTypeFromDocblock(string $propertyName, string $expectedType)
    {
        $reflectedProperty = new ReflectionMethod(ReflectionFixture::class, $propertyName);

        $this->assertSame($expectedType, $reflectedProperty->getReturnTypeFromDocBlock());
    }

    public function provideDataForGetReturnTypeFromDocblock()
    {
        return [
            'string' => ['aString', 'string'],
            'string|null' => ['aNullableString', 'string|null'],
            'int' => ['anInt', 'int'],
            'int|null' => ['aNullableInt', 'int|null'],
            'int|false' => ['anIntOrFalse', 'int|false'],
        ];
    }
}