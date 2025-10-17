<?php

namespace Larawise\Iconify\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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
#[AsCommand(name: 'iconify:remove', description: 'Remove the existing icon package for the application.')]
class IconifyRemoveCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'iconify:remove {package : Alias of the icon pack you want to remove.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the existing icon package for the application.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // Get icon pack name.
        $name = $this->argument('package');

        // Define the path to the cache file of icon packs.
        $package = $this->laravel->storagePath("app/collections/iconify/{$name}.json");

        // Check if the cache file exists in the directory.
        if (File::exists($package)) {
            // Permanently delete the cache file in the directory.
            File::delete($package);

            // Write a notification message that the cache has been cleared.
            $this->components->info("[{$name}] icon package has been successfully removed.");

            // Notify me that the operation was successful.
            return self::SUCCESS;
        }

        // Write a notification message that the cache has been cleared.
        $this->components->warn('Icon package you want to remove does not already exist.');

        // Notify me that the operation was successful.
        return self::SUCCESS;
    }
}
