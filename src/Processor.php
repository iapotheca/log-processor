<?php

namespace Iapotheca\LogProcessor;

use Monolog\Processor\ProcessorInterface;

class Processor implements ProcessorInterface
{
    protected string $appName;
    protected array $keys;

    /** @var callable|null */
    protected $preProcessingCallback;

    /**
     * @param string|null $appName
     * @param array $keys
     * @param ?callable $preProcessingCallback
     */
    public function __construct(
        ?string $appName,
        array $keys = [],
        $preProcessingCallback = null
    ) {
        $this->appName = $appName;
        $this->keys = $keys;
        $this->preProcessingCallback = $preProcessingCallback;
    }

    public function __invoke(array $record): array
    {
        $record['app'] = $this->appName;

        foreach ($this->keys as $key) {
            $record[strtolower($key)] = $this->getAttribute($key, $record['message']);
        }

        if (null !== $this->preProcessingCallback) {
            $record = ($this->preProcessingCallback)($record);
        }

        return $record;
    }

    /**
     * Extracts courier name from log text. e.g.: [DATA_NAME some-data] => some-data
     *
     * @param string $message
     * @return mixed
     */
    protected function getAttribute(string $key, string $message)
    {
        $attributeMatches = [];
        preg_match('/' . $key . '.[0-9a-zA-Z\-\.]+/', $message, $attributeMatches, PREG_OFFSET_CAPTURE);
        $attributeMatches = $this->getFirstMatch($attributeMatches);

        if (null === $attributeMatches) {
            return null;
        }

        $attributeName = $this->getValue($attributeMatches);

        return null === $attributeName ? null : $attributeName;
    }

    /**
     * @param array $matches
     * @return mixed
     */
    private function getFirstMatch(array $matches)
    {
        $firstMatch = isset($matches[0]) ? $matches[0] : null;

        if (null === $firstMatch) {
            return null;
        }

        return isset($firstMatch[0]) ? $firstMatch[0] : null;
    }

    /**
     * @param string $metadata
     * @return mixed
     */
    private function getValue(string $metadata)
    {
        $exploded = explode(' ', $metadata);
        return isset($exploded[1]) ? $exploded[1] : null;
    }
}
