<?php 
namespace App\Cli;

class Printer
{

    public function Out(string $text)
    {
        echo $text;
    }

    public function NewLine()
    {
        $this->Out(PHP_EOL);
    }

    public function Clear()
    {
        if (PHP_OS == 'WINNT') {
            system('cls');
        } else {
            system('clear');
        }
        $this->Out("\e[H\e[J");
    }
    public function Display(string $message)
    {
        $this->NewLine();
        $this->Out($message);
        $this->NewLine();
        $this->NewLine();
    }
}