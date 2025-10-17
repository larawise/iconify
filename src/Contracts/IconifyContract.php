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
interface IconifyContract
{
    /**
     * Compiles the given icon metadata into a complete svg markup.
     *
     * @param array $icon
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    public function compile($icon, $class = '', $attributes = []);

    /**
     * Returns a lazy collection of icons for the given package.
     *
     * @param $namespace
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    public function render($namespace, $class = '', $attributes = []);

    /**
     * Renders multiple icons and returns the concatenated SVG markup.
     *
     * @param array $names
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    public function renderMany($names, $class = '', $attributes = []);

    /**
     * Renders multiple icons grouped by package and name.
     *
     * @param array $names
     * @param string $class
     * @param array $attributes
     *
     * @return LazyCollection
     */
    public function renderManyGrouped($names, $class = '', $attributes = []);

    /**
     * Renders multiple icons and returns a grouped JSON-ready array.
     *
     * @param array $names
     * @param string $class
     * @param array $attributes
     *
     * @return LazyCollection
     */
    public function renderManyGroupedJson($names, $class = '', $attributes = []);

    /**
     * Renders multiple icons as grouped preview HTML blocks.
     *
     * @param array $names
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    public function renderManyGroupedPreview($names, $class = '', $attributes = []);

    /**
     * Returns a lazy collection of icons for the given package.
     *
     * @param string $package
     *
     * @return LazyCollection<string, array>
     */
    public function icons($package);
}
