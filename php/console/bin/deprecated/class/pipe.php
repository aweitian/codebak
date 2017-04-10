<?php
class pipe {
	/**
     * The object being passed through the pipeline.
     *
     * @var mixed
     */
    protected $passable;
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [];
	public function __construct() {


	}
	public function send($passable) {
		$this->passable = $passable;
        return $this;

	}
	public function through($pipes) {
		$this->pipes = is_array($pipes) ? $pipes : func_get_args();
        return $this;
	}
	public function then(Closure $destination) {
 		$firstSlice = $this->getInitialSlice($destination);

        $pipes = array_reverse($this->pipes);

        return call_user_func(
            array_reduce($pipes, $this->getSlice(), $firstSlice), $this->passable
        );

	}
	/**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return \Closure
     */
    protected function getSlice() {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                return call_user_func($pipe, $passable, $stack);
            };
        };
    }
    /**
     * Get the initial slice to begin the stack call.
     *
     * @param  \Closure  $destination
     * @return \Closure
     */
    protected function getInitialSlice(Closure $destination) {
        return function ($passable) use ($destination) {
            return call_user_func($destination, $passable);
        };
    }

}