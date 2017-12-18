<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.0 (https://modulestudio.de).
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require_once dirname(__FILE__) . '/bootstrap.php';

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ZikulaMultisitesModule - All Tests');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
