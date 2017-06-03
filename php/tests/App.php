<?php
namespace Tian;

class App
{
	private $r;
	private $s;
	private $question;
	private $result;
	private $total;
	private $level;
	private $lvtxt;
	private $routeTable = [];
	private $config = [];
	public function __construct()
	{
		$this->routeTable = parse_ini_file('conf/guide.txt');
		$this->config = parse_ini_file('conf/config.txt');
		if (isset($_GET['r'])) 
		{
			$this->r = $_GET['r'];
		}
		else
		{
			$this->r = 'showIndex';
		}
	}
	public function route()
	{
		if(method_exists($this, $this->r))
		{
			call_user_method($this->r, $this);
		}
	}
		
	public function showIndex()
	{
		//var_dump(parse_ini_file('conf/guide.txt'));exit;
		$this->showTpl($this->routeTable['index']);
	}
	public function guide()
	{
		$this->showTpl($this->routeTable['guide']);
	}
	public function submit()
	{
		$this->initResult();
		$this->calcLv();
		$this->showTpl($this->routeTable['submit']);
	}
	public function question()
	{
		if (isset($_GET['s'])) 
			$this->s = intval($_GET['s']);
		else
			$this->s = 1;
		$this->beforeShowQuestion();
		$this->showTpl($this->routeTable['question']);
	}
	private function showTpl($tpl,$env=array())
	{
		include ('tpl/'.$tpl.'.html');
	}
	private function nextQuestion($c)
	{
		$a = $_GET;
		$a['s'] = $this->s + 1;
		if(!isset($a['z']))
			$a['z'] = str_repeat('0', $this->total);

		$a['z'][$this->s-1] = $c;

		return http_build_query($a);
	}
	private function beforeShowQuestion()
	{
		if($this->s < 1)$this->s = 1;
		$this->initQuestion();
		$this->total = count($this->question);
		if($this->s > $this->total)
		{
			$this->initResult();
			$this->calcLv();
			$this->showTpl($this->routeTable["result"]);
			exit;
		}
		$key = array_keys($this->question);
		$key = $key[$this->s - 1];
		$this->question = $this->question[$key];
	}
	private function initQuestion()
	{
		$inst = new \Tian\Question2arr();
		$this->question = $inst->get();
	}
	private function calcLv()
	{
		if (empty($this->result)) 
		{
			exit('malformed result file.');
		}
		if(!isset($_GET['z']))
		{
			header('location:?r=requestion');
			exit;
		}
		$score = array_reduce(str_split($_GET['z']), function($a,$b){
			return $a+$b;
		},0) ;
		//分数不合理，使用最小值 ,所以这里设置为0
		$found_index = 0;
		$pos = 0;
		foreach($this->result as $k => $r)
		{
			if ($score >= $r[0] && $score <= $r[1]) 
			{
				$this->lvtxt = $k;
				$found_index = $pos;
				break;
			}
			$pos++;
		}
		$this->level = $found_index + 1;
	}

	private function initResult()
	{
		$inst = new \Tian\Result2arr();
		$this->result = $inst->get();
	}
	private function getSwt()
	{
		return 'http://kqi.zoossoft.com/LR/Chatpre.aspx?id=KQI10880110&cid=1492068914992521143176&lng=cn&sid=1495761703244572325811&p=http%3A//m.wx.sh9l.com/&rf1=&rf2=&e=%25u6765%25u81EA%25u9996%25u9875%25u81EA%25u52A8%25u9080%25u8BF7%25u7684%25u5BF9%25u8BDD&msg=&d=1495787685692=wxfwh';
	}
}