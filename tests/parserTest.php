<?php
/**
 * SimpleTypes
 *
 * Copyright (c) 2015, Denis Smetannikov <denis@jbzoo.com>.
 *
 * @package   SimpleTypes
 * @author    Denis Smetannikov <denis@jbzoo.com>
 * @copyright 2015 Denis Smetannikov <denis@jbzoo.com>
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 * @link      http://github.com/smetdenis/simpletypes
 */

namespace SmetDenis\SimpleTypes;

/**
 * Class parserTest
 * @package SmetDenis\SimpleTypes
 */
class parserTest extends PHPUnit
{


    public function testEmpty()
    {
        $empty = '0 eur';

        $this->batchEqualDumps(array(
            // empty
            [$empty],
            [$empty, null],
            [$empty, 0],
            [$empty, ''],

            // spaces
            [$empty, ' '],
            [$empty, '  '],
            [$empty, "\t"],
            [$empty, "\t "],
            [$empty, " \t "],
            [$empty, " \n "],
            [$empty, " \n\r "],
            [$empty, " \r\n "],

            // arrays
            [$empty, []],
            [$empty, [null, null]],
            [$empty, [0]],
            [$empty, [0, '']],
            [$empty, [0, '']],
        ));
    }

    public function testSimple()
    {
        $this->batchEqualDumps(array(
            // int
            ['1 eur', 1],
            ['-1 eur', -1],

            // float
            ['2 eur', 2.0],
            ['3.55 eur', 3.55],
            ['0.57 eur', .57],
            ['-2 eur', '-2,'],
            ['-3.55 eur', '-3,55'],
            ['-0.57 eur', '-,57'],

            // big
            ['1000000.999999 eur', '1 000 000.999 999'],

            // huge
            ['1.0E+18 eur', '1.0e+18'],
        ));
    }

    public function testRule()
    {
        $this->batchEqualDumps(array(
            ['0 eur', 'eur'],
            ['1 eur', '1eur'],
            ['1 eur', '1EUR'],
            ['1 eur', '1eUr'],
            ['1 eur', '1 eur '],
            ['1 eur', '1eur '],
            ['1 eur', '1 eur'],
            ['1 eur', '1 eur'],
            ['1 usd', "1\tusd"],
            ['1.0E+18 eur', '1.0e+18 EUR '],

            // reverse
            ['1 eur', 'eur1'],
            ['1 eur', 'eur 1'],
            ['1 eur', ' eur 1'],
            ['1 eur', ' eur 1 '],
            ['1 eur', 'eur 1 '],
        ));
    }

    public function testRound()
    {
        $this->batchEqualDumps(array(
            ['0.1 eur', '.1'],
            ['0.01 eur', '.01'],
            ['0.001 eur', '.001'],
            ['0.0001 eur', '.0001'],
            ['1.0E-5 eur', '.00001'],
            ['1.0E-6 eur', '.000001'],
            ['1.0E-7 eur', '.0000001'],
            ['1.0E-8 eur', '.00000001'],
            ['1.0E-8 eur', '.000000009'],
            ['1.0E-8 eur', '.0000000099'],
            ['1.0E-8 eur', '.000000005'],
            ['0 eur', '.000000001'],
            ['0 eur', '.000000004'],
        ));
    }

    public function testComplex()
    {
        $this->batchEqualDumps(array(
            ['-123.456 usd', ' - 1 2 3 . 4 5 6 usd '],
            ['-123.456 usd', [' - 1 2 3 , 4 5 6 eur', 'usd']],
            ['-123.456 usd', [' - 1 2 3 . 4 5 6 eur', 'usd']],

            // insane
            ['-987654321.12346 eur', 'some number - 9 8 7 6 5 4 3 2 1,      1  2 3    4   5 6 7    8  9 eur '],
        ));
    }

    public function testUndefinedRule()
    {
        $this->assertTrue($this->val('1 undefined')->isRule('eur'));
    }
}
