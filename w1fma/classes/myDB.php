<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
/**
 * Class myDB  represents a connection to the SQL database. In addition to data about the connection status, it also owns variables to hold the results, of a given query, retrieved from the database; each row from a given results is rendered as an object and stored in an array of objects. It also records the property names of the retrieved results. Once instantiated, it can be re-used to query the database more than once. *  
 */
class myDB extends mysqli
{
    /**
     * @param $config holds the credentials needed to establish a connection to the database server.
     * @param $lang holds an array of strings that may be used to communicate with the user.
     * @param $result holds the retrieved set of results from a successful database query.
     * @param, array $objects holds each row from $result rendered as an object with the row's columns as properties of the object.
     * @param objectFields holds the name of the columns/properties of  the result set (for all objects of the current query results stored in $objects).
     * $status_msg holds an error message string, relating to database connectivity, in the event there is one .
     * $query_feedback holds feedback about whether the last query was successful, if no query was yet attempted, it returns a message stating so.
     *  
     */
    private $config = array();
    private $lang = array();
    private $query_string;
    private $result;
    private $objects = array();
    private $objectFields = array();
    private $status_msg;
    private $query_feedback;
    private $activeConnec;
    
    /**
     * Constructor of myDB class. Terminates, if connection failed, else sets boolean activeConnec to true and a string variable status_msg to that effect.
     * @param an array with credentials needed to connect to database.
     * @param an array of output strings to use for different events.
     */
    public function __construct($config, $lang)
    {
        $this->config = $config;
        $this->lang   = $lang;
        
        parent::__construct($this->config['db_host'], $this->config['db_user'], $this->config['db_pass'], $this->config['db_name']);
        
        if ($this->connect_errno) {
            exit($this->lang['db_connec_error']);
        } else {
            $this->activeConnec = true;
            $this->status_msg   = $lang['connec_sucs'];
        }
    }
    
    /**
     * Method used in a case of when alteration of a table is desired, eg insertion or deletion, but where no significant return is expected apart from confirmation of whether the query was successful or not.
     * 
     * Method checks first if there's an active connection. If there is, performs a query and returns results.
     * @param a query string 
     * @returns true on success and false on failure.
     */

    
    
    public function alterTable($query_string)
    {
        $this->query_string = $query_string;
        if (!($this->activeConnec)) {
            $this->status_msg     = $this->lang['db_no_connec'];
            $this->query_feedback = $this->lang['db_fetch_error'];
            return false;
        }
        try {
            $this->result = parent::query($this->query_string);
            
            return $this->result;
            
        }
        catch (mysqli_sql_exception $ex) {
            $this->query_feedback = $this->lang['sql_exc'] . $ex->getMessage();
            return false;
        }
        
    }
    
    /**
     * Method checks first if there's an active connection. If there is, performs query and stores the results as individual objects, for each row, in an array of objects while also recording the names of the properties of the [group/class of] objects. If $objects is already set from a a previous query, it is first unset.
     * @param a query string.
     * @return true on success, false in the case of any of the following: no active connection, error fetching from database -> unknown to this class, or no results from database. 
     */
    public function runQuery($query_string)
    {
        $this->query_string = $query_string;
        if (!($this->activeConnec)) {
            $this->status_msg     = $this->lang['db_no_connec'];
            $this->query_feedback = $this->lang['db_fetch_error'];
            return false;
        }
        try {
            $this->result = parent::query($this->query_string);
            
            
            if ($this->result->num_rows == 0) {
                
                $this->query_feedback = $this->lang['db_no_resu_error'];
                return false;
            } else {
                if (isset($this->objects)) {
                    unset($this->objects);
                }
                while ($obj = $this->result->fetch_object()) {
                    $this->objects[] = $obj;
                    
                }
                
                $this->objectFields   = get_object_vars($this->objects[0]);
                $this->objectFields   = array_keys($this->objectFields);
                $this->query_feedback = $this->lang['query_sucs'];
                
                return true;
                
            }
            
            $this->result->free();
        }
        catch (mysqli_sql_exception $ex) {
            $this->query_feedback = $this->lang['sql_exc'] . $ex->getMessage();
            return false;
        }
        
        
        
    }
    
    /**
     *@return the field/column/property names of the current result set stored in $objects 
     *  
     */
    
    public function getFields()
    {
        return $this->objectFields;
    }
    
    /**
     *@return the current result set stored in $objects 
     *  
     */
    
    public function getObjects()
    {
        return $this->objects;
    }
    
    /**
     *@return feedback string for the last performed query - if no query was yet performed, it informs the user that no feedback is available
     *  
     */
    public function getQueryFdbk()
    {
        if (isset($this->query_feedback)) {
            return $this->query_feedback;
        } else {
            $this->query_feedback = $this->lang['fdbk_no_avai'];
            return $this->query_feedback;
        }
    }
    
    /**
     *@return the connectivity status of the current connection
     *  
     */
    public function getStatusMsg()
    {
        return $this->status_msg;
    }
    /**
     *Method closes current connection, sets boolean activeConnec to false so that if new query is attempted, in method runQuery, program doesn't crash.
     *  
     */
    public function close()
    {
        $this->activeConnec = false;
        $this->status_msg   = $this->lang['db_no_connec'];
        parent::close();
    }
    /**
     *Method re-establishes connection. 
     *  
     */
    public function connec()
    {
        $this->activeConnec = true;
        $this->status_msg   = $this->lang['connec_sucs'];
        parent::connect($this->config['db_host'], $this->config['db_user'], $this->config['db_pass'], $this->config['db_name']);
        
        
    }
    
    /**
     * @return whether myDB has an active connection or not. 
     *
     */
    public function isConnecActive()
    {
        if (isset($this->activeConnec)) {
            return $this->activeConnec;
        } else {
            return false;
        }
    }
    
    
}





?>