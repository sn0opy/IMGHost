<?

class db {
	private $db;
	private $res;
	private $row;

	public function __construct() {
		$this->open(DBFILE);
	}

	public function open($dbfile) {	
		$this->db = sqlite_open($dbfile);
	}

	public function close() {
		sqlite_close($this->db);	
	}

	public function query($query) {	
		$this->res = sqlite_query($this->db, $query);
		return $this->res;
	}

	public function numRows() {
		return @sqlite_num_rows($this->res);
	}

	public function fetch() {
		$this->row = @sqlite_fetch_array($this->res);
		return $this->row;
	}

	public function row($field) {
		return $this->row[$field];
	}

	public function insertID() {
		return sqlite_last_insert_rowid($this->db);		 
	}
}

?>