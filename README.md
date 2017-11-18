# Rapture PHP command component

[![PhpVersion](https://img.shields.io/badge/php-5.4-orange.svg?style=flat-square)](#)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](#)

Simple PHP command class.

## Requirements

- PHP v5.4

## Install

```
composer require mrjulio/rapture-command
```

## Quick start

```php

namespace AppName\Domain\Command;

class DayOfWeek extends Command
{
	public static function getOptions()
    {
        return [
            'd' => ['date', self::REQUIRED, 'Date', null],
        ];
    }
    
    public function execute()
    {
    	$date = new \DateTime($this->getOption('date'));
        
        $this->output($date->format('W'));
    }
}
```

```bash
# run inside \AppName\Domain\Command
php console.php --cmd=DayOfWeek --env=dev --date=2017-01-01
```

## About

### Author

Iulian N. `rapture@iuliann.ro`

### Testing

```
cd ./test && phpunit
```

### License

Rapture PHP command is licensed under the MIT License - see the `LICENSE` file for details.
