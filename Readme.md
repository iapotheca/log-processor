
# Courier Network Log Processor

This is a monolog log processor that prepares data intended to go to RabbitMQ to be observed by Logstash ingestion to persist metadata existent in the log message.

## Installation

**Step 1**

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

**Step 2**

Implement your **Custom Logger** where you use this **Processor**:

File: `app/Logging/RabbitmqLogger.php`

```php
<?php

namespace App\Logging;

use Iapotheca\CrLogProcessor\EconorouteProcessor;
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
        $handler->pushProcessor(new EconorouteProcessor(config('app.name')));
        $logger->pushHandler($handler);
        return $logger;
    }
}
```