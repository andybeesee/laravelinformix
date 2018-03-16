<?php namespace fhenne\Informix\Query;

use Illuminate\Database\Query\Grammars\Grammar as BaseGrammar;
use Illuminate\Database\Query\Builder as BaseBuilder;
use fhenne\Informix\Query\Builder;

class Grammar extends BaseGrammar
{
	/**
     * The components that make up a select clause.
     *
     * @var array
     */
    protected $selectComponents = array(
        'aggregate',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'offset',
        'limit',
        'lock',
    );

    /**
     * Compile a select query into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder
     * @return string
     */
    public function compileSelect(BaseBuilder $query)
    {
        $sql = parent::compileSelect($query);

        if ($query->unions)
        {
            $sql = '('.$sql.') '.$this->compileUnions($query);
        }

        return $sql;
    }

	/**
	 * Compile an insert statement into SQL.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array  $values
	 * @return string
	 */
	public function compileInsert(BaseBuilder $query, array $values) {
	 dd("NO INSERT");
	}

    /**
     * Compile a single union statement.
     *
     * @param  array  $union
     * @return string
     */
    protected function compileUnion(array $union)
    {
        $joiner = $union['all'] ? ' union all ' : ' union ';

        return $joiner.'('.$union['query']->toSql().')';
    }

    /**
     * Compile the lock into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  bool|string  $value
     * @return string
     */
    protected function compileLock(BaseBuilder $query, $value)
    {
        if (is_string($value)) return $value;

        return $value ? 'for update' : 'lock in share mode';
    }

    /**
     * Compile an update statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileUpdate(BaseBuilder $query, $values)
    {
		dd("NO UPDATE");
        $sql = parent::compileUpdate($query, $values);

        if (isset($query->orders))
        {
            $sql .= ' '.$this->compileOrders($query, $query->orders);
        }

        if (isset($query->limit))
        {
            $sql .= ' '.$this->compileLimit($query, $query->limit);
        }

        return rtrim($sql);
    }

    /**
     * Compile a delete statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileDelete(BaseBuilder $query)
    {
		dd("NO DELETE");
        $table = $this->wrapTable($query->from);

        $where = is_array($query->wheres) ? $this->compileWheres($query) : '';

        if (isset($query->joins))
        {
            $joins = ' '.$this->compileJoins($query, $query->joins);

            return trim("delete $table from {$table}{$joins} $where");
        }

        return trim("delete from $table $where");
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapValue($value)
    {
        if ($value === '*') return $value;

        return '' . str_replace('', '', $value) . '';
    }

    /**
     * Compile the "offset" portions of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  int  $offset
     * @return string
     */
    protected function compileOffset(BaseBuilder $query, $offset)
    {
        return 'SKIP '.(int) $offset;
    }
}
