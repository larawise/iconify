<?php

namespace Larawise\Iconify\Directive;

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
class IconifyDirective
{
    /**
     * Compiles the Blade directive expression into executable PHP.
     *
     * @param string $expression
     *
     * @return string
     */
    public function __invoke($expression)
    {
        [$name, $class] = $this->parse($expression);

        // Return a PHP echo statement that renders the icon with the given name and class.
        return "<?php echo \\Iconify::render({$name}, {$class}); ?>";
    }

    /**
     * Parses the Blade directive expression into icon name and CSS class.
     *
     * @param string $expression
     *
     * @return array{name: string, class: string}
     */
    protected function parse($expression)
    {
        // Split the expression by comma, allowing up to two parts: name and class.
        $parts = explode(',', $expression, 2);

        // Trim whitespace and ensure fallback to empty string if missing.
        $name = trim($parts[0] ?? "''");
        $class = trim($parts[1] ?? "''");

        return [$name, $class];
    }
}
