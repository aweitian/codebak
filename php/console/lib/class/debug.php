<?PHP
/**
* 
*/
class debug implements IConsole {
	
	public function __construct() {
		
	}
	public function run($argv) {
		$pipe = new pipe();
		$pipe->send(10);
		$pipe->through(function($poster, $callback){
			return $callback($poster + 1);
		},function($poster, $callback) {
			return $callback($poster + 5);
		});
		$pipe->then(function($poster){

			print $poster;
		});
	}
	public function help() {

	}
}