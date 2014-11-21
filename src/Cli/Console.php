<?php

namespace Cli;

class Console
{
    public static $out = STDOUT;
    public static $in = STDIN;
    public static $err = STDERR;

    public static function write($msg, $nb_eol = 0)
    {
        fwrite(static::$out, $msg . str_repeat(PHP_EOL, $nb_eol));
    }

    public static function writeLn($msg, $nb_eol = 1)
    {
        self::write($msg, $nb_eol);
    }

    public static function error($msg, $nb_eol = 0)
    {
        fwrite(static::$err, $msg . str_repeat(PHP_EOL, $nb_eol));
    }

    public static function errorLn($msg, $nb_eol = 1)
    {
        self::error($msg, $nb_eol);
    }

    public static function msgError($msg, $nb_eol = 1)
    {
        $color = new \Colors\Color();
        self::errorLn($color($msg)->bg('red')->bold()->white(), $nb_eol);
    }

    public static function msgSuccess($msg, $nb_eol = 2)
    {
        $color = new \Colors\Color();
        self::writeLn('');
        self::writeLn(
            $color(
                \Commando\Util\Terminal::header(' ' . $msg)
            )->white()->bg('green')->bold(),
            $nb_eol
        );
    }

    public static function execute($funciton)
    {
        try {
            $funciton();
        } catch (\Exception $e) {
            $msg = sprintf(
                "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s",
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
            self::msgError($msg);

            $color = new \Colors\Color();

            self::error('Stack trace:');
            self::error($color->bold($e->getTraceAsString()), true);
            self::error(
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
}
