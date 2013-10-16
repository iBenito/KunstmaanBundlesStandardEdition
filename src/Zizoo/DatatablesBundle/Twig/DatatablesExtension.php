<?php

namespace Zizoo\DatatablesBundle\Twig;
use Zizoo\DatatablesBundle\Datatables\Datatable;

class DatatablesExtension extends \Twig_Extension
{

    const DEFAULT_TEMPLATE = 'ZizooDatatablesBundle::blocks.html.twig';

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
                'datatable' => new \Twig_Function_Method($this, 'renderDatatable',
                        array(
                            'is_safe' => array(
                                'html'
                            )
                        )),
                'datatable_js' => new \Twig_Function_Method($this, 'renderDatatableJavaScript',
                        array(
                            'is_safe' => array(
                                'html'
                            )
                        )),
                'datatable_html' => new \Twig_Function_Method($this, 'renderDatatableHTML',
                        array(
                            'is_safe' => array(
                                'html'
                            )
                        )),
                'datatable_aoColumns' => new \Twig_Function_Method($this, 'renderDatatableColumns',
                        array(
                            'is_safe' => array(
                                'html'
                            )
                        ))
        );
    }

    
    public function renderDatatable(Datatable $datatable)
    {
        if (!$datatable->isOnlyData()) {
            return $this->renderBlock('datatable', array('datatable' => $datatable));
        }
    }

    public function renderDatatableJs(Datatable $datatable)
    {
        if (!$datatable->isOnlyData()) {
            return $this->renderBlock('datatable_js', array('datatable' => $datatable));
        }
    }

    public function renderDatatableHtml(Datatable $datatable)
    {
        if (!$datatable->isOnlyData()) {
            return $this->renderBlock('datatable_html', array('datatable' => $datatable));
        }
    }
    
    public function renderDatatableColumns(Datatable $datatable)
    {
        if (!$datatable->isOnlyData()) {
            $i=0;
            $columnDefs = array();
            foreach ($datatable->getColumns() as $key => $c){
                $def = array(   'mData'         => $key,
                                'bSearchable'   => array_key_exists('search', $c) && $c['search']!==false,
                                'bSortable'     => array_key_exists('sortable', $c) && $c['sortable']!==false,
                                'aTargets'      => array($i++),
                                'bVisible'      => array_key_exists('visible', $c)?$c['visible']:true,
                                'id'            => $key);
                //if (array_key_exists('render', $c)) $def['mRender'] = $c['render'];
                $columnDefs[] = $def;
            }
            return json_encode($columnDefs);
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

        throw new \InvalidArgumentException(sprintf('Block "%s" doesn\'t exist in datatable template.', $name));
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
    

    public function getName()
    {
        return 'zizoo_datatable_twig_extension';
    }

}
