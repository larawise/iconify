<?php

namespace Larawise\Iconify\Contracts;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;

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
interface IconifySpriterContract extends Htmlable, Stringable
{
    /**
     * Set the full SVG attributes array.
     *
     * @param array<string, string> $attributes
     *
     * @return IconifySpriterContract
     */
    public function attributes($attributes);

    /**
     * Set the SVG body content.
     *
     * @param string $body
     *
     * @return IconifySpriterContract
     */
    public function body($body);

    /**
     * Set the icon's CSS class.
     *
     * @param string $class
     *
     * @return IconifySpriterContract
     */
    public function class($class);

    /**
     * Override the auto-generated icon ID.
     *
     * @param string $id
     *
     * @return IconifySpriterContract
     */
    public function id($id);

    /**
     * Determine if the spriter has no body content.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Alias for toHtml(), improves readability in Blade and view logic.
     *
     * @return string
     */
    public function render();

    /**
     * Set the icon dimensions.
     *
     * @param int|string $width
     * @param int|string $height
     *
     * @return IconifySpriterContract
     */
    public function size($width, $height);

    /**
     * Set the Blade stack name for pushing <g> definitions.
     *
     * @param string $stack
     *
     * @return IconifySpriterContract
     */
    public function stack($stack);

    /**
     * Set the icon's inline style.
     *
     * @param string $style
     *
     * @return IconifySpriterContract
     */
    public function style($style);

    /**
     * Set the icon title and enable accessibility role.
     *
     * @param string $title
     *
     * @return IconifySpriterContract
     */
    public function title($title);
}
