<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 *
 * @see https://modulestudio.de
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Tests\Unit;

use PHPUnit\Framework\TestCase;

class StackTest extends TestCase
{
    public function testPushAndPop(): void
    {
        $stack = [];
        self::assertCount(0, $stack);

        $stack[] = 'foo';
        self::assertEquals('foo', $stack[count($stack) - 1]);
        self::assertCount(1, $stack);

        self::assertEquals('foo', array_pop($stack));
        self::assertCount(0, $stack);
    }
}
