<?php

namespace Larawise\Iconify\Console;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Laravel\Prompts\Output\ConsoleOutput;
use Symfony\Component\Console\Attribute\AsCommand;
use ZipArchive;

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
#[AsCommand(name: 'iconify:add', description: 'Add existing icon packages for the application.')]
class IconifyAddCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'iconify:add {--packages= : Comma-separated list of icon package names to update (e.g. si,ph,mdi)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add existing icon packages for the application.';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new iconify update command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Notify that the update process has started.
        $this->components->info('Fetching the latest version icon packs for Iconify.');

        // Define paths for temporary storage and final destination.
        $destination = storage_path('app');
        $iconsDestination = $this->storagePaths();
        $zipDestination = "$destination/iconify.zip";

        // Download the master branch zip if not already downloaded recently.
        if (! $this->files->exists($zipDestination) || Carbon::createFromTimestamp(filectime($zipDestination))->diffInHours() > 1) {
            $this->clearScreen();
            $this->components->info('Downloading [master] branch...');

            try {
                // Download the zip file directly from GitHub.
                Http::withoutVerifying()
                    ->timeout(300)
                    ->sink(Utils::tryFopen($zipDestination, 'w+'))
                    ->get('https://github.com/iconify/icon-sets/archive/refs/heads/master.zip');
            } catch (ConnectionException $exception) {
                // Clean up and report failure if download fails.
                $this->files->delete($zipDestination);
                $this->components->error($exception->getMessage());
                return self::FAILURE;
            }
        }

        // Extract the downloaded zip archive.
        $this->clearScreen();
        $this->components->info('Extracting iconify package files.');
        $zip = new ZipArchive;
        $zip->open($zipDestination);
        $zip->extractTo($destination);
        $zip->close();

        // Notify that cleanup and filtering will begin.
        $this->clearScreen();
        $this->components->info('Purging unused data from the icon packages.');

        // Parse the --packages option and normalize values.
        $selectedPackages = collect(explode(',', (string) $this->option('packages')))
            ->filter()
            ->map(fn ($p) => strtolower(trim($p)))
            ->values()
            ->all();

        if (empty($selectedPackages)) {
            $this->clearScreen();
            $this->components->info('No specific packages provided. Updating all available icon sets.');
        }

        // Track updated packages.
        $packages = [];

        // Iterate over all JSON files in the extracted icon sets.
        foreach ($this->files->allFiles("$destination/icon-sets-master/json") as $file) {
            // Get the package name from the filename (e.g. si.json → si).
            $filename = strtolower($file->getFilenameWithoutExtension());

            // If specific packages were requested, skip others.
            if (! empty($selectedPackages) && ! in_array($filename, $selectedPackages)) {
                continue;
            }

            // Read and decode the icon set JSON.
            $content = json_decode($this->files->get($file), true);

            // Remove unnecessary metadata keys to reduce file size.
            $prepared = (new Collection($content))->forget([
                'info', 'lastModified', 'prefixes',
                'categories', 'aliases', 'suffixes'
            ])->toJson();

            // Ensure the destination directory exists.
            $this->files->ensureDirectoryExists($iconsDestination);

            // Overwrite the original file with the cleaned version.
            $this->files->put($file, $prepared);

            // Move the cleaned file to the final destination.
            $this->files->move($file->getPathname(), "$iconsDestination/{$file->getFilename()}");

            // Track the updated package name.
            $packages[] = $filename;

            // Log the updated package.
            $this->clearScreen();
            $this->components->info("Updated package: {$filename}");
        }

        // Count how many packages were updated.
        $count = count($packages);

        // Clean up temporary files and folders.
        $this->files->delete($zipDestination);
        $this->files->deleteDirectory("$destination/icon-sets-master");

        // Report the final result.
        $this->clearScreen();
        $this->components->info("A total of [{$count}] package(s) were updated by Iconify.");

        // Notify me that the operation was successful.
        return self::SUCCESS;
    }

    protected function storagePaths()
    {
        return $this->laravel->make('config')->get(
            'iconify.destination', storage_path('app/collections/iconify')
        );
    }

    protected function cachePath()
    {
        return $this->laravel->make('config')->get(
            'iconify.compiled', storage_path('framework/cache')
        );
    }

    protected function clearScreen(): void
    {
        (new ConsoleOutput())->write("\033[H\033[J");
    }

}
