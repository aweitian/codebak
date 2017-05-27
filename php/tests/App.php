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
	public function __construct()
	{
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
		$this->showTpl('second');
	}
	public function guide()
	{
		$this->showTpl('first');
	}
	public function submit()
	{
		$this->initResult();
		$this->calcLv();
		$this->showTpl('submit');
	}
	public function question()
	{
		if (isset($_GET['s'])) 
			$this->s = intval($_GET['s']);
		else
			$this->s = 1;
		$this->beforeShowQuestion();
		$this->showTpl('question');
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
			$this->showTpl("result");
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
		$found_key = '';
		foreach($this->result as $k => $r)
		{
			if ($score >= $r[0] && $score <= $r[1]) 
			{
				$found_key = $k;
				break;
			}
		}
		if (!$found_key) 
		{
			//分数不合理，使用最小值 
			//exit('分数不合理，使用最小值');
			$f = current($this->result);
			$e = end($this->result);
			if($f[0] > $e[0])
			{
				$found_key = current(array_keys($this->result));
			}
			else
			{
				$found_key = end(array_keys($this->result));
			}
		}
		$this->lvtxt = $found_key;
		switch ($this->lvtxt) 
		{
			case '重度':
				$this->level = 1;
				break;
			case '中度':
				$this->level = 2;
				break;
			case '中轻度':
				$this->level = 3;
				break;
			case '轻度':
				$this->level = 4;
				break;
			case '不存在':
			default:
				$this->level = 5;
				
				break;
		}
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