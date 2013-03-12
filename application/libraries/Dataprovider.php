<?php
/**
* Dataprovider is meant to provide access to data outside of the formit2 db.
* This is so Selects/Checks/Radios can easily be configured to grab data from
* remote sources securely, without hacking too much into the existing code.
*
* Just add a function with an appropriate name (base it off of exampleOptions(),
* put it in the dataproviders table, with a description so people can select
* it as an option, and done.  Now people can use whatever data in forms.
*/
class Dataprovider{
	/**
	* Instance of CI
	*/
	protected $CI;

	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->model('dataprovider_model', 'dp');
		$this->dp =& $this->CI->dp;
	}

	/**
	*	Run a dataprovider method and return results
	*/
	function run($method){
		if(method_exists($this, $method)){
			return $this->$method();
		}else{
			return array();
		}
	}

	function exampleOptions(){
		//The un-commented stuff is all that should be needed		
		$db_conf = array(
			//'hostname'=>DATABASE_HOST,
			'username'=>'webs',
			'password'=>'myDB4All',
			'database'=>'bankdata',
			//'dbdriver'=>'mysqli',
			//'dbprefix'=>'',
			//'db_debug'=>TRUE,
			//'char_set'=>'utf8',
			//'dbcollat'=>'utf8_general_ci',
		);

		//actually connect to the db in question
		//this is REQUIRED before doing any querying
		$this->dp->connect($db_conf);

		//get the values from the database
		//label_col is required
		//if value_col is left null or empty, labels and values will be the same
		$query = "SELECT * FROM `stateprov`";
		$label_col = 'stateprov';
		$value_col = 'stateprovcode';
		$opt = $this->dp->getOptions($query, $label_col, $value_col);

		//kill the connection after work is done (you should do this...just please do)
		$this->dp->close();

		return $opt;
	}

	/** 
	* Get states and providences as options
	*/
	function stateprovOptions(){
		$db_conf = array(
			'username'=>'webs',
			'password'=>'myDB4All',
			'database'=>'bankdata',
		);
		$this->dp->connect($db_conf);
	
		$query = "SELECT * FROM `stateprov`";
		$opt = $this->dp->getOptions($query, 'stateprov', 'stateprovcode');
		$this->dp->close();
		return $opt;
	}

	/**
	* Get countries as options
	*/
	function countryOptions(){
		$db_conf = array(
			'username'=>'webs',
			'password'=>'myDB4All',
			'database'=>'bankdata',			
		);
		$this->dp->connect($db_conf);
	
		$query = "SELECT * FROM `countrycodes`";
		$opt = $this->dp->getOptions($query, 'country', 'countrycode');
		$this->dp->close();
		return $opt;
	}
}

?>