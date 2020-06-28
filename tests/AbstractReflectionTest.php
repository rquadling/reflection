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
use ReflectionException;
use RQuadling\Reflection\ReflectionClass;
use RQuadling\Reflection\ReflectionMethod;
use RQuadling\Reflection\ReflectionObject;
use RQuadling\Reflection\ReflectionProperty;

abstract class AbstractReflectionTest extends TestCase
{
    /**
     * @dataProvider providerForGetConstants
     *
     * @param array<int, string> $expectedConstants
     */
    public function testGetConstants(?callable $filter, array $expectedConstants): void
    {
        $this->assertSame($expectedConstants, ($this->generateReflector())->getConstants($filter));
    }

    /**
     * @dataProvider providerForGetMethods
     *
     * @param array<int, string> $expectedMethodNames
     */
    public function testGetMethods(int $filter, array $expectedMethodNames): void
    {
        $this->assertSame(
            $expectedMethodNames,
            \array_map(
                function (ReflectionMethod $method) {
                    return $method->getName();
                },
                ($this->generateReflector())->getMethods($filter)
            )
        );
    }

    /**
     * @dataProvider providerForGetProperties
     *
     * @param array<int, string> $expectedPropertyNames
     */
    public function testGetProperties(int $filter, array $expectedPropertyNames): void
    {
        $this->assertSame(
            $expectedPropertyNames,
            \array_map(
                function (ReflectionProperty $property) {
                    return \trim((string)$property);
                },
                ($this->generateReflector())->getProperties($filter)
            )
        );
    }

    /**
     * @throws ReflectionException
     * @dataProvider providerForGetProperty
     */
    public function testGetPropertyAttributeRetrieval(
        string $property,
        bool $isDelayedInject,
        bool $isInjected,
        bool $isPublic,
        bool $isProtected,
        bool $isPrivate,
        bool $isStatic
    ): void {
        $reflectedProperty = $this->generateReflector()->getProperty($property);

        $this->assertInstanceOf(ReflectionProperty::class, $reflectedProperty);

        $this->assertSame($property, $reflectedProperty->getName(), 'Mismatched property name');

        $this->assertSame($isDelayedInject, $reflectedProperty->isDelayedInjected(), 'Delayed Inject mismatch');
        $this->assertSame($isInjected, $reflectedProperty->isInjected(), 'Injected mismatch');
        $this->assertSame($isPublic, $reflectedProperty->isPublic(), 'Public mismatch');
        $this->assertSame($isProtected, $reflectedProperty->isProtected(), 'Protected mismatch');
        $this->assertSame($isPrivate, $reflectedProperty->isPrivate(), 'Private mismatch');
        $this->assertSame($isStatic, $reflectedProperty->isStatic(), 'Static mismatch');
    }

    /**
     * @return array<string, array<int, array<string, int>|(Closure(string): bool)|null>>
     */
    public function providerForGetConstants(): array
    {
        return
            [
                'Unfiltered' => [
                    null,
                    [
                        'CONSTANT_1' => 1,
                        'CONSTANT_100' => 100,
                        'ANOTHER_CONSTANT_1' => 1,
                        'ANOTHER_CONSTANT_100' => 100,
                    ],
                ],
                'Filtered' => [
                    function (string $constant): bool {
                        return false;
                    },
                    [],
                ],
                'Starts with CONSTANT' => [
                    function (string $constant): bool {
                        return \strpos($constant, 'CONSTANT') === 0;
                    },
                    [
                        'CONSTANT_1' => 1,
                        'CONSTANT_100' => 100,
                    ],
                ],
                'Does not starts with CONSTANT' => [
                    function (string $constant): bool {
                        return \strpos($constant, 'CONSTANT') !== 0;
                    },
                    [
                        'ANOTHER_CONSTANT_1' => 1,
                        'ANOTHER_CONSTANT_100' => 100,
                    ],
                ],
                'Contains CONSTANT' => [
                    function (string $constant): bool {
                        return \strpos($constant, 'CONSTANT') !== false;
                    },
                    [
                        'CONSTANT_1' => 1,
                        'CONSTANT_100' => 100,
                        'ANOTHER_CONSTANT_1' => 1,
                        'ANOTHER_CONSTANT_100' => 100,
                    ],
                ],
            ];
    }

    /**
     * @return array<string, array<int, array<int, string>|int>>
     */
    public function providerForGetMethods(): array
    {
        return
            [
                'Unfiltered' => [
                    -1,
                    [
                        'exportable',
                        'notExportable',
                        'fnAbstractPublic',
                        'fnAbstractProtected',
                        'fnAbstractStaticPublic',
                        'fnAbstractStaticProtected',
                        'aString',
                        'aNullableString',
                        'anInt',
                        'aNullableInt',
                        'anIntOrFalse',
                        'fnPublic',
                        'fnProtected',
                        'fnPrivate',
                        'fnStaticPublic',
                        'fnStaticProtected',
                        'fnStaticPrivate',
                        'fnFinalPublic',
                        'fnFinalProtected',
                        'fnFinalStaticPublic',
                        'fnFinalStaticProtected',
                    ],
                ],
                'Final' => [
                    ReflectionMethod::IS_FINAL,
                    [
                        'fnFinalPublic',
                        'fnFinalProtected',
                        'fnFinalStaticPublic',
                        'fnFinalStaticProtected',
                    ],
                ],
                'Abstract' => [
                    ReflectionMethod::IS_ABSTRACT,
                    [
                        // The concrete class does not have any abstract methods
                    ],
                ],
                'Final or Abstract' => [
                    ReflectionMethod::IS_ABSTRACT | ReflectionMethod::IS_FINAL,
                    [
                        'fnFinalPublic',
                        'fnFinalProtected',
                        'fnFinalStaticPublic',
                        'fnFinalStaticProtected',
                    ],
                ],
                'Static' => [
                    ReflectionMethod::IS_STATIC,
                    [
                        'fnAbstractStaticPublic',
                        'fnAbstractStaticProtected',
                        'fnStaticPublic',
                        'fnStaticProtected',
                        'fnStaticPrivate',
                        'fnFinalStaticPublic',
                        'fnFinalStaticProtected',
                    ],
                ],
                'Public' => [
                    ReflectionMethod::IS_PUBLIC,
                    [
                        'exportable',
                        'notExportable',
                        'fnAbstractPublic',
                        'fnAbstractStaticPublic',
                        'aString',
                        'aNullableString',
                        'anInt',
                        'aNullableInt',
                        'anIntOrFalse',
                        'fnPublic',
                        'fnStaticPublic',
                        'fnFinalPublic',
                        'fnFinalStaticPublic',
                    ],
                ],
                'Protected' => [
                    ReflectionMethod::IS_PROTECTED,
                    [
                        'fnAbstractProtected',
                        'fnAbstractStaticProtected',
                        'fnProtected',
                        'fnStaticProtected',
                        'fnFinalProtected',
                        'fnFinalStaticProtected',
                    ],
                ],
                'Private' => [
                    ReflectionMethod::IS_PRIVATE,
                    [
                        'fnPrivate',
                        'fnStaticPrivate',
                    ],
                ],
            ];
    }

    /**
     * @return array<string, array<int, array<int, string>|int>>
     */
    public function providerForGetProperties(): array
    {
        return
            [
                'Unfiltered' => [
                    -1,
                    [
                        'Property [ <default> protected $injected ]',
                        'Property [ <default> protected $notInjected ]',
                        'Property [ <default> protected $delayedInjected ]',
                        'Property [ <default> protected $notDelayedInjected ]',
                        'Property [ <default> protected $nullable ]',
                        'Property [ <default> protected $notNullable ]',
                        'Property [ <default> protected $mandatory ]',
                        'Property [ <default> protected $notMandatory ]',
                        'Property [ <default> protected $optional ]',
                        'Property [ <default> protected $notOptional ]',
                        'Property [ <default> protected $cloneable ]',
                        'Property [ <default> protected $notCloneable ]',
                        'Property [ <default> protected $id ]',
                        'Property [ <default> protected $notId ]',
                        'Property [ <default> protected $column ]',
                        'Property [ <default> protected $notColumn ]',
                        'Property [ <default> protected $aString ]',
                        'Property [ <default> protected $aNullableString ]',
                        'Property [ <default> protected $anInt ]',
                        'Property [ <default> protected $aNullableInt ]',
                        'Property [ <default> protected $anIntOrFalse ]',
                        'Property [ <default> public $public ]',
                        'Property [ <default> protected $protected ]',
                        'Property [ <default> private $private ]',
                        'Property [ public static $publicStatic ]',
                        'Property [ protected static $protectedStatic ]',
                        'Property [ private static $privateStatic ]',
                    ],
                ],
                'Static' => [
                    ReflectionProperty::IS_STATIC,
                    [
                        'Property [ public static $publicStatic ]',
                        'Property [ protected static $protectedStatic ]',
                        'Property [ private static $privateStatic ]',
                    ],
                ],
                'Public' => [
                    ReflectionProperty::IS_PUBLIC,
                    [
                        'Property [ <default> public $public ]',
                        'Property [ public static $publicStatic ]',
                    ],
                ],
                'Public Or Static' => [
                    ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_STATIC,
                    [
                        'Property [ <default> public $public ]',
                        'Property [ public static $publicStatic ]',
                        'Property [ protected static $protectedStatic ]',
                        'Property [ private static $privateStatic ]',
                    ],
                ],
                'Private' => [
                    ReflectionProperty::IS_PRIVATE,
                    [
                        'Property [ <default> private $private ]',
                        'Property [ private static $privateStatic ]',
                    ],
                ],
                'Private Or Static' => [
                    ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_STATIC,
                    [
                        'Property [ <default> private $private ]',
                        'Property [ public static $publicStatic ]',
                        'Property [ protected static $protectedStatic ]',
                        'Property [ private static $privateStatic ]',
                    ],
                ],
                'Protected' => [
                    ReflectionProperty::IS_PROTECTED,
                    [
                        'Property [ <default> protected $injected ]',
                        'Property [ <default> protected $notInjected ]',
                        'Property [ <default> protected $delayedInjected ]',
                        'Property [ <default> protected $notDelayedInjected ]',
                        'Property [ <default> protected $nullable ]',
                        'Property [ <default> protected $notNullable ]',
                        'Property [ <default> protected $mandatory ]',
                        'Property [ <default> protected $notMandatory ]',
                        'Property [ <default> protected $optional ]',
                        'Property [ <default> protected $notOptional ]',
                        'Property [ <default> protected $cloneable ]',
                        'Property [ <default> protected $notCloneable ]',
                        'Property [ <default> protected $id ]',
                        'Property [ <default> protected $notId ]',
                        'Property [ <default> protected $column ]',
                        'Property [ <default> protected $notColumn ]',
                        'Property [ <default> protected $aString ]',
                        'Property [ <default> protected $aNullableString ]',
                        'Property [ <default> protected $anInt ]',
                        'Property [ <default> protected $aNullableInt ]',
                        'Property [ <default> protected $anIntOrFalse ]',
                        'Property [ <default> protected $protected ]',
                        'Property [ protected static $protectedStatic ]',
                    ],
                ],
                'Protected Or Static' => [
                    ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_STATIC,
                    [
                        'Property [ <default> protected $injected ]',
                        'Property [ <default> protected $notInjected ]',
                        'Property [ <default> protected $delayedInjected ]',
                        'Property [ <default> protected $notDelayedInjected ]',
                        'Property [ <default> protected $nullable ]',
                        'Property [ <default> protected $notNullable ]',
                        'Property [ <default> protected $mandatory ]',
                        'Property [ <default> protected $notMandatory ]',
                        'Property [ <default> protected $optional ]',
                        'Property [ <default> protected $notOptional ]',
                        'Property [ <default> protected $cloneable ]',
                        'Property [ <default> protected $notCloneable ]',
                        'Property [ <default> protected $id ]',
                        'Property [ <default> protected $notId ]',
                        'Property [ <default> protected $column ]',
                        'Property [ <default> protected $notColumn ]',
                        'Property [ <default> protected $aString ]',
                        'Property [ <default> protected $aNullableString ]',
                        'Property [ <default> protected $anInt ]',
                        'Property [ <default> protected $aNullableInt ]',
                        'Property [ <default> protected $anIntOrFalse ]',
                        'Property [ <default> protected $protected ]',
                        'Property [ public static $publicStatic ]',
                        'Property [ protected static $protectedStatic ]',
                        'Property [ private static $privateStatic ]',
                    ],
                ],
            ];
    }

    /**
     * @return array<string, array<int|string, string|bool>>
     */
    public function providerForGetProperty(): array
    {
        return
            [
                'Injected' => [
                    'injected',
                    'isDelayedInject' => false,
                    'isInjected' => true,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Not Injected' => [
                    'notInjected',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'DelayedInjected' => [
                    'delayedInjected',
                    'isDelayedInject' => true,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Not DelayedInjected' => [
                    'notDelayedInjected',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Nullable' => [
                    'nullable',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Not Nullable' => [
                    'notNullable',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Mandatory' => [
                    'mandatory',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Not Mandatory' => [
                    'notMandatory',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Optional' => [
                    'optional',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Not Optional' => [
                    'notOptional',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Cloneable' => [
                    'cloneable',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Not Cloneable' => [
                    'notCloneable',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'ID' => [
                    'id',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Not ID' => [
                    'notId',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Column' => [
                    'column',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Not Column' => [
                    'notColumn',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],

                'Public' => [
                    'public',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => true,
                    'isProtected' => false,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Protected' => [
                    'protected',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => false,
                ],
                'Private' => [
                    'private',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => false,
                    'isPrivate' => true,
                    'isStatic' => false,
                ],
                'Public Statis' => [
                    'publicStatic',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => true,
                    'isProtected' => false,
                    'isPrivate' => false,
                    'isStatic' => true,
                ],
                'Protected Static' => [
                    'protectedStatic',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => true,
                    'isPrivate' => false,
                    'isStatic' => true,
                ],
                'Private Static' => [
                    'privateStatic',
                    'isDelayedInject' => false,
                    'isInjected' => false,
                    'isPublic' => false,
                    'isProtected' => false,
                    'isPrivate' => true,
                    'isStatic' => true,
                ],
            ];
    }

    /** @return ReflectionClass|ReflectionObject */
    abstract protected function generateReflector();
}
