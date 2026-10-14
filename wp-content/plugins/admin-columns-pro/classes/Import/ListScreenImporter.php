<?php

declare(strict_types=1);

namespace ACP\Import;

use AC\ListScreenCollection;
use ACP\Exception\UnserializeException;
use ACP\Import\Exception\ImportPathNotReadable;
use ACP\Import\Exception\InvalidImportFile;
use ACP\Storage\Unserializer\JsonUnserializer;
use ACP\Tools\ImportHandler;

final class ListScreenImporter
{
    private JsonUnserializer $unserializer;

    private ImportHandler $import_handler;

    public function __construct(JsonUnserializer $unserializer, ImportHandler $import_handler)
    {
        $this->unserializer = $unserializer;
        $this->import_handler = $import_handler;
    }

    public function from_file(string $path, ?ImportOptions $options = null): ListScreenCollection
    {
        $encoded_data = $this->decode_file($path);

        return $this->import_handler->handle(
            $encoded_data,
            null,
            ($options ?? ImportOptions::create())->to_overwrites()
        );
    }

    public function from_directory(string $dir, ?ImportOptions $options = null): ListScreenCollection
    {
        if (! is_dir($dir) || ! is_readable($dir)) {
            throw ImportPathNotReadable::from_path($dir);
        }

        $files = glob(rtrim($dir, '/') . '/*.json') ?: [];

        $encoded_data = [];

        foreach ($files as $file) {
            foreach ($this->decode_file($file) as $item) {
                $encoded_data[] = $item;
            }
        }

        return $this->import_handler->handle(
            $encoded_data,
            null,
            ($options ?? ImportOptions::create())->to_overwrites()
        );
    }

    /**
     * @return array<int|string, mixed>
     * @throws ImportPathNotReadable
     * @throws InvalidImportFile
     */
    private function decode_file(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw ImportPathNotReadable::from_path($path);
        }

        try {
            return $this->unserializer->unserialize(
                (string)file_get_contents($path)
            );
        } catch (UnserializeException $e) {
            throw InvalidImportFile::from_path($path, $e);
        }
    }

}
