<?php
/**
 * Recognizes mData sent from DataTables where dotted notations represent a related
 * entity. For example, defining the following in DataTables...
 *
 * "aoColumns": [
 *     { "mData": "id" },
 *     { "mData": "description" },
 *     { "mData": "customer.first_name" },
 *     { "mData": "customer.last_name" }
 * ]
 *
 * ...will result in a a related Entity called customer to be retrieved, and the
 * first and last name will be returned, respectively, from the customer entity.
 *
 * There are no entity depth limitations. You could just as well define nested
 * entity relations, such as...
 *
 *     { "mData": "customer.location.address" }
 *
 * Felix-Antoine Paradis is the author of the original implementation this is
 * built off of, see: https://gist.github.com/1638094 
 */

namespace Zizoo\DatatablesBundle\Datatables;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class Datatable
{
   
    protected $id;
    
    protected $class;
    protected $classMetaData;
    protected $columns;
    protected $rootEntityClassName;
    
    protected $select;
    protected $joins;
    protected $joinTypes;
    protected $rootEntity;
    
    protected $qb;
    protected $callbacks;
    protected $container;
    
    /**
     * @var array The parsed request variables for the DataTable
     */
    protected $parameters;
    
    protected $isOnlyData;
    protected $url;
    protected $echo;

    /**
     * @var \Twig_TemplateInterface
     */
    protected $templating;
    
    protected $displayCount;
    
    public function __construct(Container $container)
    {
        $this->container    = $container;
        $this->class        = null;
        $this->columns      = null;
        
        $this->select       = array();
        $this->joins        = array();
        $this->joinTypes    = array();
        $this->qb           = null;
                
        $request = $this->container->get('request');
        $this->isOnlyData = $request->isXmlHttpRequest();
        
        $this->templating = $this->container->get('templating');
        
        $this->url      = $request->getUri();
    }
    
    public function setClass($class)
    {
        $this->class = $class;
        $doctrine = $this->container->get('doctrine');
        $this->classMetaData = $doctrine->getManager()->getClassMetadata($this->class);
        $this->rootEntityClassName = $this->classMetaData->getName();
        $tableName = \Symfony\Component\DependencyInjection\Container::camelize($this->classMetaData->getTableName());
        $this->rootEntity = strtolower($tableName);
        
        return $this;
    }
    
    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }
    
    public function joinTypes($joinTypes)
    {
        $this->joinTypes = $joinTypes;
    }
    
    /**
     * Parse and configure parameter/association information for this DataTable request
     */
    public function setParameters()
    {
        $request = $this->container->get('request');
        $iColumns = $request->get('iColumns');
        if (is_numeric($iColumns)) {
            $params = array();
            $associations = array();
            for ($i=0; $i < intval($iColumns); $i++) {
                $mDataProp = $request->get('mDataProp_' . $i);
                $fields = explode('.', $mDataProp);
                $params[] = $mDataProp;
            }
            $this->parameters = $params;
        }
    }
    
    /**
     * @param object A callback function to be used at the end of 'setWhere'
     */
    public function addWhereBuilderCallback($callback) {
        if (!is_callable($callback)) {
            throw new \Exception("The callback argument must be callable.");
        }
        $this->callbacks['WhereBuilder'][] = $callback;

        return $this;
    }
    
   
    
    private function populateJoins(&$joins, $joinParts)
    {
        $numParts = count($joinParts);
        if ($numParts==0) return;
        $join = $joinParts[0];
        if (!array_key_exists($joinParts[0], $joins)){
            $joins[$join] = array();
        }
        array_shift($joinParts);
        $this->populateJoins($joins[$join], $joinParts);
    }
    
    private function createJoins(&$qb, $root, $joins, $joinTypes)
    {
        foreach ($joins as $join => $subJoins){
            $qb->leftJoin($root.'.'.$join, $join);
            $this->createJoins($qb, $join, $subJoins, $joinTypes);
        }
    }
    
    /**
     * Set any column ordering that has been requested
     *
     * @param QueryBuilder The Doctrine QueryBuilder object
     */
    public function setOrderBy($request)
    {
        $iSortCol0 = $request->get('iSortCol_0', null);
        if ($iSortCol0!==null){
            $iSortingCols = $request->get('iSortingCols', '0');
            for ($i = 0; $i < intval($iSortingCols); $i++) {
                $bSortable = $request->get('bSortable_'.intval('iSortCol_'.$i));
                if ($bSortable == "true") {
                    $sSortDir = $request->get('sSortDir_'.$i);
                    
                    $orderField = $request->get('mDataProp_'.$i);
                    $qbParam = $this->columns[$orderField];
                    $property = $qbParam['property'];
                    $joinParts = explode('.', $property);
                    $numParts = count($joinParts);

                    if ($numParts==1){
                        // root field
                        $property = $this->rootEntity . '.' . $property;
                    } else {
                        $property = implode('.', array_slice($joinParts, $numParts-2, $numParts));
                    }
                    
                    $this->qb->addOrderBy(
                        $property,
                        $sSortDir
                    );
                }
            }
        }
    }
    
    /**
     * Set the scope of the result set
     *
     */
    public function setLimit($request)
    {
        $offset = $request->get('iDisplayStart');
        $amount = $request->get('iDisplayLength');
        
        if (isset($offset) && $amount != '-1') {
            $this->qb->setFirstResult($offset)->setMaxResults($amount);
        }
    }
    
    private function setWhere($request)
    {
        // Individual column filtering
        $andExpr = $this->qb->expr()->andX();
        for ($i=0 ; $i < count($this->parameters); $i++) {
            $searchable = $request->get('bSearchable_'.$i, null);
            $search     = $request->get('sSearch_'.$i, '');
            if ($searchable == 'true' && $search != '') {
                $searchField = $request->get('mDataProp_'.$i);
                $qbParam = $this->columns[$searchField];
                $property = $qbParam['property'];
                $joinParts = explode('.', $property);
                $numParts = count($joinParts);

                if ($numParts==1){
                    // root field
                    $property = $this->rootEntity . '.' . $property;
                } else {
                    $property = implode('.', array_slice($joinParts, $numParts-2, $numParts));
                }
                $andExpr->add($this->qb->expr()->eq(
                        $property,
                        ":$searchField"
                ));
                $this->qb->setParameter($searchField, $search);
            }
        }
        if ($andExpr->count() > 0) {
            $this->qb->andWhere($andExpr);
        }
    }
    
    private function makeQuery()
    {
        if ($this->rootEntity===null) throw new \Exception('root entity not found, forgot to set class?');
        if ($this->columns===null) throw new \Exception('columns not set');
        
        $this->select[] = $this->rootEntity;
        foreach ($this->columns as $alias => $column){
            $property = $column['property'];
            $joinParts = explode('.', $property);
            $numParts = count($joinParts);
            
            if ($numParts==1){
                // root field
                $property = $this->rootEntity . '.' . $property;
            } else {
                // join found
                $this->populateJoins($this->joins, array_slice($joinParts, 0, $numParts-1));
                $property = implode('.', array_slice($joinParts, $numParts-2, $numParts));
            }
            if (array_key_exists('sql_function', $column)){
                $this->select[] = $column['sql_function'].'('.$property.') AS '.$alias;
            } else {
                $this->select[] = $property.' AS '.$alias;
            }
            
        }
        $em = $this->container->get('doctrine.orm.entity_manager');
        $this->qb = $em->createQueryBuilder()->from($this->class, $this->rootEntity);
        $this->createJoins($this->qb, $this->rootEntity, $this->joins, $this->joinTypes);
        
        $this->qb->select($this->select);
        
        if (!empty($this->callbacks['WhereBuilder'])) {
            foreach ($this->callbacks['WhereBuilder'] as $callback) {
                $callback($this->qb);
            }
        }
        
        $request = $this->container->get('request');
        
        $this->setWhere($request);
        $this->setOrderBy($request);
        $this->setLimit($request);
    }
    
    private function executeSearch($hydrationMode)
    {
        if ($this->qb===null) throw new \Exception('Forgot to makeQuery() ?');
        
        $query = $this->qb->getQuery()->setHydrationMode($hydrationMode);
        $items = new Paginator($query);
        
        return $items;
    }
    
    /**
     * @return int Total query results before searches/filtering
     */
    public function getCountAllResults()
    {
        $joins = array();
        foreach ($this->columns as $alias => $column){
            $property = $column['property'];
            $joinParts = explode('.', $property);
            $numParts = count($joinParts);

            if ($numParts==1){
                // root field
                $property = $this->rootEntity . '.' . $property;
            } else {
                // join found
                $this->populateJoins($joins, array_slice($joinParts, 0, $numParts-1));
            }

        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder()->from($this->class, $this->rootEntity);
        $this->createJoins($qb, $this->rootEntity, $joins, $this->joinTypes);
        $qb->select($this->rootEntity);

        if (!empty($this->callbacks['WhereBuilder']))  {
            foreach ($this->callbacks['WhereBuilder'] as $callback) {
                $callback($qb);
            }
        }

        return count($qb->getQuery()->getResult());
    }
    
    public function getResults($hydrationMode=Query::HYDRATE_OBJECT)
    {
        $this->setParameters();
        $this->makeQuery();
        $items = $this->executeSearch($hydrationMode);
        $results = array();
        
        try {
            foreach ($items as $item) {
                $result = array();
                $entity = null;
                foreach ($item as $field => $val){
                    if ($val instanceof $this->rootEntityClassName){
                        $entity = $val;
                        continue;
                    }
                    if (array_key_exists('callback', $this->columns[$field]) && is_callable($this->columns[$field]['callback'])){
                        $callback = $this->columns[$field]['callback'];
                        $val = $callback($field, $val, $entity);
                    }
                    if ($val instanceof \DateTime){
                        $val = '<div>' . $val->format('d/m/Y') . '</div>'
                                . '<div>' . $val->format('H:i') . '</div>';
                    }
                    $result[$field] = $val;
                }

                $results[] =  $result;
            }
            $this->displayCount = $items->count();
        } catch (\Exception $e){
            $this->displayCount = 0;
        }
        
        return $results;
    }
    
    public function isOnlyData()
    {
        return $this->isOnlyData;
    }
    
    public function getColumns()
    {
        return $this->columns;
    }


    public function render()
    {
        try {
            if ($this->id === null) $this->setId('table_'.rand());
            $request = $this->container->get('request');
            
            if ($this->isOnlyData) {

                $results = $this->getResults();
                
                $outputHeader = array(
                    "sEcho" => (int) $request->get('sEcho'),
                    "iTotalRecords" => $this->getCountAllResults(),
                    "iTotalDisplayRecords" => $this->displayCount
                );
                
                $content = array_merge($outputHeader, array('aaData' => $results));
                
                $response = new JsonResponse();
                $response->setContent(json_encode($content));

                return $response;
            } else {
                return array(
                    'datatable'   => $this
                );
            }
        } catch (\Exception $e){
            $response = new Response($e->getMessage(), 500);
            return $response;
        }
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    
    public function getTemplating(){
        return $this->templating;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}
