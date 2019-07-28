<?php
$path = "https://vnso-zn-6-tf-mcloud-bf-s7-mv-zmp3.zadn.vn/SxwyodjId8g/4fb28b25df63363d6f72/a75950233166d8388177/480/Cho-Anh-Xin-Them-1-Phut.mp4?authen=exp=1564452714~acl=/SxwyodjId8g/*~hmac=ea95f9105997783953856759613e09af";
$file = $path;
$head = array_change_key_case(get_headers($file, TRUE));
$size = $head['content-length'];
header('Content-Type: video/mp4');
header('Accept-Ranges: bytes');
header('Content-Disposition: inline');
header('Content-Length:'.$size);
readfile($file);
exit;