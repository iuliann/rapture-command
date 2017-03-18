<?php

namespace Rapture\Command;

/**
 * Rapture CLI Command
 *
 * @package Rapture\Command
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 * @see     https://linuxfrombeginning.files.wordpress.com/2008/09/colortable1.gif
 * @see     http://misc.flogisoft.com/bash/tip_colors_and_formatting
 */
class Command
{
    const REQUIRED = 'required';
    const OPTIONAL = 'optional';
    const NO_VALUE = 'no-value';

    const FONT_NORMAL = 0;
    const FONT_BOLD = 1;

    const FG_DEFAULT = 39;
    const BG_DEFAULT = 49;

    const FG_WHITE = 97;
    const FG_RED = 31;
    const FG_GREEN = 32;
    const FG_YELLOW = 33;
    const FG_BLUE = 37;

    const BG_WHITE = 107;
    const BG_RED = 41;
    const BG_GREEN = 42;
    const BG_YELLOW = 43;
    const BG_BLUE = 47;

    protected $options = [];

    /**
     * Command constructor.
     *
     * @param array    $options Options as array
     * @param callback $output  Output callback
     */
    public function __construct(array $options, $output)
    {
        $this->options = $options;
        $this->output  = $output;

        if ($this->hasOption('help')) {
            $this->help();
            exit(0);
        }

        foreach (static::getOptions() as $shortOption => $data) {
            list($longOpt, $mode, $desc, $default) = $data;
            if ($mode == self::REQUIRED && !isset($this->options[$longOpt])) {
                $this->error(" Missing required option: `{$longOpt}` ");
            }
        }
    }

    /*
     * Example
     */

    /**
     * Options definition example
     *
     * @return array
     */
    public static function getOptions()
    {
        return [
            'r' => ['required', self::REQUIRED, 'Long description', null],
            'o' => ['optional', self::OPTIONAL, 'Optional description', null],
        ];
    }

    /**
     * Execute example
     *
     * @return void
     */
    public function execute()
    {
        $this->getInput('Your name: ', 'name');
        $this->getInput(
            'Your sex: [M|f] ',
            'sex',
            function ($value) {
                return $value == 'm' || $value == 'f';
            }
        );

        echo $this->getOption('sex');

        exit(0);
    }

    /**
     * Output example
     *
     * @param string $string String to output - usually to shell
     *
     * @return void
     */
    public static function output($string)
    {
        echo $string . static::color();
    }

    /*
     * Base methods
     */

    /**
     * @return array
     */
    public static function getOpt()
    {
        $options = static::getOptions();
        $short = 'h';
        $long = ['help'];

        foreach ($options as $shortOpt => $definition) {
            list($longOpt, $mode, $description, $default) = $definition;

            switch ($mode) {
                case self::REQUIRED:
                    $short .= "{$shortOpt}:";
                    $long[] = "{$longOpt}:";
                    break;
                case self::OPTIONAL:
                    $short .= "{$shortOpt}::";
                    $long[] = "{$longOpt}::";
                    break;
                case self::NO_VALUE:
                    $short .= "{$shortOpt}";
                    $long[] = "{$longOpt}";
                    break;
            }
        }

        return [$short, $long];
    }

    /**
     * @param string $name    Option name
     * @param mixed  $default Default value if option not found
     *
     * @return mixed|null
     */
    public function getOption($name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * @param string $name Option name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * getInput
     *
     * @param string   $message   Message
     * @param string   $argument  Argument
     * @param callback $validator Validator
     *
     * @return mixed
     */
    public function getInput($message, $argument = null, $validator = null)
    {
        $this->output($message);

        $value = trim(fgets(STDIN));

        if ($validator && !$validator($value)) {
            return $this->getInput($message, $argument, $validator);
        }

        if ($argument) {
            $this->options[$argument] = $value;
        }

        return $value;
    }

    /**
     * Get class DocComment
     *
     * @return string
     */
    public function getDoc()
    {
        $lines = explode("\n", (new \ReflectionClass($this))->getDocComment()) + ['', ' * Command description'];

        return substr($lines[1], 3);
    }

    /**
     * Show help
     *
     * @return void
     */
    protected function help()
    {
        $help = "{reset}"
            . "{white}NAME{reset}\n\t{name} -- {description}"
            . "\n\n{white}SYNOPSIS{reset}\n\t{name} {usage}"
            . "\n\n{white}DESCRIPTION{reset}\n{options}"
            . "\n{reset}";

        $command = get_class($this);
        $usage = '';
        $options = "\t-h|--help\tShow this message [optional]\n";

        foreach (static::getOptions() as $shortOpt => $definition) {
            list($longOpt, $mode, $description, $default) = $definition;
            switch ($mode) {
                case self::REQUIRED:
                    $usage .= "--{$longOpt}|-{$shortOpt} ";
                    break;
                case self::OPTIONAL:
                    $usage .= "[--{$longOpt}|-{$shortOpt}] ";
                    break;
                case self::NO_VALUE:
                    $usage .= "[--{$longOpt}|-{$shortOpt}] ";
                    break;
            }

            $options .= "\t-{$shortOpt}|--{$longOpt}\t{$description} [{$mode}]\n";
        }

        $replacements = [
            '{white}'       => static::color(self::FG_WHITE),
            '{reset}'       => static::color(),
            '{name}'        => $command,
            '{description}' => $this->getDoc(),
            '{usage}'       => $usage,
            '{options}'     => $options,
        ];

        $this->output(str_replace(array_keys($replacements), $replacements, $help));
    }

    /**
     * Show error message
     *
     * @param string $message Error message
     *
     * @return void
     */
    protected function error($message)
    {
        self::output(static::color(self::FG_WHITE, self::BG_RED, self::FONT_BOLD) . $message);
        self::output("\n");
        exit(1);
    }

    /**
     * @param int $fg   Foreground color
     * @param int $bg   Background color
     * @param int $bold Font weight
     *
     * @return string
     */
    public static function color($fg = self::FG_DEFAULT, $bg = self::BG_DEFAULT, $bold = self::FONT_NORMAL)
    {
        return sprintf("\033[{$bold};{$fg};{$bg}m");
    }
}
