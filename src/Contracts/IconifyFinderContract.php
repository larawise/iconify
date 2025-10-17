<?php

namespace Larawise\Iconify\Contracts;

use Larawise\Support\Contracts\LocateableContract;

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
interface IconifyFinderContract extends LocateableContract
{
    /**
     * Get the fully qualified location of the package file.
     *
     * @param string $name
     *
     * @return string
     */
    public function find($name);

    /**
     * Flush the internal cache of resolved package paths.
     *
     * @return void
     */
    public function flush();
}
