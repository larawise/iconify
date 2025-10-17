<?php

namespace Larawise\Iconify\Contracts;

use Illuminate\Support\LazyCollection;

/**
 * Srylius - The ultimate symphony for technology architecture!
 *
 * @package     Larawise
 * @subpackage  Iconify
 * @version     v1.0.0
 * @author      Selçuk Çukur <hk@selcukcukur.com.tr>
 * @copyright   Srylius Teknoloji Limited Şirketi
 *
 * @see https://docs.larawise.com/ Larawise : Docs
 */
interface IconifyRepositoryContract
{
    /**
     * Lazily loads and caches both metadata and icons for the given package.
     *
     * @param string $package
     *
     * @return void
     */
    public function load($package);

    /**
     * Retrieves a single icon's metadata and SVG body from the given package.
     *
     * @param string $package
     * @param string $icon
     *
     * @return array|null
     */
    public function loadIcon($package, $icon);

    /**
     * Retrieves metadata for the given package (prefix, dimensions, info, etc).
     *
     * @param string $package
     *
     * @return array|null
     */
    public function loadMeta($package);

    /**
     * Retrieves all icons from the given package as a LazyCollection.
     *
     * @param string $package
     *
     * @return LazyCollection<string, array>
     */
    public function loadPackage($package);

    /**
     * Clears cached metadata and icons for a specific package or all packages.
     *
     * @param string|null $package
     *
     * @return void
     */
    public function flush($package = null);
}
