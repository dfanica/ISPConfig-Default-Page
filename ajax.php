<?php
class FRAMEWORK {
	public $ROOT = NULL;
	public $IP = NULL;
	public $BROWSER = NULL;
	public $DOMAIN = NULL;
	public $PATH = NULL;
	public $METHOD = NULL;
	public $DEBUGLEVEL = NULL;
	public $TIMEZONE = NULL;
	public $SQL = NULL;
	public $DB = NULL;
	public $TOKEN = NULL;
	public $PAGE = NULL;

	public function initialize() {
		error_reporting($this->DEBUGLEVEL);
		date_default_timezone_set($this->TIMEZONE);
		$this->DB = new mysqli($this->SQL["host"], $this->SQL["user"], $this->SQL["pass"], $this->SQL["name"]);
		$this->DB->set_charset("UTF8");
		return (0);
	}

	public function session() {
		session_set_cookie_params(0);
		session_start();
		$_SESSION["token"][] = array("timestamp" => time(), "hash" => md5(uniqid(rand(), TRUE)));
		foreach ($_SESSION["token"] as $token) {
			if ((time() - $token["timestamp"]) <= 60 * 60) {
				$this->TOKEN[] = $token["hash"];
			}
		}
		return (0);
	}

	public function check() {
		$data = array(
			"id" =>			$this->DB->real_escape_string("NULL"),
			"ip" =>			$this->DB->real_escape_string($this->IP),
			"browser" =>	$this->DB->real_escape_string($this->BROWSER),
			"domain" =>		$this->DB->real_escape_string($this->DOMAIN),
			"path" =>		$this->DB->real_escape_string($this->PATH),
			"method" =>		$this->DB->real_escape_string($this->METHOD),
			"date" =>		$this->DB->real_escape_string(date("Y-m-d H:i:s")),
			"uptodate" =>	$this->DB->real_escape_string(date("Y-m-d H:i:s")),
			"flag" =>		$this->DB->real_escape_string(0)
		);
		$result = $this->DB->query("INSERT INTO logs VALUES (".$data["id"].",
			'".$data["ip"]."',
			'".$data["browser"]."',
			'".$data["domain"]."',
			'".$data["path"]."',
			'".$data["method"]."',
			'".$data["date"]."',
			'".$data["uptodate"]."',
			'".$data["flag"]."'
		)");
		$result = $this->DB->query("SELECT * FROM pages");
		while (($row = $result->fetch_array())) {
			if ($this->match($row["domain"], $row["path"])) {
				$this->PAGE["description"] = $row["description"];
				$this->PAGE["keywords"] = $row["keywords"];
				$this->PAGE["author"] = $row["author"];
				$this->PAGE["title"] = $row["title"];
				return (0);
			}
		}
		return ($this->PATH == "/" ? 0 : $this->redirect("/"));
	}

	public function match($domain, $path) {
		$pattern = preg_replace("/\//", "\/", $domain.$path);
		return (preg_match("/".$pattern."$/", $this->DOMAIN.$this->PATH));
	}

	public function redirect($url) {
		exit(header("Location: ".$url));
		return (0);
	}

	public function format($date) {
		$date = date("F", strtotime($date))." ".date("d", strtotime($date)).", ".date("Y", strtotime($date));
		return ($date);
	}

	public function permalink($data) {
		$buffer = NULL;
		foreach (str_split(strtoupper($data)) as $char) {
			$buffer = $buffer.strtolower((ord($char) >= 48 && ord($char) <= 57) || (ord($char) >= 65 && ord($char) <= 90) ? $char : "-");
		}
		return (preg_replace("/-+/", "-", $buffer));
	}

	public function json($data) {
		if (is_string($data)) {
			return (json_decode($data, TRUE));
		}
		if (is_object($data) || is_array($data)) {
			return (json_encode($data));
		}
		return (NULL);
	}
}

$_FWK = new FRAMEWORK();

$_FWK->ROOT = $_SERVER["DOCUMENT_ROOT"];
$_FWK->IP = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
$_FWK->BROWSER = $_SERVER["HTTP_USER_AGENT"];
$_FWK->DOMAIN = $_SERVER["HTTP_HOST"];
$_FWK->PATH = current(explode("?", $_SERVER["REQUEST_URI"]));
$_FWK->METHOD = $_SERVER["REQUEST_METHOD"];

$_FWK->DEBUGLEVEL = E_ERROR;
$_FWK->TIMEZONE = "Europe/London";
?>
<?php
class API {
	public $MANDRILL = NULL;

	public function mandrill($message, $subject, $from, $fullname, $to) {
		$url = "https://mandrillapp.com/api/1.0/messages/send";
		$tmp = array(
			"key" =>					urlencode($this->MANDRILL),
			"message[text]" =>			urlencode($message),
			"message[subject]" =>		urlencode($subject),
			"message[from_email]" =>	urlencode($from),
			"message[from_name]" =>		urlencode($fullname),
			"message[to][0][email]" =>	urlencode($to)
		);
		foreach ($tmp as $key => $value) {
			$parameters = isset($parameters) ? $parameters."&".$key."=".$value : $key."=".$value;
		}
		$request = curl_init();
		curl_setopt($request, CURLOPT_URL, $url);
		curl_setopt($request, CURLOPT_POST, TRUE);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($request, CURLOPT_HTTPHEADER, NULL);
		curl_setopt($request, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($request, CURLOPT_USERPWD, NULL);
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($request);
		curl_close($request);
		return ($response);
	}
}

$_API = new API();
$_API->MANDRILL = "YOUR MANDRILL KEY";
?>
<?php
class PROCESS {
	public function deliver() {
		global $_FWK;
		global $_API;

		if (isset($_REQUEST)) {
			if (isset($_REQUEST["fullname"]) && isset($_REQUEST["from"]) && isset($_REQUEST["subject"]) && isset($_REQUEST["message"])) {
				$message = strip_tags($_REQUEST["message"]);
				$subject = strip_tags($_REQUEST["subject"]);
				$from = strip_tags($_REQUEST["from"]);
				$fullname = strip_tags($_REQUEST["fullname"]);
				$_API->mandrill($message, $subject, $from, $fullname, "YOUR EMAIL ADDRESS");
			}
		}
		return (0);
	}
}

$_PROCESS = new PROCESS();
if (isset($_REQUEST["process"]) && is_string($_REQUEST["process"]) && method_exists($_PROCESS, $_REQUEST["process"])) {
	$_PROCESS->$_REQUEST["process"]();
}
?>
