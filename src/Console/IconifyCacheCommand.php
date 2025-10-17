<?php

namespace Larawise\Iconify\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

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
#[AsCommand(name: 'iconify:cache', description: 'Cache existing icon packages for the application.')]
class IconifyCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'iconify:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache existing icon packages for the application.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Silently run cache clear command.
        $this->callSilent('iconify:clear');

        // Determine if iconify instance is present in the container.
        if (! $this->laravel->bound('iconify')) {
            // Write a notification message that the cache has been created.
            $this->components->error('Iconify instance not found in container incorrect configuration.');

            // Notify me that the operation was failed.
            return self::FAILURE;
        }

        // Collect all available icon packs for the app.
        $packages = $this->laravel->make('iconify')->all();

        // Write cache file to storage directory.
        File::put(
            // Path
            $this->laravel->storagePath('framework/cache/iconify.php'),
            // Content
            '<?php return '.var_export($packages, true).';'.PHP_EOL
        );

        // Write a notification message that the cache has been created.
        $this->components->info('Application icon packages have been cached successfully.');

        // Notify me that the operation was successful.
        return self::SUCCESS;
    }
}
