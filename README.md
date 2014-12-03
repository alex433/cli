cli
===

## Example

Here is an example

``` php
<?php

require_once 'vendor/autoload.php';

use Cli\Console;

Console::execute(
    function () {
        Console::write('Starting... ');

        //...

        Console::writeLn('[DONE]');
    }
);
