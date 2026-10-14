<?php

declare(strict_types=1);

namespace ACP\Storage;

use AC\Exception\IteratorException;
use FilterIterator;
use Iterator;
use SplFileInfo;
use Throwable;

final class FileIterator extends FilterIterator
{
    private string $extension;

    public function __construct(Iterator $iterator, string $extension)
    {
        parent::__construct($iterator);

        $this->extension = $extension;
    }

    public function accept(): bool
    {
        $file = $this->getInnerIterator()->current();

        try {
            return $file instanceof SplFileInfo &&
                   $file->isFile() &&
                   $file->isReadable() &&
                   $file->getSize() &&
                   $file->getExtension() === $this->extension;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function current(): SplFileInfo
    {
        $current = parent::current();

        if (! $current instanceof SplFileInfo) {
            throw IteratorException::from_current();
        }

        return $current;
    }

}
