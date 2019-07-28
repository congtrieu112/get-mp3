<?php
	/**
	*
 	* @miniProject: Get Info Zing Mp3 & NCT
	* @author Del Huỳnh - fb.com/huynhducuduy
	* @param [GET]title,[GET]artist?,[GET]type
	* @core https://github.com/anbinh/dna/blob/master/zi/apifuncs.go
	*/
	header("Access-Control-Allow-Headers: *");
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function getStr($string,$start,$end){
		$str = explode($start,$string,2);
		$str = explode($end,$str[1],2);
		return $str[0];
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function goCurl($url)
	{
		$options = array(
			CURLOPT_URL			   => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_FOLLOWLOCATION    => true,
		);
		$ch = curl_init();
		curl_setopt_array($ch,$options);
		$content = curl_exec($ch);
		return $content;
		curl_close($ch);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$title = $_GET['title'];
	$artist = $_GET['artist'];
	$hostType = $_GET['type'];
	$method = $_GET['method'];
	$link = $_GET['link'];
	$typeMp3 = $_GET["type-audio"];

	if (($title == '' && $method == '1')
	 || ($link == '' && $method == '2'))
	{
		$mp3['error'] = 1;
	}
	else
	{
		$mp3['error'] = 0;
		switch ($hostType) {
			case '1': // Zing Mp3

				switch ($method) {
					case '1':
					
						$url = 'http://mp3.zing.vn/tim-kiem/bai-hat.html?q='.rawurlencode($title);
			
						if ($artist != '') {
							$url .= rawurlencode(' - '.$artist);
						}
						$url .= '&filter=hq'; // hq,official,hit
						$url .= '&sort=total_play'; // total_play,created_date
				
						$mp3SearchContent = goCurl($url);
						
						$mp3EncodeId = getStr($mp3SearchContent,'<div class="item-song" id="song','"');
						
					break;
					case '2':
						$mp3EncodeId = explode('/',$link);
						
						if(count($mp3EncodeId)<2) {
							$mp3EncodeId = str_replace("video-","",$link);
							$mp3EncodeId = str_replace("song-", "", $mp3EncodeId);
						}else{
							$mp3EncodeId = explode('.', $mp3EncodeId['5']);
							$mp3EncodeId = $mp3EncodeId['0'];
						}
					break;
					default:
						$mp3['error'] = 4;
					break;
				}


				if ($mp3EncodeId != '')
				{
					$zingMp3Api = 'http://api.mp3.zing.vn/api/mobile/';
					$zingMp3Link = 'http://mp3.zing.vn';
					$zingMp3Cdn = 'http://image.mp3.zdn.vn/';
					$zingMp3Media = "https://mp3.zing.vn/xhr";
					$zingMp3ApiLink = "http://mp3.zing.vn/embed";
					$pregMatch  = "";
					$format = "song";
					if(strpos($link,"video") !== false || $typeMp3 === "video") {
						$zingMp3ApiLink .= "/video/";
						$pregMatch = '/data-xml="(.*?)"/';
						$format = "video";
					}else{
						$zingMp3ApiLink .="/song/";
						$pregMatch = '/xml="(.*?)"/';
					}
					
					$apiEmptyLink = $zingMp3ApiLink.$mp3EncodeId;
					
					////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					$dataSend = goCurl($apiEmptyLink);
					// echo ($apiEmptyLink);

					$urlToken = preg_match_all($pregMatch, $dataSend, $outputArray);
					
					$urlToken = $zingMp3Media.$outputArray[1][0];
					$linkMedia=  goCurl($urlToken);
					// var_dump($linkMedia);
					$dataJsonLink = json_decode($linkMedia,true);
					file_put_contents(__DIR__."/log.txt",print_r($dataJsonLink,true),FILE_APPEND);
					
					$linkMp3 = ($format==="song")?"https:".$dataJsonLink["data"]["source"][128]:$dataJsonLink["data"]["source"]["mp4"]["480p"];
					$mp3Json = goCurl($zingMp3Api.'song/getsonginfo?requestdata={%22id%22:%22'.$mp3EncodeId.'%22}');
					$mp3Data = json_decode($mp3Json, true);
					$mp3['encodeId'] = $mp3Data['song_id_encode'];
					$mp3['id'] = $mp3Data['song_id'];
					$mp3['link'] = $zingMp3Link.$mp3Data['link'];
					$mp3['title'] = $mp3Data['title'];
					$mp3['artist'] = $mp3Data['artist'];
					$mp3['artistId'] = $mp3Data['artist_id'];
					$artistJson = goCurl($zingMp3Api.'artist/getartistinfo?requestdata={%22id%22:'.$mp3['artistId'].'}');
					$artistData = json_decode($artistJson, true);
					if ($artistData['link'] != '') $mp3['artistLink'] = $zingMp3Link.$artistData['link'];
					if ($artistData['avatar'] != '') $mp3['artistImage'] = $zingMp3Cdn.$artistData['avatar'];
					$mp3['album'] = $mp3Data['album'];
					$mp3['albumId'] = $mp3Data['album_id'];
					$albumJson = goCurl($zingMp3Api.'playlist/getplaylistinfo?requestdata={%22id%22:'.$mp3['albumId'].'}');
					$albumData = json_decode($albumJson, true);
					if ($albumData['link'] != '') $mp3['albumLink'] = $zingMp3Link.$albumData['link'];
					if ($albumData['cover'] != '') $mp3['albumImage'] = $zingMp3Cdn.$albumData['cover']; 
					$mp3['videoId'] = $mp3['id'];
					$videoJson = goCurl($zingMp3Api.'video/getvideoinfo?requestdata={%22id%22:'.$mp3['videoId'].'}');
					$videoData = [];// json_decode($videoJson, true);
					if($format==="video"){
						$videoData['link'] = $linkMp3;
						$videoData['thumbnail'] = $dataJsonLink['data']['thumbnail'];
					}else{
						$videoData['thumbnail'] = isset($dataJsonLink['data']["album"]["thumbnail_medium"]) ? $dataJsonLink['data']["album"]["thumbnail_medium"] : $dataJsonLink["data"]["artist"]["thumbnail"];
					}
					$mp3["singer"] = $dataJsonLink["data"]["artists_names"];
					if ($videoData['link'] != '') $mp3['videoLink'] = $videoData['link'];
					if ($videoData['thumbnail'] != '') $mp3['videoImage'] = $videoData['thumbnail'];
					$mp3['title'] = $dataJsonLink['data']['title'];
					$mp3['duration'] = $mp3Data['duration'];
					$mp3['play'] = $linkMp3;//$mp3Data['total_play'];
					$mp3['lyricFile'] =  $mp3Data['lyrics_file'];
					$mp3['download128'] = $mp3['play'];// $mp3Data['source']['128']; "/video-2.php";
					$mp3['download320'] =  $mp3Data['source']['320'];
					$mp3['downloadLl'] = $mp3Data['source']['lossless'];
					$mMp3Content = goCurl('http://m.mp3.zing.vn/bai-hat/a/'.$mp3EncodeId.'.html');
					$mp3['lyricText'] = getStr($mMp3Content,'<p id="conLyrics" class="row-5">','</p>');
					$mp3['embed'] = '<iframe width="600" height="168" src="http://mp3.zing.vn/embed/song/'.$mp3EncodeId.'" frameborder="0" allowfullscreen="true"></iframe>';
					}
				else
				{
					$mp3['error'] = 3;
				}
			break;
	
			case '2': // NCT
				switch ($method) {
					case '1':
					
						$url = 'http://www.nhaccuatui.com/tim-nang-cao?title='.rawurlencode($title);
			
						if ($artist != '') {
							$url .= '&singer='.rawurlencode($artist);
						}
						
						$mp3SearchContent = goCurl($url);
						$mp3Id = getStr($mp3SearchContent,'<a href="javascript:;" id="btnShowBoxPlaylist_','"');
						
					break;
					case '2':
					
						$mp3Id = explode('/',$link);
						$mp3Id = getStr($mp3Id['4'],'.','.');
						
					break;
					default:
						$mp3['error'] = 4;
					break;
				}
				
				if ($mp3Id != '')
				{
					$mp3Content = goCurl('http://www.nhaccuatui.com/bai-hat/a.'.$mp3Id.'.html');
					$mp3XmlId = getStr($mp3Content,'http://www.nhaccuatui.com/flash/xml?html5=true&key1=','";');
					$mp3XmlContent = goCurl('http://www.nhaccuatui.com/flash/xml?html5=true&key1='.$mp3XmlId);
	
					////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
					$mp3['link'] = getStr(getStr($mp3XmlContent,'<info>','</info>'),'<![CDATA[',']]>');
					$mp3['title'] = getStr(getStr($mp3XmlContent,'<title>','</title>'),'<![CDATA[',']]>');
					$mp3['artist'] = getStr(getStr($mp3XmlContent,'<creator>','</creator>'),'<![CDATA[',']]>');
					$mp3['artistLink'] = getStr(getStr($mp3XmlContent,'<newtab>','</newtab>'),'<![CDATA[',']]>');
					$mp3['artistImage'] = getStr(getStr($mp3XmlContent,'<avatar>','</avatar>'),'<![CDATA[',']]>');
					$mp3['lyricFile'] = getStr(getStr($mp3XmlContent,'<lyric>','</lyric>'),'<![CDATA[',']]>');
					$mp3['lyricText'] = getStr($mp3Content,'<p id="divLyric" class="pd_lyric trans" style="height:auto;max-height:255px;overflow:hidden;">','</p>');
					if (strpos($mp3['lyricText'],'Hiện chưa có lời bài hát nào cho') != false) { $mp3['lyricText'] = ''; }
					$mp3['embed'] = '<iframe src="http://www.nhaccuatui.com/mh/auto/'.$mp3Id.'" width="316" height="382" frameborder="0" allowfullscreen></iframe>';
					$mp3['download128'] = getStr(getStr($mp3XmlContent,'<location>','</location>'),'<![CDATA[',']]>');
					
					////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
					$mp3HqJsonContent = goCurl('http://www.nhaccuatui.com/download/song/'.$mp3Id);
					$mp3['download320'] = str_replace('\/','/',getStr($mp3HqJsonContent,'"stream_url":"','"'));
					$mp3['downloadLl'] = '';
					
				}
				else
				{
					$mp3['error'] = 3;
				}
			break;
			default:
				$mp3['error']=2;
			break;
		}
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	die (json_encode($mp3));
?>
