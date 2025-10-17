<?php

namespace Larawise\Iconify;

use Illuminate\Support\LazyCollection;
use Larawise\Iconify\Contracts\IconifyFinderContract as Finder;
use Larawise\Iconify\Contracts\IconifyRepositoryContract;

/**
 * Srylius - The ultimate symphony for technology architecture!
 *
 * @package     Larawise
 * @subpackage  Iconify
 * @version     v1.0.0
 * @author      Selçuk Çukur <hk@selcukcukur.com.tr>
 * @copyright   Srylius Teknoloji Limited Şirketi
 *
 * @see https://docs.srylius.com/ Srylius : Docs
 */
final class IconifyRepository implements IconifyRepositoryContract
{
    /**
     * Create a new Iconify repository instance.
     *
     * @param Finder $finder
     * @param array $packagesCache
     * @param array $iconsCache
     *
     * @return void
     */
    public function __construct(
        protected Finder $finder,
        protected array $packagesCache = [],
        protected array $iconsCache = []
    ) {}

    /**
     * Lazily loads and caches both metadata and icons for the given package.
     *
     * @param string $package
     *
     * @return void
     */
    public function load($package)
    {
        if (isset($this->packagesCache[$package]) && isset($this->iconsCache[$package])) {
            return;
        }

        $path = $this->finder->find($package);
        $data = require $path;

        $this->packagesCache[$package] = $this->preparePackage($data, $package);
        $this->iconsCache[$package] = $this->prepareIcons($data, $package);
    }

    /**
     * Retrieves a single icon's metadata and SVG body from the given package.
     *
     * @param string $package
     * @param string $icon
     *
     * @return array|null
     */
    public function loadIcon($package, $icon)
    {
        $this->load($package);

        return $this->iconsCache[$package]->get($icon);
    }

    /**
     * Retrieves metadata for the given package (prefix, dimensions, info, etc).
     *
     * @param string $package
     *
     * @return array|null
     */
    public function loadMeta($package)
    {
        $this->load($package);

        return $this->packagesCache[$package] ?? null;
    }

    /**
     * Retrieves all icons from the given package as a LazyCollection.
     *
     * @param string $package
     *
     * @return LazyCollection<string, array>
     */
    public function loadPackage($package)
    {
        $this->load($package);

        return $this->iconsCache[$package];
    }

    /**
     * Converts raw icon data into a LazyCollection of normalized icon entries.
     *
     * @param array $data
     * @param string $package
     *
     * @return LazyCollection<string, array>
     */
    protected function prepareIcons($data, $package)
    {
        $meta = $this->preparePackage($data, $package);

        return LazyCollection::make(function () use ($data, $meta) {
            foreach ($data['icons'] ?? [] as $name => $icon) {
                yield $name => [
                    'package' => $meta['package'],
                    'icon' => $name,
                    'body' => $icon['body'] ?? '',
                    'width' => $meta['width'],
                    'height' => $meta['height'],
                ];
            }
        });
    }

    /**
     * Extracts and normalizes metadata from raw package data.
     *
     * @param array $data
     * @param string $package
     *
     * @return array
     */
    protected function preparePackage($data, $package)
    {
        return [
            'package' => $package,
            'prefix' => $data['prefix'] ?? $package,
            'info' => $data['info'] ?? [],
            'width' => $data['width'] ?? null,
            'height' => $data['height'] ?? null,
            'lastModified' => $data['lastModified'] ?? null,
            'suffixes' => $data['suffixes'] ?? [],
        ];
    }

    /**
     * Clears cached metadata and icons for a specific package or all packages.
     *
     * @param string|null $package
     *
     * @return void
     */
    public function flush($package = null)
    {
        if ($package) {
            unset($this->packagesCache[$package], $this->iconsCache[$package]);
        } else {
            $this->packagesCache = [];
            $this->iconsCache = [];
        }
    }
}
