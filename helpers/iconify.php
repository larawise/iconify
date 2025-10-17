<?php

if (! function_exists('iconify')) {
    /**
     * Get the available iconify instance.
     *
     * @param string|null $name
     * @param string $class
     * @param array $attributes
     *
     * @return string|\Larawise\Iconify\Contracts\IconifyContract
     */
    function iconify($name = null, $class = '', $attributes = [])
    {
        if (is_null($name)) {
            return app('iconify');
        }

        return app('iconify')->render($name, $class, $attributes);
    }
}

if (! function_exists('iconify_render_many')) {
    /**
     * Render multiple icons as a single HTML string.
     *
     * @param array<string> $names
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    function iconify_render_many(array $names, string $class = '', array $attributes = [])
    {
        return iconify()->renderMany($names, $class, $attributes);
    }
}

if (! function_exists('iconify_grouped')) {
    /**
     * Render icons grouped by package as a LazyCollection.
     *
     * @param array<string> $names
     * @param string $class
     * @param array $attributes
     *
     * @return \Illuminate\Support\LazyCollection<string, array<string, string>>
     */
    function iconify_grouped(array $names, string $class = '', array $attributes = [])
    {
        return iconify()->renderManyGrouped($names, $class, $attributes);
    }
}

if (! function_exists('iconify_preview')) {
    /**
     * Render icons as grouped HTML preview blocks.
     *
     * @param array<string> $names
     * @param string $class
     * @param array $attributes
     *
     * @return string
     */
    function iconify_preview(array $names, string $class = '', array $attributes = [])
    {
        return iconify()->renderManyGroupedPreview($names, $class, $attributes);
    }
}

if (! function_exists('iconify_json')) {
    /**
     * Render icons as grouped JSON structure.
     *
     * @param array<string> $names
     * @param string $class
     * @param array $attributes
     *
     * @return \Illuminate\Support\LazyCollection<string, array<string, array{svg: string, width: int|null, height: int|null}>>
     */
    function iconify_json(array $names, string $class = '', array $attributes = [])
    {
        return iconify()->renderManyGroupedJson($names, $class, $attributes);
    }
}
