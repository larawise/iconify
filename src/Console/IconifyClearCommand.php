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
#[AsCommand(name: 'iconify:clear', description: 'Clear the existing icon packages cache for the application.')]
class IconifyClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'iconify:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the existing icon packages cache for the application.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // Define the path to the cache file of icon packs.
        $icons = $this->laravel->storagePath('framework/cache/iconify.php');

        // Check if the cache file exists in the directory.
        if (File::exists($icons)) {
            // Permanently delete the cache file in the directory.
            File::delete($icons);

            // Write a notification message that the cache has been cleared.
            $this->components->info('The icon packages cache was cleared successfully.');

            // Notify me that the operation was successful.
            return self::SUCCESS;
        }

        // Write a notification message that the cache has been cleared.
        $this->components->warn('No action was taken because icon packs were not cached.');

        // Notify me that the operation was successful.
        return self::SUCCESS;
    }
}
