<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function array_get($arr, $key, $defaultValue = null)
{
    $keys = explode(".", $key);
    $value = $arr;
    foreach ($keys as $splitedKey) {
        if (!isset($value[$splitedKey])) {
            return $defaultValue;
        }
        $value = $value[$splitedKey];
    }
    return $value;
}

$csvFile = sprintf('%s%s%s', getcwd(), DIRECTORY_SEPARATOR, array_get($_SERVER, 'argv.1', ''));

$args = array_get($_SERVER, 'argv');
unset($args[0], $args[1]);

interface Csv_Writer_Interface
{
}

class Csv_Fixer_Writer implements Csv_Writer_Interface
{
}


class CsvFileFixer
{
    protected $csvFile;

    protected Command_Options $options;

    protected CsvLines $lines;

    protected Csv_Writer_Interface $writer;


    public function __construct($csvFile, $args = [], $writer = null)
    {
        if (!file_exists($csvFile)) {
            exit(sprintf('File CSV: "%s" not found', $csvFile));
        }

        $this->csvFile = $csvFile;
        if (!is_null($writer)) {
            $this->writer = $writer;
        }

        $this->options = new Command_Options($args);
    }

    public function run()
    {
        $this->lines = new CsvLines(file_get_contents($this->csvFile), $this->options);
        $this->lines->setWriter(
            is_null($this->writer)
            ? new Csv_Fixer_Writer()
            : $this->writer
        );
        
        $this->lines->write();
    }
}

$csvFileFixer = new CsvFileFixer($csvFile, $args, new Csv_Fixer_Writer());
$csvFileFixer->run();

















class CsvLines
{
    protected Command_Options $options;
    protected $headers = [];
    protected $lines = [];

    protected $rowColumns = null;

    protected Csv_Writer_Interface $writer;

    protected function convertLineToColumn($line): array
    {
        return explode($this->options->getOption('separator', ','), $line);
    }

    public function convert(): array
    {
        $ret = [];

        return $ret;
    }

    public function __construct($csvContent, Command_Options &$options)
    {
        $this->options = $options;
        if (empty(trim($csvContent))) {
            return;
        }
        $lines = explode("\n", $csvContent);
        if ($this->options->getOption('ignore_header', false)) {
            $this->headers = $this->convertLineToColumn(array_get($lines, 0));
            unset($lines[0]);
        }
        $this->lines = $lines;
    }

    public function setWriter(Csv_Writer_Interface $writer)
    {
        $this->writer = $writer;
    }

    public function get()
    {
        if (is_null($this->rowColumns)) {
            $this->rowColumns = $this->convert();
        }
        return $this->rowColumns;
    }
}



class Command_Options
{
    protected $args;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function parse($args)
    {
    }

    public function getOption($name, $defaultValue = null)
    {
        return $defaultValue;
    }
}

