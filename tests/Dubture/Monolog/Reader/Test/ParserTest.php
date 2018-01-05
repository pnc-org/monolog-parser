<?php

/*
 * This file is part of the monolog-parser package.
 *
 * (c) Robert Gruendler <r.gruendler@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Monolog\Reader\Test;

use Dubture\Monolog\Parser\LineLogParser;

/**
 * Class ParserTest
 * @package Dubture\Monolog\Reader\Test
 */
class ParserTest extends \PHPUnit_Framework_TestCase {
  public function testLineFormatter() {
    $parser = new LineLogParser();
    $log = $parser->parse('[2013-03-16 14:19:51] test.INFO: foobar {"foo":"bar"} []');

    $this->assertInstanceOf('\DateTime', $log['date']);
    $this->assertEquals('test', $log['logger']);
    $this->assertEquals('INFO', $log['level']);
    $this->assertEquals('foobar', $log['message']);
    $this->assertEquals(array("foo" => "bar"), $log['context']);
    $this->assertEquals(array(), $log['extra']);
  }

  public function testLineFormatter2() {
    $parser = new LineLogParser();
    $log = $parser->parse('[2013-03-16 14:19:51] test.INFO: foo bar {"foo":["bar"]} []');

    $this->assertInstanceOf('\DateTime', $log['date']);
    $this->assertEquals('test', $log['logger']);
    $this->assertEquals('INFO', $log['level']);
    $this->assertEquals('foo bar', $log['message']);
    $this->assertEquals(array("foo" => array("bar")), $log['context']);
    $this->assertEquals(array(), $log['extra']);
  }

  public function testLineFormatter3() {
    $parser = new LineLogParser();
    $log = $parser->parse('[2013-03-16 14:19:51] test.INFO: foo bar {"foo":["bar"]} {"bar":["foo"]}');

    $this->assertInstanceOf('\DateTime', $log['date']);
    $this->assertEquals('test', $log['logger']);
    $this->assertEquals('INFO', $log['level']);
    $this->assertEquals('foo bar', $log['message']);
    $this->assertEquals(array("foo" => array("bar")), $log['context']);
    $this->assertEquals(array("bar" => array("foo")), $log['extra']);
  }

  public function testLineFormatter4() {
    $parser = new LineLogParser();
    $log = $parser->parse('[2013-03-16 14:19:51] test.INFO: foo bar [] {"bar":["foo"]}');

    $this->assertInstanceOf('\DateTime', $log['date']);
    $this->assertEquals('test', $log['logger']);
    $this->assertEquals('INFO', $log['level']);
    $this->assertEquals('foo bar', $log['message']);
    $this->assertEquals(array(), $log['context']);
    $this->assertEquals(array("bar" => array("foo")), $log['extra']);
  }

  public function testLineFormatter5() {
    $parser = new LineLogParser();
    $log = $parser->parse('[2013-03-16 14:19:51] test.INFO: foo bar [] {"1":"2"} {"foo":["bar"]} []');

    $this->assertInstanceOf('\DateTime', $log['date']);
    $this->assertEquals('test', $log['logger']);
    $this->assertEquals('INFO', $log['level']);
    $this->assertEquals('foo bar [] {"1":"2"}', $log['message']);
    $this->assertEquals(array("foo" => array("bar")), $log['context']);
    $this->assertEquals(array(), $log['extra']);
  }

  public function testLineFormatter6() {
    $parser = new LineLogParser();
    $log = $parser->parse('[2013-03-16 14:19:51] test.INFO: foo bar [] {"1":"2"} {"foo":["bar"]} {"bar":["foo"]}');

    $this->assertInstanceOf('\DateTime', $log['date']);
    $this->assertEquals('test', $log['logger']);
    $this->assertEquals('INFO', $log['level']);
    $this->assertEquals('foo bar [] {"1":"2"}', $log['message']);
    $this->assertEquals(array("foo" => array("bar")), $log['context']);
    $this->assertEquals(array("bar" => array("foo")), $log['extra']);
  }
}
