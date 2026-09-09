<?php
/*
	ERP (mgl-design-system) нэвтрэлт.

	Нууц үгийг энэ сервер дээр хадгалахгүй, шалгахгүй. Хэрэглэгчийн оруулсныг
	server-to-server хүсэлтээр ERP-ийн Worker руу дамжуулж шалгуулна. Worker
	зөвшөөрвөл буцаасан token-той нь ERP рүү шилжүүлнэ. Хэрэглэгч шууд
	/app руу орвол ERP өөрийн нэвтрэлтээрээ хамгаалагдсан хэвээр байна.
*/

class ErpAuth
{
	/* IP тутам цонхонд зөвшөөрөх алдаатай оролдлого */
	const MAX_FAIL_IP		= 12;
	/* Нэг акаунт дээр (IP-аас үл хамааран) зөвшөөрөх алдаатай оролдлого */
	const MAX_FAIL_USER		= 10;
	/* Тоолуурын цонх, секундээр */
	const WINDOW			= 900;

	private $cfg;
	private $err = "";

	public function __construct($cfg)
	{
		/*
			Эдгээр нь нууц утга биш (Worker-ийн endpoint нь ил байдаг) тул
			кодод шууд байна — ингэснээр const.php-д гар хүрэхгүйгээр
			deploy хийхэд шууд ажиллана. const.php дээр ижил нэртэй
			хувьсагч зарлавал түүнийг давуулж болно.
		*/
		$defaults = array(
			"appUrl"		=> "https://mgl-design-system.pages.dev/app",
			"loginUrl"		=> "https://mgl-progress-api.mglenc-design.workers.dev/api/auth/login",
			"userKey"		=> "username",
			"passKey"		=> "password",
			"tokenPath"		=> "token",
			"redirectPath"	=> "",
			"tokenMode"		=> "fragment",
			"tokenParam"	=> "access_token",
			"apiKey"		=> "",
			"apiKeyHeader"	=> "X-Api-Key",
			"timeout"		=> 12,
			"workerMessage"	=> false,
			"caBundle"		=> "",
		);

		if(!is_array($cfg))
			$cfg = array();

		$this->cfg = array_merge($defaults, $cfg);
	}

	public function isConfigured()
	{
		return $this->cfg["loginUrl"]!="" && $this->cfg["appUrl"]!="";
	}

	public function lastError()
	{
		return $this->err;
	}

	/*
		Нэвтрэх оролдлого.

		Буцаах утга:
			array("ok"=>true,  "redirect"=>"https://...")
			array("ok"=>false, "error"=>"харуулах текст", "status"=>401)
	*/
	public function login($login, $password, $ip)
	{
		if(!$this->isConfigured())
			return $this->fail("Нэвтрэх үйлчилгээ тохируулагдаагүй байна.", 503);

		$login = trim((string)$login);

		if($login=="" || $password=="")
			return $this->fail("Нэвтрэх нэр болон нууц үгээ оруулна уу.", 400);

		if(strlen($login)>190 || strlen($password)>190)
			return $this->fail("Оруулсан утга хэт урт байна.", 400);

		$block = $this->throttleCheck($ip, $login);
		if($block!==false)
			return $this->fail("Хэт олон оролдлого хийсэн байна. ".$block." минутын дараа дахин оролдоно уу.", 429);

		$res = $this->callWorker($login, $password, $ip);

		if($res===false){
			$this->logLine("worker call failed: ".$this->err);
			return $this->fail("Системтэй холбогдож чадсангүй. Түр хүлээгээд дахин оролдоно уу.", 502);
		}

		$code = $res["code"];
		$body = $res["json"];

		if($code==400 || $code==401 || $code==403){
			$this->throttleFail($ip, $login);
			return $this->fail($this->workerMessage($body, "Нэвтрэх нэр эсвэл нууц үг буруу байна."), 401);
		}

		if($code==429){
			$this->throttleFail($ip, $login);
			return $this->fail($this->workerMessage($body, "Хэт олон оролдлого. Түр хүлээнэ үү."), 429);
		}

		if($code<200 || $code>=300){
			$this->logLine("worker http ".$code);
			return $this->fail("Системтэй холбогдож чадсангүй. Түр хүлээгээд дахин оролдоно уу.", 502);
		}

		/* Worker 200 буцаасан ч биедээ "ok:false" гэж хэлсэн байж болно */
		foreach(array("ok","success") as $flag){
			if(is_array($body) && isset($body[$flag]) && $body[$flag]===false){
				$this->throttleFail($ip, $login);
				return $this->fail($this->workerMessage($body, "Нэвтрэх нэр эсвэл нууц үг буруу байна."), 401);
			}
		}

		$redirect = $this->buildRedirect($body);

		if($redirect===false){
			/* Тохиргоог засахад тус болгож хариуны түлхүүрийн НЭРийг л бичнэ */
			$keys = is_array($body) ? implode(",", array_keys($body)) : "(json bish)";
			$this->logLine("worker 200 but no token at path '".$this->cfg["tokenPath"]."'; response keys: ".$keys);
			return $this->fail("Нэвтрэлт баталгаажсан ч систем рүү шилжих мэдээлэл дутуу байна.", 502);
		}

		$this->throttleClear($ip, $login);

		return array("ok"=>true, "redirect"=>$redirect);
	}

	/* ---------------- дотоод ---------------- */

	private function fail($msg, $status)
	{
		return array("ok"=>false, "error"=>$msg, "status"=>$status);
	}

	/*
		Анхдагчаар манай монгол бичвэрийг харуулна. Worker нь хэрэглэгчид
		шууд үзүүлэхээр монголоор бичдэг болсон үед $gloErpShowWorkerMessage-ыг
		асаавал түүний бичвэрийг дамжуулна.
	*/
	private function workerMessage($body, $fallback)
	{
		if(!is_array($body) || !$this->cfg["workerMessage"])
			return $fallback;

		foreach(array("message","error","msg") as $key){
			if(isset($body[$key]) && is_string($body[$key]) && trim($body[$key])!=""){
				$msg = trim(strip_tags($body[$key]));

				/* mbstring байхгүй сервер дээр ч UTF-8 тэмдэгт дундуур тасрахгүй */
				if(preg_match('/^.{0,200}/us', $msg, $m))
					return $m[0];

				return $fallback;
			}
		}

		return $fallback;
	}

	private function callWorker($login, $password, $ip)
	{
		$payload = json_encode(array(
			$this->cfg["userKey"] => $login,
			$this->cfg["passKey"] => $password,
		), JSON_UNESCAPED_UNICODE);

		$headers = array(
			"Content-Type: application/json",
			"Accept: application/json",
			"X-Client-IP: ".$ip,
			"X-Forwarded-For: ".$ip,
		);

		if($this->cfg["apiKey"]!="")
			$headers[] = $this->cfg["apiKeyHeader"].": ".$this->cfg["apiKey"];

		if(!function_exists("curl_init")){
			$this->err = "curl extension is missing";
			return false;
		}

		$ch = curl_init($this->cfg["loginUrl"]);
		curl_setopt_array($ch, array(
			CURLOPT_POST			=> true,
			CURLOPT_POSTFIELDS		=> $payload,
			CURLOPT_HTTPHEADER		=> $headers,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_FOLLOWLOCATION	=> false,
			CURLOPT_SSL_VERIFYPEER	=> true,
			CURLOPT_SSL_VERIFYHOST	=> 2,
			CURLOPT_CONNECTTIMEOUT	=> 6,
			CURLOPT_TIMEOUT			=> (int)$this->cfg["timeout"],
			CURLOPT_USERAGENT		=> "mglenc.com erp-login/1.0",
		));

		/* Сервер дээр CA багц тохируулаагүй бол зам нь энд дамжина.
		   Баталгаажуулалтыг унтраахгүй — зөвхөн ямар CA-г уншихыг заана. */
		if($this->cfg["caBundle"]!="" && is_file($this->cfg["caBundle"]))
			curl_setopt($ch, CURLOPT_CAINFO, $this->cfg["caBundle"]);

		$raw  = curl_exec($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		$cerr = curl_error($ch);
		curl_close($ch);

		if($raw===false){
			$this->err = $cerr;
			return false;
		}

		$json = json_decode($raw, true);

		if(!is_array($json))
			$json = null;

		return array("code"=>$code, "json"=>$json, "raw"=>$raw);
	}

	/*
		Worker-ийн хариунаас ERP рүү шилжих URL-ыг бүрдүүлнэ.
		Гадны хаяг руу шилжихээс сэргийлж эцсийн URL-ын host-ыг appUrl-тай тулгана.
	*/
	private function buildRedirect($body)
	{
		$appUrl = $this->cfg["appUrl"];

		/* 1. Worker өөрөө бэлэн redirect URL буцаадаг бол */
		if($this->cfg["redirectPath"]!=""){
			$url = $this->dotGet($body, $this->cfg["redirectPath"]);
			if(is_string($url) && $url!="")
				return $this->sameHost($url, $appUrl) ? $url : false;
		}

		/* 2. Token дамжуулахгүй горим — ERP өөрөө дахин нэвтрүүлнэ */
		if($this->cfg["tokenMode"]=="none")
			return $appUrl;

		$token = $this->dotGet($body, $this->cfg["tokenPath"]);

		if(!is_string($token) || $token=="")
			return false;

		$pair = $this->cfg["tokenParam"]."=".rawurlencode($token);

		if($this->cfg["tokenMode"]=="query")
			return $appUrl.(strpos($appUrl,"?")===false ? "?" : "&").$pair;

		/* Анхдагч: fragment — token нь server log, Referer-т үлдэхгүй */
		return $appUrl.(strpos($appUrl,"#")===false ? "#" : "&").$pair;
	}

	private function sameHost($url, $ref)
	{
		$a = parse_url($url, PHP_URL_HOST);
		$b = parse_url($ref, PHP_URL_HOST);

		if($a===null || $b===null)
			return false;

		return strtolower($a)===strtolower($b) && parse_url($url, PHP_URL_SCHEME)==="https";
	}

	private function dotGet($arr, $path)
	{
		if(!is_array($arr) || $path=="")
			return null;

		foreach(explode(".", $path) as $key){
			if(!is_array($arr) || !array_key_exists($key, $arr))
				return null;
			$arr = $arr[$key];
		}

		return $arr;
	}

	/* ---------------- оролдлогын тоолуур ---------------- */

	private function throttleDir()
	{
		$dir = rtrim(sys_get_temp_dir(), "/\\")."/mglenc-erp-login";

		if(!is_dir($dir))
			@mkdir($dir, 0700, true);

		return (is_dir($dir) && is_writable($dir)) ? $dir : false;
	}

	private function throttleFile($key)
	{
		$dir = $this->throttleDir();

		if($dir===false)
			return false;

		return $dir."/".sha1($key).".json";
	}

	private function throttleRead($key)
	{
		$file = $this->throttleFile($key);

		if($file===false || !is_file($file))
			return array("n"=>0, "start"=>time());

		$data = json_decode((string)@file_get_contents($file), true);

		if(!is_array($data) || !isset($data["start"]) || (time()-$data["start"])>self::WINDOW)
			return array("n"=>0, "start"=>time());

		return array("n"=>(int)$data["n"], "start"=>(int)$data["start"]);
	}

	/* Хаагдсан бол үлдсэн минутыг, эсрэг тохиолдолд false буцаана */
	private function throttleCheck($ip, $login)
	{
		$pairs = array(
			array("ip|".$ip, self::MAX_FAIL_IP),
			array("user|".strtolower($login), self::MAX_FAIL_USER),
		);

		foreach($pairs as $pair){
			$data = $this->throttleRead($pair[0]);

			if($data["n"] >= $pair[1]){
				$left = self::WINDOW - (time()-$data["start"]);
				return max(1, (int)ceil($left/60));
			}
		}

		return false;
	}

	private function throttleFail($ip, $login)
	{
		foreach(array("ip|".$ip, "user|".strtolower($login)) as $key){
			$file = $this->throttleFile($key);

			if($file===false)
				continue;

			$data = $this->throttleRead($key);
			$data["n"] = $data["n"] + 1;

			@file_put_contents($file, json_encode($data), LOCK_EX);
		}
	}

	private function throttleClear($ip, $login)
	{
		foreach(array("ip|".$ip, "user|".strtolower($login)) as $key){
			$file = $this->throttleFile($key);

			if($file!==false && is_file($file))
				@unlink($file);
		}
	}

	private function logLine($msg)
	{
		error_log("[erp-login] ".$msg);
	}
}

?>
