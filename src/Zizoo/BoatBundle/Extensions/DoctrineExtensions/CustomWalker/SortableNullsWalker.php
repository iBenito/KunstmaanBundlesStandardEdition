<?php
namespace Zizoo\BoatBundle\Extensions\DoctrineExtensions\CustomWalker;

use Doctrine\ORM\Query\SqlWalker;

/**

SortableNullsWalker
*/
class SortableNullsWalker extends SqlWalker {
    const NULLS_FIRST = 'NULLS FIRST';
    const NULLS_LAST = 'NULLS LAST';

    public function walkOrderByClause($orderByClause){
        $sql = parent::walkOrderByClause($orderByClause);

        if ($nullFields = $this->getQuery()->getHint('SortableNullsWalker.fields')){
            if (is_array($nullFields)){
                $platform = $this->getConnection()->getDatabasePlatform()->getName();
                switch ($platform){
                    case 'mysql':
                        // for mysql the nulls last is represented with - before the field name
                        foreach ($nullFields as $field => $sorting){
                            /**
                            NULLs are considered lower than any non-NULL value,
                            except if a - (minus) character is added before
                            the column name and ASC is changed to DESC, or DESC to ASC;
                            this minus-before-column-name feature seems undocumented.
                            */
                            if ('NULLS LAST' === $sorting) { 
                                $sql = preg_replace('/\s+([a-z])(\.' . $field . ') (ASC|DESC)?\s*/i', " -$1 $2 $3 ", $sql); 
                            }

                        }
                    break;
                    case 'oracle':
                    case 'postgresql':
                        foreach ($nullFields as $field => $sorting){ 
                            $sql = preg_replace('/(\.' . $field . ') (ASC|DESC)?\s*/i', "$1 $2 " . $sorting, $sql);     
                        }

                    break;
                    default:
                        // I don't know for other supported platforms.
                    break;
                }
            }
        }

    return $sql;
    }
}
?>
