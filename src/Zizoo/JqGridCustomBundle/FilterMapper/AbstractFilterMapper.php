<?php
namespace Zizoo\JqGridCustomBundle\FilterMapper;
use Zizoo\JqGridCustomBundle\Grid\Grid;
use Zizoo\JqGridCustomBundle\Grid\Column;

abstract class AbstractFilterMapper
{
    protected $grid;
    protected $column;

    /**
     * @param \Zizoo\JqGridCustomBundle\Grid\Grid $grid
     * @param \Zizoo\JqGridCustomBundle\Grid\Column $column
     */
    public function __construct(Grid $grid, Column $column)
    {
        $this->grid = $grid;
        $this->column = $column;
    }

    /**
     * @abstract
     *
     * @param array $rule
     * @param string $groupOperator
     *
     * @return mixed
     */
    abstract public function execute(array $rule, $groupOperator = 'OR');
}
