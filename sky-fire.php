<?php
/*
 * 海賊王 = 2
 * Bleach = 19
 * 火影忍者 = 4
 * 惡魔辯護 = 455
 * Reborn = 256
 * 結界師 = 74
 */
function getSkyfireComic($comic_id,$val,$target_path = null){
	// 建立資料夾.
	if($target_path == null){
		$target_path = dirname(__FILE__).DIRECTORY_SEPARATOR.$val;
	}else{
		$target_path .= DIRECTORY_SEPARATOR.$val;
	}
	if(!is_dir($target_path)){
		mkdir($target_path);
	}
	$skyfire_hostnames = array('pic.sfacg.com','pic2.sfacg.com','coldpic.sfacg.com','hotpic.sfacg.com','pic3.sfacg.com');
	
	shuffle($skyfire_hostnames);

	foreach($skyfire_hostnames as $skyfire_hostname){
		$url = "http://{$skyfire_hostname}/Utility/{$comic_id}/{$val}.js";
		echo $url."\n";
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url.$nowPic);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);

		$str = curl_exec($curl)."\n";
		$status = curl_getinfo($curl);
		if($status['http_code'] == 200){
			break;
		}
		curl_close($curl);
	}

	// 有時後會 comic.sky-fire.com 會把圖片放在不同的主機
	foreach($skyfire_hostnames as $skyfire_hostname){
		echo $skyfire_hostname;
		$rex = '(http://'.$skyfire_hostname.'/Pic/OnlineComic[0-9]/[[:alnum:]\/._-]*)';
		preg_match_all($rex,$str,$data,PREG_PATTERN_ORDER);
		print_r($data);
		if(count($data[0]) > 1)
			break;
	}
	if(count($data[0]) <= 1){
		$rex = '(http://v.sky-fire.com/Temp/[[:alnum:]\/._-]*)';
		preg_match_all($rex,$str,$data);
	}
	echo "本回共".count($data[0])."頁\n";

	$i = 0;
	foreach($data[0] as $img_src){
		$i++;
		$output_file_name = sprintf('%03d.png',$i);
		if(file_exists($target_path."/".$output_file_name)){
			echo $target_path."/".$output_file_name." File Exists!\n";
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
		echo "Save: ".$target_path."/".$output_file_name." ".strlen($content)."\n";
		$fp = fopen($target_path."/".$output_file_name,'w');
		fputs($fp,$content);
		fclose($fp);
		}else{
		echo "Connect Time Out.Retry \n";
		}
		}
		}
