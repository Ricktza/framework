<?php
ob_start();
declare(encoding = 'UTF-8');
$start_time = microtime(true); // starts the counter

class database {

    function database($DBhostname, $DBusername, $DBpassword, $DBdatabase) { //didn't think it would act as a constructor 
        global $link;
        $link = mysqli_connect($DBhostname, $DBusername, $DBpassword, $DBdatabase);
        if (mysqli_connect_errno($link)) {
            echo 'ERROR #1: ' . mysqli_connect_error() . " on line " . __LINE__ . " within " . __FILE__;
        }
    }

    function change_db($dbname) {
        global $link;
        mysqli_select_db($link, $dbname);
        if (mysqli_connect_errno($link)) {
            echo 'ERROR #1: ' . mysqli_connect_error() . " on line " . __LINE__ . " within " . __FILE__;
        }
    }

    function protect($var) {
        global $link;
        $protected = mysqli_real_escape_string($link, $var);
        return $protected;
    }

    function query($query, $type) {
        global $link;
        if ($result = mysqli_query($link, $query)) {
            if ($type == 'object') {
                $obj = mysqli_fetch_object($result);
                return $obj;
            } elseif ($type == 'array') {
                $obj = mysqli_fetch_array($result);
                return $obj;
            } elseif ($type == 'count') {
                $obj = mysqli_num_rows($result);
                return $obj;
            } elseif ($type == 'assoc') {
                $obj = mysqli_num_assoc($result); // Using assoc for both array and assoc since it's faster LOL and does the same.
                return $obj;
            } elseif ($type == 'lengths') {
                $obj = mysqli_fetch_lengths($result); // Outputs a array containing the length.
                return $obj;
            } elseif ($type == 'update') {
                return $result; //  no direct method of doing this i think
            } elseif ($type == 'insert') {
                return $result; //  no direct method of doing this i think
            } elseif ($type == 'query') {
                return $result; //  no direct method of doing this i think
            } elseif ($type == 'delete') {
                return $result; //  no direct method of doing this i think       
            } else {
                return $result; // instead of a error just phrase it as a normal query
            }
        } else {
            echo '<br>ERROR #2: ' . mysqli_error($link) . ' MySQL error ' . mysqli_errno($link) . " on line " . __LINE__ . " within " . __FILE__;
        }
        $result->close();
    }

}

class functions {

    //This must be excuted before anything else.
    function redirect($url) {
        header('location: ' . "$url");
        ob_clean();
        exit();
    }

    function errors($value) {
        error_reporting($value);
        ini_set('display_errors', $value);
    }

    function functions() {
//$args = func_get_args(); 
    }

    function unique_id($var) {
        return strtoupper(uniqid(strtoupper($var) . '_'));
    }

//    function session_set($var, $id) {
//        $_SESSION[$id] = $var;
//    }
//
//    function session_get($id) {
//        $return = $_SESSION[$id];
//        return $return;
//    }
//
//    function session_unset($id) {
//        unset($_SESSION[$id]);
//    }
//
//    function session_kill() {
//        session_destroy();
//    }

    //this can be used for protection also, because it converts tags into enties which mysql is uneffected by these.
    function html($var) {
        $var = htmlentities($var);
        return $var;
    }

    function remove_html($var) {
        $var = strip_tags($var);
        return $var;
    }

    function summary($var, $start, $end) {
        if (strlen($var) > $end) {
            $var = substr($var, $start, $end);
            return $var . '...';
        } else {
            $var = substr($var, $start, $end);
            return $var;
        }
    }

    //Advanaced mail sender needs adding
    function mailsend($to, $subject, $message, $from) {
        $headers = 'From: ' . $from . "\r\n" .
                'Reply-To: ' . $from . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $message, $headers);
    }

    function randstr($length) {
        $randstr = "";
        for ($i = 0; $i < $length; $i++) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } else if ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }
        return $randstr;
    }

    function checkusername($username) {
        $username = trim($username);
        if (empty($username)) {
            return "The username was left blank.";
        } elseif (strlen($username) < 4) {
            return "The username was too short (min 4)";
        } elseif (strlen($username) > 26) {
            return "The username was too long (max 26)";
        } elseif (!preg_match('~^[a-z]{2}~i', $username)) {
            return "The username must start with two letters";
        } elseif (preg_match('~[^a-z0-9_.]+~i', $username)) {
            return "The username contains invalid characters.";
        } elseif (substr_count($username, ".") > 1) {
            return "The username may only contain one or less periods.";
        } elseif (substr_count($username, "_") > 1) {
            return "The username may only contain one or less underscores.";
        }
        return true;
    }

    function checkemail($email) {
        $email = trim($email);
        if (empty($email)) {
            return "The email was left blank.";
        } elseif (strlen($email) < 4) {
            return "The email was too short (min 4)";
        } elseif (!preg_match('~^[a-z]{2}~i', $email)) {
            return "The email must start with two letters";
        } elseif (preg_match('~[^a-z0-9_.@]+~i', $email)) {
            return "The email must contains invalid characters.";
        } elseif (substr_count($email, "@") < 1) {
            return "The email must contain the @...";
        }
        return true;
    }

    function makecomma($input) {
        if (strpos($input, '.') !== false) {
            return number_format($input, 2, '.', ',');
        } else {
            return number_format($input);
        }
    }

    function encyption_input($input) {
        $hash = hash('sha512', $input);
        return $hash;
    }

    function page_name() {
        return substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
    }

    function get_request($url, $data) {
        $params = array('http' => array(
                'method' => 'GET',
                'content' => $data
        ));
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }
        return $response;
    }

    function post_request($url, $data) {
        $params = array('http' => array(
                'method' => 'POST',
                'content' => $data
        ));
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }

        return $response;
    }

    function json_encode($var) {
        return json_encode($var);
    }

    function json_decode($var) {
        return json_decode($var);
    }

    function compress($var) {
        return gzcompress($var);
    }

    function decompress($var) {
        return gzuncompress($var);
    }

    function execution_time() {
        Global $start_time;
        return round(microtime(true) - $start_time, 4);
    }

    function get_ip() {
        return $_SERVER['REMOTE_ADDR'];
    }

    function get_real_ip() {
        
    }

    function base64_encode($var) {
        return base64_encode($var);
    }

    function base64_decode($var) {
        return base64_decode($var);
    }

    function maketime($secs) {
//V2 - might be buggy not tested it...
        error_reporting($value);
        ini_set('display_errors', $value);
        if ($secs >= 86400) {
            $days = floor($secs / 86400);
            $secs = $secs % 86400;
            $r = $days . ' day';
            if ($days <> 1) {
                $r.='s';
            }if ($secs > 0) {
                $r.=', ';
            }
        }
        if ($secs >= 3600) {
            $hours = floor($secs / 3600);
            $secs = $secs % 3600;
            $r.=$hours . ' hour';
            if ($hours <> 1) {
                $r.='s';
            }if ($secs > 0) {
                $r.=', ';
            }
        }
        if ($secs >= 60) {
            $minutes = floor($secs / 60);
            $secs = $secs % 60;
            $r.=$minutes . ' minute';
            if ($minutes <> 1) {
                $r.='s';
            }if ($secs > 0) {
                $r.=', ';
            }
        }
        $r.=$secs . ' second';
        if ($secs <> 1) {
            $r.='s';
        }
        return $r;
    }

    function agent() {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    function fake_404() {
        if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
            header('HTTP/1.x 404 Not Found');
            echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">' . "\n" .
            '<html><head>' . "\n" .
            '<title>404 Not Found</title>' . "\n" .
            '</head><body>' . "\n" .
            '<h1>Not Found</h1>' . "\n" .
            '<p>The requested URL ' .
            str_replace(strstr($_SERVER['REQUEST_URI'], '?'), '', $_SERVER['REQUEST_URI']) .
            ' was not found on this server.</p>' . "\n" .
            '</body></html>' . "\n";
            exit;
        }
    }

    function read_dir($dir, $url, $exclude) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && $file != '.htaccess') {
                return $filename = substr($file, 0, strpos($file, "."));
            }
        }
    }

    function status($ip, $port) {
        $status = @fsockopen($ip, $port, $errno, $errstr, 0.5);
        if ($status) {
            @fclose($status);
            return true;
        } else {
            @fclose($status);
            return false;
        }
    }

    function screen($screen, $command) {
        $command = "screen -S " . $screen . " -X stuff '" . $command . "' -ne '\015'"; //may not work can't test yet
        exec($command);
        shell_exec($command);
    }

    function jsondb_write($location, $content) {
        $current = file_get_contents($file);
        file_put_contents($file, $content);
    }

    function json_read($location) {
        $current = file_get_contents($file);
        return $current;
    }

    function upper_case($var) {
        return strtoupper($var);
    }

    function lower_case($var) {
        return strtolower($var);
    }

    function fl_upper_case($var) {
        return ucfirst($var);
    }

    function fw_upper_case($var) {
        return ucwords($var);
    }

    function add_peroid($var) {
        $var = trim($var);
        $var = strip_tags($var);
        if (substr($var, -1) != '.') {
            $var = '.';
        }
        return $var;
    }

    function minecraft_api($url, $port, $timeout) {
        define('MQ_SERVER_ADDR', $url);
        define('MQ_SERVER_PORT', $port);
        define('MQ_TIMEOUT', $timeout);
        include $_SERVER['DOCUMENT_ROOT'] . "/api/MinecraftQuery.class.php";
        $Query = new MinecraftQuery();
        try {
            $Query->Connect(MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_TIMEOUT);
        } catch (MinecraftQueryException $e) {
            $Exception = $e;
        }
        $count = 0;
        if (( $Players = $Query->GetPlayers() ) !== false) {
            foreach ($Players as $Player) {
                $count++;
            }
        }
        return $count;
    }

    function replace($txt, $code) {

        if ($code > 0) {
            $txt = str_ireplace("[green]", "<font color=GREEN>", $txt);
            $txt = str_ireplace("[red]", "<font color=RED>", $txt);
            $txt = str_ireplace("[blue]", "<font color=BLUE>", $txt);
            $txt = str_ireplace("[purple]", "<font color=PURPLE>", $txt);
            $txt = str_ireplace("[yellow]", "<font color=YELLOW>", $txt);
            $txt = str_ireplace("[gold]", "<font color=GOLD>", $txt);
            $txt = str_ireplace("[black]", "<font color=BLACK>", $txt);
            $txt = str_ireplace("[orange]", "<font color=orange>", $txt);
            $txt = str_ireplace("[pink]", "<font color=pink>", $txt);
            $txt = str_ireplace("[grey]", "<font color=grey>", $txt);
            $txt = str_ireplace("[gray]", "<font color=grey>", $txt);
            $txt = str_ireplace("[white]", "<font color=white>", $txt);
            $txt = str_ireplace("[violet]", "<font color=violet>", $txt);
            $txt = str_ireplace("[indigo]", "<font color=indigo>", $txt);
            $txt = str_ireplace("[/color]", "</font>", $txt);
            $txt = str_ireplace("[/colour]", "</font>", $txt);
            $txt = str_ireplace("[b]", "<b>", $txt);
            $txt = str_ireplace("[/b]", "</b>", $txt);
            $txt = str_ireplace("[i]", "<i>", $txt);
            $txt = str_ireplace("[/i]", "<i>", $txt);
            $txt = str_ireplace(array("\r\n", "\n", "\r"), "<br>", $txt);
            $txt = str_ireplace("[bold]", "<b>", $txt);
            $txt = str_ireplace("[u]", "<u>", $txt);
            $txt = str_ireplace("[/u]", "</u>", $txt);
        }
//chat and forums
        if ($code > 1) {
            //$username = $_SESSION['username'];
            // This so happens to not work right :P
            //$txt = str_ireplace("@$username", "<font color=black><b>$username</b></font>", $txt);
            //
            //$txt = str_ireplace("$username", "<font color=black><b>$username</b></font>", $txt);
            $txt = str_ireplace(":)", "<img src='/img/emotions/smile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":=)", "<img src='/img/emotions/smile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace("=)", "<img src='/img/emotions/smile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":-)", "<img src='/img/emotions/smile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":(", "<img src='/img/emotions/sadsmile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":=(", "<img src='/img/emotions/sadsmile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace("=(", "<img src='/img/emotions/sadsmile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":-(", "<img src='/img/emotions/sadsmile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":D", "<img src='/img/emotions/bigsmile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":-D", "<img src='/img/emotions/bigsmile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":=d", "<img src='/img/emotions/bigsmile.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(";)", "<img src='/img/emotions/wink.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(";-)", "<img src='/img/emotions/wink.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":*", "<img src='/img/emotions/kiss.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":p", "<img src='/img/emotions/tongueout.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":$", "<img src='/img/emotions/blush.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":^)", "<img src='/img/emotions/wondering.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":@", "<img src='/img/emotions/angry.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(":s", "<img src='/img/emotions/worried.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace("(y)", "<img src='/img/emotions/yes.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace("(n)", "<img src='/img/emotions/no.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(">:)", "<img src='/img/emotions/evilgrin.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace(">:(", "<img src='/img/emotions/angry.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace("(poolparty)", "<img src='/img/emotions/poolparty.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace("(drunk)", "<img src='/img/emotions/drunk.gif' width='19' height='19'>", $txt);
            $txt = str_ireplace("(thanks)", "<img src='/img/emotions/thanks.gif' width='25' height='25'>", $txt);
            $txt = str_ireplace("[center]", "<center>", $txt);
            $txt = str_ireplace("[/center]", "</center>", $txt);
            $txt = str_ireplace("[italic]", "<i>", $txt);
            $txt = str_ireplace("[underline]", "<u>", $txt);
            $txt = str_ireplace("[br]", "</ br>", $txt);
            $txt = str_ireplace("[CraftTM-]", "<marquee behavior='alternate' direction='right'>", $txt);
            $txt = str_ireplace("[hr]", "<hr>", $txt);
            $txt = str_ireplace("[xx-small]", "<font style='font-size:xx-small;'>", $txt);
            $txt = str_ireplace("[small]", "<font style='font-size:small;'>", $txt);
            $txt = str_ireplace("[medium]", "<font style='font-size:medium;'>", $txt);
            $txt = str_ireplace("[large]", "<font style='font-size:large;'>", $txt);
            $txt = str_ireplace("[xx-large]", "<font style='font-size:x-large;'>", $txt);
            $txt = str_ireplace("[fancy]", "<font style='font-family: Allan, helvetica, arial, sans-serif;font-size: 14px;'>", $txt);
            $txt = str_ireplace("[o]", "<font style='font-style:oblique;'>", $txt);
            $txt = str_ireplace("[outline]", "<span style='border:1px solid green;outline:green dotted thin;background-color:yellow;opacity:0.5;filter:Alpha(opacity=50);'>", $txt);
            $txt = str_ireplace("[highlight]", "<span style='background-color:yellow;opacity:0.5;filter:Alpha(opacity=50);'>", $txt);
        }
//forums only codes.
        if ($code > 2) {
            $txt = str_ireplace("[/youtube]", "'></iframe>", $txt);
            $txt = str_ireplace("[youtube]", "<br /><br /><iframe width='530' height='290' frameborder='0' allowfullscreen src='http://www.youtube.com/embed/", $txt);
            $txt = str_ireplace("[img]", "<br /><br /><img border=0 style='max-height: 1000px; max-width: 530px;' src=", $txt);
            $txt = str_ireplace("[/img]", ">", $txt);

            $txt = str_ireplace("[url=&quot;", '<a href=', $txt);
            $txt = str_ireplace("&quot;]", " target='_blank'>", $txt);

            $txt = str_ireplace("[/url]", "</a>", $txt);
        }
        return $txt;
    }

    function ago($time, $tense_show) {
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60", "59", "24", "7", "3.35", "11", "10");
        $now = time();
        $difference = $now - $time;
        if ($tense_show == 1) {
            $tense = "ago";
        } else {
            $tense = "";
        }
        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }
        if ($difference != 1) {
            $periods[$j].= "s";
        }
        $difference = round($difference);
        if ($difference >= 60 && $periods[$j] == years) {
            $difference = 'Unknown';
            $periods[$j] = '';
            $tense = '';
        }
        return "$difference " . $periods[$j] . " $tense";
    }

}

if (define('APC_EXTENSION_LOADED', extension_loaded('apc') && ini_get('apc.enabled'))) {

    class cache {

        function store($id, $data) {
            apc_store($id, $data, '-1'); //-1 means forever, or at least till server restart/apache restart.
        }

        function get($id) {
            if (apc_fetch($id)) {
                return apc_fetch($id);
            } else {
                return null;
            }
        }

        function set($id, $data, $time) {
            if (apc_fetch($id)) {
                return apc_fetch($id);
            } else {
                apc_store($id, $data, $time);
                return apc_fetch($id);
            }
        }

        function remove($id) {
            apc_delete($id);
        }

        function clear() {
            apc_clear_cache();
        }

        function dump() {
            return apc_bin_dump($files);
        }

        function add_page($filename) {
            apc_compile_file($filename);
        }

        function remove_page($filename) {
            apc_delete_file($filename);
        }

        function info() {
            apc_cache_info(); //need to format this at somepoint.
        }

    }

}
ob_clean();
?>
