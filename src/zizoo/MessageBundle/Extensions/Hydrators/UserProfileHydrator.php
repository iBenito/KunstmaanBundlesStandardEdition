<?php
namespace Zizoo\MessageBundle\Extensions\Hydrators;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class UserProfileHydrator extends AbstractHydrator
{
   /**
     * Hydrates all rows from the current statement instance at once.
     */
    protected function _hydrateAll()
    {
        $result = array();

        while ($data = $this->_stmt->fetch(\PDO::FETCH_NUM)) {
            $value = $data[0];

            if(is_numeric($value)) {
                if (false === mb_strpos($value, '.', 0, 'UTF-8')) {
                    $value = (int) $value;
                } else {
                    $value = (float) $value;
                }
            }

            $result[] = $value;
        }

        return $result;
    }

    /**
     * (non-PHPdoc)
     * @see Doctrine\ORM\Internal\Hydration.AbstractHydrator::hydrateAllData()
     */
    protected function hydrateAllData()
    {
        return $this->_hydrateAll();
    }
}
?>