<?php
class genericTable{
	public $db;
	protected $dbName;
	protected $last_id;
	protected $unique_id; // UNIQUE TABLE IDENTIFIER HAS TO BE VAR 1
	protected $sql_query;
	protected $variables;
	
	public function __construct($dbName, $unique_id){
		global $mysqli; 
		$this->db = $mysqli;
		$this->variables = array(); 
		$this->dbName = $dbName;
		$this->unique_id = $unique_id;
	}

	public function __destruct(){
		if (isset($this->sql_query) && ($this->sql_query != null))
			$this->sql_query->close();
	}


	public function set_variable($varName, $value){$this->variables[$varName]=$value;}
	public function get_variable($varName){return $this->variables[$varName];}
		
	public function reset_query(){
		if ($this->sql_query  && isset($this->sql_query)){
			$this->sql_query->free();
			$this->sql_query=NULL;
		}
		foreach ($this->variables as $key => $value){
			$this->variables = array();
		}
	}
		
	public function loadRowInformation($row){
		foreach ($row as $key => $value){
			$this->variables[$key] = $row[$key];
		}

	}
		
	public function createNew(){
		if (!empty($this->variables[$this->unique_id]) && $this->countAll() > 0) { 
			return $this->update();
		}else{
			$this->db->query("INSERT INTO " . $this->dbName . " " . $this->getSetString());
			return $this->last_id=$this->db->insert_id;
		}
	}
		
	public function update(){
		$this->db->query("UPDATE " . $this->dbName . " " . $this->getSetString(). " WHERE " . $this->unique_id . "='" . $this->variables[$this->unique_id] . "'" );
		return $this->last_id = $this->get_variable($this->unique_id);
	}
	
	public function delete($whereClause=''){
		if ($whereClause == ''){
			$whereClause = $this->getWhereString();
		}
		$sql = "DELETE FROM "  . $this->dbName . " " . $whereClause;
		return $this->db->query($sql);
	}
	

	public function load($extraWhere='', $selectValue='*', $loadRow = true, $orderby=''){
		$sql = "SELECT ".$selectValue." FROM " . $this->dbName . " " . $this->getWhereString($extraWhere);
		$sql .= $orderby;	
		$this->sql_query=$this->db->query($sql);
		if (($this->sql_query && $loadRow) && $row = $this->sql_query->fetch_assoc() ) 
			$this->loadRowInformation($row);
		else
			return !$loadRow;
		return true;
	}
	public function loadOr($extraWhere='', $selectValue='*', $loadRow = true, $orderby=''){
		$sql = "SELECT ".$selectValue." FROM " . $this->dbName . " " . $this->getWhereString($extraWhere, false);
		$sql .= $orderby;	
		$this->sql_query=$this->db->query($sql);
		if (($this->sql_query && $loadRow) && $row = $this->sql_query->fetch_assoc() ) 
			$this->loadRowInformation($row);
		else
			return !$loadRow;
		return true;
	}
	
		
			
	public function loadNext($extraWhere='', $orderby=''){
		if ($this->sql_query  && isset($this->sql_query)){
			if ($row = $this->sql_query->fetch_assoc() ) 
				$this->loadRowInformation($row);
			else
				return false;
		}else{
			return $this->load($extraWhere, '*', true, $orderby );
		}
		return true;
	}

	public function loadNextOr($extraWhere='', $orderby=''){
		if ($this->sql_query  && isset($this->sql_query)){
			if ($row = $this->sql_query->fetch_assoc() ) 
				$this->loadRowInformation($row);
			else
				return false;
		}else{
			return $this->loadOr($extraWhere, '*', true, $orderby );
		}
		return true;
	}


	private function getSetString()
	{
		$set = "";
		foreach ($this->variables as $key => $value){
			if ($key == $this->unique_id) continue;
			if (isset($this->variables[$key])) $set.= $key . "='" . $this->variables[$key] . "', " ;
		}
		if (!empty($set))
			$set = "SET " . substr($set, 0, strrpos($set, ','));
		return $set;
	}

	public function getWhereString($extraWhere='', $useAnd = true)
	{
		$where = "";
		$conjunction = ($useAnd) ? "AND" : "OR";
		if (!empty($this->variables[$this->unique_id]) ){
			$where = "WHERE " . $this->unique_id . "='" . $this->variables[$this->unique_id] . "'";
		} else {
			foreach ($this->variables as $key => $value){
				if ($key == $this->unique_id) continue;
				if ($this->variables[$key]!=='') {
					$where .= $key . "='" . $this->variables[$key] . "' " . $conjunction . " ";
				}
			}
			if (!empty($where))
				$where = "WHERE " . substr($where, 0, strripos($where, $conjunction));			
		}
		if (!empty($extraWhere)){
			if (!empty($where)){
				$where .= " ". $conjunction ." " . $extraWhere . " ";
			} else {
				$where = " WHERE " . $extraWhere . " ";
			}
		}
		return $where;
	}
		
	public function countAll($extraWhere='', $orderby='', $selectValue='*')
	{
		$numRows=0;
		if ($this->load($extraWhere, $selectValue, false, $orderby))
			$numRows = mysqli_num_rows($this->sql_query);			
		return intval($numRows);
	}
	
	public function debug(){
		$currentFieldsString = print_r($this->variables, true);
		$updateString = "UPDATE " . $this->dbName . " " . $this->getSetString(). " WHERE " . $this->unique_id . "='" . $this->variables[$this->unique_id] . "'" ;
		$loadString = "SELECT * FROM " . $this->dbName . " " . $this->getWhereString();
		$createString = "INSERT INTO " . $this->dbName . " " . $this->getSetString();

		return "DB=" . $this->dbName . "\nUniqueIDName=". $this->unique_id . 
			"\nFields=" . $currentFieldsString . "\nUpdateString=" . $updateString . "\nLoad = " . $loadString . "\nCreate = " . $createString ;
	}
	
	public static function GetVariableArray($table, $options, $deleteToFirstUnderscore ){
		$table->load();		
		$newVars = array();
		foreach ($table->variables as $key => $value){
			$keyVals = split("_", $key);
			if ($deleteToFirstUnderscore){
				array_shift($keyVals);
			}
			$optionKey = implode($keyVals);
			if (isset($options[$optionKey])){
				$newVars[$key] = $options[$optionKey];
			}
		}
		$table->reset_query();
		return $newVars;		
	}
	public function updateWithPostData($options, $deleteToFirstUnderscore = true){
		$this->variables = genericTable::GetVariableArray($this, $options, $deleteToFirstUnderscore);
		while ($this->loadNext()){
			$id = $this->get_variable($this->unique_id);
			$valueChanged = false;
			$changes = array();
			foreach ($this->variables as $key => $value){
				$testKey = $key . "_" . $id;
				if (isset($_POST[$testKey])){
					if ($_POST[$testKey] != $value){
						array_push($changes, $testKey);
						$this->set_variable($key, $_POST[$testKey]);
						$valueChanged = true;
					} else {
						//echo "::EQUAL KEY - " . $testKey;
					}
				} else {
					//echo "::Can't find KEY - " . $testKey;
				}
			}
			if ($valueChanged){
				echo "<br>UPDATING ROW OF ID=" . $id . " AND CHANGING " . implode(",",$changes);
				$this->update();
			}
		}
	}
	
	public function buildTable($options, $allowEdit=null, $deleteToFirstUnderscore = true, $orderBy=""){
		$retStr = "<form name='postable' action='#' method='post'><table id='" . $this->dbName . "' class='openEditTable' >";
		$rowHeader = "<thead><tr>";
		$rowId = "";
		$createHeader = true;
		$allRowsStr = "<tbody>";
		$this->variables = genericTable::GetVariableArray($this, $options, $deleteToFirstUnderscore);
		while ($this->loadNext('', $orderBy)){
			$rowStr = "<tr id='{{ROWID}}'>";
			$id = $this->get_variable($this->unique_id);
			foreach ($this->variables as $key => $value){
				if ($createHeader){
					if ($key != $this->unique_id && isset($this->variables[$key])){
						$keyVals = split("_", $key);
						if ($deleteToFirstUnderscore){
							array_shift($keyVals);
						}
						$rowHeader.= "<th>" . implode($keyVals) . "</th>" ;						
					} 
				}
				
				if ($key == $this->unique_id) 
					$rowId = $value;
				else {
					if (isset($this->variables[$key]) && isset($allowEdit[$key])){
						$rowStr.= "<td><input type='text' name='" . $key . "_" . $id . "' value='". $value . "'></td>" ;
						
					} else {
						$rowStr.= "<td class='border'>". $value . "</td>" ;
						
					}
				}
				
			}
			$rowStr = str_replace("{{ROWID}}", $rowId, $rowStr);
			$rowStr .= "</tr>";
			$allRowsStr .= $rowStr;
			$createHeader = false;
		}
		$allRowsStr.= "</tbody>";
		$rowHeader .= "</tr></thead>";
		
		$retStr .= $rowHeader . $allRowsStr . "</table><input type='submit' value='Submit'></form>";
		return $retStr;
	}
}