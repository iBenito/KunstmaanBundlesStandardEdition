<?php

/*
 * This file is part of the DataGridBundle.
 *
 * (c) Stanislav Turza <sorien@mail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zizoo\JqGridCustomBundle\Twig;
use Zizoo\JqGridCustomBundle\Grid\Grid;

class JqGridExtension extends \Twig_Extension
{

    const DEFAULT_TEMPLATE = 'ZizooJqGridCustomBundle::blocks.html.twig';

    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var \Twig_TemplateInterface[]
     */
    protected $templates;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
                'jqgrid' => new \Twig_Function_Method($this, 'renderGrid',
                        array(
                            'is_safe' => array(
                                'html'
                            )
                        )),
                'jqgrid_js' => new \Twig_Function_Method($this, 'renderGridJs',
                        array(
                            'is_safe' => array(
                                'html'
                            )
                        )),
                'jqgrid_html' => new \Twig_Function_Method($this, 'renderGridHtml',
                        array(
                            'is_safe' => array(
                                'html'
                            )
                        )),
                'jqgrid_subgridmodel' => new \Twig_Function_Method($this, 'subGridConfig',
                        array(
                            'is_safe' => array(
                                'html'
                            )
                        )),
        );
    }

    public function renderGrid(Grid $grid)
    {
        if (!$grid->isOnlyData()) {
            return $this->renderBlock('jqgrid', array('grid' => $grid));
        }
    }

    public function renderGridJs(Grid $grid)
    {
        if (!$grid->isOnlyData()) {
            return $this->renderBlock('jqgrid_j', array('grid' => $grid));
        }
    }

    public function renderGridHtml(Grid $grid)
    {
        if (!$grid->isOnlyData()) {
            return $this->renderBlock('jqgrid_h', array('grid' => $grid));
        }
    }

    /**
     * Render block
     *
     * @param $name string
     * @param $parameters string
     * @return string
     */
    private function renderBlock($name, $parameters)
    {
        foreach ($this->getTemplates() as $template) {
            if ($template->hasBlock($name)) {
                return $template->renderBlock($name, $parameters);
            }
        }

        throw new \InvalidArgumentException(sprintf('Block "%s" doesn\'t exist in grid template.', $name));
    }

    /**
     * Has block
     *
     * @param $name string
     * @return boolean
     */
    private function hasBlock($name)
    {
        foreach ($this->getTemplates() as $template) {
            if ($template->hasBlock($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Template Loader
     *
     * @return \Twig_TemplateInterface[]
     * @throws \Exception
     */
    private function getTemplates()
    {
        if (empty($this->templates)) {
            $this->templates[] = $this->environment->loadTemplate($this::DEFAULT_TEMPLATE);
        }

        return $this->templates;
    }
    
    public function subGridConfig(Grid $grid)
    {
        $arr = array();
        $names = array();
        $widths = array();
        foreach ($grid->getSubGrid()->getColumns() as $c){
            $names[] = $c->getName();
            $widths[] = $c->getField('width');
        }
        $arr['name']    = $names;
        $arr['width']   = $widths;
        $extraParams = $grid->getExtraParams();
        if ($extraParams && array_key_exists('subGridParams', $extraParams)){
            $arr['params']  = $extraParams['subGridParams'];
        }
        return json_encode($arr);
    }

    public function getName()
    {
        return 'jq_grid_custom_twig_extension';
    }

}
