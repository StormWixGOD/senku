<?php 

// Normal

namespace App\Db;

use App\Db\Connection;
use \PDO;
use PDOException;

class Query {

    /** Actual query */
    public $instance;
    /** Afectt Rows in last query */
    public $afectRows;
    /** PDO connection */
    private $db;

    private $query;
    private $datas;


    /**
     * Set data to use in the query
     */
    private function SetQuery(string $query, $datas=''):void
    {
        $this->query = $query;
        $this->datas = $datas;
    }

    /**
     * Execute query sql
     */
    private function ExecuteQuery():void
    {
        if ($this->db == null) $this->db = Connection::GetConnection();
        $this->instance = $this->db->prepare($this->query);

        if (empty($this->datas)) {
            $this->instance->execute();
        } else {
            $this->instance->execute($this->datas);
        }
        $this->afectRows = $this->instance->rowCount();
    }

    /**
     * Execute sql query
     *
     * @param string $query SQL query
     * @param string $datas Params
     */
    public function Exec(string $query, $datas=''):array
    {
        $this->SetQuery($query, $datas);
        try {
            $this->ExecuteQuery();
            return [
                'ok'       => $this->afectRows > 0,
                'afectRow' => $this->afectRows,
                'data'     => $this->instance->fetch(PDO::FETCH_ASSOC),
                'obj'      => $this->instance
            ];
        } catch (\PDOException $e) {
            error_log('[sql] [query] ' . $this->query);
            error_log('[sql] [datas] ' . json_encode($this->datas));
            error_log('[sql] [error] ' . $e);
            return ['ok' => false, 'error' => true, 'msg' => $e->getMessage()];
        }
    }

    /**
     * Get info of all rows
     *
     * @param string $query SQL query
     * @param string $datas Params
     */
    public function GetAllRows(string $query, $datas=''):array
    {
        $this->SetQuery($query, $datas);
        $responses = [];

        try {
            $this->ExecuteQuery($query, $datas);
            $rows = $this->instance->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $responses[] = $row;
            }

            return [
                'ok' => $this->afectRows > 0,
                'afectRow' => $this->afectRows,
                'rows' => $responses
            ];
        } catch (\PDOException $e) {

            error_log('[sql] [query] ' . $this->query);
            error_log('[sql] [datas] ' . json_encode($this->datas));
            error_log('[sql] [error] ' . $e);
            return ['ok' => false, 'error' => true, 'msg' => $e->getMessage()];
        }
    }
}