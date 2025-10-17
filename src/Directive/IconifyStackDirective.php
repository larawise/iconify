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
class IconifyStackDirective
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
        return "<?php echo view('iconify::spriter')->render(); ?>";
    }
}
