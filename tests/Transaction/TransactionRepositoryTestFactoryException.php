<?php

namespace Prophit\Core\Tests\Transaction;

use Prophit\Core\Transaction\TransactionRepository;

class TransactionRepositoryTestFactoryException extends \RuntimeException
{
    const CODE_CANNOT_RESOLVE_CLASS = 1;
    const CODE_CLASS_MISSING_INTERFACE = 2;

    /**
     * @param class-string $fqcn
     */
    public static function cannotResolveClass(string $fqcn): self
    {
        $message = sprintf('Cannot resolve class: %s', $fqcn);
        return new self($message, static::CODE_CANNOT_RESOLVE_CLASS);
    }

    /**
     * @param class-string $fqcn
     */
    public static function classMissingInterface(string $fqcn): self
    {
        $message = sprintf('Class does not implement %s: %s', TransactionRepository::class, $fqcn);
        return new self($message, static::CODE_CLASS_MISSING_INTERFACE);
    }
}
