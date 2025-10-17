<?php

namespace Larawise\Iconify;

use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;
use Larawise\Iconify\Directive\IconifyDirective;
use Larawise\Iconify\Directive\IconifyStackDirective;
use Larawise\Packagify\Packagify;
use Larawise\Packagify\PackagifyProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\VarExporter\VarExporter;

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
class IconifyProvider extends PackagifyProvider
{
    /**
     * Configure the packagify package.
     *
     * @param Packagify $package
     *
     * @return void
     */
    public function configure(Packagify $package)
    {
        // Set package name
        $package->name('iconify');

        // Set package description
        $package->description('Iconify - ');

        // Set package description
        $package->version('1.0.0');

        // Set the package provideable.
        $package->hasConfigurations();
        $package->hasHelpers();
        $package->hasViews();
        $package->hasComponents('iconify', $this->path('resources/views/components'));
        $package->hasCommands([
            Console\IconifyCacheCommand::class,
            Console\IconifyClearCommand::class,
            Console\IconifyUpdateCommand::class,
            Console\IconifyRemoveCommand::class,
        ]);

        $package->hasSingletons([
            'iconify.spriter' => fn () => new IconifySpriter,
            'iconify.finder' => fn () => new IconifyFinder($this->app['files'], $this->app->make('config')->get('iconify.paths')),
            'iconify.repository' => fn () => new IconifyRepository($this->app['iconify.finder']),
            'iconify' => fn () => new Iconify($this->app['iconify.spriter'], $this->app['iconify.repository'], $this->app->make('config')->get('iconify')),
        ]);

        $package->hasDirectives([
            'iconify' => app(IconifyDirective::class),
            'iconifyStack' => app(IconifyStackDirective::class)
        ]);
    }
}
