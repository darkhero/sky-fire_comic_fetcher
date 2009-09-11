<?
function getSkyfireComic($val,$t = null ){
	if(!is_null($t)){
		$url = "http://hotpic.sky-fire.com/Utility/2/{$t}/{$val}.js";
	}else{
		$url = "http://hotpic.sky-fire.com/Utility/2/{$val}.js";
	}
	echo $url;
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $url.$nowPic);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);

	$str = curl_exec($curl)."\n";
	curl_close($curl);

		// 有時後會 comic.sky-fire.com 會把圖片放在不同的主機
		preg_match_all('(http://hotpic.sky-fire.com/Pic/OnlineComic[0-9]/[[:alnum:]\/._-]*)',$str,$data);
	//	preg_match_all('(http://v.sky-fire.com/Temp/[[:alnum:]\/._-]*)',$str,$data);
			$img_src = $data[1];

			$i = 0;
			foreach($data[0] as $img_src){
				$i++;
				$pid = pcntl_fork();
				if($pid == -1){
				    die('could not fork');
				}else if($pid){
				    echo 'Fork to '.$pid.' for get '.$img_src."\n";
				}else{
				$output_file_name = sprintf('%02d.jpg',$i);
				if(file_exists($val."/".$output_file_name)){
					echo $val."/".$output_file_name." File Exists!\n";
					exit;
				}
				$curl = curl_init();

				$header[] = 'Accept: image/png,*/*;q=0.5';
				$header[] = 'Accept-Language: zh-tw,en-us;q=0.7,en;q=0.3';
				$header[] = 'Accept-Encoding: gzip,deflate';
				$header[] = 'Accept-Charset: Big5,utf-8;q=0.7,*;q=0.7';
				$cookie = 'cnzz02=3; rtime=0; CommunityServer-UserCookie1=lv=1999-1-1 0:00:00&mra='.$mra.'; ltime=1174363041366; cnzz_eid=52991425-';

				curl_setopt($curl, CURLOPT_URL, $img_src);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($curl, CURLOPT_COOKIE,$cookie);
				curl_setopt($curl, CURLOPT_REFERER,$url.$nowPic.".html");
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,10);
				//curl_setopt($curl, CURLOPT_PROXY,'proxy.hinet.net');
				//curl_setopt($curl, CURLOPT_PROXYPORT, 80);
				$content = curl_exec($curl);
				curl_close($curl);

				if(strlen($content) > 100){
					echo "Save: ".$val."/".$output_file_name." ".strlen($content)."\n";
					$fp = fopen($val."/".$output_file_name,'w');
					fputs($fp,$content);
					fclose($fp);
					exit;
				}else{
					echo "Connect Time Out.Retry \n";
					exit;
				}
				}
			}
}

//mkdir(sprintf('%03d',$_SERVER['argv'][1]));
mkdir($_SERVER['argv'][1]);
if(isset($_SERVER['argv'][2])){
	getSkyfireComic($_SERVER['argv'][1],$_SERVER['argv'][2]);
}else{
	getSkyfireComic($_SERVER['argv'][1]);
}
/*
for($i = 218; $i <= 226; $i++){
	mkdir(sprintf('%03d',$i));
	getSkipBeat(sprintf('%03d',$i));
}
*/
?>
