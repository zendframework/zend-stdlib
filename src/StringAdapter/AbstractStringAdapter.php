<?php

namespace Zend\Stdlib\StringAdapter;

abstract class AbstractStringAdapter implements StringAdapterInterface
{

    /**
     * List of supported character sets (upper case)
     *
     * @var string[]
     */
    protected static $charsets = array();

    public function isCharsetSupported($charset)
    {
        $charset = strtoupper($charset);
        return in_array($charset, static::$charsets);
    }

    public function getSupportedCharsets()
    {
        return static::$charsets;
    }

    /**
     * Word wrap
     *
     * @param  string  $string
     * @param  integer $width
     * @param  string  $break
     * @param  boolean $cut
     * @param  string  $charset
     * @return string
     */
    public function wordWrap($string, $width = 75, $break = "\n", $cut = false, $charset = 'UTF-8')
    {
        $string = (string) $string;
        if ($string === '') {
            return '';
        }

        $break = (string) $break;
        if ($break === '') {
            throw new Exception\InvalidArgumentException('Break string cannot be empty');
        }

        $width = (int) $width;
        $cut   = (bool) $cut;
        if ($width === 0 && $cut) {
            throw new Exception\InvalidArgumentException('Cannot force cut when width is zero');
        }

        $charset     = strtoupper($charset);
        $stringWidth = $this->strlen($string, $charset);
        $breakWidth  = $this->strlen($break, $charset);

        $result    = '';
        $lastStart = $lastSpace = 0;

        for ($current = 0; $current < $stringWidth; $current++) {
            $char = $this->substr($string, $current, 1, $charset);

            $possibleBreak = $char;
            if ($breakWidth !== 1) {
                $possibleBreak = $this->substr($string, $current, $breakWidth, $charset);
            }

            if ($possibleBreak === $break) {
                $result    .= $this->substr($string, $lastStart, $current - $lastStart + $breakWidth, $charset);
                $current   += $breakWidth - 1;
                $lastStart  = $lastSpace = $current + 1;
                continue;
            }

            if ($char === ' ') {
                if ($current - $lastStart >= $width) {
                    $result    .= $this->substr($string, $lastStart, $current - $lastStart, $charset) . $break;
                    $lastStart  = $current + 1;
                }

                $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $cut && $lastStart >= $lastSpace) {
                $result    .= $this->substr($string, $lastStart, $current - $lastStart, $charset) . $break;
                $lastStart  = $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $lastStart < $lastSpace) {
                $result    .= $this->substr($string, $lastStart, $lastSpace - $lastStart, $charset) . $break;
                $lastStart  = $lastSpace = $lastSpace + 1;
                continue;
            }
        }

        if ($lastStart !== $current) {
            $result .= $this->substr($string, $lastStart, $current - $lastStart, $charset);
        }

        return $result;
    }

    /**
     * String padding
     *
     * @param  string  $input
     * @param  integer $padLength
     * @param  string  $padString
     * @param  integer $padType
     * @param  string  $charset
     * @return string
     */
    public function strPad($input, $padLength, $padString = ' ', $padType = \STR_PAD_RIGHT, $charset = 'UTF-8')
    {
        $charset         = strtoupper($charset);
        $return          = '';
        $lengthOfPadding = $padLength - $this->strlen($input, $charset);
        $padStringLength = $this->strlen($padString, $charset);

        if ($padStringLength === 0 || $lengthOfPadding <= 0) {
            $return = $input;
        } else {
            $repeatCount = floor($lengthOfPadding / $padStringLength);

            if ($padType === \STR_PAD_BOTH) {
                $lastStringLeft  = '';
                $lastStringRight = '';
                $repeatCountLeft = $repeatCountRight = ($repeatCount - $repeatCount % 2) / 2;

                $lastStringLength       = $lengthOfPadding - 2 * $repeatCountLeft * $padStringLength;
                $lastStringLeftLength   = $lastStringRightLength = floor($lastStringLength / 2);
                $lastStringRightLength += $lastStringLength % 2;

                $lastStringLeft  = $this->substr($padString, 0, $lastStringLeftLength, $charset);
                $lastStringRight = $this->substr($padString, 0, $lastStringRightLength, $charset);

                $return = str_repeat($padString, $repeatCountLeft) . $lastStringLeft
                    . $input
                    . str_repeat($padString, $repeatCountRight) . $lastStringRight;
            } else {
                $lastString = $this->substr($padString, 0, $lengthOfPadding % $padStringLength, $charset);

                if ($padType === \STR_PAD_LEFT) {
                    $return = str_repeat($padString, $repeatCount) . $lastString . $input;
                } else {
                    $return = $input . str_repeat($padString, $repeatCount) . $lastString;
                }
            }
        }

        return $return;
    }
}
