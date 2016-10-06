<?php
error_reporting(0);
class Ubiquiti
{
	function ip($value){ 
		return $this->ip = $value;
	}
	function username($value){ 
		return $this->username = $value;
	}
	function password($value){ 
		return $this->password = $value;
	}
	function ngecurl($url , $post=null , $header=null){
        $ch = curl_init($url);
        if($post != null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; U; CPU iPhone OS 8_3_3 like Mac OS X; en-SG) AppleWebKit/537.25 (KHTML, like Gecko) Version/7.0 Mobile/8C3 Safari/6533.18.1");
        curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd()."cookies.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd()."cookies.txt");
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        if($header != null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        }
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_COOKIESESSION, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        return curl_exec($ch);
        curl_close($ch);
    }
    function Ok(){
    	$header = array("origin:https://".$this->ip,"Upgrade-Insecure-Requests:1","referer:https://".$this->ip."/login.cgi");
		$result = json_decode($this->ngecurl("https://".$this->ip."/survey.json.cgi?iface=ath0",$header),true);
		if($result){
			$this->check($result);
		}else{
			$this->run();
		}
    }
    function check($result){
    	foreach ($result as $key => $value) {
    		if($value[encryption] =="wpa"){
    			$value[encryption] = "wpa ";
    		}
    		if($value[signal_level] <= "-75" ){
    			$status = " ";
    		}else{
    			$status = "+";
    		}
    		if($value[essid] != ""){
    			if(str_replace("-","",$value[signal_level]) < "75" ){
    				$apik[]  =  "| ".$value[signal_level]."  | ".$status." | ".$value[encryption]." | ".$value[mac]." | ".$value[essid]."\r\n";
    			}else{
    				$elek[] =  "| ".$value[signal_level]."  | ".$status." | ".$value[encryption]." | ".$value[mac]." | ".$value[essid]."\r\n";
    			}
    			if($value[encryption] =="none"){
    				$none[]  =  "| ".$value[signal_level]."  | ".$status." | ".$value[encryption]." | ".$value[mac]." | ".$value[essid]."\r\n";
    			}
    		}
    	}
    	sort($woot,SORT_STRING);
    	sort($none,SORT_NUMERIC);
    	sort($apik,SORT_NUMERIC);
    	echo "+------------------------------------------------------------\r\n";
    	echo "+ SNYL | N | ENC  | BSSID/MAC ADDRESS | SSID/Nama Jaringan --\r\n";
    	echo "+------------------------------------------------------------\r\n";
    	foreach ($apik as $value) {
    		echo $value;
    	}
    	echo "-----------------------------------------------------------+\r\n";
    	foreach ($elek as $value) {
    		echo $value;
    	}
    	echo "-----------------------------------------------------------+\r\n";
    	foreach ($none as $value) {
    		echo $value;
    	}
    	echo "-----------------------------------------------------------+\r\n";
    	$this->clearStdin();
    	$this->Ok();
    	echo "\r\n\n\n\n";
    }
    function clearStdin(){
    	sleep(5);
    	for ($i = 0; $i < 50; $i++) echo "\r\n";
	}
	function run(){ 
		$header = array("origin:https://".$this->ip,"Upgrade-Insecure-Requests:1","referer:https://".$this->ip."/login.cgi");
		$result = json_decode($this->ngecurl(
			"https://".$this->ip."/login.cgi" , 
			"password=".$this->password."&uri=/survey.json.cgi?iface=ath0&username=".$this->username,$header),true);
		if($result){
			$this->check($result);
		}else{
			$this->run();
		}
	}
}
$Ubiquiti = new Ubiquiti;
/*$Ubiquiti->ip("192.168.1.4");
$Ubiquiti->username("ubnt");
$Ubiquiti->password("");*/
echo "[Ubiquiti] IP Ubiquiti : ";$Ubiquiti->ip(fgets(fopen ("php://stdin","r")));
echo "[Ubiquiti] Username Ubiquiti : ";$Ubiquiti->username(fgets(fopen ("php://stdin","r")));
echo "[Ubiquiti] Password Ubiquiti : ";$Ubiquiti->password(fgets(fopen ("php://stdin","r")));
$Ubiquiti->run();
