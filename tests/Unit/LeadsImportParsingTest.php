<?php

namespace Tests\Unit;

use App\Imports\LeadsImport;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class LeadsImportParsingTest extends TestCase
{
    private function invokePrivate($obj, $method, $args = [])
    {
        $ref = new ReflectionClass($obj);
        $m = $ref->getMethod($method);
        $m->setAccessible(true);
        return $m->invokeArgs($obj, $args);
    }

    public function testParseMoney()
    {
    // test ImportSanitizer::parseMoney
    $this->assertEquals(1000.0, \App\Support\ImportSanitizer::parseMoney(1000));
    $this->assertEquals(1234.56, \App\Support\ImportSanitizer::parseMoney('1,234.56'));
    $this->assertEquals(3000.0, \App\Support\ImportSanitizer::parseMoney('$3K'));
    $this->assertEquals(15000.0, \App\Support\ImportSanitizer::parseMoney('15k'));
    $this->assertEquals(0.0, \App\Support\ImportSanitizer::parseMoney('0'));
    $this->assertNull(\App\Support\ImportSanitizer::parseMoney(''));
    }

    public function testParseExcelDate()
    {
    // test ImportSanitizer::parseExcelDate
    $result = \App\Support\ImportSanitizer::parseExcelDate(44927);
    $this->assertIsString($result);

    $this->assertEquals('2025-11-13', \App\Support\ImportSanitizer::parseExcelDate('2025-11-13'));
    $this->assertNull(\App\Support\ImportSanitizer::parseExcelDate('not a date'));
    }
}
