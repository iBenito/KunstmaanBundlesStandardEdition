<?php
namespace Zizoo\JqGridCustomBundle\FilterMapper;
use Zizoo\JqGridCustomBundle\Grid\Grid;
use Zizoo\JqGridCustomBundle\Grid\Column;

class FilterMapperFactory
{

    const FORMATTER_DATE = 'date';

    /**
     * @param \Zizoo\JqGridCustomBundle\Grid\Grid $grid
     * @param \Zizoo\JqGridCustomBundle\Grid\Column $column
     *
     * @return \Zizoo\JqGridCustomBundle\FilterMapper\AbstractFilterMapper
     */
    public static function getFilterMapper(Grid $grid, Column $column)
    {
        if ($column->getFieldFormatter() == self::FORMATTER_DATE) {

            return new DateRangeFilterMapper($grid, $column);

        } elseif ($column->getFieldHaving()) {

            return new HavingFilterMapper($grid, $column);

        } else {

            return new ComparisionFilterMapper($grid, $column);
        }
    }
}
