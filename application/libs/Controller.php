<?
use DatabaseManager\Database;

class Controller
{
    public $title = '';
    public $smarty = null;
    public $hidePatern = true;
    public $subfolder = 'site/';
    public static $pageTitle;
		public $output = array();

    function __construct($arg = array())
    {
        Session::init();
        $this->gets = Helper::GET();

				$this->output = array(
					'account' => false,
					'error' => false,
					'errorMessage' => false,
					'filters' => array(),
					'data' => array()
				);

        if ($arg['root']) {
            $this->subfolder = $arg['root'] . '/';
        }

        /**
         * CORE
         **/
        // SMARTY
        $this->db      = new Database();
        $template_root = VIEW . $this->subfolder . 'templates/';

        $this->smarty                 = new Smarty();
        $this->smarty->caching        = false;
        $this->smarty->cache_lifetime = 0;
        $this->smarty->setTemplateDir($template_root);
        $this->smarty->setCompileDir(VIEW . $this->subfolder . 'templates_c/');
        $this->smarty->setConfigDir('./settings');
        $this->smarty->setCacheDir(VIEW . $this->subfolder . 'cache/');

        $this->out('template_root', $template_root);

        // SETTINGS
        $this->out('settings', $this->settings);
        // GETS
        $this->gets = Helper::GET();
        $this->out('GETS', $this->gets);

        $this->loadAllVars();

        if (!$arg[hidePatern]) {
            $this->hidePatern = false;
        }
    }

		public function outputAPI()
		{
			header('Content-Type: application/json');
			echo json_encode($this->output);
		}

    function out($viewKey, $output)
    {
        $this->smarty->assign($viewKey, $output);
    }

    function outSet($set_array = array())
    {
        foreach ($set_array as $key => $value) {
            $this->smarty->assign($key, $value);
        }
    }

    public function getAllVars()
    {
        $vars = array();

        if (!$this->smarty)
            return false;

        $list = $this->smarty->tpl_vars;

        foreach ($list as $key => $value) {
            $vars[$key] = $value->value;
        }

        return $vars;
    }

    public function loadAllVars()
    {
        $vars = array();

        if (!$this->smarty)
            return false;

        $list = $this->smarty->tpl_vars;

        foreach ($list as $key => $value) {
            $this->vars[$key] = $value->value;
        }
    }

    public function getVar($key)
    {
        $vars = $this->smarty->tpl_vars[$key]->value;

        return $vars;
    }


    function bodyHead($key = '')
    {
        $subfolder = '';

        $this->theme_wire = ($key != '') ? $key : '';

        if ($this->getThemeFolder() != '') {
            $subfolder = $this->getThemeFolder() . '/';
        }

        # Oldal címe
        if (self::$pageTitle != null) {
            $this->title = self::$pageTitle . ' | ' . $this->settings['page_title'];
        } else {
            $this->title = $this->settings['page_title'] . $this->settings['page_description'];
        }

        # Render HEADER
        if (!$this->hidePatern) {
            $this->out('title', $this->title);
            $this->displayView($subfolder . $this->theme_wire . 'head');
        }

        # Aloldal átadása a VIEW-nek
        $this->called = $this->fnTemp;
    }

    function __destruct()
    {
        $mode      = false;
        $subfolder = '';

        if ($this->getThemeFolder() != '') {
            $mode      = true;
            $subfolder = $this->getThemeFolder() . '/';
        }

        if (!$this->hidePatern) {
            # Render FOOTER
            $this->displayView($subfolder . $this->theme_wire . 'footer');
        }

        $this->db     = null;
        $this->smarty = null;
    }

    function setThemeFolder($folder = '')
    {
        $this->theme_folder = $folder;
    }

    protected function getThemeFolder()
    {
        return $this->theme_folder;
    }

    public function displayView($tpl, $has_folder = false)
    {
        $folder = '';

        if ($has_folder) {
            if ($this->subfolder != 'site/') {
                $tpl = str_replace($this->subfolder, '', $tpl);
            }
            $folder = ($this->gets[1] ?: 'home') . '/';
        }

        $templateDir = $this->smarty->getTemplateDir();

        if (!file_exists($templateDir[0] . $folder . $tpl . '.tpl')) {
            if ($this->subfolder == 'site/') {
                $folder = '';
            } else {
                $folder = 'PageNotFound/';
            }
        }

        $this->smarty->display($folder . $tpl . '.tpl');
    }
}

?>
