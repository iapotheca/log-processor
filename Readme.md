
# Log Processor

[![Tests](https://github.com/iapotheca/log-processor/actions/workflows/php.yml/badge.svg)](https://github.com/iapotheca/log-processor/actions/workflows/php.yml)

This is a monolog log processor that prepares data intended to go to RabbitMQ to be observed by Logstash ingestion to persist metadata existent in the log message.

This package parses data from the log message into RabbitMQ plain fields. E.g: the following message will be converted to the data structure after:

> Message: `[KEY value] My message here`
> 
> Data: `{key: "value", message: "[KEY value] My message here"}`

## Installation

**Step 1**

Install dependencies and this package:

```shell
composer require php-amqplib/php-amqplib iapotheca/log-processor
```

**Step 2**

Install a RabbitMQ driver into your laravel instance by adding this to your `config/logging`:

```php
// ...
use App\Logging\RabbitmqLogger;
//...
    'rabbitmq' => [
        'driver' => 'custom',
        'via' => RabbitmqLogger::class, // <-- this is your custom logger
        'exchange-name' => env('LOG_RABBITMQ_EXCHANGE_NAME', 'logs'),
        'host' => env('LOG_RABBITMQ_HOST', 'localhost'),
        'port' => env('LOG_RABBITMQ_PORT', 5672),
        'user' => env('LOG_RABBITMQ_USER', 'guest'),
        'password' => env('LOG_RABBITMQ_PASSWORD', 'password'),
        'log-name' => env('LOG_RABBITMQ_LOG_NAME', 'logstash'),
    ],
//...
```

**Step 3**

Implement your **Custom Logger** where you use this **Processor**:

File: `app/Logging/RabbitmqLogger.php`

```php
<?php

namespace App\Logging;

use Iapotheca\LogProcessor\Processor;
use Monolog\Logger;
use PhpAmqpLib\Connection\AMQPSocketConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use Monolog\Handler\AmqpHandler;

class RabbitmqLogger
{
    public function __invoke(array $config)
    {
        $connection = new AMQPSocketConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password']
        );
        $channel = new AMQPChannel($connection);
        $logger = new Logger($config['log-name']);
        $handler = new AmqpHandler($channel, $config['exchange-name']);
        $handler->pushProcessor(new Processor(config('app.name'), [
            // these are the observed keys within the log message
            'KEY_ONE', 'KEY_TWO'
        ]));
        $logger->pushHandler($handler);
        return $logger;
    }
}
```
