<?php

namespace Larawise\Iconify\Facades;

use Illuminate\Support\Facades\Facade;

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
 *
 * @method static string compile(array $payload, string $class, array $attributes = [])
 * @method static \Illuminate\Support\LazyCollection icons(string $package)
 * @method static \Illuminate\Support\LazyCollection packages()
 * @method static string render(string $namespace, string $class = '', array $attributes = [])
 * @method static string renderMany(string $namespace, string $class = '', array $attributes = [])
 * @method static string renderManyGrouped(string $namespace, string $class = '', array $attributes = [])
 * @method static string renderManyGroupedJson(string $namespace, string $class = '', array $attributes = [])
 * @method static string renderManyGroupedPreview(string $namespace, string $class = '', array $attributes = [])
 * @method static \Illuminate\Support\LazyCollection search(string $name)
 *
 * @see \Larawise\Iconify\Iconify
 */
class Iconify extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'iconify';
    }
}
