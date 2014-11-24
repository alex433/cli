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
        fwrite(static::$out, $msg . str_repeat(PHP_EOL, $nb_eol));
    }

    public static function writeLn($msg, $nb_eol = 1)
    {
        static::write($msg, $nb_eol);
    }

    public static function error($msg, $nb_eol = 0)
    {
        fwrite(static::$err, $msg . str_repeat(PHP_EOL, $nb_eol));
    }

    public static function errorLn($msg, $nb_eol = 1)
    {
        static::error($msg, $nb_eol);
    }

    public static function msgError($msg, $nb_eol = 1)
    {
        $color = new \Colors\Color();
        static::errorLn($color($msg)->bg('red')->bold()->white(), $nb_eol);
    }

    public static function msgSuccess($msg, $nb_eol = 2)
    {
        $color = new \Colors\Color();
        static::writeLn('');
        static::writeLn(
            $color(
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

            $color = new \Colors\Color();

            static::errorLn('Stack trace:');
            static::errorLn($color->bold($e->getTraceAsString()));
            static::errorLn(
                sprintf(
                    $color->bold('  thrown in %s on line %s'),
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
