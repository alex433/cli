<?php

namespace Cli;

use Colors\Color;
use Commando\Util\Terminal;

class Console
{
    public static $out = STDOUT;
    public static $in = STDIN;
    public static $err = STDERR;

    public static $beep_on_error = true;
    public static $set_error_handler = true;

    /**
     * @param string $msg Output message
     * @param int    $nb_eol Number of the end of lines
     *
     * @return int
     */
    public static function write($msg, $nb_eol = 0)
    {
        return fwrite(static::$out, $msg . str_repeat(PHP_EOL, $nb_eol));
    }

    /**
     * @param string $msg Output message
     * @param int    $nb_eol Number of the end of lines
     *
     * @return int
     */
    public static function writeLn($msg = '', $nb_eol = 1)
    {
        return static::write($msg, $nb_eol);
    }

    /**
     * @param string $msg Output message
     * @param int    $nb_eol Number of the end of lines
     *
     * @return int
     */
    public static function error($msg, $nb_eol = 0)
    {
        return fwrite(static::$err, $msg . str_repeat(PHP_EOL, $nb_eol));
    }

    /**
     * @param string $msg Output message
     * @param int    $nb_eol Number of the end of lines
     *
     * @return int
     */
    public static function errorLn($msg = '', $nb_eol = 1)
    {
        return static::error($msg, $nb_eol);
    }

    /**
     * Read line from input
     *
     * @return string
     */
    public static function readLn()
    {
        return fgets(static::$in);
    }

    /**
     * @param string $msg Text message
     *
     * @return Color
     */
    public static function text($msg = '')
    {
        return new Color($msg);
    }

    /**
     * @param string $msg Error message
     * @param int    $nb_eol Number of the end of lines
     *
     * @return int
     */
    public static function msgError($msg, $nb_eol = 1)
    {
        return static::errorLn(static::text($msg)->bg('red')->bold()->white(), $nb_eol);
    }

    /**
     * @param string $msg Success message
     * @param int    $nb_eol Number of the end of lines
     */
    public static function msgSuccess($msg, $nb_eol = 2)
    {
        static::writeLn('');
        static::writeLn(
            static::text(
                Terminal::header(' ' . $msg)
            )->white()->bg('green')->bold(),
            $nb_eol
        );
    }

    /**
     * @param callable $callback
     */
    public static function execute(\Closure $callback)
    {
        if (static::$set_error_handler) {
            static::_setErrorHandler();
        }

        try {
            $callback();
        } catch (\Exception $e) {
            if (static::$beep_on_error) {
                Terminal::beep();
            }

            $msg = sprintf(
                "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s",
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
            static::msgError($msg);

            static::errorLn('Stack trace:');
            static::errorLn(static::text($e->getTraceAsString())->bold());
            static::errorLn(
                sprintf(
                    static::text('  thrown in %s on line %s')->bold(),
                    $e->getFile(),
                    $e->getLine()
                )
            );

            exit(1);
        }
        exit(0);
    }

    /**
     *  Override php error handler
     */
    protected static function _setErrorHandler()
    {
        set_error_handler(
            function ($errno, $errstr, $errfile, $errline ) {
                throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
            },
            error_reporting()
        );
    }
}
