<?php

namespace Cli;

class Console
{
    public static $out = STDOUT;
    public static $in = STDIN;
    public static $err = STDERR;

    public static function write($msg)
    {
        fwrite(static::$out, $msg);
    }

    public static function writeLn($msg)
    {
        self::write($msg . PHP_EOL);
    }

    public static function error($msg)
    {
        fwrite(static::$err, $msg);
    }

    public static function errorLn($msg)
    {
        self::error($msg . PHP_EOL);
    }

    public static function msgError($msg)
    {
        $color = new \Colors\Color();
        self::errorLn($color($msg)->bg('red')->bold()->white());
    }

    public static function msgSuccess($msg)
    {
        $color = new \Colors\Color();
        self::writeLn('');
        self::writeLn(
            $color(
                \Commando\Util\Terminal::header(' ' . $msg)
            )->white()->bg('green')->bold()
        );
        self::writeLn('');
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
