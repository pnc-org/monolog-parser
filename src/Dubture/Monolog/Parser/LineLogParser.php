<?php

/*
 * This file is part of the monolog-parser package.
 *
 * (c) Robert Gruendler <r.gruendler@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Monolog\Parser;

/**
 * Class LineLogParser
 * @package Dubture\Monolog\Parser
 */
class LineLogParser implements LogParserInterface
{
  protected $pattern = '/\[(?P<date>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<rest>.*)/';

  /**
   * Constructor
   * @param string $pattern
   */
  public function __construct($pattern = null)
  {
    $this->pattern = ($pattern) ?: $this->pattern;
  }

  /**
   * {@inheritdoc}
   */
  public function parse($log)
  {
    if( !is_string($log) || strlen($log) === 0) {
      return array();
    }

    preg_match($this->pattern, $log, $data);


    $this->parseRest($data);

    if (!isset($data['date'])) {
      return array();
    }

    return array(
      'date' => \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']),
      'logger' => $data['logger'],
      'level' => $data['level'],
      'message' => $data['message'],
      'context' => json_decode($data['context'], true),
      'extra' => json_decode($data['extra'], true)
    );
  }

  private function parseRest(&$data) {
    $jsons = array();
    $rest = $data['rest'];
    unset($data['rest']);
    $parts = explode(" ", $rest);
    for ($i = 0; $i < count($parts); $i++) {
      for ($j = $i; $j < count($parts); $j++) {
        $segment = implode(" ", array_slice($parts, $i, $j - $i + 1));
        $decoded = json_decode($segment, true);
        if (json_last_error() == JSON_ERROR_NONE) {
          $jsons[$i] = $segment;
          $i = $j;
          continue;
        }
      }
    }

    krsort($jsons);
    $messageEndPos = max(array_keys($jsons));
    $keys = array("extra", "context");
    $i = 0;
    foreach ($jsons as $pos => $value) {
      if ($i < count($keys)) {
        $data[$keys[$i]] = $value;
      }
      $i++;
      $messageEndPos = min($pos, $messageEndPos);
      if ($i >= count($keys)) {
        break;
      }
    }

    $data['message'] = implode(" ", array_slice($parts, 0, $messageEndPos));
  }
}
