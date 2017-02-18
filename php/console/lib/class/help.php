<?PHP
class help extends console
{
	public function __construct($app)
	{
		$this->app = $app;
	}
	public function run($argv)
	{

		$this->help();	
	}
	
	public function help()
	{
		$this->app->help();
	}

}