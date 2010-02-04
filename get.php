#!/usr/bin/php
<?php
/*
 * 海賊王 = 2
 * Bleach = 19
 * 火影忍者 = 4
 * 惡魔辯護 = 455
 * Reborn = 256
 */
function getSkyfireComic($comic_id,$val,$t = null ){
	// 建立資料夾.
	if(!is_dir(dirname(__FILE__).DIRECTORY_SEPARATOR.$val)){
		mkdir(dirname(__FILE__).DIRECTORY_SEPARATOR.$val);
	}

	$skyfire_hostname = 'hotpic.sky-fire.com';
	$url = "http://{$skyfire_hostname}/Utility/{$comic_id}/{$val}.js";
	echo $url."\n";
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $url.$nowPic);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);

	$str = curl_exec($curl)."\n";
	$status = curl_getinfo($curl);
	if($status['http_code'] != 200){
		$skyfire_hostname = 'coldpic.sky-fire.com';

		$url = "http://{$skyfire_hostname}/Utility/{$comic_id}/{$val}.js";
		echo $url."\n";
		curl_setopt($curl, CURLOPT_URL, $url.$nowPic);
		$str = curl_exec($curl)."\n";
	}
	curl_close($curl);

		// 有時後會 comic.sky-fire.com 會把圖片放在不同的主機
		preg_match_all('(http://'.$skyfire_hostname.'/Pic/OnlineComic[0-9]/[[:alnum:]\/._-]*)',$str,$data);
		echo count($data[0]);
		if(count($data[0]) <= 1){
			preg_match_all('(http://v.sky-fire.com/Temp/[[:alnum:]\/._-]*)',$str,$data);
		}
			$i = 0;
			foreach($data[0] as $img_src){
				$i++;
				$output_file_name = sprintf('%02d.jpg',$i);
				if(file_exists($val."/".$output_file_name)){
					echo $val."/".$output_file_name." File Exists!\n";
					continue;
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
					continue;
				}else{
					echo "Connect Time Out.Retry \n";
					continue;
				}
			}
}

$comic_id = trim($_SERVER['argv'][1]);
$set_id = $_SERVER['argv'][2];

getSkyfireComic($_SERVER['argv'][1],$_SERVER['argv'][2]);