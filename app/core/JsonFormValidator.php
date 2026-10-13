<?php

declare(strict_types=1);

namespace App\Core;

use JsonException;

/**
 * Small dependency-free validator. Form rules live in JSON so PHP validation
 * and the database-facing controllers share an explicit, readable contract.
 */
final class JsonFormValidator
{
    /** @var array<string, array<string, mixed>> */
    private array $schemas;

    public function __construct(string $schemaFile)
    {
        try {
            $contents = file_get_contents($schemaFile);
            $decoded = json_decode($contents ?: '', true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new \RuntimeException('The form validation JSON file is invalid.');
        }

        $this->schemas = is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    public function validate(string $schemaName, array $data): array
    {
        $schema = $this->schemas[$schemaName] ?? null;
        if (!is_array($schema)) {
            throw new \InvalidArgumentException('Unknown validation schema: ' . $schemaName);
        }

        // Decode again after encoding to guarantee the submitted payload is JSON-safe.
        json_decode(json_encode($data, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        $errors = [];

        foreach (($schema['fields'] ?? []) as $field => $rules) {
            $value = $data[$field] ?? null;
            $label = (string) ($rules['label'] ?? ucfirst((string) $field));
            $stringValue = is_string($value) ? trim($value) : $value;

            if (($rules['required'] ?? false) && ($stringValue === '' || $stringValue === null)) {
                $errors[$field] = $label . ' is required.';
                continue;
            }
            if ($stringValue === '' || $stringValue === null) {
                continue;
            }
            if (
                ($rules['type'] ?? 'string') === 'email'
                && (!is_string($stringValue) || filter_var($stringValue, FILTER_VALIDATE_EMAIL) === false)
            ) {
                $errors[$field] = 'Enter a valid email address.';
            }
            if (
                isset($rules['minLength'])
                && (!is_string($stringValue) || mb_strlen($stringValue) < (int) $rules['minLength'])
            ) {
                $errors[$field] = $label . ' must be at least ' . (int) $rules['minLength'] . ' characters.';
            }
            if (
                isset($rules['maxLength'])
                && is_string($stringValue)
                && mb_strlen($stringValue) > (int) $rules['maxLength']
            ) {
                $errors[$field] = $label . ' must not exceed ' . (int) $rules['maxLength'] . ' characters.';
            }
            if (($rules['type'] ?? '') === 'integer' && filter_var($stringValue, FILTER_VALIDATE_INT) === false) {
                $errors[$field] = $label . ' must be a whole number.';
            }
            if (isset($rules['minimum']) && is_numeric($stringValue) && (int) $stringValue < (int) $rules['minimum']) {
                $errors[$field] = $label . ' cannot be below ' . (int) $rules['minimum'] . '.';
            }
            if (
                isset($rules['pattern'])
                && is_string($stringValue)
                && preg_match('/' . $rules['pattern'] . '/', $stringValue) !== 1
            ) {
                $errors[$field] = (string) ($rules['message'] ?? ($label . ' has an invalid format.'));
            }
        }

        return $errors;
    }
}
