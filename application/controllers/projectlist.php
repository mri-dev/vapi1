<?
use ProjectManager\Projects;

class projectlist extends Controller  {
  public $ctrl = null;
	function __construct(){
		$this->ctrl = parent::__construct();

    $this->projects = new Projects(array(
      'controller' => $this->ctrl
    ));

	}

	function __destruct(){
		parent::outputAPI();
	}
}

?>
