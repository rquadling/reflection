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

namespace RQuadlingTests\Reflection\Fixtures;

use stdClass;

class ReflectionFixture extends AbstractReflectionFixture
{
    const CONSTANT_1 = 1;
    const CONSTANT_100 = 100;
    const ANOTHER_CONSTANT_1 = 1;
    const ANOTHER_CONSTANT_100 = 100;

    /**
     * @var stdClass
     * @Inject
     */
    protected $injected;

    /** @var stdClass */
    protected $notInjected;

    /**
     * @var stdClass
     * @DelayedInject
     */
    protected $delayedInjected;

    /** @var stdClass */
    protected $notDelayedInjected;

    /** @var string|null */
    protected $nullable;

    /** @var string */
    protected $notNullable;

    /**
     * @var string
     * @mandatory
     */
    protected $mandatory;

    /** @var string */
    protected $notMandatory;

    /**
     * @var string
     * @optional
     */
    protected $optional;

    /** @var string */
    protected $notOptional;

    /**
     * @var string
     * @cloneable
     */
    protected $cloneable;

    /** @var string */
    protected $notCloneable;

    /**
     * @var string
     * @id
     */
    protected $id;

    /** @var string */
    protected $notId;

    /**
     * @var string
     * @column
     */
    protected $column;

    /** @var string */
    protected $notColumn;

    /** @var string */
    protected $aString;

    /** @var string|null */
    protected $aNullableString;

    /** @var int */
    protected $anInt;

    /** @var int|null */
    protected $aNullableInt;

    /** @var int|false */
    protected $anIntOrFalse;

    public $public;
    protected $protected;
    private $private;
    public static $publicStatic;
    protected static $protectedStatic;
    private static $privateStatic;

    public function exportable()
    {
    }

    /**
     * @no-export
     */
    public function notExportable()
    {
    }

    public function fnAbstractPublic()
    {
    }

    protected function fnAbstractProtected()
    {
    }

    public static function fnAbstractStaticPublic()
    {
    }

    protected static function fnAbstractStaticProtected()
    {
    }

    /**
     * @return string returns a string
     */
    public function aString(): string
    {
        return '';
    }

    /**
     * @return string|null
     */
    public function aNullableString()
    {
        return '';
    }

    /**
     * @return int returns an int
     */
    public function anInt(): int
    {
        return 0;
    }

    /**
     * @return int|null
     */
    public function aNullableInt()
    {
        return 0;
    }

    /**
     * @return int|false
     */
    public function anIntOrFalse()
    {
        return 0;
    }
}
