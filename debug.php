<?php

namespace debug;

use function file_exists;

class debug
{
    /**
     * new-line constant
     */
    const NL = "\n";
    const TAB = "\t";

    /**
     * Prints out $toInspect using print_r()
     *
     * @param mixed $toInspect Data to display in a very pretty looking debug frame
     * @param mixed $logToFile mixed false = log to screen,
     *                                  1 = log to file
     *                                  2 = add backtrace to file-log.
     *                                  Defaults to false, ie log to screen
     */
    public function __construct($toInspect = false, $logToFile = false)
    {
        $debug   = debug_backtrace();
        $heading = '';

        if (!$logToFile) {
            $heading .= '<span ' . (isset($debug[1]['file']) ? 'title="' . $debug[1]['file'] . '" ' : '') . '>';
        }

        $heading .= date('Y-m-d H:i:s') . '::';

        if (file_exists('/var/www/.git/HEAD')) {
            $heading .= trim(implode('/', array_slice(explode('/', file_get_contents('/var/www/.git/HEAD')), 2))) . '::';
        }

        $heading .= (isset($debug[1]['class']) ? $debug[1]['class'] : $debug[1]['file']) . '::';
        $heading .= (isset($debug[1]['function']) ? $debug[1]['function'] : '') . '::';
        $heading .= (isset($debug[0]['line']) ? $debug[0]['line'] : '');

        if (!$logToFile) {
            $heading .= '</span>';
        }

        $display = '<div style="background-color: #f5f5f5; margin-top: 5px; border-top: 3px #8B0000 dashed; display: block; clear: both; text-align: left; padding: 5px; margin-top: 2px">' . self::NL;
        $display .= '<!-- Debug info -->' . self::NL;
        $display .= '<span style="font-weight: bold; font-size: 12px;">' . $heading . '</span><br />' . self::NL;
        $display .= '<pre style="font-size: 12px;">' . self::NL . utf8_encode(htmlentities(print_r($toInspect,
                true), ENT_QUOTES)) . self::NL . '</pre>' . self::NL;

        if ($toInspect === false) {
            $display .= '(bool) false';
        }

        if ($toInspect === true) {
            $display .= '(bool) true';
        }

        $display .= '</div>' . self::NL;

        if ($logToFile) {
            $display = self::NL;
            $display .= $heading . self::NL;
            $display .= utf8_encode(html_entity_decode(print_r($toInspect, true), ENT_QUOTES)) . self::NL;
            $display .= self::NL;

            if (2 === $logToFile) {
                $ex   = new \Exception();
                $trace = explode(self::NL, $ex->getTraceAsString());
                $trace = array_reverse($trace);
                array_shift($trace);
                array_pop($trace);
                $iLength = count($trace);
                $result  = array();
                for ($i = 0; $i < $iLength; $i++) {
                    $result[] = ($i + 1) . ')' . substr($trace[$i], strpos($trace[$i], ' '));
                }

                $display .= self::NL . self::TAB . 'BACKTRACE';
                $display .= self::NL . self::TAB . implode(self::NL . self::TAB, $result) . self::NL;
            }

            file_put_contents('/var/www/storage/logs/debug.log', $display, FILE_APPEND | LOCK_EX);

        } else {

            echo $display;
        }
    }
}
