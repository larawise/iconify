<?php

namespace Larawise\Iconify;

use Illuminate\Support\Facades\Blade;
use Larawise\Iconify\Contracts\IconifySpriterContract;

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
final class IconifySpriter implements IconifySpriterContract
{
    /**
     * The compiled SVG attributes.
     *
     * @var array<string, string>
     */
    protected $attributes = [];

    /**
     * The raw SVG body content.
     *
     * @var string|null
     */
    protected $body = null;

    /**
     * The default icon height.
     *
     * @var int|string
     */
    protected $height = '256';

    /**
     * Optional override for the generated icon ID.
     *
     * @var string|null
     */
    protected $id = null;

    /**
     * The Blade stack name used for pushing <g> definitions.
     *
     * @var string
     */
    protected $stack = 'iconify';

    /**
     * The default icon width.
     *
     * @var int|string
     */
    protected $width = '256';

    /**
     * Set the full SVG attributes array.
     *
     * @param array<string, string> $attributes
     *
     * @return $this
     */
    public function attributes($attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        $this->attributes['xmlns'] ??= 'http://www.w3.org/2000/svg';
        $this->attributes['viewBox'] ??= "0 0 {$this->width} {$this->height}";

        return $this;
    }

    /**
     * Set the SVG body content.
     *
     * @param string $body
     *
     * @return $this
     */
    public function body($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Build a string of HTML attributes from the given array.
     *
     * @param array<string, string|null> $attributes
     *
     * @return string
     */
    protected function buildAttributeString($attributes)
    {
        return collect($attributes)
            ->filter(fn($value) => $value !== null && $value !== '')
            ->map(fn($value, $key) => sprintf(' %s="%s"', $key, $value))
            ->implode('');
    }

    /**
     * Build the reusable <g> definition block using Blade stack.
     *
     * @param string $id
     *
     * @return string
     */
    protected function buildDefinition($id)
    {
        return <<<BLADE
        @once('{$id}')
            @push('{$this->stack}')
                <g id="{$id}">{$this->body}</g>
            @endpush
        @endonce
    BLADE;
    }

    /**
     * Build the final <svg> element with attributes, title, and use tag.
     *
     * @param string $id
     *
     * @return string
     */
    protected function buildSvg($id)
    {
        $attributes = $this->buildAttributeString($this->attributes);
        $title = $this->buildTitleTag(empty($this->attributes['title']) ? '' : $this->attributes['title']);
        $use = $this->buildUseTag($id);

        return sprintf('<svg%s>%s%s</svg>', $attributes, $title, $use);
    }

    /**
     * Build the <title> tag if a title is provided.
     *
     * @param string|null $title
     *
     * @return string
     */
    protected function buildTitleTag($title)
    {
        return $title ? '<title>' . e($title) . '</title>' : '';
    }

    /**
     * Build the <use> tag referencing the icon definition by ID.
     *
     * @param string $id
     *
     * @return string
     */
    protected function buildUseTag($id)
    {
        return sprintf('<use href="#%s"></use>', $id);
    }

    /**
     * Set the icon's CSS class.
     *
     * @param string $class
     *
     * @return $this
     */
    public function class($class)
    {
        $this->attributes['class'] = trim($class);

        return $this;
    }

    /**
     * Override the auto-generated icon ID.
     *
     * @param string $id
     *
     * @return $this
     */
    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Determine if the spriter has no body content.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->body);
    }

    /**
     * Alias for toHtml(), improves readability in Blade and view logic.
     *
     * @return string
     */
    public function render()
    {
        return $this->toHtml();
    }

    /**
     * Set the icon dimensions.
     *
     * @param int|string $width
     * @param int|string $height
     *
     * @return $this
     */
    public function size($width, $height)
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * Set the Blade stack name for pushing <g> definitions.
     *
     * @param string $stack
     *
     * @return $this
     */
    public function stack($stack)
    {
        $this->stack = $stack;

        return $this;
    }

    /**
     * Set the icon's inline style.
     *
     * @param string $style
     *
     * @return $this
     */
    public function style($style)
    {
        $this->attributes['style'] = trim(($this->attributes['style'] ?? '') . ';' . rtrim($style, ';'));

        return $this;
    }

    /**
     * Set the icon title and enable accessibility role.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->attributes['title'] = $title;
        $this->attributes['role'] = 'img';

        return $this;
    }

    /**
     * Render the full HTML output including SVG and Blade definition.
     *
     * @return string
     */
    public function toHtml()
    {
        if ($this->isEmpty()) {
            return '';
        }

        $id = $this->id ?? $this->generateId($this->body);
        $svg = $this->buildSvg($id);
        $definition = $this->buildDefinition($id);

        return $svg . PHP_EOL . Blade::render($definition);
    }

    /**
     * Generate a unique ID for the icon definition.
     *
     * @param string $body
     *
     * @return string
     */
    protected function generateId($body)
    {
        $normalized = str_replace(["\r\n", "\r", PHP_EOL], "\n", trim($body));

        return sprintf('iconify-%s', md5($normalized));
    }

    /**
     * Return the rendered HTML when cast to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}
