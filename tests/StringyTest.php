<?php

require_once __DIR__ . '/../src/Stringy.php';

use Stringy\Stringy as S;
use voku\helper\UTF8;

/**
 * Class StringyTest
 *
 * @internal
 */
final class StringyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function appendProvider(): array
    {
        return [
            ['foobar', 'foo', 'bar'],
            ['fòôbàř', 'fòô', 'bàř', 'UTF-8'],
        ];
    }

    /**
     * Asserts that a variable is of a Stringy instance.
     *
     * @param mixed $actual
     */
    public function assertStringy($actual)
    {
        static::assertInstanceOf('Stringy\Stringy', $actual);
    }

    /**
     * @return array
     */
    public function atProvider(): array
    {
        return [
            ['f', 'foo bar', 0],
            ['o', 'foo bar', 1],
            ['r', 'foo bar', 6],
            ['', 'foo bar', 7],
            ['f', 'fòô bàř', 0, 'UTF-8'],
            ['ò', 'fòô bàř', 1, 'UTF-8'],
            ['ř', 'fòô bàř', 6, 'UTF-8'],
            ['', 'fòô bàř', 7, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function betweenProvider(): array
    {
        return [
            ['', 'foo', '{', '}'],
            ['', '{foo', '{', '}'],
            ['foo', '{foo}', '{', '}'],
            ['{foo', '{{foo}', '{', '}'],
            ['', '{}foo}', '{', '}'],
            ['foo', '}{foo}', '{', '}'],
            ['foo', 'A description of {foo} goes here', '{', '}'],
            ['bar', '{foo} and {bar}', '{', '}', 1],
            ['', 'fòô', '{', '}', 0, 'UTF-8'],
            ['', '{fòô', '{', '}', 0, 'UTF-8'],
            ['fòô', '{fòô}', '{', '}', 0, 'UTF-8'],
            ['{fòô', '{{fòô}', '{', '}', 0, 'UTF-8'],
            ['', '{}fòô}', '{', '}', 0, 'UTF-8'],
            ['fòô', '}{fòô}', '{', '}', 0, 'UTF-8'],
            ['fòô', 'A description of {fòô} goes here', '{', '}', 0, 'UTF-8'],
            ['bàř', '{fòô} and {bàř}', '{', '}', 1, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function camelizeProvider(): array
    {
        return [
            ['camelCase', 'CamelCase'],
            ['camelCase', 'Camel-Case'],
            ['camelCase', 'camel case'],
            ['camelCase', 'camel -case'],
            ['camelCase', 'camel - case'],
            ['camelCase', 'camel_case'],
            ['camelCTest', 'camel c test'],
            ['stringWith1Number', 'string_with1number'],
            ['stringWith22Numbers', 'string-with-2-2 numbers'],
            ['dataRate', 'data_rate'],
            ['backgroundColor', 'background-color'],
            ['yesWeCan', 'yes_we_can'],
            ['mozSomething', '-moz-something'],
            ['carSpeed', '_car_speed_'],
            ['serveHTTP', 'ServeHTTP'],
            ['1Camel2Case', '1camel2case'],
            ['camelΣase', 'camel σase', 'UTF-8'],
            ['στανιλCase', 'Στανιλ case', 'UTF-8'],
            ['σamelCase', 'σamel  Case', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function capitalizePersonalNameProvider(): array
    {
        return [
            ['Marcus Aurelius', 'marcus aurelius'],
            ['Torbjørn Færøvik', 'torbjørn færøvik'],
            ['Jaap de Hoop Scheffer', 'jaap de hoop scheffer'],
            ['K. Anders Ericsson', 'k. anders ericsson'],
            ['Per-Einar', 'per-einar'],
            [
                'Line Break',
                'line
             break',
            ],
            ['ab', 'ab'],
            ['af', 'af'],
            ['al', 'al'],
            ['and', 'and'],
            ['ap', 'ap'],
            ['bint', 'bint'],
            ['binte', 'binte'],
            ['da', 'da'],
            ['de', 'de'],
            ['del', 'del'],
            ['den', 'den'],
            ['der', 'der'],
            ['di', 'di'],
            ['dit', 'dit'],
            ['ibn', 'ibn'],
            ['la', 'la'],
            ['mac', 'mac'],
            ['nic', 'nic'],
            ['of', 'of'],
            ['ter', 'ter'],
            ['the', 'the'],
            ['und', 'und'],
            ['van', 'van'],
            ['von', 'von'],
            ['y', 'y'],
            ['zu', 'zu'],
            ['Bashar al-Assad', 'bashar al-assad'],
            ["d'Name", "d'Name"],
            ['ffName', 'ffName'],
            ["l'Name", "l'Name"],
            ['macDuck', 'macDuck'],
            ['mcDuck', 'mcDuck'],
            ['nickMick', 'nickMick'],
        ];
    }

    /**
     * @return array
     */
    public function charsProvider(): array
    {
        return [
            [[], ''],
            [['T', 'e', 's', 't'], 'Test'],
            [['F', 'ò', 'ô', ' ', 'B', 'à', 'ř'], 'Fòô Bàř', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function collapseWhitespaceProvider(): array
    {
        return [
            ['foo bar', '  foo   bar  '],
            ['test string', 'test string'],
            ['Ο συγγραφέας', '   Ο     συγγραφέας  '],
            ['123', ' 123 '],
            ['', ' ', 'UTF-8'], // no-break space (U+00A0)
            ['', '           ', 'UTF-8'], // spaces U+2000 to U+200A
            ['', ' ', 'UTF-8'], // narrow no-break space (U+202F)
            ['', ' ', 'UTF-8'], // medium mathematical space (U+205F)
            ['', '　', 'UTF-8'], // ideographic space (U+3000)
            ['1 2 3', '  1  2  3　　', 'UTF-8'],
            ['', ' '],
            ['', ''],
        ];
    }

    /**
     * @return array
     */
    public function containsAllProvider(): array
    {
        // One needle
        $singleNeedle = \array_map(
            static function ($array) {
                $array[2] = [$array[2]];

                return $array;
            },
            $this->containsProvider()
        );

        $provider = [
            // One needle
            [false, 'Str contains foo bar', []],
            [false, 'Str contains foo bar', ['']],
            // Multiple needles
            [true, 'Str contains foo bar', ['foo', 'bar']],
            [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*', '&^%']],
            [true, 'Ο συγγραφέας είπε', ['συγγρ', 'αφέας'], 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å´¥', '©'], true, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å˚ ', '∆'], true, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['øœ', '¬'], true, 'UTF-8'],
            [false, 'Str contains foo bar', ['Foo', 'bar']],
            [false, 'Str contains foo bar', ['foobar', 'bar']],
            [false, 'Str contains foo bar', ['foo bar ', 'bar']],
            [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', '  συγγραφ '], true, 'UTF-8'],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßå˚', ' ß '], true, 'UTF-8'],
            [true, 'Str contains foo bar', ['Foo bar', 'bar'], false],
            [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*&^%', '*&^%'], false],
            [true, 'Ο συγγραφέας είπε', ['ΣΥΓΓΡΑΦΈΑΣ', 'ΑΦΈΑ'], false, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å´¥©', '¥©'], false, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å˚ ∆', ' ∆'], false, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['ØŒ¬', 'Œ'], false, 'UTF-8'],
            [false, 'Str contains foo bar', ['foobar', 'none'], false],
            [false, 'Str contains foo bar', ['foo bar ', ' ba'], false],
            [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', ' ραφέ '], false, 'UTF-8'],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßÅ˚', ' Å˚ '], false, 'UTF-8'],
        ];

        return \array_merge($singleNeedle, $provider);
    }

    /**
     * @return array
     */
    public function containsAnyProvider(): array
    {
        // One needle
        $singleNeedle = \array_map(
            static function ($array) {
                $array[2] = [$array[2]];

                return $array;
            },
            $this->containsProvider()
        );

        $provider = [
            // No needles
            [false, 'Str contains foo bar', []],
            // Multiple needles
            [true, 'Str contains foo bar', ['foo', 'bar']],
            [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*', '&^%']],
            [true, 'Ο συγγραφέας είπε', ['συγγρ', 'αφέας'], 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å´¥', '©'], true, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å˚ ', '∆'], true, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['øœ', '¬'], true, 'UTF-8'],
            [false, 'Str contains foo bar', ['Foo', 'Bar']],
            [false, 'Str contains foo bar', ['foobar', 'bar ']],
            [false, 'Str contains foo bar', ['foo bar ', '  foo']],
            [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', '  συγγραφ '], true, 'UTF-8'],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßå˚', ' ß '], true, 'UTF-8'],
            [true, 'Str contains foo bar', ['Foo bar', 'bar'], false],
            [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*&^%', '*&^%'], false],
            [true, 'Ο συγγραφέας είπε', ['ΣΥΓΓΡΑΦΈΑΣ', 'ΑΦΈΑ'], false, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å´¥©', '¥©'], false, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å˚ ∆', ' ∆'], false, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['ØŒ¬', 'Œ'], false, 'UTF-8'],
            [false, 'Str contains foo bar', ['foobar', 'none'], false],
            [false, 'Str contains foo bar', ['foo bar ', ' ba '], false],
            [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', ' ραφέ '], false, 'UTF-8'],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßÅ˚', ' Å˚ '], false, 'UTF-8'],
        ];

        return \array_merge($singleNeedle, $provider);
    }

    /**
     * @return array
     */
    public function containsProvider(): array
    {
        return [
            [true, 'Str contains foo bar', 'foo bar'],
            [true, '12398!@(*%!@# @!%#*&^%', ' @!%#*&^%'],
            [true, 'Ο συγγραφέας είπε', 'συγγραφέας', 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'å´¥©', true, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'å˚ ∆', true, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'øœ¬', true, 'UTF-8'],
            [false, 'Str contains foo bar', 'Foo bar'],
            [false, 'Str contains foo bar', 'foobar'],
            [false, 'Str contains foo bar', 'foo bar '],
            [false, 'Ο συγγραφέας είπε', '  συγγραφέας ', true, 'UTF-8'],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ' ßå˚', true, 'UTF-8'],
            [true, 'Str contains foo bar', 'Foo bar', false],
            [true, '12398!@(*%!@# @!%#*&^%', ' @!%#*&^%', false],
            [true, 'Ο συγγραφέας είπε', 'ΣΥΓΓΡΑΦΈΑΣ', false, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'Å´¥©', false, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'Å˚ ∆', false, 'UTF-8'],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'ØŒ¬', false, 'UTF-8'],
            [false, 'Str contains foo bar', 'foobar', false],
            [false, 'Str contains foo bar', 'foo bar ', false],
            [false, 'Ο συγγραφέας είπε', '  συγγραφέας ', false, 'UTF-8'],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ' ßÅ˚', false, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function countSubstrProvider(): array
    {
        return [
            [0, '', 'foo'],
            [0, 'foo', 'bar'],
            [1, 'foo bar', 'foo'],
            [2, 'foo bar', 'o'],
            [0, '', 'fòô', 'UTF-8'],
            [0, 'fòô', 'bàř', 'UTF-8'],
            [1, 'fòô bàř', 'fòô', 'UTF-8'],
            [2, 'fôòô bàř', 'ô', 'UTF-8'],
            [0, 'fÔÒÔ bàř', 'ô', 'UTF-8'],
            [0, 'foo', 'BAR', false],
            [1, 'foo bar', 'FOo', false],
            [2, 'foo bar', 'O', false],
            [1, 'fòô bàř', 'fÒÔ', false, 'UTF-8'],
            [2, 'fôòô bàř', 'Ô', false, 'UTF-8'],
            [2, 'συγγραφέας', 'Σ', false, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function dasherizeProvider(): array
    {
        return [
            ['test-case', 'testCase'],
            ['test-case', 'Test-Case'],
            ['test-case', 'test case'],
            ['-test-case', '-test -case'],
            ['test-case', 'test - case'],
            ['test-case', 'test_case'],
            ['test-c-test', 'test c test'],
            ['test-d-case', 'TestDCase'],
            ['test-c-c-test', 'TestCCTest'],
            ['string-with1number', 'string_with1number'],
            ['string-with-2-2-numbers', 'String-with_2_2 numbers'],
            ['1test2case', '1test2case'],
            ['data-rate', 'dataRate'],
            ['car-speed', 'CarSpeed'],
            ['yes-we-can', 'yesWeCan'],
            ['background-color', 'backgroundColor'],
            ['dash-σase', 'dash Σase', 'UTF-8'],
            ['στανιλ-case', 'Στανιλ case', 'UTF-8'],
            ['σash-case', 'Σash  Case', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function delimitProvider(): array
    {
        return [
            ['test*case', 'testCase', '*'],
            ['test&case', 'Test-Case', '&'],
            ['test#case', 'test case', '#'],
            ['test**case', 'test -case', '**'],
            ['~!~test~!~case', '-test - case', '~!~'],
            ['test*case', 'test_case', '*'],
            ['test%c%test', '  test c test', '%'],
            ['test+u+case', 'TestUCase', '+'],
            ['test=c=c=test', 'TestCCTest', '='],
            ['string#>with1number', 'string_with1number', '#>'],
            ['1test2case', '1test2case', '*'],
            ['test ύα σase', 'test Σase', ' ύα ', 'UTF-8'],
            ['στανιλαcase', 'Στανιλ case', 'α', 'UTF-8'],
            ['σashΘcase', 'Σash  Case', 'Θ', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function endsWithAnyProvider(): array
    {
        return [
            [true, 'foo bars', ['foo', 'o bars']],
            [true, 'FOO bars', ['foo', 'o bars'], false],
            [true, 'FOO bars', ['foo', 'o BARs'], false],
            [true, 'FÒÔ bàřs', ['foo', 'ô bàřs'], false, 'UTF-8'],
            [true, 'fòô bàřs', ['foo', 'ô BÀŘs'], false, 'UTF-8'],
            [false, 'foo bar', ['foo']],
            [false, 'foo bar', ['foo', 'foo bars']],
            [false, 'FOO bar', ['foo', 'foo bars']],
            [false, 'FOO bars', ['foo', 'foo BARS']],
            [false, 'FÒÔ bàřs', ['fòô', 'fòô bàřs'], true, 'UTF-8'],
            [false, 'fòô bàřs', ['fòô', 'fòô BÀŘS'], true, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function endsWithProvider(): array
    {
        return [
            [true, 'foo bars', 'o bars'],
            [true, 'FOO bars', 'o bars', false],
            [true, 'FOO bars', 'o BARs', false],
            [true, 'FÒÔ bàřs', 'ô bàřs', false, 'UTF-8'],
            [true, 'fòô bàřs', 'ô BÀŘs', false, 'UTF-8'],
            [false, 'foo bar', 'foo'],
            [false, 'foo bar', 'foo bars'],
            [false, 'FOO bar', 'foo bars'],
            [false, 'FOO bars', 'foo BARS'],
            [false, 'FÒÔ bàřs', 'fòô bàřs', true, 'UTF-8'],
            [false, 'fòô bàřs', 'fòô BÀŘS', true, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function ensureLeftProvider(): array
    {
        return [
            ['foobar', 'foobar', 'f'],
            ['foobar', 'foobar', 'foo'],
            ['foo/foobar', 'foobar', 'foo/'],
            ['http://foobar', 'foobar', 'http://'],
            ['http://foobar', 'http://foobar', 'http://'],
            ['fòôbàř', 'fòôbàř', 'f', 'UTF-8'],
            ['fòôbàř', 'fòôbàř', 'fòô', 'UTF-8'],
            ['fòô/fòôbàř', 'fòôbàř', 'fòô/', 'UTF-8'],
            ['http://fòôbàř', 'fòôbàř', 'http://', 'UTF-8'],
            ['http://fòôbàř', 'http://fòôbàř', 'http://', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function ensureRightProvider(): array
    {
        return [
            ['foobar', 'foobar', 'r'],
            ['foobar', 'foobar', 'bar'],
            ['foobar/bar', 'foobar', '/bar'],
            ['foobar.com/', 'foobar', '.com/'],
            ['foobar.com/', 'foobar.com/', '.com/'],
            ['fòôbàř', 'fòôbàř', 'ř', 'UTF-8'],
            ['fòôbàř', 'fòôbàř', 'bàř', 'UTF-8'],
            ['fòôbàř/bàř', 'fòôbàř', '/bàř', 'UTF-8'],
            ['fòôbàř.com/', 'fòôbàř', '.com/', 'UTF-8'],
            ['fòôbàř.com/', 'fòôbàř.com/', '.com/', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function escapeProvider(): array
    {
        return [
            ['', ''],
            ['raboof &lt;3', 'raboof <3'],
            ['řàbôòf&lt;foo&lt;lall&gt;&gt;&gt;', 'řàbôòf<foo<lall>>>'],
            ['řàb &lt;ô&gt;òf', 'řàb <ô>òf'],
            ['&lt;∂∆ onerro=&quot;alert(xss)&quot;&gt; ˚åß', '<∂∆ onerro="alert(xss)"> ˚åß'],
            ['&#039;œ … &#039;’)', '\'œ … \'’)'],
        ];
    }

    /**
     * @return array
     */
    public function firstProvider(): array
    {
        return [
            ['', 'foo bar', -5],
            ['', 'foo bar', 0],
            ['f', 'foo bar', 1],
            ['foo', 'foo bar', 3],
            ['foo bar', 'foo bar', 7],
            ['foo bar', 'foo bar', 8],
            ['', 'fòô bàř', -5, 'UTF-8'],
            ['', 'fòô bàř', 0, 'UTF-8'],
            ['f', 'fòô bàř', 1, 'UTF-8'],
            ['fòô', 'fòô bàř', 3, 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 7, 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 8, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function hasLowerCaseProvider(): array
    {
        return [
            [false, ''],
            [true, 'foobar'],
            [false, 'FOO BAR'],
            [true, 'fOO BAR'],
            [true, 'foO BAR'],
            [true, 'FOO BAr'],
            [true, 'Foobar'],
            [false, 'FÒÔBÀŘ', 'UTF-8'],
            [true, 'fòôbàř', 'UTF-8'],
            [true, 'fòôbàř2', 'UTF-8'],
            [true, 'Fòô bàř', 'UTF-8'],
            [true, 'fòôbÀŘ', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function hasUpperCaseProvider(): array
    {
        return [
            [false, ''],
            [true, 'FOOBAR'],
            [false, 'foo bar'],
            [true, 'Foo bar'],
            [true, 'FOo bar'],
            [true, 'foo baR'],
            [true, 'fOOBAR'],
            [false, 'fòôbàř', 'UTF-8'],
            [true, 'FÒÔBÀŘ', 'UTF-8'],
            [true, 'FÒÔBÀŘ2', 'UTF-8'],
            [true, 'fÒÔ BÀŘ', 'UTF-8'],
            [true, 'FÒÔBàř', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function htmlDecodeProvider(): array
    {
        return [
            ['&', '&amp;'],
            ['"', '&quot;'],
            ["'", '&#039;', \ENT_QUOTES],
            ['<', '&lt;'],
            ['>', '&gt;'],
        ];
    }

    /**
     * @return array
     */
    public function htmlEncodeProvider(): array
    {
        return [
            ['&amp;', '&'],
            ['&quot;', '"'],
            ['&#039;', "'", \ENT_QUOTES],
            ['&lt;', '<'],
            ['&gt;', '>'],
        ];
    }

    /**
     * @return array
     */
    public function humanizeProvider(): array
    {
        return [
            ['Author', 'author_id'],
            ['Test user', ' _test_user_'],
            ['Συγγραφέας', ' συγγραφέας_id ', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function indexOfLastProvider(): array
    {
        return [
            [6, 'foo & bar', 'bar'],
            [6, 'foo & bar', 'bar', 0],
            [false, 'foo & bar', 'baz'],
            [false, 'foo & bar', 'baz', 0],
            [12, 'foo & bar & foo', 'foo', 0],
            [0, 'foo & bar & foo', 'foo', -5],
            [6, 'fòô & bàř', 'bàř', 0, 'UTF-8'],
            [false, 'fòô & bàř', 'baz', 0, 'UTF-8'],
            [12, 'fòô & bàř & fòô', 'fòô', 0, 'UTF-8'],
            [0, 'fòô & bàř & fòô', 'fòô', -5, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function indexOfLastIgnoreCaseProvider(): array
    {
        return [
            [6, 'foo & bar', 'Bar'],
            [6, 'foo & bar', 'bAr', 0],
            [false, 'foo & bar', 'baZ'],
            [false, 'foo & bar', 'baZ', 0],
            [12, 'foo & bar & foo', 'fOo', 0],
            [0, 'foo & bar & foo', 'fOO', -5],
            [6, 'fòô & bàř', 'bàř', 0, 'UTF-8'],
            [false, 'fòô & bàř', 'baz', 0, 'UTF-8'],
            [12, 'fòô & bàř & fòô', 'fòô', 0, 'UTF-8'],
            [0, 'fòô & bàř & fòô', 'fòÔ', -5, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function indexOfIgnoreCaseProvider(): array
    {
        return [
            [6, 'foo & bar', 'Bar'],
            [6, 'foo & bar', 'bar', 0],
            [false, 'foo & bar', 'Baz'],
            [false, 'foo & bar', 'bAz', 0],
            [0, 'foo & bar & foo', 'foO', 0],
            [12, 'foo & bar & foo', 'fOO', 5],
            [6, 'fòô & bàř', 'bàř', 0, 'UTF-8'],
            [false, 'fòô & bàř', 'baz', 0, 'UTF-8'],
            [0, 'fòô & bàř & fòô', 'fòô', 0, 'UTF-8'],
            [12, 'fòô & bàř & fòô', 'fòÔ', 5, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function indexOfProvider(): array
    {
        return [
            [6, 'foo & bar', 'bar'],
            [6, 'foo & bar', 'bar', 0],
            [false, 'foo & bar', 'baz'],
            [false, 'foo & bar', 'baz', 0],
            [0, 'foo & bar & foo', 'foo', 0],
            [12, 'foo & bar & foo', 'foo', 5],
            [6, 'fòô & bàř', 'bàř', 0, 'UTF-8'],
            [false, 'fòô & bàř', 'baz', 0, 'UTF-8'],
            [0, 'fòô & bàř & fòô', 'fòô', 0, 'UTF-8'],
            [12, 'fòô & bàř & fòô', 'fòô', 5, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function insertProvider(): array
    {
        return [
            ['foo bar', 'oo bar', 'f', 0],
            ['foo bar', 'f bar', 'oo', 1],
            ['f bar', 'f bar', 'oo', 20],
            ['foo bar', 'foo ba', 'r', 6],
            ['fòôbàř', 'fòôbř', 'à', 4, 'UTF-8'],
            ['fòô bàř', 'òô bàř', 'f', 0, 'UTF-8'],
            ['fòô bàř', 'f bàř', 'òô', 1, 'UTF-8'],
            ['fòô bàř', 'fòô bà', 'ř', 6, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function isAlphaProvider(): array
    {
        return [
            [true, ''],
            [true, 'foobar'],
            [false, 'foo bar'],
            [false, 'foobar2'],
            [true, 'fòôbàř', 'UTF-8'],
            [false, 'fòô bàř', 'UTF-8'],
            [false, 'fòôbàř2', 'UTF-8'],
            [true, 'ҠѨњфгШ', 'UTF-8'],
            [false, 'ҠѨњ¨ˆфгШ', 'UTF-8'],
            [true, '丹尼爾', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function isAlphanumericProvider(): array
    {
        return [
            [true, ''],
            [true, 'foobar1'],
            [false, 'foo bar'],
            [false, 'foobar2"'],
            [false, "\nfoobar\n"],
            [true, 'fòôbàř1', 'UTF-8'],
            [false, 'fòô bàř', 'UTF-8'],
            [false, 'fòôbàř2"', 'UTF-8'],
            [true, 'ҠѨњфгШ', 'UTF-8'],
            [false, 'ҠѨњ¨ˆфгШ', 'UTF-8'],
            [true, '丹尼爾111', 'UTF-8'],
            [true, 'دانيال1', 'UTF-8'],
            [false, 'دانيال1 ', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function isBase64Provider(): array
    {
        return [
            [false, ' '],
            [false, ''],
            [true, \base64_encode('FooBar')],
            [true, \base64_encode(' ')],
            [true, \base64_encode('FÒÔBÀŘ')],
            [true, \base64_encode('συγγραφέας')],
            [false, 'Foobar'],
        ];
    }

    /**
     * @return array
     */
    public function isBlankProvider(): array
    {
        return [
            [true, ''],
            [true, ' '],
            [true, "\n\t "],
            [true, "\n\t  \v\f"],
            [false, "\n\t a \v\f"],
            [false, "\n\t ' \v\f"],
            [false, "\n\t 2 \v\f"],
            [true, '', 'UTF-8'],
            [true, ' ', 'UTF-8'], // no-break space (U+00A0)
            [true, '           ', 'UTF-8'], // spaces U+2000 to U+200A
            [true, ' ', 'UTF-8'], // narrow no-break space (U+202F)
            [true, ' ', 'UTF-8'], // medium mathematical space (U+205F)
            [true, '　', 'UTF-8'], // ideographic space (U+3000)
            [false, '　z', 'UTF-8'],
            [false, '　1', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function isHexadecimalProvider(): array
    {
        return [
            [true, ''],
            [true, 'abcdef'],
            [true, 'ABCDEF'],
            [true, '0123456789'],
            [true, '0123456789AbCdEf'],
            [false, '0123456789x'],
            [false, 'ABCDEFx'],
            [true, 'abcdef', 'UTF-8'],
            [true, 'ABCDEF', 'UTF-8'],
            [true, '0123456789', 'UTF-8'],
            [true, '0123456789AbCdEf', 'UTF-8'],
            [false, '0123456789x', 'UTF-8'],
            [false, 'ABCDEFx', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function isJsonProvider(): array
    {
        return [
            [false, ''],
            [false, '  '],
            [false, 'null'],
            [false, 'true'],
            [false, 'false'],
            [true, '[]'],
            [true, '{}'],
            [false, '123'],
            [true, '{"foo": "bar"}'],
            [false, '{"foo":"bar",}'],
            [false, '{"foo"}'],
            [true, '["foo"]'],
            [false, '{"foo": "bar"]'],
            [false, '123', 'UTF-8'],
            [true, '{"fòô": "bàř"}', 'UTF-8'],
            [false, '{"fòô":"bàř",}', 'UTF-8'],
            [false, '{"fòô"}', 'UTF-8'],
            [false, '["fòô": "bàř"]', 'UTF-8'],
            [true, '["fòô"]', 'UTF-8'],
            [false, '{"fòô": "bàř"]', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function isLowerCaseProvider(): array
    {
        return [
            [true, ''],
            [true, 'foobar'],
            [false, 'foo bar'],
            [false, 'Foobar'],
            [true, 'fòôbàř', 'UTF-8'],
            [false, 'fòôbàř2', 'UTF-8'],
            [false, 'fòô bàř', 'UTF-8'],
            [false, 'fòôbÀŘ', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function isProvider(): array
    {
        return [
            [true, 'Gears\\String\\Str', 'Gears\\String\\Str'],
            [true, 'Gears\\String\\Str', 'Gears\\*\\Str'],
            [true, 'Gears\\String\\Str', 'Gears\\*\\*'],
            [true, 'Gears\\String\\Str', '*\\*\\*'],
            [true, 'Gears\\String\\Str', '*\\String\\*'],
            [true, 'Gears\\String\\Str', '*\\*\\Str'],
            [true, 'Gears\\String\\Str', '*\\Str'],
            [true, 'Gears\\String\\Str', '*'],
            [true, 'Gears\\String\\Str', '**'],
            [true, 'Gears\\String\\Str', '****'],
            [true, 'Gears\\String\\Str', '*Str'],
            [false, 'Gears\\String\\Str', '*\\'],
            [false, 'Gears\\String\\Str', 'Gears-*-*'],
        ];
    }

    /**
     * @return array
     */
    public function isSerializedProvider(): array
    {
        return [
            [false, ''],
            [true, 'a:1:{s:3:"foo";s:3:"bar";}'],
            [false, 'a:1:{s:3:"foo";s:3:"bar"}'],
            [true, \serialize(['foo' => 'bar'])],
            [true, 'a:1:{s:5:"fòô";s:5:"bàř";}', 'UTF-8'],
            [false, 'a:1:{s:5:"fòô";s:5:"bàř"}', 'UTF-8'],
            [true, \serialize(['fòô' => 'bár']), 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function isUpperCaseProvider(): array
    {
        return [
            [true, ''],
            [true, 'FOOBAR'],
            [false, 'FOO BAR'],
            [false, 'fOOBAR'],
            [true, 'FÒÔBÀŘ', 'UTF-8'],
            [false, 'FÒÔBÀŘ2', 'UTF-8'],
            [false, 'FÒÔ BÀŘ', 'UTF-8'],
            [false, 'FÒÔBàř', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function lastProvider(): array
    {
        return [
            ['', 'foo bar', -5],
            ['', 'foo bar', 0],
            ['r', 'foo bar', 1],
            ['bar', 'foo bar', 3],
            ['foo bar', 'foo bar', 7],
            ['foo bar', 'foo bar', 8],
            ['', 'fòô bàř', -5, 'UTF-8'],
            ['', 'fòô bàř', 0, 'UTF-8'],
            ['ř', 'fòô bàř', 1, 'UTF-8'],
            ['bàř', 'fòô bàř', 3, 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 7, 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 8, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function lengthProvider(): array
    {
        return [
            [11, '  foo bar  '],
            [1, 'f'],
            [0, ''],
            [7, 'fòô bàř', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function linesProvider(): array
    {
        return [
            [[], ''],
            [[''], "\r\n"],
            [['foo', 'bar'], "foo\nbar"],
            [['foo', 'bar'], "foo\rbar"],
            [['foo', 'bar'], "foo\r\nbar"],
            [['foo', '', 'bar'], "foo\r\n\r\nbar"],
            [['foo', 'bar', ''], "foo\r\nbar\r\n"],
            [['', 'foo', 'bar'], "\r\nfoo\r\nbar"],
            [['fòô', 'bàř'], "fòô\nbàř", 'UTF-8'],
            [['fòô', 'bàř'], "fòô\rbàř", 'UTF-8'],
            [['fòô', 'bàř'], "fòô\n\rbàř", 'UTF-8'],
            [['fòô', 'bàř'], "fòô\r\nbàř", 'UTF-8'],
            [['fòô', '', 'bàř'], "fòô\r\n\r\nbàř", 'UTF-8'],
            [['fòô', 'bàř', ''], "fòô\r\nbàř\r\n", 'UTF-8'],
            [['', 'fòô', 'bàř'], "\r\nfòô\r\nbàř", 'UTF-8'],
            [['1111111111111111111'], '1111111111111111111', 'UTF-8'],
            [['1111111111111111111111'], '1111111111111111111111', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function longestCommonPrefixProvider(): array
    {
        return [
            ['foo', 'foobar', 'foo bar'],
            ['foo bar', 'foo bar', 'foo bar'],
            ['f', 'foo bar', 'far boo'],
            ['', 'toy car', 'foo bar'],
            ['', 'foo bar', ''],
            ['fòô', 'fòôbar', 'fòô bar', 'UTF-8'],
            ['fòô bar', 'fòô bar', 'fòô bar', 'UTF-8'],
            ['fò', 'fòô bar', 'fòr bar', 'UTF-8'],
            ['', 'toy car', 'fòô bar', 'UTF-8'],
            ['', 'fòô bar', '', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function longestCommonSubstringProvider(): array
    {
        return [
            ['foo', 'foobar', 'foo bar'],
            ['foo bar', 'foo bar', 'foo bar'],
            ['oo ', 'foo bar', 'boo far'],
            ['foo ba', 'foo bad', 'foo bar'],
            ['', 'foo bar', ''],
            ['fòô', 'fòôbàř', 'fòô bàř', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 'fòô bàř', 'UTF-8'],
            [' bàř', 'fòô bàř', 'fòr bàř', 'UTF-8'],
            [' ', 'toy car', 'fòô bàř', 'UTF-8'],
            ['', 'fòô bàř', '', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function longestCommonSuffixProvider(): array
    {
        return [
            ['bar', 'foobar', 'foo bar'],
            ['foo bar', 'foo bar', 'foo bar'],
            ['ar', 'foo bar', 'boo far'],
            ['', 'foo bad', 'foo bar'],
            ['', 'foo bar', ''],
            ['bàř', 'fòôbàř', 'fòô bàř', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 'fòô bàř', 'UTF-8'],
            [' bàř', 'fòô bàř', 'fòr bàř', 'UTF-8'],
            ['', 'toy car', 'fòô bàř', 'UTF-8'],
            ['', 'fòô bàř', '', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function lowerCaseFirstProvider(): array
    {
        return [
            ['test', 'Test'],
            ['test', 'test'],
            ['1a', '1a'],
            ['σ test', 'Σ test', 'UTF-8'],
            ['σ Test', 'Σ Test', 'UTF-8'],
            [' Σ test', ' Σ test', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function offsetExistsProvider(): array
    {
        return [
            [true, 0],
            [true, 2],
            [false, 3],
            [true, -1],
            [true, -3],
            [false, -4],
        ];
    }

    /**
     * @return array
     */
    public function padBothProvider(): array
    {
        return [
            ['foo bar ', 'foo bar', 8],
            [' foo bar ', 'foo bar', 9, ' '],
            ['fòô bàř ', 'fòô bàř', 8, ' ', 'UTF-8'],
            [' fòô bàř ', 'fòô bàř', 9, ' ', 'UTF-8'],
            ['fòô bàř¬', 'fòô bàř', 8, '¬ø', 'UTF-8'],
            ['¬fòô bàř¬', 'fòô bàř', 9, '¬ø', 'UTF-8'],
            ['¬fòô bàř¬ø', 'fòô bàř', 10, '¬ø', 'UTF-8'],
            ['¬øfòô bàř¬ø', 'fòô bàř', 11, '¬ø', 'UTF-8'],
            ['¬fòô bàř¬ø', 'fòô bàř', 10, '¬øÿ', 'UTF-8'],
            ['¬øfòô bàř¬ø', 'fòô bàř', 11, '¬øÿ', 'UTF-8'],
            ['¬øfòô bàř¬øÿ', 'fòô bàř', 12, '¬øÿ', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function padLeftProvider(): array
    {
        return [
            ['  foo bar', 'foo bar', 9],
            ['_*foo bar', 'foo bar', 9, '_*'],
            ['_*_foo bar', 'foo bar', 10, '_*'],
            ['  fòô bàř', 'fòô bàř', 9, ' ', 'UTF-8'],
            ['¬øfòô bàř', 'fòô bàř', 9, '¬ø', 'UTF-8'],
            ['¬ø¬fòô bàř', 'fòô bàř', 10, '¬ø', 'UTF-8'],
            ['¬ø¬øfòô bàř', 'fòô bàř', 11, '¬ø', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function padProvider(): array
    {
        return [
            // length <= str
            ['foo bar', 'foo bar', -1],
            ['foo bar', 'foo bar', 7],
            ['fòô bàř', 'fòô bàř', 7, ' ', 'right', 'UTF-8'],

            // right
            ['foo bar  ', 'foo bar', 9],
            ['foo bar_*', 'foo bar', 9, '_*', 'right'],
            ['fòô bàř¬ø¬', 'fòô bàř', 10, '¬ø', 'right', 'UTF-8'],

            // left
            ['  foo bar', 'foo bar', 9, ' ', 'left'],
            ['_*foo bar', 'foo bar', 9, '_*', 'left'],
            ['¬ø¬fòô bàř', 'fòô bàř', 10, '¬ø', 'left', 'UTF-8'],

            // both
            ['foo bar ', 'foo bar', 8, ' ', 'both'],
            ['¬fòô bàř¬ø', 'fòô bàř', 10, '¬ø', 'both', 'UTF-8'],
            ['¬øfòô bàř¬øÿ', 'fòô bàř', 12, '¬øÿ', 'both', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function padRightProvider(): array
    {
        return [
            ['foo bar  ', 'foo bar', 9],
            ['foo bar_*', 'foo bar', 9, '_*'],
            ['foo bar_*_', 'foo bar', 10, '_*'],
            ['fòô bàř  ', 'fòô bàř', 9, ' ', 'UTF-8'],
            ['fòô bàř¬ø', 'fòô bàř', 9, '¬ø', 'UTF-8'],
            ['fòô bàř¬ø¬', 'fòô bàř', 10, '¬ø', 'UTF-8'],
            ['fòô bàř¬ø¬ø', 'fòô bàř', 11, '¬ø', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function prependProvider(): array
    {
        return [
            ['foobar', 'bar', 'foo'],
            ['fòôbàř', 'bàř', 'fòô', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function regexReplaceProvider(): array
    {
        return [
            ['', '', '', ''],
            ['bar', 'foo', 'f[o]+', 'bar'],
            ['//bar//', '/foo/', '/f[o]+/', '//bar//', 'msr', '#'],
            ['o bar', 'foo bar', 'f(o)o', '\1'],
            ['bar', 'foo bar', 'f[O]+\s', '', 'i'],
            ['foo', 'bar', '[[:alpha:]]{3}', 'foo'],
            ['', '', '', '', 'msr', '/', 'UTF-8'],
            ['bàř', 'fòô ', 'f[òô]+\s', 'bàř', 'msr', '/', 'UTF-8'],
            ['fòô', 'fò', '(ò)', '\\1ô', 'msr', '/', 'UTF-8'],
            ['fòô', 'bàř', '[[:alpha:]]{3}', 'fòô', 'msr', '/', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function removeHtmlBreakProvider(): array
    {
        return [
            ['', ''],
            ['raboof <3', 'raboof <3', '<ä>'],
            ['řàbôòf <foo<lall>>>', 'řàbôòf<br/><foo<lall>>>', ' '],
            [
                'řàb <ô>òf\', ô<br><br/>foo <a href="#">lall</a>',
                'řàb <ô>òf\', ô<br/>foo <a href="#">lall</a>',
                '<br><br/>',
            ],
            ['<∂∆ onerror="alert(xss)">˚åß', '<∂∆ onerror="alert(xss)">' . "\n" . '˚åß'],
            ['\'œ … \'’)', '\'œ … \'’)'],
        ];
    }

    /**
     * @return array
     */
    public function removeHtmlProvider(): array
    {
        return [
            ['', ''],
            ['raboof ', 'raboof <3', '<3>'],
            ['řàbôòf>', 'řàbôòf<foo<lall>>>', '<lall><lall/>'],
            ['řàb òf\', ô<br/>foo lall', 'řàb <ô>òf\', ô<br/>foo <a href="#">lall</a>', '<br><br/>'],
            [' ˚åß', '<∂∆ onerror="alert(xss)"> ˚åß'],
            ['\'œ … \'’)', '\'œ … \'’)'],
        ];
    }

    /**
     * @return array
     */
    public function removeLeftProvider(): array
    {
        return [
            ['foo bar', 'foo bar', ''],
            ['oo bar', 'foo bar', 'f'],
            ['bar', 'foo bar', 'foo '],
            ['foo bar', 'foo bar', 'oo'],
            ['foo bar', 'foo bar', 'oo bar'],
            ['oo bar', 'foo bar', S::create('foo bar')->first(1), 'UTF-8'],
            ['oo bar', 'foo bar', S::create('foo bar')->at(0), 'UTF-8'],
            ['fòô bàř', 'fòô bàř', '', 'UTF-8'],
            ['òô bàř', 'fòô bàř', 'f', 'UTF-8'],
            ['bàř', 'fòô bàř', 'fòô ', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 'òô', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 'òô bàř', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function removeRightProvider(): array
    {
        return [
            ['foo bar', 'foo bar', ''],
            ['foo ba', 'foo bar', 'r'],
            ['foo', 'foo bar', ' bar'],
            ['foo bar', 'foo bar', 'ba'],
            ['foo bar', 'foo bar', 'foo ba'],
            ['foo ba', 'foo bar', S::create('foo bar')->last(1), 'UTF-8'],
            ['foo ba', 'foo bar', S::create('foo bar')->at(6), 'UTF-8'],
            ['fòô bàř', 'fòô bàř', '', 'UTF-8'],
            ['fòô bà', 'fòô bàř', 'ř', 'UTF-8'],
            ['fòô', 'fòô bàř', ' bàř', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 'bà', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', 'fòô bà', 'UTF-8'],
        ];
    }

    /**
     * @return array
     *
     * @noinspection CssUnknownTarget
     */
    public function removeXssProvider(): array
    {
        return [
            ['', ''],
            [
                'Hello, i try to  your site',
                'Hello, i try to <script>alert(\'Hack\');</script> your site',
            ],
            [
                '<IMG >',
                '<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>',
            ],
            ['<XSS >', '<XSS STYLE="behavior: url(xss.htc);">'],
            ['&lt;∂∆ &gt; ˚åß', '<∂∆ onerror="alert(xss)"> ˚åß'],
            ['\'œ … <a href="#foo"> \'’)', '\'œ … <a href="#foo"> \'’)'],
        ];
    }

    /**
     * @return array
     */
    public function equalsProvider(): array
    {
        return [
            [
                true,
                'fòô bàř',
            ],
            [
                false,
                'Fòô bàř',
            ],
            [
                false,
                0,
            ],
            [
                false,
                1,
            ],
            [
                false,
                1.1,
            ],
            [
                false,
                'null',
            ],
            [
                false,
                '',
            ],
        ];
    }

    /**
     * @return array
     */
    public function emptyProvider(): array
    {
        return [
            [
                true,
                '',
            ],
            [
                false,
                'Hello',
            ],
            [
                false,
                1,
            ],
            [
                false,
                1.1,
            ],
            [
                true,
                null,
            ],
        ];
    }

    /**
     * @return array
     */
    public function repeatProvider(): array
    {
        return [
            ['', 'foo', 0],
            ['foo', 'foo', 1],
            ['foofoo', 'foo', 2],
            ['foofoofoo', 'foo', 3],
            ['fòô', 'fòô', 1, 'UTF-8'],
            ['fòôfòô', 'fòô', 2, 'UTF-8'],
            ['fòôfòôfòô', 'fòô', 3, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function replaceAllProvider(): array
    {
        return [
            ['', '', [], ''],
            ['', '', [''], ''],
            ['foo', ' ', [' ', ''], 'foo'],
            ['foo', '\s', ['\s', '\t'], 'foo'],
            ['foo bar', 'foo bar', [''], ''],
            ['\1 bar', 'foo bar', ['f(o)o', 'foo'], '\1'],
            ['\1 \1', 'foo bar', ['foo', 'föö', 'bar'], '\1'],
            ['bar', 'foo bar', ['foo '], ''],
            ['far bar', 'foo bar', ['foo'], 'far'],
            ['bar bar', 'foo bar foo bar', ['foo ', ' foo'], ''],
            ['bar bar bar bar', 'foo bar foo bar', ['foo ', ' foo'], ['bar ', ' bar']],
            ['', '', [''], '', 'UTF-8'],
            ['fòô', ' ', [' ', '', '  '], 'fòô', 'UTF-8'],
            ['fòôòô', '\s', ['\s', 'f'], 'fòô', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', [''], '', 'UTF-8'],
            ['bàř', 'fòô bàř', ['fòô '], '', 'UTF-8'],
            ['far bàř', 'fòô bàř', ['fòô'], 'far', 'UTF-8'],
            ['bàř bàř', 'fòô bàř fòô bàř', ['fòô ', 'fòô'], '', 'UTF-8'],
            ['bàř bàř', 'fòô bàř fòô bàř', ['fòô '], ''],
            ['bàř bàř', 'fòô bàř fòô bàř', ['fòô '], ''],
            ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', ['Fòô '], ''],
            ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', ['fòÔ '], ''],
            ['fòô bàř bàř', 'fòô bàř [[fòô]] bàř', ['[[fòô]] ', '[]'], ''],
            ['', '', [''], '', 'UTF-8', false],
            ['fòô', ' ', [' ', '', '  '], 'fòô', 'UTF-8', false],
            ['fòôòô', '\s', ['\s', 'f'], 'fòô', 'UTF-8', false],
            ['fòô bàř', 'fòô bàř', [''], '', 'UTF-8', false],
            ['bàř', 'fòô bàř', ['fòÔ '], '', 'UTF-8', false],
            ['bàř', 'fòô bàř', ['fòÔ '], [''], 'UTF-8', false],
            ['far bàř', 'fòô bàř', ['Fòô'], 'far', 'UTF-8', false],
        ];
    }

    /**
     * @return array
     */
    public function replaceBeginningProvider(): array
    {
        return [
            ['', '', '', ''],
            ['foo', '', '', 'foo'],
            ['foo', '\s', '\s', 'foo'],
            ['foo bar', 'foo bar', '', ''],
            ['foo bar', 'foo bar', 'f(o)o', '\1'],
            ['\1 bar', 'foo bar', 'foo', '\1'],
            ['bar', 'foo bar', 'foo ', ''],
            ['far bar', 'foo bar', 'foo', 'far'],
            ['bar foo bar', 'foo bar foo bar', 'foo ', ''],
            ['', '', '', '', 'UTF-8'],
            ['fòô', '', '', 'fòô', 'UTF-8'],
            ['fòô', '\s', '\s', 'fòô', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'],
            ['bàř', 'fòô bàř', 'fòô ', '', 'UTF-8'],
            ['far bàř', 'fòô bàř', 'fòô', 'far', 'UTF-8'],
            ['bàř fòô bàř', 'fòô bàř fòô bàř', 'fòô ', '', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function replaceFirstProvider(): array
    {
        return [
            ['', '', '', ''],
            ['foofoofoo', 'foofoo', 'foo', 'foofoo'],
            ['foo', '\s', '\s', 'foo'],
            ['foo bar', 'foo bar', '', ''],
            ['foo bar', 'foo bar', 'f(o)o', '\1'],
            ['\1 bar', 'foo bar', 'foo', '\1'],
            ['bar', 'foo bar', 'foo ', ''],
            ['far bar', 'foo bar', 'foo', 'far'],
            ['bar foo bar', 'foo bar foo bar', 'foo ', ''],
            ['', '', '', '', 'UTF-8'],
            ['fòô', '\s', '\s', 'fòô', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'],
            ['bàř', 'fòô bàř', 'fòô ', '', 'UTF-8'],
            ['fòô bàř', 'fòô fòô bàř', 'fòô ', '', 'UTF-8'],
            ['far bàř', 'fòô bàř', 'fòô', 'far', 'UTF-8'],
            ['bàř fòô bàř', 'fòô bàř fòô bàř', 'fòô ', '', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function replaceLastProvider(): array
    {
        return [
            ['', '', '', ''],
            ['foofoofoo', 'foofoo', 'foo', 'foofoo'],
            ['foo', '\s', '\s', 'foo'],
            ['foo bar', 'foo bar', '', ''],
            ['foo bar', 'foo bar', 'f(o)o', '\1'],
            ['\1 bar', 'foo bar', 'foo', '\1'],
            ['bar', 'foo bar', 'foo ', ''],
            ['foo lall', 'foo bar', 'bar', 'lall'],
            ['foo bar foo ', 'foo bar foo bar', 'bar', ''],
            ['', '', '', '', 'UTF-8'],
            ['fòô', '\s', '\s', 'fòô', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'],
            ['fòô', 'fòô bàř', ' bàř', '', 'UTF-8'],
            ['fòôfar', 'fòô bàř', ' bàř', 'far', 'UTF-8'],
            ['fòô bàř fòô', 'fòô bàř fòô bàř', ' bàř', '', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function replaceEndingProvider(): array
    {
        return [
            ['', '', '', ''],
            ['foo', '', '', 'foo'],
            ['foo', '\s', '\s', 'foo'],
            ['foo bar', 'foo bar', '', ''],
            ['foo bar', 'foo bar', 'f(o)o', '\1'],
            ['foo bar', 'foo bar', 'foo', '\1'],
            ['foo bar', 'foo bar', 'foo ', ''],
            ['foo lall', 'foo bar', 'bar', 'lall'],
            ['foo bar foo ', 'foo bar foo bar', 'bar', ''],
            ['', '', '', '', 'UTF-8'],
            ['fòô', '', '', 'fòô', 'UTF-8'],
            ['fòô', '\s', '\s', 'fòô', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'],
            ['fòô', 'fòô bàř', ' bàř', '', 'UTF-8'],
            ['fòôfar', 'fòô bàř', ' bàř', 'far', 'UTF-8'],
            ['fòô bàř fòô', 'fòô bàř fòô bàř', ' bàř', '', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function replaceProvider(): array
    {
        return [
            ['', '', '', ''],
            ['foo', ' ', ' ', 'foo'],
            ['foo', '\s', '\s', 'foo'],
            ['foo bar', 'foo bar', '', ''],
            ['foo bar', 'foo bar', 'f(o)o', '\1'],
            ['\1 bar', 'foo bar', 'foo', '\1'],
            ['bar', 'foo bar', 'foo ', ''],
            ['far bar', 'foo bar', 'foo', 'far'],
            ['bar bar', 'foo bar foo bar', 'foo ', ''],
            ['', '', '', '', 'UTF-8'],
            ['fòô', ' ', ' ', 'fòô', 'UTF-8'],
            ['fòô', '\s', '\s', 'fòô', 'UTF-8'],
            ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'],
            ['bàř', 'fòô bàř', 'fòô ', '', 'UTF-8'],
            ['far bàř', 'fòô bàř', 'fòô', 'far', 'UTF-8'],
            ['bàř bàř', 'fòô bàř fòô bàř', 'fòô ', '', 'UTF-8'],
            ['bàř bàř', 'fòô bàř fòô bàř', 'fòô ', ''],
            ['bàř bàř', 'fòô bàř fòô bàř', 'fòô ', ''],
            ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', 'Fòô ', ''],
            ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', 'fòÔ ', ''],
            ['fòô bàř bàř', 'fòô bàř [[fòô]] bàř', '[[fòô]] ', ''],
            ['', '', '', '', 'UTF-8', false],
            ['òô', ' ', ' ', 'òô', 'UTF-8', false],
            ['fòô', '\s', '\s', 'fòô', 'UTF-8', false],
            ['fòô bàř', 'fòô bàř', '', '', 'UTF-8', false],
            ['bàř', 'fòô bàř', 'Fòô ', '', 'UTF-8', false],
            ['far bàř', 'fòô bàř', 'fòÔ', 'far', 'UTF-8', false],
            ['bàř bàř', 'fòô bàř fòô bàř', 'Fòô ', '', 'UTF-8', false],
        ];
    }

    /**
     * @return array
     */
    public function reverseProvider(): array
    {
        return [
            ['', ''],
            ['raboof', 'foobar'],
            ['řàbôòf', 'fòôbàř', 'UTF-8'],
            ['řàb ôòf', 'fòô bàř', 'UTF-8'],
            ['∂∆ ˚åß', 'ßå˚ ∆∂', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function safeTruncateProvider(): array
    {
        return [
            ['Test foo bar', 'Test foo bar', 12],
            ['Test foo', 'Test foo bar', 11],
            ['Test foo', 'Test foo bar', 8],
            ['Test', 'Test foo bar', 7],
            ['Test', 'Test foo bar', 4],
            ['Test', 'Testfoobar', 4],
            ['Test foo bar', 'Test foo bar', 12, '...'],
            ['Test foo...', 'Test foo bar', 11, '...'],
            ['Test...', 'Test foo bar', 8, '...'],
            ['Test...', 'Test foo bar', 7, '...'],
            ['...', 'Test foo bar', 4, '...'],
            ['Test....', 'Test foo bar', 11, '....'],
            ['Test fòô bàř', 'Test fòô bàř', 12, '', 'UTF-8'],
            ['Test fòô', 'Test fòô bàř', 11, '', 'UTF-8'],
            ['Test fòô', 'Test fòô bàř', 8, '', 'UTF-8'],
            ['Test', 'Test fòô bàř', 7, '', 'UTF-8'],
            ['Test', 'Test fòô bàř', 4, '', 'UTF-8'],
            ['Test fòô bàř', 'Test fòô bàř', 12, 'ϰϰ', 'UTF-8'],
            ['Test fòôϰϰ', 'Test fòô bàř', 11, 'ϰϰ', 'UTF-8'],
            ['Testϰϰ', 'Test fòô bàř', 8, 'ϰϰ', 'UTF-8'],
            ['Testϰϰ', 'Test fòô bàř', 7, 'ϰϰ', 'UTF-8'],
            ['ϰϰ', 'Test fòô bàř', 4, 'ϰϰ', 'UTF-8'],
            ['What are your plans...', 'What are your plans today?', 22, '...'],
        ];
    }

    /**
     * @return array
     */
    public function shortenAfterWordProvider(): array
    {
        return [
            ['this...', 'this is a test', 5, '...'],
            ['this is...', 'this is öäü-foo test', 8, '...'],
            ['fòô', 'fòô bàř fòô', 6, ''],
            ['fòô bàř', 'fòô bàř fòô', 8, ''],
        ];
    }

    /**
     * @return array
     */
    public function shuffleProvider(): array
    {
        return [
            ['foo bar'],
            ['∂∆ ˚åß', 'UTF-8'],
            ['å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function sliceProvider(): array
    {
        return [
            ['foobar', 'foobar', 0],
            ['foobar', 'foobar', 0, null],
            ['foobar', 'foobar', 0, 6],
            ['fooba', 'foobar', 0, 5],
            ['', 'foobar', 3, 0],
            ['', 'foobar', 3, 2],
            ['ba', 'foobar', 3, 5],
            ['ba', 'foobar', 3, -1],
            ['fòôbàř', 'fòôbàř', 0, null, 'UTF-8'],
            ['fòôbàř', 'fòôbàř', 0, null],
            ['fòôbàř', 'fòôbàř', 0, 6, 'UTF-8'],
            ['fòôbà', 'fòôbàř', 0, 5, 'UTF-8'],
            ['', 'fòôbàř', 3, 0, 'UTF-8'],
            ['', 'fòôbàř', 3, 2, 'UTF-8'],
            ['bà', 'fòôbàř', 3, 5, 'UTF-8'],
            ['bà', 'fòôbàř', 3, -1, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function slugifyProvider(): array
    {
        return [
            ['bar', 'foooooo'],
            ['foo-bar', ' foo  bar '],
            ['foo-bar', 'foo -.-"-...bar'],
            ['another-and-foo-bar', 'another..& foo -.-"-...bar'],
            ['foo-dbar', " Foo d'Bar "],
            ['a-string-with-dashes', 'A string-with-dashes'],
            ['using-strings-like-foo-bar', 'Using strings like fòô bàř'],
            ['numbers-1234', 'numbers 1234'],
            ['perevirka-riadka', 'перевірка рядка'],
            ['bukvar-s-bukvoi-y', 'букварь с буквой ы'],
            ['podieexal-k-podieezdu-moego-doma', 'подъехал к подъезду моего дома'],
            ['foo:bar:baz', 'Foo bar baz', ':'],
            ['a_string_with_underscores', 'A_string with_underscores', '_'],
            ['a_string_with_dashes', 'A string-with-dashes', '_'],
            ['a\string\with\dashes', 'A string-with-dashes', '\\'],
            ['an_odd_string', '--   An odd__   string-_', '_'],
        ];
    }

    /**
     * @return array
     */
    public function snakeizeProvider(): array
    {
        return [
            ['snake_case', 'SnakeCase'],
            ['snake_case', 'Snake-Case'],
            ['snake_case', 'snake case'],
            ['snake_case', 'snake -case'],
            ['snake_case', 'snake - case'],
            ['snake_case', 'snake_case'],
            ['camel_c_test', 'camel c test'],
            ['string_with_1_number', 'string_with 1 number'],
            ['string_with_1_number', 'string_with1number'],
            ['string_with_2_2_numbers', 'string-with-2-2 numbers'],
            ['data_rate', 'data_rate'],
            ['background_color', 'background-color'],
            ['yes_we_can', 'yes_we_can'],
            ['moz_something', '-moz-something'],
            ['car_speed', '_car_speed_'],
            ['serve_h_t_t_p', 'ServeHTTP'],
            ['1_camel_2_case', '1camel2case'],
            ['camel_σase', 'camel σase', 'UTF-8'],
            ['στανιλ_case', 'Στανιλ case', 'UTF-8'],
            ['σamel_case', 'σamel  Case', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function splitProvider(): array
    {
        return [
            [['foo,bar,baz'], 'foo,bar,baz', ''],
            [['foo,bar,baz'], 'foo,bar,baz', '-'],
            [['foo', 'bar', 'baz'], 'foo,bar,baz', ','],
            [['foo', 'bar', 'baz'], 'foo,bar,baz', ',', -1],
            [[], 'foo,bar,baz', ',', 0],
            [['foo'], 'foo,bar,baz', ',', 1],
            [['foo', 'bar'], 'foo,bar,baz', ',', 2],
            [['foo', 'bar', 'baz'], 'foo,bar,baz', ',', 3],
            [['foo', 'bar', 'baz'], 'foo,bar,baz', ',', 10],
            [['fòô,bàř,baz'], 'fòô,bàř,baz', '-', -1, 'UTF-8'],
            [['fòô', 'bàř', 'baz'], 'fòô,bàř,baz', ',', -1, 'UTF-8'],
            [[], 'fòô,bàř,baz', ',', 0, 'UTF-8'],
            [['fòô'], 'fòô,bàř,baz', ',', 1, 'UTF-8'],
            [['fòô', 'bàř'], 'fòô,bàř,baz', ',', 2, 'UTF-8'],
            [['fòô', 'bàř', 'baz'], 'fòô,bàř,baz', ',', 3, 'UTF-8'],
            [['fòô', 'bàř', 'baz'], 'fòô,bàř,baz', ',', 10, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function chunkProvider(): array
    {
        return [
            [
                [
                    0 => 'f',
                    1 => 'ò',
                    2 => 'ô',
                    3 => ',',
                    4 => 'b',
                    5 => 'à',
                    6 => 'ř',
                ],
                'fòô,bàř',
                1,
            ],
            [
                [
                    0 => 'fò',
                    1 => 'ô,',
                    2 => 'bà',
                    3 => 'ř',
                ],
                'fòô,bàř',
                2,
            ],
            [
                [],
                '',
                2,
            ],
            [
                [
                    0 => 'fòô',
                    1 => ',bà',
                    2 => 'ř',
                ],
                'fòô,bàř',
                3,
            ],
            [['fòô,bàř'], 'fòô,bàř', 10],
        ];
    }

    /**
     * @return array
     */
    public function startsWithProvider(): array
    {
        return [
            [true, 'foo bars', 'foo bar'],
            [true, 'FOO bars', 'foo bar', false],
            [true, 'FOO bars', 'foo BAR', false],
            [true, 'FÒÔ bàřs', 'fòô bàř', false, 'UTF-8'],
            [true, 'fòô bàřs', 'fòô BÀŘ', false, 'UTF-8'],
            [false, 'foo bar', 'bar'],
            [false, 'foo bar', 'foo bars'],
            [false, 'FOO bar', 'foo bars'],
            [false, 'FOO bars', 'foo BAR'],
            [false, 'FÒÔ bàřs', 'fòô bàř', true, 'UTF-8'],
            [false, 'fòô bàřs', 'fòô BÀŘ', true, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function startsWithProviderAny(): array
    {
        return [
            [true, 'foo bars', ['foo bar']],
            [true, 'foo bars', ['foo', 'bar']],
            [true, 'FOO bars', ['foo', 'bar'], false],
            [true, 'FOO bars', ['foo', 'BAR'], false],
            [true, 'FÒÔ bàřs', ['fòô', 'bàř'], false, 'UTF-8'],
            [true, 'fòô bàřs', ['fòô BÀŘ'], false, 'UTF-8'],
            [false, 'foo bar', ['bar']],
            [false, 'foo bar', ['foo bars']],
            [false, 'FOO bar', ['foo bars']],
            [false, 'FOO bars', ['foo BAR']],
            [false, 'FÒÔ bàřs', ['fòô bàř'], true, 'UTF-8'],
            [false, 'fòô bàřs', ['fòô BÀŘ'], true, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function stripWhitespaceProvider(): array
    {
        return [
            ['foobar', '  foo   bar  '],
            ['teststring', 'test string'],
            ['Οσυγγραφέας', '   Ο     συγγραφέας  '],
            ['123', ' 123 '],
            ['', ' ', 'UTF-8'], // no-break space (U+00A0)
            ['', '           ', 'UTF-8'], // spaces U+2000 to U+200A
            ['', ' ', 'UTF-8'], // narrow no-break space (U+202F)
            ['', ' ', 'UTF-8'], // medium mathematical space (U+205F)
            ['', '　', 'UTF-8'], // ideographic space (U+3000)
            ['123', '  1  2  3　　', 'UTF-8'],
            ['', ' '],
            ['', ''],
        ];
    }

    /**
     * @return array
     */
    public function substrProvider(): array
    {
        return [
            ['foo bar', 'foo bar', 0],
            ['bar', 'foo bar', 4],
            ['bar', 'foo bar', 4, null],
            ['o b', 'foo bar', 2, 3],
            ['', 'foo bar', 4, 0],
            ['fòô bàř', 'fòô bàř', 0, null, 'UTF-8'],
            ['bàř', 'fòô bàř', 4, null, 'UTF-8'],
            ['ô b', 'fòô bàř', 2, 3, 'UTF-8'],
            ['', 'fòô bàř', 4, 0, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function surroundProvider(): array
    {
        return [
            ['__foobar__', 'foobar', '__'],
            ['test', 'test', ''],
            ['**', '', '*'],
            ['¬fòô bàř¬', 'fòô bàř', '¬'],
            ['ßå∆˚ test ßå∆˚', ' test ', 'ßå∆˚'],
        ];
    }

    /**
     * @return array
     */
    public function swapCaseProvider(): array
    {
        return [
            ['TESTcASE', 'testCase'],
            ['tEST-cASE', 'Test-Case'],
            [' - σASH  cASE', ' - Σash  Case', 'UTF-8'],
            ['νΤΑΝΙΛ', 'Ντανιλ', 'UTF-8'],
        ];
    }

    public function testAddPassword()
    {
        // init
        $disallowedChars = 'ф0Oo1l';
        $allowedChars = '2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ!?_#';

        $passwords = [];
        for ($i = 0; $i <= 100; ++$i) {
            $stringy = S::create('');
            $passwords[] = $stringy->appendPassword(16);
        }

        // check for allowed chars
        $errors = [];
        foreach ($passwords as $password) {
            foreach (\str_split($password) as $char) {
                if (\strpos($allowedChars, $char) === false) {
                    $errors[] = $char;
                }
            }
        }
        static::assertCount(0, $errors);

        // check for disallowed chars
        $errors = [];
        foreach ($passwords as $password) {
            foreach (UTF8::str_split((string) $password) as $char) {
                if (\strpos($disallowedChars, $char) !== false) {
                    $errors[] = $char;
                }
            }
        }
        static::assertCount(0, $errors);

        // check the string length
        foreach ($passwords as $password) {
            static::assertSame(16, \strlen($password));
        }
    }

    public function testAddRandomString()
    {
        $testArray = [
            'abc'       => [1, 1],
            'öäü'       => [10, 10],
            ''          => [10, 0],
            ' '         => [10, 10],
            'κόσμε-öäü' => [10, 10],
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create('');
            $stringy = $stringy->appendRandomString($testResult[0], $testString);

            static::assertSame($testResult[1], $stringy->length(), 'tested: ' . $testString . ' | ' . $stringy->toString());
        }
    }

    public function testAddUniqueIdentifier()
    {
        $uniquIDs = [];
        for ($i = 0; $i <= 100; ++$i) {
            $stringy = S::create('');
            $uniquIDs[] = (string) $stringy->appendUniqueIdentifier();
        }

        // detect duplicate values in the array
        foreach (\array_count_values($uniquIDs) as $uniquID => $count) {
            static::assertSame(1, $count);
        }

        // check the string length
        foreach ($uniquIDs as $uniquID) {
            static::assertSame(32, \strlen($uniquID));
        }
    }

    public function testAfterFirst()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'ar',
            '<h1></h1> '               => '',
            '</b></b>'                 => '></b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => '<b></b>',
            '<b><b>lall</b>'           => '><b>lall</b>',
            '</b>lall</b>'             => '>lall</b>',
            '[B][/B]'                  => '',
            '[b][/b]'                  => '][/b]',
            'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
            'bκόσμbε'                  => 'κόσμbε',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->afterFirst('b')->toString());
        }
    }

    public function testAfterFirstIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>Bar'          => 'ar',
            'foo<h1></h1>bar'          => 'ar',
            '<h1></h1> '               => '',
            '</b></b>'                 => '></b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => '<b></b>',
            '<b><b>lall</b>'           => '><b>lall</b>',
            '</b>lall</b>'             => '>lall</b>',
            '[B][/B]'                  => '][/B]',
            'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
            'bκόσμbε'                  => 'κόσμbε',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->afterFirstIgnoreCase('b')->toString());
        }
    }

    public function testAfterLasIgnoreCaset()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'ar',
            'foo<h1></h1>Bar'          => 'ar',
            '<h1></h1> '               => '',
            '</b></b>'                 => '>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => '>',
            '<b><b>lall</b>'           => '>',
            '</b>lall</b>'             => '>',
            '[B][/B]'                  => ']',
            '[b][/b]'                  => ']',
            'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->afterLastIgnoreCase('b')->toString());
        }
    }

    public function testAfterLast()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'ar',
            '<h1></h1> '               => '',
            '</b></b>'                 => '>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => '>',
            '<b><b>lall</b>'           => '>',
            '</b>lall</b>'             => '>',
            '[b][/b]'                  => ']',
            '[B][/B]'                  => '',
            'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->afterLast('b')->toString());
        }
    }

    /**
     * @dataProvider appendProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $string
     * @param null $encoding
     */
    public function testAppend($expected, $str, $string, $encoding = null)
    {
        $result = S::create($str, $encoding)->append($string);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
    }

    public function testAppendv2()
    {
        $result = S::create('abc')->append('123', 'öäü', 'def');
        static::assertSame('abc123öäüdef', $result->toString());
    }

    public function testAppendStringy()
    {
        $result = S::create('abc')->appendStringy(S::create('zzz'), \Stringy\CollectionStringy::createFromStrings(['123', 'öäü', 'def']));
        static::assertSame('abczzz123öäüdef', $result->toString());
    }

    /**
     * @dataProvider atProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $index
     * @param null $encoding
     */
    public function testAt($expected, $str, $index, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->at($index);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testBeforeFirst()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'foo<h1></h1>',
            '<h1></h1> '               => '',
            '</b></b>'                 => '</',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => ' ',
            '<b><b>lall</b>'           => '<',
            '</b>lall</b>'             => '</',
            '[b][/b]'                  => '[',
            '[B][/B]'                  => '',
            'κόσμbε ¡-öäü'             => 'κόσμ',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->beforeFirst('b')->toString());
            static::assertSame($testResult, $stringy->substringOf('b', true)->toString());
        }
    }

    public function testBeforeFirstIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>Bar'          => 'foo<h1></h1>',
            'foo<h1></h1>bar'          => 'foo<h1></h1>',
            '<h1></h1> '               => '',
            '</b></b>'                 => '</',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => ' ',
            '<b><b>lall</b>'           => '<',
            '</b>lall</b>'             => '</',
            '[B][/B]'                  => '[',
            'κόσμbε ¡-öäü'             => 'κόσμ',
            'Bκόσμbε'                  => '',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->beforeFirstIgnoreCase('b')->toString());
            static::assertSame($testResult, $stringy->substringOfIgnoreCase('b', true)->toString());
        }
    }

    public function testBeforeLast()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'foo<h1></h1>',
            '<h1></h1> '               => '',
            '</b></b>'                 => '</b></',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => ' b<b></',
            '<b><b>lall</b>'           => '<b><b>lall</',
            '</b>lall</b>'             => '</b>lall</',
            '[b][/b]'                  => '[b][/',
            '[B][/B]'                  => '',
            'κόσμbε ¡-öäü'             => 'κόσμ',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->beforeLast('b')->toString());
            static::assertSame($testResult, $stringy->lastSubstringOf('b', true)->toString());
        }
    }

    public function testBeforeLastIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>Bar'          => 'foo<h1></h1>',
            'foo<h1></h1>bar'          => 'foo<h1></h1>',
            '<h1></h1> '               => '',
            '</b></b>'                 => '</b></',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => ' b<b></',
            '<b><b>lall</b>'           => '<b><b>lall</',
            '</b>lall</b>'             => '</b>lall</',
            '[B][/B]'                  => '[B][/',
            'κόσμbε ¡-öäü'             => 'κόσμ',
            'bκόσμbε'                  => 'bκόσμ',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->beforeLastIgnoreCase('b')->toString());
        }
    }

    /**
     * @dataProvider betweenProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $start
     * @param      $end
     * @param null $offset
     * @param null $encoding
     */
    public function testBetween($expected, $str, $start, $end, $offset = 0, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->between($start, $end, $offset);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider camelizeProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testCamelize($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->camelize();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider capitalizePersonalNameProvider()
     *
     * @param string      $expected
     * @param string      $str
     * @param string|null $encoding
     */
    public function testCapitalizePersonalName($expected, $str, $encoding = null)
    {
        /** @var S $stringy */
        $stringy = S::create($str, $encoding);
        $result = $stringy->capitalizePersonalName();
        static::assertEquals($expected, $result);
        static::assertEquals($str, $stringy);
    }

    public function testChaining()
    {
        $stringy = S::create('Fòô     Bàř', 'UTF-8');
        $this->assertStringy($stringy);
        $result = $stringy->collapseWhitespace()->swapCase()->upperCaseFirst();
        static::assertSame('FÒÔ bÀŘ', $result->toString());
    }

    /**
     * @dataProvider charsProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testChars($expected, $str, $encoding = null)
    {
        $result = S::create($str, $encoding)->chars();
        static::assertTrue(\is_array($result));
        foreach ($result as $char) {
            static::assertTrue(\is_string($char));
        }
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider collapseWhitespaceProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testCollapseWhitespace($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->collapseWhitespace();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testConstruct()
    {
        $stringy = new S('foo bar', 'UTF-8');
        $this->assertStringy($stringy);
        static::assertSame('foo bar', (string) $stringy);
        static::assertSame('UTF-8', $stringy->getEncoding());
    }

    public function testConstructWithArray()
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @noinspection PhpExpressionResultUnusedInspection */
        (string) new S([]);
        static::fail('Expecting exception when the constructor is passed an array');
    }

    /**
     * @dataProvider containsProvider()
     *
     * @param      $expected
     * @param      $haystack
     * @param      $needle
     * @param bool $caseSensitive
     * @param null $encoding
     */
    public function testContains($expected, $haystack, $needle, $caseSensitive = true, $encoding = null)
    {
        $stringy = S::create($haystack, $encoding);
        $result = $stringy->contains($needle, $caseSensitive);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($haystack, $stringy->toString());
    }

    /**
     * @dataProvider containsAllProvider()
     *
     * @param bool     $expected
     * @param string   $haystack
     * @param string[] $needles
     * @param bool     $caseSensitive
     * @param string   $encoding
     */
    public function testContainsAll($expected, $haystack, $needles, $caseSensitive = true, $encoding = null)
    {
        $stringy = S::create($haystack, $encoding);
        $result = $stringy->containsAll($needles, $caseSensitive);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result, 'tested: ' . $haystack);
        static::assertSame($haystack, $stringy->toString());
    }

    public function testCount()
    {
        $stringy = S::create('Fòô', 'UTF-8');
        static::assertSame(3, $stringy->count());
        static::assertCount(3, $stringy);
    }

    /**
     * @dataProvider countSubstrProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param bool $caseSensitive
     * @param null $encoding
     */
    public function testCountSubstr($expected, $str, $substring, $caseSensitive = true, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->countSubstr($substring, $caseSensitive);
        static::assertSame($expected, $result, 'tested:' . $str);
        static::assertSame($str, $stringy->toString(), 'tested:' . $str);
    }

    public function testCreate()
    {
        $stringy = S::create('foo bar', 'UTF-8');
        $this->assertStringy($stringy);
        static::assertSame('foo bar', (string) $stringy);
        static::assertSame('UTF-8', $stringy->getEncoding());
    }

    /**
     * @dataProvider dasherizeProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testDasherize($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->dasherize();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider delimitProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $delimiter
     * @param null $encoding
     */
    public function testDelimit($expected, $str, $delimiter, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->delimit($delimiter);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testEmptyConstruct()
    {
        $stringy = new S();
        $this->assertStringy($stringy);
        static::assertSame('', (string) $stringy);
    }

    /**
     * @dataProvider endsWithProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param bool $caseSensitive
     * @param null $encoding
     */
    public function testEndsWith($expected, $str, $substring, $caseSensitive = true, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->endsWith($substring, $caseSensitive);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider endsWithAnyProvider()
     *
     * @param string $expected
     * @param string $str
     * @param array  $substrings
     * @param bool   $caseSensitive
     * @param null   $encoding
     */
    public function testEndsWithAny($expected, $str, $substrings, $caseSensitive = true, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->endsWithAny($substrings, $caseSensitive);
        static::assertTrue(\is_bool($result));
        static::assertEquals($expected, $result);
        static::assertEquals($str, $stringy);
    }

    /**
     * @dataProvider ensureLeftProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param null $encoding
     */
    public function testEnsureLeft($expected, $str, $substring, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->ensureLeft($substring);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider ensureRightProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param null $encoding
     */
    public function testEnsureRight($expected, $str, $substring, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->ensureRight($substring);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider escapeProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testEscape($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->escape();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testExtractText()
    {
        $testArray = [
            ''                                                                                                                                          => '',
            '<h1>test</h1>'                                                                                                                             => '<h1>test</h1>',
            'test'                                                                                                                                      => 'test',
            'A PHP string manipulation library with multibyte support. Compatible with PHP 5.3+, PHP 7, and HHVM.'                                      => 'A PHP string manipulation library with multibyte support…',
            'A PHP string manipulation library with multibyte support. κόσμε-öäü κόσμε-öäü κόσμε-öäü foobar Compatible with PHP 5.3+, PHP 7, and HHVM.' => '…support. κόσμε-öäü κόσμε-öäü κόσμε-öäü foobar Compatible with PHP 5…',
            'A PHP string manipulation library with multibyte support. foobar Compatible with PHP 5.3+, PHP 7, and HHVM.'                               => '…with multibyte support. foobar Compatible with PHP 5…',
        ];

        foreach ($testArray as $testString => $testExpected) {
            $stringy = S::create($testString);
            static::assertSame($testExpected, (string) $stringy->extractText('foobar'), 'tested: ' . $testString);
        }

        // ----------------

        $testString = 'this is only a Fork of Stringy';
        $stringy = S::create($testString);
        static::assertSame('…a Fork of Stringy', (string) $stringy->extractText('Fork', 5), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of Stringy, take a look at the new features.';
        $stringy = S::create($testString);
        static::assertSame('…Fork of Stringy…', (string) $stringy->extractText('Stringy', 15), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of Stringy, take a look at the new features.';
        $stringy = S::create($testString);
        static::assertSame('…only a Fork of Stringy, take a…', (string) $stringy->extractText('Stringy'), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of Stringy, take a look at the new features.';
        $stringy = S::create($testString);
        static::assertSame('This is only a Fork of Stringy…', (string) $stringy->extractText(), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of Stringy, take a look at the new features.';
        $stringy = S::create($testString);
        static::assertSame('This…', (string) $stringy->extractText('', 0), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of Stringy, take a look at the new features.';
        $stringy = S::create($testString);
        static::assertSame('…Stringy, take a look at the new features.', (string) $stringy->extractText('Stringy', 0), 'tested: ' . $testString);

        // ----------------

        $testArray = [
            'Yes. The bird is flying in the wind. The fox is jumping in the garden when he is happy. But that is not the whole story.' => '…The fox is jumping in the <strong>garden</strong> when he is happy. But that…',
            'The bird is flying in the wind. The fox is jumping in the garden when he is happy. But that is not the whole story.'      => '…The fox is jumping in the <strong>garden</strong> when he is happy. But that…',
            'The fox is jumping in the garden when he is happy. But that is not the whole story.'                                      => '…is jumping in the <strong>garden</strong> when he is happy…',
            'Yes. The fox is jumping in the garden when he is happy. But that is not the whole story.'                                 => '…fox is jumping in the <strong>garden</strong> when he is happy…',
            'Yes. The fox is jumping in the garden when he is happy. But that is not the whole story of the garden story.'             => '…The fox is jumping in the <strong>garden</strong> when he is happy. But…',
        ];

        $searchString = 'garden';
        foreach ($testArray as $testString => $testExpected) {
            $stringy = S::create($testString);
            $result = $stringy->extractText($searchString)
                ->replace($searchString, '<strong>' . $searchString . '</strong>')
                ->toString();
            static::assertSame($testExpected, $result, 'tested: ' . $testString);
        }

        // ----------------

        $testArray = [
            'Yes. The bird is flying in the wind. The fox is jumping in the garden when he is happy. But that is not the whole story.' => '…flying in the wind. <strong>The fox is jumping in the garden</strong> when he…',
            'The bird is flying in the wind. The fox is jumping in the garden when he is happy. But that is not the whole story.'      => '…in the wind. <strong>The fox is jumping in the garden</strong> when he is…',
            'The fox is jumping in the garden when he is happy. But that is not the whole story.'                                      => '<strong>The fox is jumping in the garden</strong> when he is…',
            'Yes. The fox is jumping in the garden when he is happy. But that is not the whole story.'                                 => 'Yes. <strong>The fox is jumping in the garden</strong> when he…',
            'Yes. The fox is jumping in the garden when he is happy. But that is not the whole story of the garden story.'             => 'Yes. <strong>The fox is jumping in the garden</strong> when he is happy…',
        ];

        $searchString = 'The fox is jumping in the garden';
        foreach ($testArray as $testString => $testExpected) {
            $stringy = S::create($testString);
            $result = $stringy->extractText($searchString)
                ->replace($searchString, '<strong>' . $searchString . '</strong>')
                ->toString();
            static::assertSame($testExpected, $result, 'tested: ' . $testString);
        }
    }

    /**
     * @dataProvider firstProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $n
     * @param null $encoding
     */
    public function testFirst($expected, $str, $n, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->first($n);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testGetIterator()
    {
        $stringy = S::create('Fòô Bàř', 'UTF-8');

        $valResult = [];
        foreach ($stringy as $char) {
            $valResult[] = $char;
        }

        $keyValResult = [];
        foreach ($stringy as $pos => $char) {
            $keyValResult[$pos] = $char;
        }

        static::assertSame(['F', 'ò', 'ô', ' ', 'B', 'à', 'ř'], $valResult);
        static::assertSame(['F', 'ò', 'ô', ' ', 'B', 'à', 'ř'], $keyValResult);
    }

    /**
     * @dataProvider hasLowerCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testHasLowerCase($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->hasLowerCase();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider hasUpperCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testHasUpperCase($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->hasUpperCase();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider htmlDecodeProvider()
     *
     * @param      $expected
     * @param      $str
     * @param int  $flags
     * @param null $encoding
     */
    public function testHtmlDecode($expected, $str, $flags = \ENT_COMPAT, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->htmlDecode($flags);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider htmlEncodeProvider()
     *
     * @param      $expected
     * @param      $str
     * @param int  $flags
     * @param null $encoding
     */
    public function testHtmlEncode($expected, $str, $flags = \ENT_COMPAT, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->htmlEncode($flags);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider humanizeProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testHumanize($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->humanize();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider indexOfProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $subStr
     * @param int  $offset
     * @param null $encoding
     */
    public function testIndexOf($expected, $str, $subStr, $offset = 0, $encoding = null)
    {
        $result = S::create($str, $encoding)->indexOf($subStr, $offset);
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider indexOfIgnoreCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $subStr
     * @param int  $offset
     * @param null $encoding
     */
    public function testIndexOfIgnoreCase($expected, $str, $subStr, $offset = 0, $encoding = null)
    {
        $result = S::create($str, $encoding)->indexOfIgnoreCase($subStr, $offset);
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider indexOfLastProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $subStr
     * @param int  $offset
     * @param null $encoding
     */
    public function testIndexOfLast($expected, $str, $subStr, $offset = 0, $encoding = null)
    {
        $result = S::create($str, $encoding)->indexOfLast($subStr, $offset);
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider indexOfLastIgnoreCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $subStr
     * @param int  $offset
     * @param null $encoding
     */
    public function testIndexOfLastIgnoreCase($expected, $str, $subStr, $offset = 0, $encoding = null)
    {
        $result = S::create($str, $encoding)->indexOfLastIgnoreCase($subStr, $offset);
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider insertProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param      $index
     * @param null $encoding
     */
    public function testInsert($expected, $str, $substring, $index, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->insert($substring, $index);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider isProvider()
     *
     * @param      $expected
     * @param      $string
     * @param      $pattern
     * @param null $encoding
     */
    public function testIs($expected, $string, $pattern, $encoding = null)
    {
        $str = S::create($string, $encoding);
        $result = $str->is($pattern);
        static::assertTrue(\is_bool($result));
        static::assertEquals($expected, $result, 'tested: ' . $pattern);
        static::assertEquals($string, $str);
    }

    /**
     * @dataProvider isAlphaProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testIsAlpha($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->isAlpha();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider isAlphanumericProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testIsAlphanumeric($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->isAlphanumeric();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider isBase64Provider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsBase64($expected, $str)
    {
        $stringy = S::create($str);
        $result = $stringy->isBase64(false);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    public function testIsSimilarity()
    {
        $stringy = S::create('κόσμbε-abcdefgh-öäü');

        static::assertTrue($stringy->isSimilar('κσόμbε-abcedfgh-öäü'));
        static::assertTrue($stringy->isSimilar('abcdefgh-öäü', 50));

        static::assertFalse($stringy->isSimilar('κσόμbε-abcedfgh-öäü', 99.9));
        static::assertFalse($stringy->isSimilar('abcdefgh-öäü', 80));
    }

    public function testIsBinary()
    {
        $stringy = S::create(0x00);
        $result = $stringy->isBinary();
        static::assertTrue(\is_bool($result));
        static::assertTrue($result);
        static::assertSame('0', $stringy->toString());

        // --

        $stringy = S::create('a');
        $result = $stringy->isBinary();
        static::assertTrue(\is_bool($result));
        static::assertFalse($result);
        static::assertSame('a', $stringy->toString());
    }

    /**
     * @dataProvider isBlankProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testIsBlank($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->isBlank();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());

        // ---

        $stringy = S::create($str, $encoding);
        $result = $stringy->isWhitespace();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    public function testIsEmail()
    {
        $testArray = [
            ''             => false,
            'foo@bar'      => false,
            'foo@bar.foo'  => true,
            'foo@bar.foo ' => false,
            ' foo@bar.foo' => false,
            'lall'         => false,
            'κόσμbε@¡-öäü' => false,
            'lall.de'      => false,
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->isEmail());
        }

        // --- example domain check

        $stringy = S::create('test@test.com');
        static::assertTrue($stringy->isEmail());

        $stringy = S::create('test@test.com');
        static::assertFalse($stringy->isEmail(true));

        // --- tpyp domain check

        $stringy = S::create('test@aecor.de');
        static::assertTrue($stringy->isEmail());

        $stringy = S::create('test@aecor.de');
        static::assertFalse($stringy->isEmail(false, true));
    }

    /**
     * @dataProvider isHexadecimalProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testIsHexadecimal($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->isHexadecimal();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    public function testIsHtml()
    {
        $testArray = [
            ''                         => false,
            '<h1>test</h1>'            => true,
            'test'                     => false,
            '<b>lall</b>'              => true,
            'öäü<strong>lall</strong>' => true,
            ' <b>lall</b>'             => true,
            '<b><b>lall</b>'           => true,
            '</b>lall</b>'             => true,
            '[b]lall[b]'               => false,
            ' <test>κόσμε</test> '     => true,
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->isHtml(), 'tested: ' . $testString);
        }
    }

    /**
     * @dataProvider isJsonProvider()
     *
     * @param bool   $expected
     * @param string $str
     * @param mixed  $encoding
     */
    public function testIsJson($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->isJson(true);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result, 'tested: ' . $str);
        static::assertSame($str, $stringy->toString());
    }

    public function testJsonEncode()
    {
        $stringy = S::create('lall 123 \' - öäü');

        $json = \json_encode($stringy);
        static::assertSame('"lall 123 \' - \u00f6\u00e4\u00fc"', $json);

        $stringyFoo = \json_decode($json);
        static::assertSame('lall 123 \' - öäü', $stringyFoo);
    }

    /**
     * @dataProvider isLowerCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testIsLowerCase($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->isLowerCase();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider isSerializedProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testIsSerialized($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->isSerialized();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider isUpperCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testIsUpperCase($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->isUpperCase();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider lastProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $n
     * @param null $encoding
     */
    public function testLast($expected, $str, $n, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->last($n);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testLastSubstringOf()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'bar',
            '<h1></h1> '               => '',
            '</b></b>'                 => 'b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => 'b>',
            '<b><b>lall</b>'           => 'b>',
            '</b>lall</b>'             => 'b>',
            '[b][/b]'                  => 'b]',
            '[B][/B]'                  => '',
            'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->lastSubstringOf('b', false)->toString());
        }
    }

    public function testLastSubstringOfIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'bar',
            'foo<h1></h1>Bar'          => 'Bar',
            '<h1></h1> '               => '',
            '</b></b>'                 => 'b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => 'b>',
            '<b><b>lall</b>'           => 'b>',
            '</b>lall</b>'             => 'b>',
            '[B][/B]'                  => 'B]',
            '[b][/b]'                  => 'b]',
            'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->lastSubstringOfIgnoreCase('b', false)->toString());
        }
    }

    /**
     * @dataProvider lengthProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testLength($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->length();
        static::assertTrue(\is_int($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider linesProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testLines($expected, $str, $encoding = null)
    {
        $result = S::create($str, $encoding)->lines(true);

        foreach ($result as $line) {
            $this->assertStringy($line);
        }

        $counter = \count($expected);
        for ($i = 0; $i < $counter; ++$i) {
            static::assertSame($expected[$i], $result[$i]->toString());
        }
    }

    public function testLinewrap()
    {
        $testArray = [
            ''                      => "\n",
            ' '                     => ' ' . "\n",
            'http:// moelleken.org' => 'http://' . "\n" . 'moelleken.' . "\n" . 'org' . "\n",
            'http://test.de'        => 'http://tes' . "\n" . 't.de' . "\n",
            'http://öäü.de'         => 'http://öäü' . "\n" . '.de' . "\n",
            'http://menadwork.com'  => 'http://men' . "\n" . 'adwork.com' . "\n",
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->lineWrap(10)->toString());
        }
    }

    public function testLinewrapAfterWord()
    {
        $testArray = [
            ''                                                                                                      => "\n",
            ' '                                                                                                     => ' ' . "\n",
            'http:// moelleken.org'                                                                                 => 'http://' . "\n" . 'moelleken.org' . "\n",
            'http://test.de'                                                                                        => 'http://test.de' . "\n",
            'http://öäü.de'                                                                                         => 'http://öäü.de' . "\n",
            'http://menadwork.com'                                                                                  => 'http://menadwork.com' . "\n",
            'test.de'                                                                                               => 'test.de' . "\n",
            'test'                                                                                                  => 'test' . "\n",
            '0123456 789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789' => '0123456' . "\n" . '789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789' . "\n",
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->lineWrapAfterWord(10)->toString());
        }
    }

    /**
     * @dataProvider longestCommonPrefixProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $otherStr
     * @param null $encoding
     */
    public function testLongestCommonPrefix($expected, $str, $otherStr, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->longestCommonPrefix($otherStr);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider longestCommonSubstringProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $otherStr
     * @param null $encoding
     */
    public function testLongestCommonSubstring($expected, $str, $otherStr, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->longestCommonSubstring($otherStr);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider longestCommonSuffixProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $otherStr
     * @param null $encoding
     */
    public function testLongestCommonSuffix($expected, $str, $otherStr, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->longestCommonSuffix($otherStr);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider lowerCaseFirstProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testLowerCaseFirst($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->lowerCaseFirst();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testMissingToString()
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @noinspection PhpExpressionResultUnusedInspection */
        (string) new S(new stdClass());
        static::fail(
            'Expecting exception when the constructor is passed an ' .
            'object without a __toString method'
        );
    }

    /**
     * @dataProvider offsetExistsProvider()
     *
     * @param $expected
     * @param $offset
     */
    public function testOffsetExists($expected, $offset)
    {
        $stringy = S::create('fòô', 'UTF-8');
        static::assertSame($expected, $stringy->offsetExists($offset));
        static::assertSame($expected, isset($stringy[$offset]));
    }

    public function testOffsetGet()
    {
        $stringy = S::create('fòô', 'UTF-8');

        static::assertSame('f', $stringy->offsetGet(0));
        static::assertSame('ô', $stringy->offsetGet(2));

        static::assertSame('ô', $stringy[2]);
    }

    public function testOffsetGetOutOfBounds()
    {
        $this->expectException(\OutOfBoundsException::class);

        $stringy = S::create('fòô', 'UTF-8');
        /** @noinspection PhpUnusedLocalVariableInspection */
        /** @noinspection OnlyWritesOnParameterInspection */
        $test = $stringy[3];
    }

    public function testOffsetSet()
    {
        $this->expectException(\Exception::class);

        /** @noinspection OnlyWritesOnParameterInspection */
        $stringy = S::create('fòô', 'UTF-8');
        /** @noinspection OnlyWritesOnParameterInspection */
        $stringy[1] = 'invalid';
    }

    public function testOffsetUnset()
    {
        $this->expectException(\Exception::class);

        $stringy = S::create('fòô', 'UTF-8');
        unset($stringy[1]);
    }

    /**
     * @dataProvider padProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $padStr
     * @param string $padType
     * @param null   $encoding
     */
    public function testPad($expected, $str, $length, $padStr = ' ', $padType = 'right', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->pad($length, $padStr, $padType);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider padBothProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $padStr
     * @param null   $encoding
     */
    public function testPadBoth($expected, $str, $length, $padStr = ' ', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->padBoth($length, $padStr);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testPadException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $stringy = S::create('foo');
        $stringy->pad(5, 'foo', 'bar');
    }

    /**
     * @dataProvider padLeftProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $padStr
     * @param null   $encoding
     */
    public function testPadLeft($expected, $str, $length, $padStr = ' ', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->padLeft($length, $padStr);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider padRightProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $padStr
     * @param null   $encoding
     */
    public function testPadRight($expected, $str, $length, $padStr = ' ', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->padRight($length, $padStr);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider prependProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $string
     * @param null $encoding
     */
    public function testPrepend($expected, $str, $string, $encoding = null)
    {
        $result = S::create($str, $encoding)->prepend($string);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
    }

    public function testPrependv2()
    {
        $result = S::create('abc')->prepend('123', 'öäü', 'def');
        static::assertSame('123öäüdefabc', $result->toString());
    }

    public function testPrependStringy()
    {
        $result = S::create('abc')->prependStringy(S::create('zzz'), \Stringy\CollectionStringy::createFromStrings(['123', 'öäü', 'def']));
        static::assertSame('zzz123öäüdefabc', $result->toString());
    }

    /**
     * @dataProvider equalsProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testEquals($expected, $str, $encoding = null)
    {
        $result = S::create('fòô bàř', $encoding)->isEquals($str);
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider emptyProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testEmpty($expected, $str, $encoding = null)
    {
        $result = S::create($str, $encoding)->isEmpty();
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider emptyProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testNotEmpty($expected, $str, $encoding = null)
    {
        $result = S::create($str, $encoding)->isNotEmpty();
        static::assertSame(!$expected, $result);
    }

    /**
     * @dataProvider removeHtmlProvider()
     *
     * @param        $expected
     * @param        $str
     * @param string $allowableTags
     * @param null   $encoding
     */
    public function testRemoveHtml($expected, $str, $allowableTags = '', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->removeHtml($allowableTags);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider removeHtmlBreakProvider()
     *
     * @param        $expected
     * @param        $str
     * @param string $replacement
     * @param null   $encoding
     */
    public function testRemoveHtmlBreak($expected, $str, $replacement = '', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->removeHtmlBreak($replacement);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testNewLineToHtmlBreak()
    {
        $stringy = S::create('foo bar' . "\n" . 'lall<br />');

        static::assertSame('foo bar<br>lall<br>', $stringy->newLineToHtmlBreak()->toString());
    }

    /**
     * @dataProvider removeLeftProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param null $encoding
     */
    public function testRemoveLeft($expected, $str, $substring, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->removeLeft($substring);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider removeRightProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param null $encoding
     */
    public function testRemoveRight($expected, $str, $substring, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->removeRight($substring);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider removeXssProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testRemoveXss($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->removeXss();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString(), 'tested: ' . $str);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider repeatProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $multiplier
     * @param null $encoding
     */
    public function testRepeat($expected, $str, $multiplier, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->repeat($multiplier);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider replaceProvider()
     *
     * @param string $expected
     * @param string $str
     * @param string $search
     * @param string $replacement
     * @param null   $encoding
     * @param bool   $caseSensitive
     */
    public function testReplace($expected, $str, $search, $replacement, $encoding = null, $caseSensitive = true)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->replace($search, $replacement, $caseSensitive);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider replaceAllProvider()
     *
     * @param string $expected
     * @param string $str
     * @param array  $search
     * @param string $replacement
     * @param null   $encoding
     * @param bool   $caseSensitive
     */
    public function testReplaceAll($expected, $str, $search, $replacement, $encoding = null, $caseSensitive = true)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->replaceAll($search, $replacement, $caseSensitive);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider replaceBeginningProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $search
     * @param      $replacement
     * @param null $encoding
     */
    public function testReplaceBeginning($expected, $str, $search, $replacement, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->replaceBeginning($search, $replacement);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider replaceEndingProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $search
     * @param      $replacement
     * @param null $encoding
     */
    public function testReplaceEnding($expected, $str, $search, $replacement, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->replaceEnding($search, $replacement);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider replaceFirstProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $search
     * @param      $replacement
     * @param null $encoding
     */
    public function testReplaceFirst($expected, $str, $search, $replacement, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->replaceFirst($search, $replacement);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider replaceLastProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $search
     * @param      $replacement
     * @param null $encoding
     */
    public function testReplaceLast($expected, $str, $search, $replacement, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->replaceLast($search, $replacement);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider reverseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testReverse($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->reverse();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider safeTruncateProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $substring
     * @param null   $encoding
     */
    public function testSafeTruncate($expected, $str, $length, $substring = '', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->safeTruncate($length, $substring, false);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString(), 'tested: ' . $str . ' | ' . $substring . ' (' . $length . ')');
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider shortenAfterWordProvider()
     *
     * @param        $expected
     * @param        $str
     * @param int    $length
     * @param string $strAddOn
     * @param null   $encoding
     */
    public function testShortenAfterWord($expected, $str, $length, $strAddOn = '...', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->shortenAfterWord($length, $strAddOn);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider shuffleProvider()
     *
     * @param      $str
     * @param null $encoding
     */
    public function testShuffle($str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $encoding = $encoding ?: \mb_internal_encoding();
        $result = $stringy->shuffle();

        $this->assertStringy($result);
        static::assertSame($str, $stringy->toString());
        static::assertSame(
            \mb_strlen($str, $encoding),
            \mb_strlen($result, $encoding)
        );

        // We'll make sure that the chars are present after shuffle
        $length = \mb_strlen($str, $encoding);
        for ($i = 0; $i < $length; ++$i) {
            $char = \mb_substr($str, $i, 1, $encoding);
            $countBefore = \mb_substr_count($str, $char, $encoding);
            $countAfter = \mb_substr_count($result, $char, $encoding);
            static::assertSame($countBefore, $countAfter);
        }
    }

    /**
     * @dataProvider sliceProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $start
     * @param null $end
     * @param null $encoding
     */
    public function testSlice($expected, $str, $start, $end = null, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->slice($start, $end);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider slugifyProvider()
     *
     * @param        $expected
     * @param        $str
     * @param string $replacement
     */
    public function testSlugify($expected, $str, $replacement = '-')
    {
        $stringy = S::create($str);
        $result = $stringy->urlify($replacement, 'en', ['foooooo' => 'bar']);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider snakeizeProvider()
     *
     * @param string      $expected
     * @param string      $str
     * @param string|null $encoding
     */
    public function testSnakeize($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->snakeize();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider chunkProvider()
     *
     * @param string[] $expected
     * @param string   $str
     * @param int      $length
     * @param null     $encoding
     */
    public function testChunk($expected, $str, $length = 1, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->chunkCollection($length, true);

        static::assertInstanceOf(\Stringy\CollectionStringy::class, $result);
        foreach ($result as $string) {
            $this->assertStringy($string);
        }

        $resultNew = [];
        foreach ($result as $key => $strTmp) {
            $resultNew[$key] = $strTmp->toString();
        }

        static::assertSame($expected, $resultNew);
    }

    public function testWords()
    {
        static::assertSame(['iñt', 'ërn', 'I'], S::create('iñt ërn I')->wordsCollection('', true, null)->toStrings());
        static::assertSame(['iñt', 'ërn'], S::create('iñt ërn I')->wordsCollection('', false, 1)->toStrings());
        static::assertSame(['', '中文空白', ' ', 'oöäü#s', ''], S::create('中文空白 oöäü#s')->wordsCollection('#', false, null)->toStrings());
        static::assertSame(['中文空白', 'oöäü#s'], S::create('中文空白 oöäü#s')->wordsCollection('#', true)->toStrings());
        static::assertSame(['', 'foo', ' ', 'oo', ' ', 'oöäü', '#', 's', ''], S::create('foo oo oöäü#s')->wordsCollection('', false, null)->toStrings());
        static::assertSame([''], S::create('')->wordsCollection('', false, null)->toStrings());

        $testArray = [
            'Düsseldorf'                                                                                => 'Düsseldorf',
            'Ã'                                                                                         => 'Ã',
            'foobar  || 😃'                                                                              => 'foobar  || 😃',
            ' '                                                                                         => ' ',
            ''                                                                                          => '',
            "\n"                                                                                        => "\n",
            'test'                                                                                      => 'test',
            'Here&#39;s some quoted text.'                                                              => 'Here&#39;s some quoted text.',
            '&#39;'                                                                                     => '&#39;',
            "\u0063\u0061\u0074"                                                                        => 'cat',
            "\u0039&#39;\u0039"                                                                         => '9&#39;9',
            '&#35;&#8419;'                                                                              => '&#35;&#8419;',
            "\xcf\x80"                                                                                  => 'π',
            'ðñòó¡¡à±áâãäåæçèéêëì¡í¡îï¡¡¢£¤¥¦§¨©ª«¬­®¯ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß°±²³´µ¶•¸¹º»¼½¾¿' => 'ðñòó¡¡à±áâãäåæçèéêëì¡í¡îï¡¡¢£¤¥¦§¨©ª«¬­®¯ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß°±²³´µ¶•¸¹º»¼½¾¿',
            '%ABREPRESENT%C9%BB. «REPRESENTÉ»'                                                          => '%ABREPRESENT%C9%BB. «REPRESENTÉ»',
            'éæ'                                                                                        => 'éæ',
        ];

        foreach ($testArray as $test => $unused) {
            static::assertSame($test, S::create($test)->wordsCollection('', false, null)->implode(''));
        }
    }

    /**
     * @dataProvider splitProvider()
     *
     * @param string[]    $expected
     * @param string      $str
     * @param string      $pattern
     * @param int         $limit
     * @param string|null $encoding
     */
    public function testSplit($expected, $str, $pattern, $limit = -1, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->split($pattern, $limit);

        static::assertTrue(\is_array($result));
        foreach ($result as $string) {
            static::assertInstanceOf(\Stringy\Stringy::class, $string);
            $this->assertStringy($string);
        }

        $counter = \count($expected);
        for ($i = 0; $i < $counter; ++$i) {
            static::assertSame($expected[$i], $result[$i]->toString());
        }
    }

    /**
     * @dataProvider startsWithProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param bool $caseSensitive
     * @param null $encoding
     */
    public function testStartsWith($expected, $str, $substring, $caseSensitive = true, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->startsWith($substring, $caseSensitive);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider startsWithProviderAny()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param bool $caseSensitive
     * @param null $encoding
     */
    public function testStartsWithAny($expected, $str, $substring, $caseSensitive = true, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->startsWithAny($substring, $caseSensitive);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider stripWhitespaceProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testStripWhitespace($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->stripWhitespace();
        $this->assertStringy($result);
        static::assertEquals($expected, $result);
        static::assertEquals($str, $stringy);
    }

    public function testStripeEmptyTags()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '<h1>test</h1>',
            'foo<h1></h1>bar'          => 'foobar',
            '<h1></h1> '               => ' ',
            '</b></b>'                 => '</b></b>',
            'öäü<strong>lall</strong>' => 'öäü<strong>lall</strong>',
            ' b<b></b>'                => ' b',
            '<b><b>lall</b>'           => '<b><b>lall</b>',
            '</b>lall</b>'             => '</b>lall</b>',
            '[b][/b]'                  => '[b][/b]',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->stripeEmptyHtmlTags()->toString());
        }
    }

    public function testStripeMediaQueries()
    {
        $testArray = [
            'test lall '                                                                         => 'test lall ',
            ''                                                                                   => '',
            ' '                                                                                  => ' ',
            'test @media (min-width:660px){ .des-cla #mv-tiles{width:480px} } test '             => 'test  test ',
            'test @media only screen and (max-width: 950px) { .des-cla #mv-tiles{width:480px} }' => 'test ',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->stripeCssMediaQueries()->toString());
        }
    }

    public function testSubStringOf()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'bar',
            '<h1></h1> '               => '',
            '</b></b>'                 => 'b></b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => 'b<b></b>',
            '<b><b>lall</b>'           => 'b><b>lall</b>',
            '</b>lall</b>'             => 'b>lall</b>',
            '[B][/B]'                  => '',
            '[b][/b]'                  => 'b][/b]',
            'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
            'bκόσμbε'                  => 'bκόσμbε',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->substringOf('b', false)->toString());
        }
    }

    /**
     * @dataProvider substrProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $start
     * @param null $length
     * @param null $encoding
     */
    public function testSubstr($expected, $str, $start, $length = null, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->substr($start, $length);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function testSubstringOfIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>Bar'          => 'Bar',
            'foo<h1></h1>bar'          => 'bar',
            '<h1></h1> '               => '',
            '</b></b>'                 => 'b></b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => 'b<b></b>',
            '<b><b>lall</b>'           => 'b><b>lall</b>',
            '</b>lall</b>'             => 'b>lall</b>',
            '[B][/B]'                  => 'B][/B]',
            'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
            'bκόσμbε'                  => 'bκόσμbε',
        ];

        foreach ($testArray as $testString => $testResult) {
            $stringy = S::create($testString);
            static::assertSame($testResult, $stringy->substringOfIgnoreCase('b', false)->toString());
        }
    }

    /**
     * @dataProvider surroundProvider()
     *
     * @param $expected
     * @param $str
     * @param $substring
     */
    public function testSurround($expected, $str, $substring)
    {
        $stringy = S::create($str);
        $result = $stringy->surround($substring);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());

        // --

        $stringy = S::create($str);
        $result = $stringy->wrap($substring);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider swapCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testSwapCase($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->swapCase();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider tidyProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testTidy($expected, $str)
    {
        $stringy = S::create($str);
        $result = $stringy->tidy();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider titleizeProvider()
     *
     * @param             $expected
     * @param             $str
     * @param array|null  $ignore
     * @param string|null $word_define_chars
     * @param string|null $encoding
     */
    public function testTitleize($expected, $str, $ignore = null, $word_define_chars = null, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->titleize($ignore, $word_define_chars);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider titleizeForHumansProvider()
     *
     * @param string      $expected
     * @param string      $str
     * @param array       $ignore
     * @param string|null $encoding
     */
    public function testTitleizeForHumans($str, $expected, $ignore = [], $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->titleizeForHumans($ignore);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    public function titleizeForHumansProvider()
    {
        return [
            ['TITLE CASE', 'Title Case'],
            ['testing the method', 'Testing the Method'],
            ['i like to watch DVDs at home', 'I Like to watch DVDs at Home', ['watch']],
            ['  Θα ήθελα να φύγει  ', 'Θα Ήθελα Να Φύγει', [], 'UTF-8'],
            [
                'For step-by-step directions email someone@gmail.com',
                'For Step-by-Step Directions Email someone@gmail.com',
            ],
            [
                "2lmc Spool: 'Gruber on OmniFocus and Vapo(u)rware'",
                "2lmc Spool: 'Gruber on OmniFocus and Vapo(u)rware'",
            ],
            ['Have you read “The Lottery”?', 'Have You Read “The Lottery”?'],
            ['your hair[cut] looks (nice)', 'Your Hair[cut] Looks (Nice)'],
            [
                "People probably won't put http://foo.com/bar/ in titles",
                "People Probably Won't Put http://foo.com/bar/ in Titles",
            ],
            [
                'Scott Moritz and TheStreet.com’s million iPhone la‑la land',
                'Scott Moritz and TheStreet.com’s Million iPhone La‑La Land',
            ],
            ['BlackBerry vs. iPhone', 'BlackBerry vs. iPhone'],
            [
                'Notes and observations regarding Apple’s announcements from ‘The Beat Goes On’ special event',
                'Notes and Observations Regarding Apple’s Announcements From ‘The Beat Goes On’ Special Event',
            ],
            [
                'Read markdown_rules.txt to find out how _underscores around words_ will be interpretted',
                'Read markdown_rules.txt to Find Out How _Underscores Around Words_ Will Be Interpretted',
            ],
            [
                "Q&A with Steve Jobs: 'That's what happens in technology'",
                "Q&A With Steve Jobs: 'That's What Happens in Technology'",
            ],
            ["What is AT&T's problem?", "What Is AT&T's Problem?"],
            ['Apple deal with AT&T falls through', 'Apple Deal With AT&T Falls Through'],
            ['this v that', 'This v That'],
            ['this vs that', 'This vs That'],
            ['this v. that', 'This v. That'],
            ['this vs. that', 'This vs. That'],
            ["The SEC's Apple probe: what you need to know", "The SEC's Apple Probe: What You Need to Know"],
            [
                "'by the way, small word at the start but within quotes.'",
                "'By the Way, Small Word at the Start but Within Quotes.'",
            ],
            ['Small word at end is nothing to be afraid of', 'Small Word at End Is Nothing to Be Afraid Of'],
            [
                'Starting sub-phrase with a small word: a trick, perhaps?',
                'Starting Sub-Phrase With a Small Word: A Trick, Perhaps?',
            ],
            [
                "Sub-phrase with a small word in quotes: 'a trick, perhaps?'",
                "Sub-Phrase With a Small Word in Quotes: 'A Trick, Perhaps?'",
            ],
            [
                'Sub-phrase with a small word in quotes: "a trick, perhaps?"',
                'Sub-Phrase With a Small Word in Quotes: "A Trick, Perhaps?"',
            ],
            ['"Nothing to Be Afraid of?"', '"Nothing to Be Afraid Of?"'],
            ['a thing', 'A Thing'],
            [
                'Dr. Strangelove (or: how I Learned to Stop Worrying and Love the Bomb)',
                'Dr. Strangelove (Or: How I Learned to Stop Worrying and Love the Bomb)',
            ],
            ['  this is trimming', 'This Is Trimming'],
            ['this is trimming  ', 'This Is Trimming'],
            ['  this is trimming  ', 'This Is Trimming'],
            ['IF IT’S ALL CAPS, FIX IT', 'If It’s All Caps, Fix It'],
            ['What could/should be done about slashes?', 'What Could/Should Be Done About Slashes?'],
            [
                'Never touch paths like /var/run before/after /boot',
                'Never Touch Paths Like /var/run Before/After /boot',
            ],
        ];
    }

    /**
     * @dataProvider toTransliterateProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testTesttoTransliterate($expected, $str)
    {
        $stringy = S::create($str);
        $result = $stringy->toTransliterate();

        $this->assertStringy($result);
        static::assertSame($expected, $result->toString(), 'tested:' . $str);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider toBooleanProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testToBoolean($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->toBoolean();
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result, 'tested: ' . $str);
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider toLowerCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testToLowerCase($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->toLowerCase();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider toSpacesProvider()
     *
     * @param     $expected
     * @param     $str
     * @param int $tabLength
     */
    public function testToSpaces($expected, $str, $tabLength = 4)
    {
        $stringy = S::create($str);
        $result = $stringy->toSpaces($tabLength);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider toStringProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testToString($expected, $str)
    {
        $stringy = new S($str);
        static::assertSame($expected, (string) $stringy);
        static::assertSame($expected, $stringy->toString());
    }

    public function testToStringMethod()
    {
        $stringy = S::create('öäü - foo');
        $result = $stringy->toString();
        static::assertTrue(\is_string($result));
        static::assertSame((string) $stringy, $result);
        static::assertSame('öäü - foo', $result);
    }

    /**
     * @dataProvider toTabsProvider()
     *
     * @param     $expected
     * @param     $str
     * @param int $tabLength
     */
    public function testToTabs($expected, $str, $tabLength = 4)
    {
        $stringy = S::create($str);
        $result = $stringy->toTabs($tabLength);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider toTitleCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testToTitleCase($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->toTitleCase();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider toUpperCaseProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testToUpperCase($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->toUpperCase();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider trimProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $chars
     * @param null $encoding
     */
    public function testTrim($expected, $str, $chars = null, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->trim($chars);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider trimLeftProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $chars
     * @param null $encoding
     */
    public function testTrimLeft($expected, $str, $chars = null, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->trimLeft($chars);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider trimRightProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $chars
     * @param null $encoding
     */
    public function testTrimRight($expected, $str, $chars = null, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->trimRight($chars);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider truncateProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $substring
     * @param null   $encoding
     */
    public function testTruncate($expected, $str, $length, $substring = '', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->truncate($length, $substring);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider underscoredProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testUnderscored($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->underscored();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider upperCamelizeProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testUpperCamelize($expected, $str, $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->upperCamelize();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @dataProvider upperCaseFirstProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $encoding
     */
    public function testUpperCaseFirst($expected, $str, $encoding = null)
    {
        $result = S::create($str, $encoding)->upperCaseFirst();
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
    }

    public function testUtf8ify()
    {
        /** @noinspection CheckDtdRefs */
        $examples = [
            '' => [''],
            // Valid UTF-8 + UTF-8 NO-BREAK SPACE
            "κόσμε\xc2\xa0" => ['κόσμε' . "\xc2\xa0" => 'κόσμε' . "\xc2\xa0"],
            // Valid UTF-8
            '中' => ['中' => '中'],
            // Valid UTF-8 + ISO-Error
            'DÃ¼sseldorf' => ['Düsseldorf' => 'Düsseldorf'],
            // Valid UTF-8 + Invalid Chars
            "κόσμε\xa0\xa1-öäü" => ['κόσμε-öäü' => 'κόσμε-öäü'],
            // Valid ASCII
            'a' => ['a' => 'a'],
            // Valid ASCII + Invalid Chars
            "a\xa0\xa1-öäü" => ['a-öäü' => 'a-öäü'],
            // Valid 2 Octet Sequence
            "\xc3\xb1" => ['ñ' => 'ñ'],
            // Invalid 2 Octet Sequence
            "\xc3\x28" => ['�(' => '('],
            // Invalid Sequence Identifier
            "\xa0\xa1" => ['��' => ''],
            // Valid 3 Octet Sequence
            "\xe2\x82\xa1" => ['₡' => '₡'],
            // Invalid 3 Octet Sequence (in 2nd Octet)
            "\xe2\x28\xa1" => ['�(�' => '('],
            // Invalid 3 Octet Sequence (in 3rd Octet)
            "\xe2\x82\x28" => ['�(' => '('],
            // Valid 4 Octet Sequence
            "\xf0\x90\x8c\xbc" => ['𐌼' => '𐌼'],
            // Invalid 4 Octet Sequence (in 2nd Octet)
            "\xf0\x28\x8c\xbc" => ['�(��' => '('],
            // Invalid 4 Octet Sequence (in 3rd Octet)
            "\xf0\x90\x28\xbc" => ['�(�' => '('],
            // Invalid 4 Octet Sequence (in 4th Octet)
            " \xf0\x28\x8c\x28" => ['�(�(' => ' (('],
            // Valid 5 Octet Sequence (but not Unicode!)
            "\xf8\xa1\xa1\xa1\xa1" => ['�' => ''],
            // Valid 6 Octet Sequence (but not Unicode!) + UTF-8 EN SPACE
            "\xfc\xa1\xa1\xa1\xa1\xa1\xe2\x80\x82" => ['�' => ' '],
            // test for database-insert
            '
        <h1>«DÃ¼sseldorf» &ndash; &lt;Köln&gt;</h1>
        <br /><br />
        <p>
          &nbsp;�&foo;❤&nbsp;
        </p>
        ' => [
                '' => '
        <h1>«Düsseldorf» &ndash; &lt;Köln&gt;</h1>
        <br /><br />
        <p>
          &nbsp;&foo;❤&nbsp;
        </p>
        ',
            ],
        ];

        foreach ($examples as $testString => $testResults) {
            $stringy = S::create($testString);
            foreach ($testResults as $before => $after) {
                static::assertSame($after, $stringy->utf8ify()->toString());
            }
        }

        $examples = [
            // Valid UTF-8
            'κόσμε'    => ['κόσμε' => 'κόσμε'],
            '中'        => ['中' => '中'],
            '«foobar»' => ['«foobar»' => '«foobar»'],
            // Valid UTF-8 + Invalied Chars
            "κόσμε\xa0\xa1-öäü" => ['κόσμε-öäü' => 'κόσμε-öäü'],
            // Valid ASCII
            'a' => ['a' => 'a'],
            // Valid emoji (non-UTF-8)
            '😃' => ['😃' => '😃'],
            // Valid ASCII + Invalied Chars
            "a\xa0\xa1-öäü" => ['a-öäü' => 'a-öäü'],
            // Valid 2 Octet Sequence
            "\xc3\xb1" => ['ñ' => 'ñ'],
            // Invalid 2 Octet Sequence
            "\xc3\x28" => ['�(' => '('],
            // Invalid Sequence Identifier
            "\xa0\xa1" => ['��' => ''],
            // Valid 3 Octet Sequence
            "\xe2\x82\xa1" => ['₡' => '₡'],
            // Invalid 3 Octet Sequence (in 2nd Octet)
            "\xe2\x28\xa1" => ['�(�' => '('],
            // Invalid 3 Octet Sequence (in 3rd Octet)
            "\xe2\x82\x28" => ['�(' => '('],
            // Valid 4 Octet Sequence
            "\xf0\x90\x8c\xbc" => ['𐌼' => '𐌼'],
            // Invalid 4 Octet Sequence (in 2nd Octet)
            "\xf0\x28\x8c\xbc" => ['�(��' => '('],
            // Invalid 4 Octet Sequence (in 3rd Octet)
            "\xf0\x90\x28\xbc" => ['�(�' => '('],
            // Invalid 4 Octet Sequence (in 4th Octet)
            "\xf0\x28\x8c\x28" => ['�(�(' => '(('],
            // Valid 5 Octet Sequence (but not Unicode!)
            "\xf8\xa1\xa1\xa1\xa1" => ['�' => ''],
            // Valid 6 Octet Sequence (but not Unicode!)
            "\xfc\xa1\xa1\xa1\xa1\xa1" => ['�' => ''],
        ];

        $counter = 0;
        foreach ($examples as $testString => $testResults) {
            $stringy = S::create($testString);
            foreach ($testResults as $before => $after) {
                static::assertSame($after, $stringy->utf8ify()->toString(), $counter);
            }
            ++$counter;
        }
    }

    /**
     * @dataProvider containsAnyProvider()
     *
     * @param      $expected
     * @param      $haystack
     * @param      $needles
     * @param bool $caseSensitive
     * @param null $encoding
     */
    public function testTestcontainsAny($expected, $haystack, $needles, $caseSensitive = true, $encoding = null)
    {
        $stringy = S::create($haystack, $encoding);
        $result = $stringy->containsAny($needles, $caseSensitive);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
        static::assertSame($haystack, $stringy->toString());
    }

    /**
     * @dataProvider regexReplaceProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $pattern
     * @param        $replacement
     * @param string $options
     * @param string $delimiter
     * @param null   $encoding
     */
    public function testTestregexReplace($expected, $str, $pattern, $replacement, $options = 'msr', $delimiter = '/', $encoding = null)
    {
        $stringy = S::create($str, $encoding);
        $result = $stringy->regexReplace($pattern, $replacement, $options, $delimiter);
        $this->assertStringy($result);
        static::assertSame($expected, $result->toString());
        static::assertSame($str, $stringy->toString());
    }

    /**
     * @return array
     */
    public function tidyProvider(): array
    {
        return [
            ['"I see..."', '“I see…”'],
            ["'This too'", '‘This too’'],
            ['test-dash', 'test—dash'],
            ['Ο συγγραφέας είπε...', 'Ο συγγραφέας είπε…'],
        ];
    }

    /**
     * @return array
     */
    public function titleizeProvider(): array
    {
        $ignore = ['at', 'by', 'for', 'in', 'of', 'on', 'out', 'to', 'the'];

        return [
            ['Title Case', 'TITLE CASE'],
            ['Up-to-Date', 'up-to-date', ['to'], '-'],
            ['Up-to-Date', 'up-to-date', ['to'], '-*'],
            ['Up-To-Date', 'up-to-date', [], '-*'],
            ['Up-To-D*A*T*E*', 'up-to-d*a*t*e*', [], '-*'],
            ['Testing The Method', 'testing the method'],
            ['Testing the Method', 'testing the method', $ignore],
            [
                'I Like to Watch Dvds at Home',
                'i like to watch DVDs at home',
                $ignore,
            ],
            ['Θα Ήθελα Να Φύγει', '  Θα ήθελα να φύγει  ', null, null, 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function toTransliterateProvider(): array
    {
        return [
            ['foo bar', 'fòô bàř'],
            [' TEST ', ' ŤÉŚŢ '],
            ['ph = z = 3', 'φ = ź = 3'],
            ['kh Kh shch Shch \' \' \' \'', 'х Х щ Щ ъ Ъ ь Ь'],
            ['perevirka', 'перевірка'],
            ['lysaia gora', 'лысая гора'],
            ['shchuka', 'щука'],
            ['Han Zi ', '漢字'],
            ['xin chao the gioi', 'xin chào thế giới'],
            ['XIN CHAO THE GIOI', 'XIN CHÀO THẾ GIỚI'],
            ['dam phat chet luon', 'đấm phát chết luôn'],
            [' ', ' '], // no-break space (U+00A0)
            ['           ', '           '], // spaces U+2000 to U+200A
            [' ', ' '], // narrow no-break space (U+202F)
            [' ', ' '], // medium mathematical space (U+205F)
            [' ', '　'], // ideographic space (U+3000)
            ['?', '𐍉'], // some uncommon, unsupported character (U+10349)
        ];
    }

    /**
     * @return array
     */
    public function toBooleanProvider(): array
    {
        return [
            [true, 'true'],
            [true, '1'],
            [true, 'on'],
            [true, 'ON'],
            [true, 'yes'],
            [true, '999'],
            [false, 'false'],
            [false, '0'],
            [false, 'off'],
            [false, 'OFF'],
            [false, 'no'],
            [false, '-999'],
            [false, ''],
            [false, ' '],
            [false, '  ', 'UTF-8'], // narrow no-break space (U+202F)
        ];
    }

    /**
     * @return array
     */
    public function toLowerCaseProvider(): array
    {
        return [
            ['foo bar', 'FOO BAR'],
            [' foo_bar ', ' FOO_bar '],
            ['fòô bàř', 'FÒÔ BÀŘ', 'UTF-8'],
            [' fòô_bàř ', ' FÒÔ_bàř ', 'UTF-8'],
            ['αυτοκίνητο', 'ΑΥΤΟΚΊΝΗΤΟ', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function toSpacesProvider(): array
    {
        return [
            ['    foo    bar    ', '	foo	bar	'],
            ['     foo     bar     ', '	foo	bar	', 5],
            ['    foo  bar  ', '		foo	bar	', 2],
            ['foobar', '	foo	bar	', 0],
            ["    foo\n    bar", "	foo\n	bar"],
            ["    fòô\n    bàř", "	fòô\n	bàř"],
        ];
    }

    /**
     * @return array
     */
    public function toStringProvider(): array
    {
        return [
            ['', null],
            ['', false],
            ['1', true],
            ['-9', -9],
            ['1.18', 1.18],
            [' string  ', ' string  '],
        ];
    }

    /**
     * @return array
     */
    public function toTabsProvider(): array
    {
        return [
            ['	foo	bar	', '    foo    bar    '],
            ['	foo	bar	', '     foo     bar     ', 5],
            ['		foo	bar	', '    foo  bar  ', 2],
            ["	foo\n	bar", "    foo\n    bar"],
            ["	fòô\n	bàř", "    fòô\n    bàř"],
        ];
    }

    /**
     * @return array
     */
    public function toTitleCaseProvider(): array
    {
        return [
            ['Foo Bar', 'foo bar'],
            [' Foo_Bar ', ' foo_bar '],
            ['Fòô Bàř', 'fòô bàř', 'UTF-8'],
            [' Fòô_Bàř ', ' fòô_bàř ', 'UTF-8'],
            ['Αυτοκίνητο Αυτοκίνητο', 'αυτοκίνητο αυτοκίνητο', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function toUpperCaseProvider(): array
    {
        return [
            ['FOO BAR', 'foo bar'],
            [' FOO_BAR ', ' FOO_bar '],
            ['FÒÔ BÀŘ', 'fòô bàř', 'UTF-8'],
            [' FÒÔ_BÀŘ ', ' FÒÔ_bàř ', 'UTF-8'],
            ['ΑΥΤΟΚΊΝΗΤΟ', 'αυτοκίνητο', 'UTF-8'],
            ['ἙΛΛΗΝΙΚῊ', 'ἑλληνικὴ'],
        ];
    }

    /**
     * @return array
     */
    public function trimLeftProvider(): array
    {
        return [
            ['foo   bar  ', '  foo   bar  '],
            ['foo bar', ' foo bar'],
            ['foo bar ', 'foo bar '],
            ["foo bar \n\t", "\n\t foo bar \n\t"],
            ['fòô   bàř  ', '  fòô   bàř  '],
            ['fòô bàř', ' fòô bàř'],
            ['fòô bàř ', 'fòô bàř '],
            ['foo bar', '--foo bar', '-'],
            ['fòô bàř', 'òòfòô bàř', 'ò', 'UTF-8'],
            ["fòô bàř \n\t", "\n\t fòô bàř \n\t", null, 'UTF-8'],
            ['fòô ', ' fòô ', null, 'UTF-8'], // narrow no-break space (U+202F)
            ['fòô  ', '  fòô  ', null, 'UTF-8'], // medium mathematical space (U+205F)
            ['fòô', '           fòô', null, 'UTF-8'], // spaces U+2000 to U+200A
        ];
    }

    /**
     * @return array
     */
    public function trimProvider(): array
    {
        return [
            ['foo   bar', '  foo   bar  '],
            ['foo bar', ' foo bar'],
            ['foo bar', 'foo bar '],
            ['foo bar', "\n\t foo bar \n\t"],
            ['fòô   bàř', '  fòô   bàř  '],
            ['fòô bàř', ' fòô bàř'],
            ['fòô bàř', 'fòô bàř '],
            [' foo bar ', "\n\t foo bar \n\t", "\n\t"],
            ['fòô bàř', "\n\t fòô bàř \n\t", null, 'UTF-8'],
            ['fòô', ' fòô ', null, 'UTF-8'], // narrow no-break space (U+202F)
            ['fòô', '  fòô  ', null, 'UTF-8'], // medium mathematical space (U+205F)
            ['fòô', '           fòô', null, 'UTF-8'], // spaces U+2000 to U+200A
        ];
    }

    /**
     * @return array
     */
    public function trimRightProvider(): array
    {
        return [
            ['  foo   bar', '  foo   bar  '],
            ['foo bar', 'foo bar '],
            [' foo bar', ' foo bar'],
            ["\n\t foo bar", "\n\t foo bar \n\t"],
            ['  fòô   bàř', '  fòô   bàř  '],
            ['fòô bàř', 'fòô bàř '],
            [' fòô bàř', ' fòô bàř'],
            ['foo bar', 'foo bar--', '-'],
            ['fòô bàř', 'fòô bàřòò', 'ò', 'UTF-8'],
            ["\n\t fòô bàř", "\n\t fòô bàř \n\t", null, 'UTF-8'],
            [' fòô', ' fòô ', null, 'UTF-8'], // narrow no-break space (U+202F)
            ['  fòô', '  fòô  ', null, 'UTF-8'], // medium mathematical space (U+205F)
            ['fòô', 'fòô           ', null, 'UTF-8'], // spaces U+2000 to U+200A
        ];
    }

    /**
     * @return array
     */
    public function truncateProvider(): array
    {
        return [
            ['Test foo bar', 'Test foo bar', 12],
            ['Test foo ba', 'Test foo bar', 11],
            ['Test foo', 'Test foo bar', 8],
            ['Test fo', 'Test foo bar', 7],
            ['Test', 'Test foo bar', 4],
            ['Test foo bar', 'Test foo bar', 12, '...'],
            ['Test foo...', 'Test foo bar', 11, '...'],
            ['Test ...', 'Test foo bar', 8, '...'],
            ['Test...', 'Test foo bar', 7, '...'],
            ['T...', 'Test foo bar', 4, '...'],
            ['Test fo....', 'Test foo bar', 11, '....'],
            ['Test fòô bàř', 'Test fòô bàř', 12, '', 'UTF-8'],
            ['Test fòô bà', 'Test fòô bàř', 11, '', 'UTF-8'],
            ['Test fòô', 'Test fòô bàř', 8, '', 'UTF-8'],
            ['Test fò', 'Test fòô bàř', 7, '', 'UTF-8'],
            ['Test', 'Test fòô bàř', 4, '', 'UTF-8'],
            ['Test fòô bàř', 'Test fòô bàř', 12, 'ϰϰ', 'UTF-8'],
            ['Test fòô ϰϰ', 'Test fòô bàř', 11, 'ϰϰ', 'UTF-8'],
            ['Test fϰϰ', 'Test fòô bàř', 8, 'ϰϰ', 'UTF-8'],
            ['Test ϰϰ', 'Test fòô bàř', 7, 'ϰϰ', 'UTF-8'],
            ['Teϰϰ', 'Test fòô bàř', 4, 'ϰϰ', 'UTF-8'],
            ['What are your pl...', 'What are your plans today?', 19, '...'],
        ];
    }

    /**
     * @return array
     */
    public function underscoredProvider(): array
    {
        return [
            ['test_case', 'testCase'],
            ['test_case', 'Test-Case'],
            ['test_case', 'test case'],
            ['test_case', 'test -case'],
            ['_test_case', '-test - case'],
            ['test_case', 'test_case'],
            ['test_c_test', '  test c test'],
            ['test_u_case', 'TestUCase'],
            ['test_c_c_test', 'TestCCTest'],
            ['string_with1number', 'string_with1number'],
            ['string_with_2_2_numbers', 'String-with_2_2 numbers'],
            ['1test2case', '1test2case'],
            ['yes_we_can', 'yesWeCan'],
            ['test_σase', 'test Σase', 'UTF-8'],
            ['στανιλ_case', 'Στανιλ case', 'UTF-8'],
            ['σash_case', 'Σash  Case', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function upperCamelizeProvider(): array
    {
        return [
            ['CamelCase', 'camelCase'],
            ['CamelCase', 'Camel-Case'],
            ['CamelCase', 'camel case'],
            ['CamelCase', 'camel -case'],
            ['CamelCase', 'camel - case'],
            ['CamelCase', 'camel_case'],
            ['CamelCTest', 'camel c test'],
            ['StringWith1Number', 'string_with1number'],
            ['StringWith22Numbers', 'string-with-2-2 numbers'],
            ['1Camel2Case', '1camel2case'],
            ['CamelΣase', 'camel σase', 'UTF-8'],
            ['ΣτανιλCase', 'στανιλ case', 'UTF-8'],
            ['ΣamelCase', 'Σamel  Case', 'UTF-8'],
        ];
    }

    /**
     * @return array
     */
    public function upperCaseFirstProvider(): array
    {
        return [
            ['Test', 'Test'],
            ['Test', 'test'],
            ['1a', '1a'],
            ['Σ test', 'σ test', 'UTF-8'],
            [' σ test', ' σ test', 'UTF-8'],
        ];
    }

    /**
     * @param $haystack
     * @param $needle
     * @param $expected
     * @param $mainEncoding
     * @param $encodingParameter
     * @dataProvider strBeginsProvider
     */
    public function testStrBegins($haystack, $needle, $expected, $mainEncoding, $encodingParameter)
    {
        static::assertSame(
            $expected,
            S::create($haystack, $encodingParameter)->startsWith($needle)
        );
    }

    /**
     * @return array
     */
    public function strBeginsProvider()
    {
        $euc_jp = '0123この文字列は日本語です。EUC-JPを使っています。0123日本語は面倒臭い。';
        $string_ascii = 'abc def';
        $string_mb = \base64_decode('5pel5pys6Kqe44OG44Kt44K544OI44Gn44GZ44CCMDEyMzTvvJXvvJbvvJfvvJjvvJnjgII=', true);

        return [
            [$euc_jp, '0123こ', true, 'UTF-8', 'EUC-JP'],
            [$euc_jp, '韓国語', false, 'UTF-8', 'EUC-JP'],
            [$euc_jp, '0123', true, 'EUC-JP', null],
            [$euc_jp, '韓国語', false, 'EUC-JP', null],
            [$euc_jp, '', true, 'UTF-8', 'EUC-JP'],
            [$string_ascii, 'a', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'A', false, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'b', false, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, '', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'abc', true, 'UTF-8', null],
            [$string_ascii, 'bc', false, 'UTF-8', null],
            [$string_ascii, '', true, 'UTF-8', null],
            [$string_mb, \base64_decode('5pel5pys6Kqe', true), true, 'UTF-8', null],
            [$string_mb, \base64_decode('44GT44KT44Gr44Gh44Gv44CB5LiW55WM', true), false, 'UTF-8', null],
            [$string_mb, '', true, 'UTF-8', null],
            ['Τὴ γλῶσσα μοῦ ἔδωσαν ἑλληνικὴ', 'ΤῊ', false, 'UTF-8', null],
        ];
    }

    /**
     * @param $haystack
     * @param $needle
     * @param $expected
     * @param $mainEncoding
     * @param $encodingParameter
     * @dataProvider strEndsProvider
     */
    public function testStrEnds($haystack, $needle, $expected, $mainEncoding, $encodingParameter)
    {
        static::assertSame(
            $expected,
            S::create($haystack, $encodingParameter)->endsWith($needle)
        );
    }

    /**
     * @return array
     */
    public function strEndsProvider()
    {
        $euc_jp = '0123この文字列は日本語です。EUC-JPを使っています。0123日本語は面倒臭い。';
        $string_ascii = 'abc def';
        $string_mb = \base64_decode('5pel5pys6Kqe44OG44Kt44K544OI44Gn44GZ44CCMDEyMzTvvJXvvJbvvJfvvJjvvJnjgII=', true);

        return [
            [$euc_jp, 'い。', true, 'UTF-8', 'EUC-JP'],
            [$euc_jp, '韓国語', false, 'UTF-8', 'EUC-JP'],
            [$euc_jp, 'い。', true, 'EUC-JP', null],
            [$euc_jp, '韓国語', false, 'EUC-JP', null],
            [$euc_jp, '', true, 'UTF-8', 'EUC-JP'],
            [$string_ascii, 'f', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'F', false, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'e', false, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, '', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'def', true, 'UTF-8', null],
            [$string_ascii, 'de', false, 'UTF-8', null],
            [$string_ascii, '', true, 'UTF-8', null],
            [$string_mb, \base64_decode('77yZ44CC', true), true, 'UTF-8', null],
            [$string_mb, \base64_decode('44GT44KT44Gr44Gh44Gv44CB5LiW55WM', true), false, 'UTF-8', null],
            [$string_mb, '', true, 'UTF-8', null],
            ['Τὴ γλῶσσα μοῦ ἔδωσαν ἑλληνικὴ', 'ἙΛΛΗΝΙΚῊ', false, 'UTF-8', null],
        ];
    }

    /**
     * @param $haystack
     * @param $needle
     * @param $expected
     * @param $mainEncoding
     * @param $encodingParameter
     * @dataProvider strIbeginsProvider
     */
    public function testStrIbegins($haystack, $needle, $expected, $mainEncoding, $encodingParameter)
    {
        static::assertSame(
            $expected,
            S::create($haystack, $encodingParameter)->startsWith($needle, false)
        );
    }

    /**
     * @return array
     */
    public function strIbeginsProvider()
    {
        $euc_jp = '0123この文字列は日本語です。EUC-JPを使っています。0123日本語は面倒臭い。';
        $string_ascii = 'abc def';
        $string_mb = \base64_decode('5pel5pys6Kqe44OG44Kt44K544OI44Gn44GZ44CCMDEyMzTvvJXvvJbvvJfvvJjvvJnjgII=', true);

        return [
            [$euc_jp, '0123こ', true, 'UTF-8', 'EUC-JP'],
            [$euc_jp, '韓国語', false, 'UTF-8', 'EUC-JP'],
            [$euc_jp, '0123', true, 'EUC-JP', null],
            [$euc_jp, '韓国語', false, 'EUC-JP', null],
            [$euc_jp, '', true, 'UTF-8', 'EUC-JP'],
            [$string_ascii, 'a', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'A', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'b', false, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, '', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'abc', true, 'UTF-8', null],
            [$string_ascii, 'AbC', true, 'UTF-8', null],
            [$string_ascii, 'bc', false, 'UTF-8', null],
            [$string_ascii, '', true, 'UTF-8', null],
            [$string_mb, \base64_decode('5pel5pys6Kqe', true), true, 'UTF-8', null],
            [$string_mb, \base64_decode('44GT44KT44Gr44Gh44Gv44CB5LiW55WM', true), false, 'UTF-8', null],
            [$string_mb, '', true, 'UTF-8', null],
            ['Τὴ γλῶσσα μοῦ ἔδωσαν ἑλληνικὴ', 'ΤῊ', true, 'UTF-8', null],
        ];
    }

    /**
     * @param $haystack
     * @param $needle
     * @param $expected
     * @param $mainEncoding
     * @param $encodingParameter
     * @dataProvider strIendsProvider
     */
    public function testStrIends($haystack, $needle, $expected, $mainEncoding, $encodingParameter)
    {
        static::assertSame(
            $expected,
            S::create($haystack, $encodingParameter)->endsWith($needle, false)
        );
    }

    public function testFormatPassthrough()
    {
        $result = \Stringy\create('One: %d, %s: 2, %s: %d')->format(1, 'two', 'three', 3);
        static::assertEquals('One: 1, two: 2, three: 3', (string) $result);

        $result = \Stringy\create('One: %2$d, %1$s: 2, %3$s: %4$d')->format('two', 1, 'three', 3);
        static::assertEquals('One: 1, two: 2, three: 3', (string) $result);
    }

    public function testFormatNamedAndUnnamed()
    {
        $result = \Stringy\create('One: %d, %:text_two: 2, %s: %d')->format(1, 'three', 3, ['text_two' => 'two']);
        static::assertEquals('One: 1, two: 2, three: 3', (string) $result);
    }

    public function testFormatOnlyNamed()
    {
        $result = \Stringy\create('One: %:1, %:text_two: 2, %:text_three: %:3')->format(['1' => '1'], ['text_two' => 'two', 'text_three' => 'three', '3' => 3]);
        static::assertEquals('One: 1, two: 2, three: 3', (string) $result);
    }

    public function testFormatInvalidNamed()
    {
        $result = \Stringy\create('One: %:1, %:text_two: 2, %:text_three: %:3')->format(['text_three' => 'three', '1' => 1]);
        static::assertEquals('One: %:1, %:text_two: 2, three: %:3', (string) $result);
    }

    public function testFormatComplexNamed()
    {
        $result = \Stringy\create('One: %:1, %:text_two: 2, %:text_three: %:3')->format(['text_three' => '%s', '1' => 1], 'three');
        static::assertEquals('One: %:1, %:text_two: 2, three: %:3', (string) $result);

        $result = \Stringy\create('One: %2$d, %1$s: 2, %:text_three: %3$d')->format('two', 1, 3, ['text_three' => 'three']);
        static::assertEquals('One: 1, two: 2, three: 3', (string) $result);

        $result = \Stringy\create('One: %2$d, %1$s: 2, %:text_three: %3$d')->format('two', 1, 3, 'three', ['text_three' => '%4$s']);
        static::assertEquals('One: 1, two: 2, three: 3', (string) $result);

        $result = \Stringy\create('One: %2$d, %1$s: 2, %:text_three: %3$d')->format(['text_three' => '%4$s'], 'two', 1, 3, 'three');
        static::assertEquals('One: 1, two: 2, three: 3', (string) $result);
    }

    /**
     * @return array
     */
    public function strIendsProvider()
    {
        $euc_jp = '0123この文字列は日本語です。EUC-JPを使っています。0123日本語は面倒臭い。';
        $string_ascii = 'abc def';
        $string_mb = \base64_decode('5pel5pys6Kqe44OG44Kt44K544OI44Gn44GZ44CCMDEyMzTvvJXvvJbvvJfvvJjvvJnjgII=', true);

        return [
            [$euc_jp, 'い。', true, 'UTF-8', 'EUC-JP'],
            [$euc_jp, '韓国語', false, 'UTF-8', 'EUC-JP'],
            [$euc_jp, 'い。', true, 'EUC-JP', null],
            [$euc_jp, '韓国語', false, 'EUC-JP', null],
            [$euc_jp, '', true, 'UTF-8', 'EUC-JP'],
            [$string_ascii, 'f', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'F', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'e', false, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, '', true, 'UTF-8', 'ISO-8859-1'],
            [$string_ascii, 'def', true, 'UTF-8', null],
            [$string_ascii, 'DeF', true, 'UTF-8', null],
            [$string_ascii, 'de', false, 'UTF-8', null],
            [$string_ascii, '', true, 'UTF-8', null],
            [$string_mb, \base64_decode('77yZ44CC', true), true, 'UTF-8', null],
            [$string_mb, \base64_decode('44GT44KT44Gr44Gh44Gv44CB5LiW55WM', true), false, 'UTF-8', null],
            [$string_mb, '', true, 'UTF-8', null],
            // ['Τὴ γλῶσσα μοῦ ἔδωσαν ἑλληνικὴ', 'ἙΛΛΗΝΙΚῊ', true, 'UTF-8', null], // php 7.3 thingy
        ];
    }

    public function testItCanGetEveryNthCharacter()
    {
        $string = \Stringy\create('john pinkerton');
        $nth = $string->nth(3);
        static::assertInstanceOf(\Stringy\Stringy::class, $nth);
        static::assertEquals('jnieo', $nth);
    }

    public function testItCanGetEveryNthCharacterStartingAtAnOffset()
    {
        $string = \Stringy\create('john pinkerton');
        $nth = $string->nth(3, 2);
        static::assertInstanceOf(\Stringy\Stringy::class, $nth);
        static::assertEquals('hpkt', $nth);
    }

    public function testItCanGetEveryNthCharacterOfAMultibyteString()
    {
        $string = \Stringy\create('宮本 任天堂 茂');
        $nth = $string->nth(3);
        static::assertInstanceOf(\Stringy\Stringy::class, $nth);
        static::assertEquals('宮任 ', $nth);
    }

    public function testItPreservesEncodingNth()
    {
        $string = \Stringy\create('john pinkerton', 'ASCII');
        $nth = $string->nth(3);
        static::assertSame('ASCII', $nth->getEncoding());
    }

    public function testCrc32()
    {
        $content = 'john pinkerton';
        $string = new \Stringy\Stringy($content);
        $crc32 = $string->crc32();
        static::assertEquals(\crc32($content), $crc32);

        // ---

        $content = '宮本 茂';
        $string = new \Stringy\Stringy($content);
        $crc32 = $string->crc32();
        static::assertEquals(\crc32($content), $crc32);
    }

    public function testSha1()
    {
        $content = '宮本 茂';
        $string = new \Stringy\Stringy($content);
        $hash = $string->sha1();
        static::assertEquals(\sha1($content), $hash);
    }

    public function testSha256()
    {
        $content = '宮本 茂';
        $string = new \Stringy\Stringy($content);
        $hash = $string->sha256();
        static::assertEquals(\hash('sha256', $content), $hash);
    }

    public function testSha512()
    {
        $content = '宮本 茂';
        $string = new \Stringy\Stringy($content);
        $hash = $string->sha512();
        static::assertEquals(\hash('sha512', $content), $hash);
    }

    public function testMd5()
    {
        $content = '宮本 茂';
        $string = new \Stringy\Stringy($content);
        $hash = $string->md5();
        static::assertEquals(\hash('md5', $content), $hash);

        // ---

        $content = '宮本 茂';
        $string = new \Stringy\Stringy($content);
        $hash = $string->hash('md5');
        static::assertEquals(\hash('md5', $content), $hash);
    }

    public function testItCanDetermineIfItDoesNotExistInAnotherString()
    {
        $string = new \Stringy\Stringy('purple');
        $notIn = $string->in('john pinkerton');
        static::assertFalse($notIn);
    }

    public function testItCanDetermineIfItExistsInAnotherStringCaseInsensitive()
    {
        $string = new \Stringy\Stringy('Pink');
        $in = $string->in('john pinkerton', false);
        static::assertTrue($in);
    }

    public function testItCanDetermineIfAMultibyteStringExistsInAnotherString()
    {
        $string = new \Stringy\Stringy('任天堂');
        $in = $string->in('宮本 任天堂 茂 ');
        static::assertTrue($in);
    }

    public function testItCanBeSensitivelyMatched()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $matches = $string->matchCaseSensitive('JoHN PiNKeRToN');
        $differs = $string->matchCaseSensitive('BoB BeLCHeR');
        static::assertFalse($matches);
        static::assertFalse($differs);

        // ---

        $string = new \Stringy\Stringy('john pinkerton');
        $matches = $string->matchCaseInsensitive('john pinkerton');
        $differs = $string->matchCaseInsensitive('BoB BeLCHeR');
        static::assertTrue($matches);
        static::assertFalse($differs);
    }

    public function testItCanBeInsensitivelyMatched()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $matches = $string->matchCaseInsensitive('JoHN PiNKeRToN');
        $differs = $string->matchCaseInsensitive('BoB BeLCHeR');
        static::assertTrue($matches);
        static::assertFalse($differs);
    }

    public function testAMultibyteStringCanBeInsensitivelyMatched()
    {
        $string = new \Stringy\Stringy('宮本 茂');
        $matches = $string->matchCaseInsensitive('宮本 茂');
        $differs = $string->matchCaseInsensitive('任天堂');
        static::assertTrue($matches);
        static::assertFalse($differs);
    }

    public function testItCanBeStripped()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $striped = $string->strip('pink');
        static::assertInstanceOf(\Stringy\Stringy::class, $striped);
        static::assertEquals('john erton', $striped);
    }

    public function testItCanStripMultipleStringsFromTheString()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $striped = $string->strip(['a', 'e', 'i', 'o', 'u']);
        static::assertInstanceOf(\Stringy\Stringy::class, $striped);
        static::assertEquals('jhn pnkrtn', $striped);
    }

    public function testItIsMultibyteCompatible()
    {
        $string = new \Stringy\Stringy('宮本 茂');
        $stripped = $string->strip('本');
        static::assertInstanceOf(\Stringy\Stringy::class, $stripped);
        static::assertEquals('宮 茂', $stripped);
    }

    public function testItCanBeSoftWrapped()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $wrappedSoft = $string->softWrap(5);
        static::assertInstanceOf(\Stringy\Stringy::class, $wrappedSoft);
        static::assertEquals("john\npinkerton", $wrappedSoft);
    }

    public function testItPreservesEncodingSoftWrap()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $wrappedSoft = $string->softWrap(5);
        static::assertSame('ASCII', $wrappedSoft->getEncoding());
    }

    public function testItCanBeHardWrapped()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $wrappedHard = $string->hardWrap(5);
        static::assertInstanceOf(\Stringy\Stringy::class, $wrappedHard);
        static::assertEquals("john\npinke\nrton", $wrappedHard);
    }

    public function testItPreservesEncodingHardWrap()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $wrappedHard = $string->hardWrap(5);
        static::assertSame('ASCII', $wrappedHard->getEncoding());
    }

    public function testItCanBeHexEncoded()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $hex = $string->hexEncode();
        static::assertInstanceOf(\Stringy\Stringy::class, $hex);
        static::assertEquals('U+006aU+006fU+0068U+006eU+0020U+0070U+0069U+006eU+006bU+0065U+0072U+0074U+006fU+006e', $hex->toString());
    }

    public function testAMultibyteStringCanBeHexEncoded()
    {
        $string = new \Stringy\Stringy('宮本 茂');
        $hex = $string->hexEncode();
        static::assertInstanceOf(\Stringy\Stringy::class, $hex);
        static::assertEquals('U+5baeU+672cU+0020U+8302', $hex);
    }

    public function testItCanBeHexDecoded()
    {
        $string = new \Stringy\Stringy('\x6a\x6f\x68\x6e\x20\x70\x69\x6e\x6b\x65\x72\x74\x6f\x6e');
        $plaintext = $string->hexDecode();
        static::assertInstanceOf(\Stringy\Stringy::class, $plaintext);
        static::assertEquals('john pinkerton', $plaintext);
    }

    public function testAMultibyteStringCanBeHexDecoded()
    {
        $string = new \Stringy\Stringy('\x5bae\x672c\x20\x8302');
        $plaintext = $string->hexDecode();
        static::assertInstanceOf(\Stringy\Stringy::class, $plaintext);
        static::assertEquals('宮本 茂', $plaintext);
    }

    public function testItCanBeBase64Decoded()
    {
        $string = new \Stringy\Stringy('am9obiBwaW5rZXJ0b24=');
        $plaintext = $string->base64Decode();
        static::assertInstanceOf(\Stringy\Stringy::class, $plaintext);
        static::assertEquals('john pinkerton', $plaintext);
    }

    public function testAMultibyteStringCanBeBase64Decoded()
    {
        $string = new \Stringy\Stringy('5a6u5pysIOiMgg==');
        $plaintext = $string->base64Decode();
        static::assertInstanceOf(\Stringy\Stringy::class, $plaintext);
        static::assertEquals('宮本 茂', $plaintext);
    }

    public function testItHasCanBeBase64Encoded()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $base64 = $string->base64Encode();
        static::assertInstanceOf(\Stringy\Stringy::class, $base64);
        static::assertEquals('am9obiBwaW5rZXJ0b24=', $base64);
    }

    public function testAMultibyteStringCanBeBase64Encoded()
    {
        $string = new \Stringy\Stringy('宮本 茂');
        $base64 = $string->base64Encode();
        static::assertInstanceOf(\Stringy\Stringy::class, $base64);
        static::assertEquals('5a6u5pysIOiMgg==', $base64);
    }

    public function testItCanBeSplitIntoChunks()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $chunks = $string->chunkCollection(3, true);
        static::assertEquals(['joh', 'n p', 'ink', 'ert', 'on'], $chunks->getArray());
        foreach ($chunks as $chunk) {
            static::assertInstanceOf(\Stringy\Stringy::class, $chunk);
        }
    }

    public function testAMultibyteStringCanBeChunked()
    {
        $string = new \Stringy\Stringy('宮本 任天堂 茂');
        $chunks = $string->chunkCollection(3, true);
        static::assertEquals(['宮本 ', '任天堂', ' 茂'], $chunks->getArray());
        foreach ($chunks as $chunk) {
            static::assertInstanceOf(\Stringy\Stringy::class, $chunk);
        }
    }

    public function testItPreservesEncodingChunk()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $chunks = $string->chunk(3);
        foreach ($chunks as $chunk) {
            static::assertSame('ASCII', $chunk->getEncoding());
        }
    }

    public function testItCanBeExploded()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $exploded = $string->explodeCollection(' ', \PHP_INT_MAX, true);
        static::assertEquals(['john', 'pinkerton'], $exploded->toArray());
        foreach ($exploded as $substring) {
            static::assertInstanceOf(\Stringy\Stringy::class, $substring);
        }
    }

    public function testItCanBeExplodedWithALimit()
    {
        $string = new \Stringy\Stringy('john maurice mcclean pinkerton');
        $exploded = $string->explode(' ', 3);
        static::assertEquals(['john', 'maurice', 'mcclean pinkerton'], $exploded);
        foreach ($exploded as $substring) {
            static::assertInstanceOf(\Stringy\Stringy::class, $substring);
        }
    }

    public function testItPreservesEncodingExplode()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $exploded = $string->explode(' ');
        foreach ($exploded as $string) {
            static::assertSame('ASCII', $string->getEncoding());
        }
    }

    public function testItCanGetPartOfAStringAfterACharacter()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $lastName = $string->after(' ');
        static::assertInstanceOf(\Stringy\Stringy::class, $lastName);
        static::assertEquals('pinkerton', $lastName->toString());
    }

    public function testItCanGetPartOfAStringAfterACharacterWithMultipleDelimiters()
    {
        $string = new \Stringy\Stringy('john pinkerton jr');
        $lastNameAndSuffix = $string->after(' ');
        static::assertInstanceOf(\Stringy\Stringy::class, $lastNameAndSuffix);
        static::assertEquals('pinkerton jr', $lastNameAndSuffix->toString());
    }

    public function testItCanGetPartOfAMultibyteStringAfterAMultibyteString()
    {
        $string = new \Stringy\Stringy('宮本 茂');
        $after = $string->after('本');
        static::assertInstanceOf(\Stringy\Stringy::class, $after);
        static::assertEquals(' 茂', $after->toString());
    }

    public function testItPreservesEncodingAfter()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $lastName = $string->after(' ');
        static::assertSame('ASCII', $lastName->getEncoding());
    }

    public function testItCanGetPartOfAStringBeforeACharacter()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $firstName = $string->before(' ');
        static::assertInstanceOf(\Stringy\Stringy::class, $firstName);
        static::assertEquals('john', $firstName->toString());
    }

    public function testItCanGetPartOfAStringBeforeACharacterWithMultipleDelimiters()
    {
        $string = new \Stringy\Stringy('john pinkerton jr');
        $firstName = $string->before(' ');
        static::assertInstanceOf(\Stringy\Stringy::class, $firstName);
        static::assertEquals('john', $firstName->toString());
    }

    public function testItCanGetPartOfAMultibyteStringBeforeAMltibyteString()
    {
        $string = new \Stringy\Stringy('宮本 茂');
        $firstName = $string->before('本');
        static::assertInstanceOf(\Stringy\Stringy::class, $firstName);
        static::assertEquals('宮', $firstName->toString());
    }

    public function testItPreservesEncodingBefore()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $firstName = $string->before(' ');
        static::assertSame('ASCII', $firstName->getEncoding());
    }

    public function testItCanBeHashedWithBcrypt()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $bcrypt = $string->bcrypt();
        static::assertInstanceOf(\Stringy\Stringy::class, $bcrypt);
        if (\method_exists(__CLASS__, 'assertMatchesRegularExpression')) {
            static::assertMatchesRegularExpression('/\$2y\$10\$[a-zA-Z0-9+.\/]{53}/', (string) $bcrypt);
        } else {
            static::assertRegExp('/\$2y\$10\$[a-zA-Z0-9+.\/]{53}/', (string) $bcrypt);
        }
    }

    public function testAMultibyteStringCanBeHashedWithBcrypt()
    {
        $string = new \Stringy\Stringy('宮本 茂');
        $bcrypt = $string->bcrypt([\PASSWORD_BCRYPT_DEFAULT_COST]);
        static::assertInstanceOf(\Stringy\Stringy::class, $bcrypt);
        if (\method_exists(__CLASS__, 'assertMatchesRegularExpression')) {
            static::assertMatchesRegularExpression('/\$2y\$10\$[a-zA-Z0-9+.\/]{53}/', (string) $bcrypt);
        } else {
            static::assertRegExp('/\$2y\$10\$[a-zA-Z0-9+.\/]{53}/', (string) $bcrypt);
        }
    }

    public function testItPreservesEncodingBcrypt()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $bcrypt = $string->bcrypt();
        static::assertSame('ASCII', $bcrypt->getEncoding());
    }

    public function testItCanBeHashedWithCrypt()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $crypt = $string->crypt('NaCl');
        static::assertInstanceOf(\Stringy\Stringy::class, $crypt);
        static::assertEquals('Naq9mOMsN7Yac', $crypt);
    }

    public function testAMultibyteStringCanBeHashedWithCrypt()
    {
        $string = new \Stringy\Stringy('宮本 茂');
        $crypt = $string->crypt('NaCl');
        static::assertInstanceOf(\Stringy\Stringy::class, $crypt);
        static::assertEquals('NaJqmdk5MYCgs', $crypt);
    }

    public function testItPreservesEncodingCrypt()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $crypt = $string->crypt('NaCL');
        static::assertSame('ASCII', $crypt->getEncoding());
    }

    public function testItCanBeDecrypted()
    {
        $encryptedString = (new \Stringy\Stringy('john pinkerton'))->encrypt('secret');

        $string = new \Stringy\Stringy($encryptedString);
        $decrypted = $string->decrypt('secret');
        static::assertInstanceOf(\Stringy\Stringy::class, $decrypted);
        static::assertEquals('john pinkerton', $decrypted);
    }

    public function testItThrowsAnExceptionWhenDecryptingAnInvalidString()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $this->expectException(\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException::class);
        $string->decrypt('secret');
    }

    public function testItThrowsAnExceptionWhenDecryptionFails()
    {
        $encryptedString = (new \Stringy\Stringy('john pinkerton'))->encrypt('secret');

        $string = new \Stringy\Stringy($encryptedString);
        $this->expectException(\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException::class);
        $string->decrypt('shmecret');
    }

    public function testAMultibyteStringCanBeDecrypted()
    {
        $encryptedString = (new \Stringy\Stringy('宮本 茂'))->encrypt('任天堂');

        $string = new \Stringy\Stringy($encryptedString);
        $decrypted = $string->decrypt('任天堂');
        static::assertInstanceOf(\Stringy\Stringy::class, $decrypted);
        static::assertEquals('宮本 茂', $decrypted);
    }

    public function testExtractIntegers()
    {
        $intString = (new \Stringy\Stringy('宮1本 茂2.3'))->extractIntegers();

        static::assertInstanceOf(\Stringy\Stringy::class, $intString);
        static::assertEquals('123', $intString);

        // --

        $intString = (new \Stringy\Stringy('宮本 茂'))->extractIntegers();

        static::assertInstanceOf(\Stringy\Stringy::class, $intString);
        static::assertEquals('', $intString);

        // --

        $intString = (new \Stringy\Stringy(''))->extractIntegers();

        static::assertInstanceOf(\Stringy\Stringy::class, $intString);
        static::assertEquals('', $intString);
    }

    public function testExtractSpecialCharacters()
    {
        $str = (new \Stringy\Stringy('宮!1本 *?茂2.3'))->extractSpecialCharacters();

        static::assertInstanceOf(\Stringy\Stringy::class, $str);
        static::assertEquals('!*?.', $str);

        // --

        $str = (new \Stringy\Stringy('宮本 茂'))->extractSpecialCharacters();

        static::assertInstanceOf(\Stringy\Stringy::class, $str);
        static::assertEquals('', $str);

        // --

        $str = (new \Stringy\Stringy(''))->extractSpecialCharacters();

        static::assertInstanceOf(\Stringy\Stringy::class, $str);
        static::assertEquals('', $str);
    }

    public function testItPreservesEncodingDecrypt()
    {
        $encryptedString = (new \Stringy\Stringy('john pinkerton'))->encrypt('secret');

        $string = new \Stringy\Stringy($encryptedString, 'ASCII');
        $decrypted = $string->decrypt('secret');
        static::assertSame('ASCII', $decrypted->getEncoding());
    }

    public function testItCanSetTheInternalEncoding()
    {
        $string = new \Stringy\Stringy('john pinkerton 宮本');
        $ascii = $string->setInternalEncoding('ASCII');
        static::assertInstanceOf(\Stringy\Stringy::class, $ascii);
        static::assertSame('john pinkerton 宮本', $ascii->toString());
        static::assertSame('ASCII', $ascii->getEncoding());
    }

    public function testItCanUseNewEncoding()
    {
        $string = new \Stringy\Stringy('john pinkerton 宮本');
        $ascii = $string->encode('ASCII');
        static::assertInstanceOf(\Stringy\Stringy::class, $ascii);
        static::assertSame('john pinkerton ??', $ascii->toString());
        static::assertSame('ASCII', $ascii->getEncoding());
    }

    public function testItCanUseNewEncodingv2()
    {
        $string = new \Stringy\Stringy('john pinkerton 宮本');
        $ascii = $string->encode('HTML', true);
        static::assertInstanceOf(\Stringy\Stringy::class, $ascii);
        static::assertSame('john pinkerton &#23470;&#26412;', $ascii->toString());
        static::assertSame('HTML-ENTITIES', $ascii->getEncoding());
    }

    public function testItCanDetermineIfTheStringIsNumeric()
    {
        $string = new \Stringy\Stringy('1337');
        $numeric = $string->isNumeric();
        static::assertTrue($numeric);
    }

    public function testItCanDetermineIfTheNegativStringIsNumeric()
    {
        $string = new \Stringy\Stringy('-1337');
        $numeric = $string->isNumeric();
        static::assertTrue($numeric);
    }

    public function testItCanDetermineIfTheStringIsNotNumeric()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $notNumeric = $string->isNumeric();
        static::assertFalse($notNumeric);
    }

    public function testItCanDetermineIfAMultibyteStringIsNumeric()
    {
        $string = new \Stringy\Stringy('任天堂');
        $numeric = $string->isNumeric();
        static::assertFalse($numeric);
    }

    public function testItCanDetermineIfTheStringIsPrintable()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $printable = $string->isPrintable();
        static::assertTrue($printable);
    }

    public function testItCanDetermineIfTheStringIsNotPrintable()
    {
        $string = new \Stringy\Stringy("john\0pinkerton");
        $notPrintable = $string->isPrintable();
        static::assertFalse($notPrintable);
    }

    public function testItCanDetermineIfAMultibyteStringIsPrintable()
    {
        $string = new \Stringy\Stringy('任天堂');
        $printable = $string->isPrintable();
        static::assertTrue($printable);
    }

    public function testItCanDetermineIfTheStringIsPunctuation()
    {
        $string = new \Stringy\Stringy('*&$();,.?');
        $punctuation = $string->isPunctuation();
        static::assertTrue($punctuation);
    }

    public function testItCanDetermineIfTheStringIsNotPunctuation()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $notPunctuation = $string->isPunctuation();
        static::assertFalse($notPunctuation);
    }

    public function testItCanDetermineIfAMultibyteStringIsPunctuation()
    {
        $string = new \Stringy\Stringy('任天堂');
        $punctuation = $string->isPunctuation();
        static::assertFalse($punctuation);
    }

    public function testItCanConvertToStudlyCase()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $studlyCase = $string->studlyCase();
        static::assertInstanceOf(\Stringy\Stringy::class, $studlyCase);
        static::assertEquals('JohnPinkerton', $studlyCase->toString());
    }

    public function testItAMultibyteStringCanBeConvertedToStudlyCase()
    {
        $string = new \Stringy\Stringy('джон пинкертон');
        $studlyCase = $string->studlyCase();
        static::assertInstanceOf(\Stringy\Stringy::class, $studlyCase);
        static::assertEquals('ДжонПинкертон', $studlyCase);
    }

    public function testItPreservesEncodingStudlyCase()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $studlyCase = $string->studlyCase();
        static::assertSame('ASCII', $studlyCase->getEncoding());
    }

    public function testItCanConvertToPascalCase()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $pascalCase = $string->pascalCase();
        static::assertInstanceOf(\Stringy\Stringy::class, $pascalCase);
        static::assertEquals('JohnPinkerton', $pascalCase);
    }

    public function testItAMultibyteStringCanBeConvertedToPascalCase()
    {
        $string = new \Stringy\Stringy('джон пинкертон');
        $pascalCase = $string->pascalCase();
        static::assertInstanceOf(\Stringy\Stringy::class, $pascalCase);
        static::assertEquals('ДжонПинкертон', $pascalCase);
    }

    public function testItPreservesEncodingPascalCase()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $pascalCase = $string->pascalCase();
        static::assertSame('ASCII', $pascalCase->getEncoding());
    }

    public function testItCanConvertToSnakeCase()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $snakeCase = $string->snakeCase();
        static::assertInstanceOf(\Stringy\Stringy::class, $snakeCase);
        static::assertEquals('john_pinkerton', $snakeCase);
    }

    public function testItAMultibyteStringCanBeConvertedToSnakeCase()
    {
        $string = new \Stringy\Stringy('джон пинкертон');
        $snakeCase = $string->snakeCase();
        static::assertInstanceOf(\Stringy\Stringy::class, $snakeCase);
        static::assertEquals('джон_пинкертон', $snakeCase);
    }

    public function testItPreservesEncodingSnakeCase()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $snakeCase = $string->snakeCase();
        static::assertSame('ASCII', $snakeCase->getEncoding());
    }

    public function testItCanConvertToKebabCase()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $kebabCase = $string->kebabCase();
        static::assertInstanceOf(\Stringy\Stringy::class, $kebabCase);
        static::assertEquals('john-pinkerton', $kebabCase);
    }

    public function testItAMultibyteStringCanBeConvertedToKebabCase()
    {
        $string = new \Stringy\Stringy('джон пинкертон');
        $kebabCase = $string->kebabCase();
        static::assertInstanceOf(\Stringy\Stringy::class, $kebabCase);
        static::assertEquals('джон-пинкертон', $kebabCase);
    }

    public function testItPreservesEncodingKebabCase()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $kebabCase = $string->kebabCase();
        static::assertSame('ASCII', $kebabCase->getEncoding());
    }

    public function testItCanReturnASubstring()
    {
        $string = new \Stringy\Stringy('john pinkerton');
        $substring = $string->substring(5);
        $partial = $string->substring(5, 4);
        static::assertInstanceOf(\Stringy\Stringy::class, $substring);
        static::assertEquals('pinkerton', $substring);
        static::assertEquals('pink', $partial);
    }

    public function testItCanReturnAMultibyteSubstring()
    {
        $string = new \Stringy\Stringy('宮本 茂');
        $substring = $string->substring(1);
        $partial = $string->substring(1, 2);
        static::assertInstanceOf(\Stringy\Stringy::class, $substring);
        static::assertEquals('本 茂', $substring);
        static::assertEquals('本 ', $partial);
    }

    public function testItPreservesEncodingSubstring()
    {
        $string = new \Stringy\Stringy('john pinkerton', 'ASCII');
        $substring = $string->substring(5, 4);
        static::assertSame('ASCII', $substring->getEncoding());
    }

    public function testUrlDecodeMulti()
    {
        $testArray = [
            'W%F6bse'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            => 'Wöbse',
            'Ã'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => 'Ã',
            'Ã¤'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 => 'ä',
            ' '                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => ' ',
            ''                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   => '',
            "\n"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 => "\n",
            "\u00ed"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'í',
            'tes%20öäü%20\u00edtest+test'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        => 'tes öäü ítest test',
            'test+test@foo.bar'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => 'test test@foo.bar',
            'con%5cu00%366irm'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   => 'confirm',
            '%3A%2F%2F%252567%252569%252573%252574'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              => '://gist',
            '%253A%252F%252F%25252567%25252569%25252573%25252574'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                => '://gist',
            "tes%20öäü%20\u00edtest"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'tes öäü ítest',
            'Düsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         => 'Düsseldorf',
            'Duesseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        => 'Duesseldorf',
            'D&#252;sseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    => 'Düsseldorf',
            'D%FCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       => 'Düsseldorf',
            'D&#xFC;sseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    => 'Düsseldorf',
            'D%26%23xFC%3Bsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              => 'Düsseldorf',
            'DÃ¼sseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        => 'Düsseldorf',
            'D%C3%BCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    => 'Düsseldorf',
            'D%C3%83%C2%BCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              => 'Düsseldorf',
            'D%25C3%2583%25C2%25BCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      => 'Düsseldorf',
            '<strong>D&#252;sseldorf</strong>'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   => '<strong>Düsseldorf</strong>',
            'Hello%2BWorld%2B%253E%2Bhow%2Bare%2Byou%253F'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       => 'Hello World > how are you?',
            '%e7%ab%a0%e5%ad%90%e6%80%a1'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        => '章子怡',
            'Fran%c3%a7ois Truffaut'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'François Truffaut',
            '%e1%83%a1%e1%83%90%e1%83%a5%e1%83%90%e1%83%a0%e1%83%97%e1%83%95%e1%83%94%e1%83%9a%e1%83%9d'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         => 'საქართველო',
            '%25e1%2583%25a1%25e1%2583%2590%25e1%2583%25a5%25e1%2583%2590%25e1%2583%25a0%25e1%2583%2597%25e1%2583%2595%25e1%2583%2594%25e1%2583%259a%25e1%2583%259d'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'საქართველო',
            '%2525e1%252583%2525a1%2525e1%252583%252590%2525e1%252583%2525a5%2525e1%252583%252590%2525e1%252583%2525a0%2525e1%252583%252597%2525e1%252583%252595%2525e1%252583%252594%2525e1%252583%25259a%2525e1%252583%25259d'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 => 'საქართველო',
            'Bj%c3%b6rk Gu%c3%b0mundsd%c3%b3ttir'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                => 'Björk Guðmundsdóttir',
            '%e5%ae%ae%e5%b4%8e%e3%80%80%e9%a7%bf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               => '宮崎　駿',
            '%u7AE0%u5B50%u6021'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 => '章子怡',
            '%u0046%u0072%u0061%u006E%u00E7%u006F%u0069%u0073%u0020%u0054%u0072%u0075%u0066%u0066%u0061%u0075%u0074'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'François Truffaut',
            '%u10E1%u10D0%u10E5%u10D0%u10E0%u10D7%u10D5%u10D4%u10DA%u10DD'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       => 'საქართველო',
            '%u0042%u006A%u00F6%u0072%u006B%u0020%u0047%u0075%u00F0%u006D%u0075%u006E%u0064%u0073%u0064%u00F3%u0074%u0074%u0069%u0072'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           => 'Björk Guðmundsdóttir',
            '%u5BAE%u5D0E%u3000%u99FF'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           => '宮崎　駿',
            '&#31456;&#23376;&#24609;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           => '章子怡',
            '&#70;&#114;&#97;&#110;&#231;&#111;&#105;&#115;&#32;&#84;&#114;&#117;&#102;&#102;&#97;&#117;&#116;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => 'François Truffaut',
            '&#4321;&#4304;&#4325;&#4304;&#4320;&#4311;&#4309;&#4308;&#4314;&#4317;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'საქართველო',
            '&#66;&#106;&#246;&#114;&#107;&#32;&#71;&#117;&#240;&#109;&#117;&#110;&#100;&#115;&#100;&#243;&#116;&#116;&#105;&#114;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              => 'Björk Guðmundsdóttir',
            '&#23470;&#23822;&#12288;&#39423;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   => '宮崎　駿',
            'https://foo.bar/tpl_preview.php?pid=122&json=%7B%22recipe_id%22%3A-1%2C%22recipe_created%22%3A%22%22%2C%22recipe_title%22%3A%22vxcvxc%22%2C%22recipe_description%22%3A%22%22%2C%22recipe_yield%22%3A0%2C%22recipe_prepare_time%22%3A0%2C%22recipe_image%22%3A%22%22%2C%22recipe_legal%22%3A0%2C%22recipe_live%22%3A0%2C%22recipe_user_guid%22%3A%22%22%2C%22recipe_category_id%22%3A%5B%5D%2C%22recipe_category_name%22%3A%5B%5D%2C%22recipe_variety_id%22%3A%5B%5D%2C%22recipe_variety_name%22%3A%5B%5D%2C%22recipe_tag_id%22%3A%5B%5D%2C%22recipe_tag_name%22%3A%5B%5D%2C%22recipe_instruction_id%22%3A%5B%5D%2C%22recipe_instruction_text%22%3A%5B%5D%2C%22recipe_ingredient_id%22%3A%5B%5D%2C%22recipe_ingredient_name%22%3A%5B%5D%2C%22recipe_ingredient_amount%22%3A%5B%5D%2C%22recipe_ingredient_unit%22%3A%5B%5D%2C%22formMatchingArray%22%3A%7B%22unites%22%3A%5B%22Becher%22%2C%22Beete%22%2C%22Beutel%22%2C%22Blatt%22%2C%22Bl%5Cu00e4tter%22%2C%22Bund%22%2C%22B%5Cu00fcndel%22%2C%22cl%22%2C%22cm%22%2C%22dicke%22%2C%22dl%22%2C%22Dose%22%2C%22Dose%5C%2Fn%22%2C%22d%5Cu00fcnne%22%2C%22Ecke%28n%29%22%2C%22Eimer%22%2C%22einige%22%2C%22einige+Stiele%22%2C%22EL%22%2C%22EL%2C+geh%5Cu00e4uft%22%2C%22EL%2C+gestr.%22%2C%22etwas%22%2C%22evtl.%22%2C%22extra%22%2C%22Fl%5Cu00e4schchen%22%2C%22Flasche%22%2C%22Flaschen%22%2C%22g%22%2C%22Glas%22%2C%22Gl%5Cu00e4ser%22%2C%22gr.+Dose%5C%2Fn%22%2C%22gr.+Fl.%22%2C%22gro%5Cu00dfe%22%2C%22gro%5Cu00dfen%22%2C%22gro%5Cu00dfer%22%2C%22gro%5Cu00dfes%22%2C%22halbe%22%2C%22Halm%28e%29%22%2C%22Handvoll%22%2C%22K%5Cu00e4stchen%22%2C%22kg%22%2C%22kl.+Bund%22%2C%22kl.+Dose%5C%2Fn%22%2C%22kl.+Glas%22%2C%22kl.+Kopf%22%2C%22kl.+Scheibe%28n%29%22%2C%22kl.+St%5Cu00fcck%28e%29%22%2C%22kl.Flasche%5C%2Fn%22%2C%22kleine%22%2C%22kleinen%22%2C%22kleiner%22%2C%22kleines%22%2C%22Knolle%5C%2Fn%22%2C%22Kopf%22%2C%22K%5Cu00f6pfe%22%2C%22K%5Cu00f6rner%22%2C%22Kugel%22%2C%22Kugel%5C%2Fn%22%2C%22Kugeln%22%2C%22Liter%22%2C%22m.-gro%5Cu00dfe%22%2C%22m.-gro%5Cu00dfer%22%2C%22m.-gro%5Cu00dfes%22%2C%22mehr%22%2C%22mg%22%2C%22ml%22%2C%22Msp.%22%2C%22n.+B.%22%2C%22Paar%22%2C%22Paket%22%2C%22Pck.%22%2C%22Pkt.%22%2C%22Platte%5C%2Fn%22%2C%22Port.%22%2C%22Prise%28n%29%22%2C%22Prisen%22%2C%22Prozent+%25%22%2C%22Riegel%22%2C%22Ring%5C%2Fe%22%2C%22Rippe%5C%2Fn%22%2C%22Rolle%28n%29%22%2C%22Sch%5Cu00e4lchen%22%2C%22Scheibe%5C%2Fn%22%2C%22Schuss%22%2C%22Spritzer%22%2C%22Stange%5C%2Fn%22%2C%22St%5Cu00e4ngel%22%2C%22Stiel%5C%2Fe%22%2C%22Stiele%22%2C%22St%5Cu00fcck%28e%29%22%2C%22Tafel%22%2C%22Tafeln%22%2C%22Tasse%22%2C%22Tasse%5C%2Fn%22%2C%22Teil%5C%2Fe%22%2C%22TL%22%2C%22TL+%28geh%5Cu00e4uft%29%22%2C%22TL+%28gestr.%29%22%2C%22Topf%22%2C%22Tropfen%22%2C%22Tube%5C%2Fn%22%2C%22T%5Cu00fcte%5C%2Fn%22%2C%22viel%22%2C%22wenig%22%2C%22W%5Cu00fcrfel%22%2C%22Wurzel%22%2C%22Wurzel%5C%2Fn%22%2C%22Zehe%5C%2Fn%22%2C%22Zweig%5C%2Fe%22%5D%2C%22yield%22%3A%7B%221%22%3A%221+Portion%22%2C%222%22%3A%222+Portionen%22%2C%223%22%3A%223+Portionen%22%2C%224%22%3A%224+Portionen%22%2C%225%22%3A%225+Portionen%22%2C%226%22%3A%226+Portionen%22%2C%227%22%3A%227+Portionen%22%2C%228%22%3A%228+Portionen%22%2C%229%22%3A%229+Portionen%22%2C%2210%22%3A%2210+Portionen%22%2C%2211%22%3A%2211+Portionen%22%2C%2212%22%3A%2212+Portionen%22%7D%2C%22prepare_time%22%3A%7B%221%22%3A%22schnell%22%2C%222%22%3A%22mittel%22%2C%223%22%3A%22aufwendig%22%7D%2C%22category%22%3A%7B%221%22%3A%22Vorspeise%22%2C%222%22%3A%22Suppe%22%2C%223%22%3A%22Salat%22%2C%224%22%3A%22Hauptspeise%22%2C%225%22%3A%22Beilage%22%2C%226%22%3A%22Nachtisch%5C%2FDessert%22%2C%227%22%3A%22Getr%5Cu00e4nke%22%2C%228%22%3A%22B%5Cu00fcffet%22%2C%229%22%3A%22Fr%5Cu00fchst%5Cu00fcck%5C%2FBrunch%22%7D%2C%22variety%22%3A%7B%221%22%3A%22Basmati+Reis%22%2C%222%22%3A%22Basmati+%26amp%3B+Wild+Reis%22%2C%223%22%3A%22R%5Cu00e4ucherreis%22%2C%224%22%3A%22Jasmin+Reis%22%2C%225%22%3A%221121+Basmati+Wunderreis%22%2C%226%22%3A%22Spitzen+Langkorn+Reis%22%2C%227%22%3A%22Wildreis%22%2C%228%22%3A%22Naturreis%22%2C%229%22%3A%22Sushi+Reis%22%7D%2C%22tag--ingredient%22%3A%7B%221%22%3A%22Eier%22%2C%222%22%3A%22Gem%5Cu00fcse%22%2C%223%22%3A%22Getreide%22%2C%224%22%3A%22Fisch%22%2C%225%22%3A%22Fleisch%22%2C%226%22%3A%22Meeresfr%5Cu00fcchte%22%2C%227%22%3A%22Milchprodukte%22%2C%228%22%3A%22Obst%22%2C%229%22%3A%22Salat%22%7D%2C%22tag--preparation%22%3A%7B%2210%22%3A%22Backen%22%2C%2211%22%3A%22Blanchieren%22%2C%2212%22%3A%22Braten%5C%2FSchmoren%22%2C%2213%22%3A%22D%5Cu00e4mpfen%5C%2FD%5Cu00fcnsten%22%2C%2214%22%3A%22Einmachen%22%2C%2215%22%3A%22Frittieren%22%2C%2216%22%3A%22Gratinieren%5C%2F%5Cu00dcberbacken%22%2C%2217%22%3A%22Grillen%22%2C%2218%22%3A%22Kochen%22%7D%2C%22tag--kitchen%22%3A%7B%2219%22%3A%22Afrikanisch%22%2C%2220%22%3A%22Alpenk%5Cu00fcche%22%2C%2221%22%3A%22Asiatisch%22%2C%2222%22%3A%22Deutsch+%28regional%29%22%2C%2223%22%3A%22Franz%5Cu00f6sisch%22%2C%2224%22%3A%22Mediterran%22%2C%2225%22%3A%22Orientalisch%22%2C%2226%22%3A%22Osteurop%5Cu00e4isch%22%2C%2227%22%3A%22Skandinavisch%22%2C%2228%22%3A%22S%5Cu00fcdamerikanisch%22%2C%2229%22%3A%22US-Amerikanisch%22%2C%2230%22%3A%22%22%7D%2C%22tag--difficulty%22%3A%7B%2231%22%3A%22Einfach%22%2C%2232%22%3A%22Mittelschwer%22%2C%2233%22%3A%22Anspruchsvoll%22%7D%2C%22tag--feature%22%3A%7B%2234%22%3A%22Gut+vorzubereiten%22%2C%2235%22%3A%22Kalorienarm+%5C%2F+leicht%22%2C%2236%22%3A%22Klassiker%22%2C%2237%22%3A%22Preiswert%22%2C%2238%22%3A%22Raffiniert%22%2C%2239%22%3A%22Vegetarisch+%5C%2F+Vegan%22%2C%2240%22%3A%22Vitaminreich%22%2C%2241%22%3A%22Vollwert%22%2C%2242%22%3A%22%22%7D%2C%22tag%22%3A%7B%221%22%3A%22Eier%22%2C%222%22%3A%22Gem%5Cu00fcse%22%2C%223%22%3A%22Getreide%22%2C%224%22%3A%22Fisch%22%2C%225%22%3A%22Fleisch%22%2C%226%22%3A%22Meeresfr%5Cu00fcchte%22%2C%227%22%3A%22Milchprodukte%22%2C%228%22%3A%22Obst%22%2C%229%22%3A%22Salat%22%2C%2210%22%3A%22Backen%22%2C%2211%22%3A%22Blanchieren%22%2C%2212%22%3A%22Braten%5C%2FSchmoren%22%2C%2213%22%3A%22D%5Cu00e4mpfen%5C%2FD%5Cu00fcnsten%22%2C%2214%22%3A%22Einmachen%22%2C%2215%22%3A%22Frittieren%22%2C%2216%22%3A%22Gratinieren%5C%2F%5Cu00dcberbacken%22%2C%2217%22%3A%22Grillen%22%2C%2218%22%3A%22Kochen%22%2C%2219%22%3A%22Afrikanisch%22%2C%2220%22%3A%22Alpenk%5Cu00fcche%22%2C%2221%22%3A%22Asiatisch%22%2C%2222%22%3A%22Deutsch+%28regional%29%22%2C%2223%22%3A%22Franz%5Cu00f6sisch%22%2C%2224%22%3A%22Mediterran%22%2C%2225%22%3A%22Orientalisch%22%2C%2226%22%3A%22Osteurop%5Cu00e4isch%22%2C%2227%22%3A%22Skandinavisch%22%2C%2228%22%3A%22S%5Cu00fcdamerikanisch%22%2C%2229%22%3A%22US-Amerikanisch%22%2C%2230%22%3A%22%22%2C%2231%22%3A%22Einfach%22%2C%2232%22%3A%22Mittelschwer%22%2C%2233%22%3A%22Anspruchsvoll%22%2C%2234%22%3A%22Gut+vorzubereiten%22%2C%2235%22%3A%22Kalorienarm+%5C%2F+leicht%22%2C%2236%22%3A%22Klassiker%22%2C%2237%22%3A%22Preiswert%22%2C%2238%22%3A%22Raffiniert%22%2C%2239%22%3A%22Vegetarisch+%5C%2F+Vegan%22%2C%2240%22%3A%22Vitaminreich%22%2C%2241%22%3A%22Vollwert%22%2C%2242%22%3A%22%22%7D%7D%2C%22errorArray%22%3A%7B%22recipe_prepare_time%22%3A%22error%22%2C%22recipe_yield%22%3A%22error%22%2C%22recipe_category_name%22%3A%22error%22%2C%22recipe_tag_name%22%3A%22error%22%2C%22recipe_instruction_text%22%3A%22error%22%2C%22recipe_ingredient_name%22%3A%22error%22%7D%2C%22errorMessage%22%3A%22Bitte+f%5Cu00fclle+die+rot+markierten+Felder+korrekt+aus.%22%2C%22db%22%3A%7B%22query_count%22%3A20%7D%7D' => 'https://foo.bar/tpl_preview.php?pid=122&json={"recipe_id":-1,"recipe_created":"","recipe_title":"vxcvxc","recipe_description":"","recipe_yield":0,"recipe_prepare_time":0,"recipe_image":"","recipe_legal":0,"recipe_live":0,"recipe_user_guid":"","recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"formMatchingArray":{"unites":["Becher","Beete","Beutel","Blatt","Blätter","Bund","Bündel","cl","cm","dicke","dl","Dose","Dose\/n","dünne","Ecke(n)","Eimer","einige","einige Stiele","EL","EL, gehäuft","EL, gestr.","etwas","evtl.","extra","Fläschchen","Flasche","Flaschen","g","Glas","Gläser","gr. Dose\/n","gr. Fl.","große","großen","großer","großes","halbe","Halm(e)","Handvoll","Kästchen","kg","kl. Bund","kl. Dose\/n","kl. Glas","kl. Kopf","kl. Scheibe(n)","kl. Stück(e)","kl.Flasche\/n","kleine","kleinen","kleiner","kleines","Knolle\/n","Kopf","Köpfe","Körner","Kugel","Kugel\/n","Kugeln","Liter","m.-große","m.-großer","m.-großes","mehr","mg","ml","Msp.","n. B.","Paar","Paket","Pck.","Pkt.","Platte\/n","Port.","Prise(n)","Prisen","Prozent %","Riegel","Ring\/e","Rippe\/n","Rolle(n)","Schälchen","Scheibe\/n","Schuss","Spritzer","Stange\/n","Stängel","Stiel\/e","Stiele","Stück(e)","Tafel","Tafeln","Tasse","Tasse\/n","Teil\/e","TL","TL (gehäuft)","TL (gestr.)","Topf","Tropfen","Tube\/n","Tüte\/n","viel","wenig","Würfel","Wurzel","Wurzel\/n","Zehe\/n","Zweig\/e"],"yield":{"1":"1 Portion","2":"2 Portionen","3":"3 Portionen","4":"4 Portionen","5":"5 Portionen","6":"6 Portionen","7":"7 Portionen","8":"8 Portionen","9":"9 Portionen","10":"10 Portionen","11":"11 Portionen","12":"12 Portionen"},"prepare_time":{"1":"schnell","2":"mittel","3":"aufwendig"},"category":{"1":"Vorspeise","2":"Suppe","3":"Salat","4":"Hauptspeise","5":"Beilage","6":"Nachtisch\/Dessert","7":"Getränke","8":"Büffet","9":"Frühstück\/Brunch"},"variety":{"1":"Basmati Reis","2":"Basmati & Wild Reis","3":"Räucherreis","4":"Jasmin Reis","5":"1121 Basmati Wunderreis","6":"Spitzen Langkorn Reis","7":"Wildreis","8":"Naturreis","9":"Sushi Reis"},"tag--ingredient":{"1":"Eier","2":"Gemüse","3":"Getreide","4":"Fisch","5":"Fleisch","6":"Meeresfrüchte","7":"Milchprodukte","8":"Obst","9":"Salat"},"tag--preparation":{"10":"Backen","11":"Blanchieren","12":"Braten\/Schmoren","13":"Dämpfen\/Dünsten","14":"Einmachen","15":"Frittieren","16":"Gratinieren\/Überbacken","17":"Grillen","18":"Kochen"},"tag--kitchen":{"19":"Afrikanisch","20":"Alpenküche","21":"Asiatisch","22":"Deutsch (regional)","23":"Französisch","24":"Mediterran","25":"Orientalisch","26":"Osteuropäisch","27":"Skandinavisch","28":"Südamerikanisch","29":"US-Amerikanisch","30":""},"tag--difficulty":{"31":"Einfach","32":"Mittelschwer","33":"Anspruchsvoll"},"tag--feature":{"34":"Gut vorzubereiten","35":"Kalorienarm \/ leicht","36":"Klassiker","37":"Preiswert","38":"Raffiniert","39":"Vegetarisch \/ Vegan","40":"Vitaminreich","41":"Vollwert","42":""},"tag":{"1":"Eier","2":"Gemüse","3":"Getreide","4":"Fisch","5":"Fleisch","6":"Meeresfrüchte","7":"Milchprodukte","8":"Obst","9":"Salat","10":"Backen","11":"Blanchieren","12":"Braten\/Schmoren","13":"Dämpfen\/Dünsten","14":"Einmachen","15":"Frittieren","16":"Gratinieren\/Überbacken","17":"Grillen","18":"Kochen","19":"Afrikanisch","20":"Alpenküche","21":"Asiatisch","22":"Deutsch (regional)","23":"Französisch","24":"Mediterran","25":"Orientalisch","26":"Osteuropäisch","27":"Skandinavisch","28":"Südamerikanisch","29":"US-Amerikanisch","30":"","31":"Einfach","32":"Mittelschwer","33":"Anspruchsvoll","34":"Gut vorzubereiten","35":"Kalorienarm \/ leicht","36":"Klassiker","37":"Preiswert","38":"Raffiniert","39":"Vegetarisch \/ Vegan","40":"Vitaminreich","41":"Vollwert","42":""}},"errorArray":{"recipe_prepare_time":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_name":"error"},"errorMessage":"Bitte fülle die rot markierten Felder korrekt aus.","db":{"query_count":20}}',
            '<a href="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere</a>'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       => '<a href="javascript:confirm(1)">Clickhere</a>',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, S::create($before)->urlDecodeMulti()->toString(), 'testing: ' . $before);
        }
    }

    public function testUrlDecode()
    {
        $testArray = [
            'Ã'                                                   => 'Ã',
            ' '                                                   => ' ',
            ''                                                    => '',
            "\n"                                                  => "\n",
            'test+test@foo.bar'                                   => 'test test@foo.bar',
            '%3A%2F%2F%252567%252569%252573%252574'               => '://%2567%2569%2573%2574',
            '%253A%252F%252F%25252567%25252569%25252573%25252574' => '%3A%2F%2F%252567%252569%252573%252574',
            "tes%20öäü%20\u00edtest"                              => 'tes öäü \u00edtest',
            'Düsseldorf'                                          => 'Düsseldorf',
            'D&#252;sseldorf'                                     => 'D&#252;sseldorf',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, S::create($before)->urlDecode()->toString(), 'testing: ' . $before);
        }

        foreach ($testArray as $before => $after) {
            static::assertSame($before, S::create($before)->urlEncode()->urlDecode()->toString(), 'testing: ' . $before);
        }
    }

    public function testUrlDecodeRawMulti()
    {
        $testArray = [
            'W%F6bse'                                                                                                                                                                                                                                                                                                                                                                                                                      => 'Wöbse',
            'Ã'                                                                                                                                                                                                                                                                                                                                                                                                                            => 'Ã',
            'Ã¤'                                                                                                                                                                                                                                                                                                                                                                                                                           => 'ä',
            ' '                                                                                                                                                                                                                                                                                                                                                                                                                            => ' ',
            ''                                                                                                                                                                                                                                                                                                                                                                                                                             => '',
            "\n"                                                                                                                                                                                                                                                                                                                                                                                                                           => "\n",
            "\u00ed"                                                                                                                                                                                                                                                                                                                                                                                                                       => 'í',
            'tes%20öäü%20\u00edtest+test'                                                                                                                                                                                                                                                                                                                                                                                                  => 'tes öäü ítest+test',
            'test+test@foo.bar'                                                                                                                                                                                                                                                                                                                                                                                                            => 'test+test@foo.bar',
            'con%5cu00%366irm'                                                                                                                                                                                                                                                                                                                                                                                                             => 'confirm',
            '%3A%2F%2F%252567%252569%252573%252574'                                                                                                                                                                                                                                                                                                                                                                                        => '://gist',
            '%253A%252F%252F%25252567%25252569%25252573%25252574'                                                                                                                                                                                                                                                                                                                                                                          => '://gist',
            "tes%20öäü%20\u00edtest"                                                                                                                                                                                                                                                                                                                                                                                                       => 'tes öäü ítest',
            'Düsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                   => 'Düsseldorf',
            'Duesseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                  => 'Duesseldorf',
            'D&#252;sseldorf'                                                                                                                                                                                                                                                                                                                                                                                                              => 'Düsseldorf',
            'D%FCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                 => 'Düsseldorf',
            'D&#xFC;sseldorf'                                                                                                                                                                                                                                                                                                                                                                                                              => 'Düsseldorf',
            'D%26%23xFC%3Bsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                        => 'Düsseldorf',
            'DÃ¼sseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                  => 'Düsseldorf',
            'D%C3%BCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                              => 'Düsseldorf',
            'D%C3%83%C2%BCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                        => 'Düsseldorf',
            'D%25C3%2583%25C2%25BCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                => 'Düsseldorf',
            '<strong>D&#252;sseldorf</strong>'                                                                                                                                                                                                                                                                                                                                                                                             => '<strong>Düsseldorf</strong>',
            'Hello%2BWorld%2B%253E%2Bhow%2Bare%2Byou%253F'                                                                                                                                                                                                                                                                                                                                                                                 => 'Hello+World+>+how+are+you?',
            '%e7%ab%a0%e5%ad%90%e6%80%a1'                                                                                                                                                                                                                                                                                                                                                                                                  => '章子怡',
            'Fran%c3%a7ois Truffaut'                                                                                                                                                                                                                                                                                                                                                                                                       => 'François Truffaut',
            '%e1%83%a1%e1%83%90%e1%83%a5%e1%83%90%e1%83%a0%e1%83%97%e1%83%95%e1%83%94%e1%83%9a%e1%83%9d'                                                                                                                                                                                                                                                                                                                                   => 'საქართველო',
            '%25e1%2583%25a1%25e1%2583%2590%25e1%2583%25a5%25e1%2583%2590%25e1%2583%25a0%25e1%2583%2597%25e1%2583%2595%25e1%2583%2594%25e1%2583%259a%25e1%2583%259d'                                                                                                                                                                                                                                                                       => 'საქართველო',
            '%2525e1%252583%2525a1%2525e1%252583%252590%2525e1%252583%2525a5%2525e1%252583%252590%2525e1%252583%2525a0%2525e1%252583%252597%2525e1%252583%252595%2525e1%252583%252594%2525e1%252583%25259a%2525e1%252583%25259d'                                                                                                                                                                                                           => 'საქართველო',
            'Bj%c3%b6rk Gu%c3%b0mundsd%c3%b3ttir'                                                                                                                                                                                                                                                                                                                                                                                          => 'Björk Guðmundsdóttir',
            '%e5%ae%ae%e5%b4%8e%e3%80%80%e9%a7%bf'                                                                                                                                                                                                                                                                                                                                                                                         => '宮崎　駿',
            '%u7AE0%u5B50%u6021'                                                                                                                                                                                                                                                                                                                                                                                                           => '章子怡',
            '%u0046%u0072%u0061%u006E%u00E7%u006F%u0069%u0073%u0020%u0054%u0072%u0075%u0066%u0066%u0061%u0075%u0074'                                                                                                                                                                                                                                                                                                                       => 'François Truffaut',
            '%u10E1%u10D0%u10E5%u10D0%u10E0%u10D7%u10D5%u10D4%u10DA%u10DD'                                                                                                                                                                                                                                                                                                                                                                 => 'საქართველო',
            '%u0042%u006A%u00F6%u0072%u006B%u0020%u0047%u0075%u00F0%u006D%u0075%u006E%u0064%u0073%u0064%u00F3%u0074%u0074%u0069%u0072'                                                                                                                                                                                                                                                                                                     => 'Björk Guðmundsdóttir',
            '%u5BAE%u5D0E%u3000%u99FF'                                                                                                                                                                                                                                                                                                                                                                                                     => '宮崎　駿',
            '&#31456;&#23376;&#24609;'                                                                                                                                                                                                                                                                                                                                                                                                     => '章子怡',
            '&#70;&#114;&#97;&#110;&#231;&#111;&#105;&#115;&#32;&#84;&#114;&#117;&#102;&#102;&#97;&#117;&#116;'                                                                                                                                                                                                                                                                                                                            => 'François Truffaut',
            '&#4321;&#4304;&#4325;&#4304;&#4320;&#4311;&#4309;&#4308;&#4314;&#4317;'                                                                                                                                                                                                                                                                                                                                                       => 'საქართველო',
            '&#66;&#106;&#246;&#114;&#107;&#32;&#71;&#117;&#240;&#109;&#117;&#110;&#100;&#115;&#100;&#243;&#116;&#116;&#105;&#114;'                                                                                                                                                                                                                                                                                                        => 'Björk Guðmundsdóttir',
            '&#23470;&#23822;&#12288;&#39423;'                                                                                                                                                                                                                                                                                                                                                                                             => '宮崎　駿',
            '<a href="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere</a>' => '<a href="javascript:confirm(1)">Clickhere</a>',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, S::create($before)->urlDecodeRawMulti()->toString(), 'testing: ' . $before);
        }
    }

    public function testUrlDecodeRaw()
    {
        $testArray = [
            'Ã'                                                   => 'Ã',
            ' '                                                   => ' ',
            ''                                                    => '',
            "\n"                                                  => "\n",
            'test+test@foo.bar'                                   => 'test+test@foo.bar',
            '%3A%2F%2F%252567%252569%252573%252574'               => '://%2567%2569%2573%2574',
            '%253A%252F%252F%25252567%25252569%25252573%25252574' => '%3A%2F%2F%252567%252569%252573%252574',
            "tes%20öäü%20\u00edtest"                              => 'tes öäü \u00edtest',
            'Düsseldorf'                                          => 'Düsseldorf',
            'D&#252;sseldorf'                                     => 'D&#252;sseldorf',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, S::create($before)->urlDecodeRaw()->toString(), 'testing: ' . $before);
        }

        foreach ($testArray as $before => $after) {
            static::assertSame($before, S::create($before)->urlEncodeRaw()->urlDecodeRaw()->toString(), 'testing: ' . $before);
        }
    }

    public function testCallUserFunctionTypeError()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Passed object must have a __toString method');

        static::assertSame('FOO', S::create('foo')->callUserFunction(static function ($str) {
            return new \stdClass();
        })->toString());
    }

    public function testCallUserFunction()
    {
        static::assertSame('FOO', S::create('foo')->callUserFunction('strtoupper')->toString());

        static::assertSame('FOO', S::create('foo')->callUserFunction([UTF8::class, 'strtoupper'])->toString());

        static::assertSame('foo bar lall', S::create('foo bar lall')->callUserFunction([UTF8::class, 'str_limit'])->toString());
        static::assertSame('foo b...', S::create('foo bar lall')->callUserFunction([UTF8::class, 'str_limit'], 8, '...')->toString());

        static::assertSame('FOO', S::create('foo')->callUserFunction(static function ($str) {
            return \strtoupper($str);
        })->toString());

        static::assertSame('foo bar…', S::create('foo bar lall')->callUserFunction(static function ($str) {
            return UTF8::str_limit($str, 8);
        })->toString());
    }
}
