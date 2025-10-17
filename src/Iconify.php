<?php

namespace Larawise\Iconify;

use Illuminate\Support\LazyCollection;
use Larawise\Iconify\Contracts\IconifyContract;
use Larawise\Iconify\Contracts\IconifyRepositoryContract as Repository;
use Larawise\Iconify\Contracts\IconifySpriterContract as Spriter;

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
class Iconify implements IconifyContract
{
    /**
     * Create a new Iconify instance.
     *
     * @param Spriter $spriter
     * @param Repository $repository
     * @param array $config
     *
     * @return void
     */
    public function __construct(
        protected Spriter $spriter,
        protected Repository $repository,
        protected array $config
    ) { }

    /**
     * Compiles the given icon metadata into a complete svg markup.
     *
     * @param array $icon
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    public function compile($icon, $class = '', $attributes = [])
    {
        return $this->spriter
            ->size($icon['width'], $icon['height'])
            ->body($icon['body'])
            ->class($class)
            ->attributes($attributes)
            ->toHtml();
    }

    /**
     * Returns a lazy collection of icons for the given package.
     *
     * @param $namespace
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    public function render($namespace, $class = '', $attributes = [])
    {
        return ($meta = $this->resolve($namespace))
            ? $this->compile($meta, $class, $attributes)
            : '';
    }

    /**
     * Renders multiple icons and returns the concatenated SVG markup.
     *
     * @param array $names
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    public function renderMany($names, $class = '', $attributes = [])
    {
        return $this->resolveMany($names)
            ->map(fn($meta) => $this->compile($meta, $class, $attributes))
            ->implode('');
    }

    /**
     * Renders multiple icons grouped by package and name.
     *
     * @param array $names
     * @param string $class
     * @param array $attributes
     *
     * @return LazyCollection
     */
    public function renderManyGrouped($names, $class = '', $attributes = [])
    {
        return $this->resolveMany($names)
            ->groupBy(fn($meta) => $meta['package'])
            ->map(fn($group) => $group->mapWithKeys(fn($meta) => [
                $meta['icon'] => $this->compile($meta, $class, $attributes),
            ]));
    }

    /**
     * Renders multiple icons and returns a grouped JSON-ready array.
     *
     * @param array $names
     * @param string $class
     * @param array $attributes
     *
     * @return LazyCollection
     */
    public function renderManyGroupedJson($names, $class = '', $attributes = [])
    {
        return $this->resolveMany($names)
            ->groupBy(fn($meta) => $meta['package'])
            ->map(fn($group) => $group->mapWithKeys(fn($meta) => [
                $meta['icon'] => [
                    'svg' => $this->compile($meta, $class, $attributes),
                    'width' => $meta['width'],
                    'height' => $meta['height'],
                ]
            ]));
    }

    /**
     * Renders multiple icons as grouped preview HTML blocks.
     *
     * @param array $names
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    public function renderManyGroupedPreview($names, $class = '', $attributes = [])
    {
        return $this->renderManyGroupedJson($names, $class, $attributes)
            ->map(fn($icons, $package) => collect($icons)
                ->map(fn($data, $name) => "<div data-icon=\"{$name}\">{$data['svg']}</div>")
                ->implode('')
            )
            ->map(fn($body, $package) => "<div data-package=\"{$package}\">{$body}</div>")
            ->implode('');
    }

    /**
     * Prepares icon metadata from the registered icon packages.
     *
     * @param string $namespace
     *
     * @return array|null
     */
    protected function resolve($namespace)
    {
        [$package, $icon] = $this->extractPackageAndIcon($namespace);

        return $this->repository->loadIcon($package, $icon);
    }

    /**
     * Resolves multiple icons using optimal strategy based on icon and package count.
     *
     * @param array $names
     *
     * @return LazyCollection<array>
     */
    protected function resolveMany($names)
    {
        $analysis = $this->inspect($names);

        if ($analysis['strategies']['icon'] === 'single' && $analysis['strategies']['package'] === 'single') {
            return LazyCollection::make($names)
                ->map(fn($name) => $this->resolve($name))
                ->filter();
        }

        return $analysis['grouped_icons']->flatMap(function ($icons, $package) {
            $requested = $icons->map(fn($n) => explode(':', $n)[1])->all();

            return $this->repository->loadPackage($package)
                ->only($requested)
                ->values();
        });
    }

    /**
     * Returns a lazy collection of icons for the given package.
     *
     * @param string $package
     *
     * @return LazyCollection<string, array>
     */
    public function icons($package)
    {
        return $this->repository->loadPackage($package);
    }

    /**
     * Validate and parse an icon namespace string.
     *
     * @param string $name
     *
     * @return array|bool
     */
    protected function extractPackageAndIcon($name)
    {
        // Ensure the namespace contains a colon separator (e.g. "package:icon").
        if (! str_contains($name, ':')) {
            // Invalid format: missing separator.
            return false;
        }

        // Split the namespace into package and icon parts.
        [$package, $icon] = explode(':', $name, 2);

        // Return parsed parts if both are non-empty, otherwise mark as invalid.
        return (! empty($package) && ! empty($icon)) ? [$package, $icon] : false;
    }

    /**
     * Analyzes the given icon namespace list and determines optimal resolution strategies.
     *
     * @param array<string> $names
     *
     * @return array
     */
    protected function inspect($names)
    {
        // Thresholds for switching to bulk resolution
        $iconThreshold = $this->config['icon_threshold'] ?? 10;
        $packageThreshold = $this->config['package_threshold'] ?? 3;

        // Group icons by package name (prefix before colon)
        $grouped = collect($names)->groupBy(fn($n) => explode(':', $n)[0]);

        // Count total icons and distinct packages
        $icons = count($names);
        $packages = $grouped->keys()->count();

        // Determine resolution strategy based on thresholds
        $strategies = [
            'icon' => $icons > $iconThreshold ? 'bulk' : 'single',
            'package' => $packages > $packageThreshold ? 'bulk' : 'single',
        ];

        // Return analysis result for use in resolveMany and renderMany
        return [
            'strategies' => $strategies,
            'total_icons' => $icons,
            'total_packages' => $packages,
            'grouped_icons' => $grouped,
        ];
    }
}
