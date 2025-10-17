<?php

namespace Larawise\Iconify;

use Illuminate\Filesystem\Filesystem;
use Larawise\Iconify\Contracts\IconifyFinderContract;
use Larawise\Iconify\Exceptions\IconifyException;
use Larawise\Support\Traits\Locateable;

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
class IconifyFinder implements IconifyFinderContract
{
    use Locateable;

    /**
     * Cache of resolved package paths keyed by identifier.
     *
     * @var array<string, string>
     */
    protected $packages;

    /**
     * Create a new iconify finder instance.
     *
     * @param Filesystem $files
     * @param array $paths
     * @param array|null $extensions
     *
     * @return void
     */
    public function __construct(Filesystem $files, array $paths, ?array $extensions = null)
    {
        $this->files = $files;
        $this->paths = array_map($this->resolvePath(...), $paths);

        if (isset($extensions)) {
            $this->extensions = $extensions;
        }
    }

    /**
     * Get the fully qualified location of the package file.
     *
     * @param string $name
     *
     * @return string
     */
    public function find($name)
    {
        // Return cached result if already resolved
        if (isset($this->packages[$name])) {
            return $this->packages[$name];
        }

        // Trim and check if the name includes namespace hint information
        if ($this->hasHintInformation($name = trim($name))) {
            // Resolve using namespace-specific hint paths
            return $this->packages[$name] = $this->findInNamespace($name);
        }

        // Search in globally registered paths
        return $this->packages[$name] = $this->findInPaths($name, $this->paths);
    }

    /**
     * Resolve and locate a package file using its namespaced identifier.
     *
     * @param string $name
     *
     * @return string
     * @throws IconifyException
     */
    protected function findInNamespace($name)
    {
        // Split the identifier into namespace and package segments
        [$namespace, $package] = $this->resolveNamespace($name);

        // Ensure both segments are present and valid
        if (! isset($namespace, $package)) {
            throw new IconifyException("Invalid package identifier [{$name}]. Expected format: namespace{$this->delimiter}package.");
        }

        // Ensure the namespace has registered hint paths
        if (! isset($this->hints[$namespace])) {
            throw new IconifyException("No hint paths registered for namespace [{$namespace}].");
        }

        // Search for the package file within the namespace's hint paths
        return $this->findInPaths($package, $this->hints[$namespace]);
    }

    /**
     * Search for the given package name across the provided paths.
     *
     * @param string $name
     * @param string|string[] $paths
     *
     * @return string
     * @throws IconifyException
     */
    protected function findInPaths($name, $paths)
    {
        // Iterate over each search path
        foreach ((array) $paths as $path) {
            // Generate possible file names for the given identifier and extensions
            foreach ($this->getPossibleFiles($name, $this->getExtensions()) as $file) {
                $packagePath = $path . DIRECTORY_SEPARATOR . $file;

                // Ensure path length is safe and file exists in filesystem
                if (strlen($packagePath) < (PHP_MAXPATHLEN - 1) && $this->files->exists($packagePath)) {
                    // Return the first matching file path
                    return $packagePath;
                }
            }
        }

        // No matching file found in any path — throw a descriptive exception
        throw new IconifyException("Package [{$name}] not found in any registered paths. Check name, extensions, and hint configuration.");
    }

    /**
     * Flush the internal cache of resolved package paths.
     *
     * @return void
     */
    public function flush()
    {
        $this->packages = [];
    }
}
