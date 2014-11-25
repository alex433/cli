<?php

namespace Cli;

class Console
{
    public static $out = STDOUT;
    public static $in = STDIN;
    public static $err = STDERR;

    public static $beep_on_error = true;
    public static $set_error_handler = true;

    public static function write($msg, $nb_eol = 0)
    {
        return fwrite(static::$out, $msg . str_repeat(PHP_EOL, $nb_eol));
    }

    public static function writeLn($msg = '', $nb_eol = 1)
    {
        return static::write($msg, $nb_eol);
    }

    public static function error($msg, $nb_eol = 0)
    {
        return fwrite(static::$err, $msg . str_repeat(PHP_EOL, $nb_eol));
    }

    public static function errorLn($msg = '', $nb_eol = 1)
    {
        return static::error($msg, $nb_eol);
    }

    public static function text($msg = '')
    {
        return new \Colors\Color($msg);
    }

    public static function msgError($msg, $nb_eol = 1)
    {
        return static::errorLn(static::text($msg)->bg('red')->bold()->white(), $nb_eol);
    }

    public static function msgSuccess($msg, $nb_eol = 2)
    {
        static::writeLn('');
        static::writeLn(
            static::text(
                \Commando\Util\Terminal::header(' ' . $msg)
            )->white()->bg('green')->bold(),
            $nb_eol
        );
    }

    public static function execute($funciton)
    {
        if (static::$set_error_handler) {
            static::_setErrorHandler();
        }

        try {
            $funciton();
        } catch (\Exception $e) {
            if (static::$beep_on_error) {
                \Commando\Util\Terminal::beep();
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
