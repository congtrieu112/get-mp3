/**
 *
 * @miniProject: Get Info Zing Mp3 & NCT
 * @author Del Huỳnh - fb.com/huynhducuduy
 *
 */

$(document).ready(function() {
	var player = document.getElementById('playerSource'),
		playVideo = document.getElementById('playerSourceVideo'),
		submitItem = document.getElementById("submit"),
	playPause = document.getElementById('playPause'),
	currentTime = document.getElementById('currentTime'),
	seek = document.getElementById('seek'),
	durationTime = document.getElementById('durationTime'),
	muted = document.getElementById('muted'),
	timeInterval,
	i;

	window.onload = function() {
		playPause.addEventListener('click', playPauseMusic, false);
		playPause.addEventListener('click', playPauseVideo, false);
		muted.addEventListener('click', mutedMusic, false);
		muted.addEventListener('click', mutedVideo, false);
		player.addEventListener('ended', endedMusic, false);
		playVideo.addEventListener('ended', endedVideo, false);
		playVideo.ontimeupdate = getCurrentTime;
		playVideo.onvolumechange = getVolumChange;
		playVideo.addEventListener("change", function () {
			console.log("change");
			playVideo.play();
		})
		
		submitItem.addEventListener('build', function (e) {
			// e.target matches elem
			alert(23432);
		}, false);


	};
	function getVolumChange() {
		console.log("playVideo.volume", playVideo.volume)
	}
	function getCurrentTime() {
		// console.log(playVideo.currentTime);
		if (playVideo.currentTime > 0) {
			// playVideo.volume = 1;
			// playVideo.pause();
		}
	}

	function playPauseMusic() {
		if (player.paused) {
			player.play();
			timeInterval = setInterval(timeUpdateMusic, 100);
			seek.addEventListener('change', seekMusic, false);
			playPause.classList.remove('icon-play');
			playPause.classList.add('icon-pause');
		} else {
			player.pause();
			clearInterval(timeInterval);
			playPause.classList.remove('icon-pause');
			playPause.classList.add('icon-play');
		}
	}

	function fetchVideoAndPlay(url,video) {
		fetch(url)
			.then(response => {
				console.log(response)
				response.blob()
			})
			.then(blob => {
				video.srcObject = blob;
				return video.play();
			})
			.then(_ => {
				// Video playback started ;)
			})
			.catch(e => {
				// Video playback failed ;(
			})
	}

	function playPauseVideo() {
		if (playVideo.paused) {
			
			fetchVideoAndPlay($("#playerSourceVideo").attr("src"), playVideo);
			// console.log($("#playerSourceVideo").get(0).currentSrc, $("#playerSourceVideo").attr("src"));
			
			// var isPlaying = playVideo.currentTime > 0 && !playVideo.paused && !playVideo.ended
			// 	&& playVideo.readyState > 2;

			// if (!isPlaying) {
			// 	playVideo.play();
			// }
			// playVideo.muted = true;
			

			// playVideo.muted = true;
			// playVideo.volume = 0;
			// playVideo.pause();
			// playVideo.dispatchEvent(new CustomEvent("change"))
			// setTimeout(function () {
				

			// 	playVideo.play();
			// },1000)
			// document.getElementById("playerSourceVideo").play()
			// if (playPromise !== null) {
			// 	playPromise.catch(() => { playVideo.play(); document.getElementById("playerSourceVideo").play() })
			// }
			// var promise = document.querySelector('video').play();

			// if (promise !== undefined) {
			// 	promise.then(_ => {
			// 		// Autoplay started!
			// 		console.log("ok")
			// 	}).catch(error => {
			// 		// Autoplay was prevented.
			// 		// Show a "Play" button so that user can start playback.
			// 		console.log("error", error)
					
			// 	});
			// }

			timeInterval = setInterval(timeUpdateVideo, 100);
			seek.addEventListener('change', seekVideo, false);
			playPause.classList.remove('icon-play');
			playPause.classList.add('icon-pause');
		} else {
			playVideo.pause();
			clearInterval(timeInterval);
			playPause.classList.remove('icon-pause');
			playPause.classList.add('icon-play');
		}
	}

	function seekMusic() {
		player.currentTime = seek.value;
	}

	function seekVideo() {
		playVideo.currentTime = seek.value;
	}

	function mutedMusic() {
		if (player.muted) {
			player.muted = false;
			muted.classList.remove('icon-volume-mute');
			muted.classList.add('icon-volume-high');
		} else {
			player.muted = true;
			muted.classList.remove('icon-volume-high');
			muted.classList.add('icon-volume-mute');
		}
	}

	function mutedVideo() {
		if (playVideo.muted) {
			playVideo.muted = false;
			muted.classList.remove('icon-volume-mute');
			muted.classList.add('icon-volume-high');
		} else {
			playVideo.muted = true;
			muted.classList.remove('icon-volume-high');
			muted.classList.add('icon-volume-mute');
		}
	}

	function timeUpdateMusic() {
		durationTime.innerHTML = secondToMinutes(player.duration);
		currentTime.innerHTML = secondToMinutes(player.currentTime);
		seek.max = player.duration;
		seek.value = player.currentTime;
	}
	function timeUpdateVideo() {
		durationTime.innerHTML = secondToMinutes(playVideo.duration);
		currentTime.innerHTML = secondToMinutes(playVideo.currentTime);
		seek.max = playVideo.duration;
		seek.value = playVideo.currentTime;
	}

	function secondToMinutes(seconds) {
		var numMinutes = Math.floor((((seconds % 31536000) % 86400) % 3600) / 60),
		numSeconds = (((seconds % 3153600) % 86400) % 3600) % 60;

		numMinutes = numMinutes >= 10 ? numMinutes : ('0' + numMinutes);

		if (numSeconds >= 10) {
			return numMinutes + ':' + Math.round(numSeconds);
		} else {
			return numMinutes + ':0' + Math.round(numSeconds);
		}
	}

	function endedMusic() {
		player.pause();
		player.currentTime = 0;
		playPause.classList.remove('icon-pause');
		playPause.classList.add('icon-play');
	}

	function endedVideo() {
		playVideo.pause();
		playVideo.currentTime = 0;
		playPause.classList.remove('icon-pause');
		playPause.classList.add('icon-play');
	}
	
	$('#method').change(function(){
		if ($('#method').val() == '1')
		{
			$('#method1').show();
			$('#method2').hide();
		} else if ($('#method').val() == '2')
		{
			$('#method2').show();
			$('#method1').hide();
		}
	});
	console.log(window.location.pathname);
	if (window.location.pathname.indexOf("video") > -1 || window.location.pathname.indexOf("song") > -1) {
		$('#method').val(2).trigger("change");
		$('#type').val(1).trigger("change");
		if (window.location.pathname.indexOf("video") > -1) {
			$('#link').val(window.location.pathname.replace('/', ''));
		} else if (window.location.pathname.indexOf("song") > -1) {
			$('#link').val(window.location.pathname.replace('/', ''));
		}
		$("#submit").trigger("click");
		// var e = new Event('click');
		var event = document.createEvent('Event');
		event.initEvent("build", true, true);
    submitItem.dispatchEvent(event);
		// var submitData = new CustomEvent('printerstatechanged',{"detail":123});
		// submitItem.dispatchEvent(submitData);
	}
});


	
	function getInfo()
	{
		$('#result').fadeIn('fast', function () {
			$('#errorAlert').fadeOut('fast', function () {
				$('#resultContainer').fadeOut('fast', function () {
					$('#loading').fadeIn('fast', function () {
						$.ajax({
							url : 'process.php',
							type : 'get',
							dataType : 'json',
							data : {
								title : $('#title').val(),
								artist: $('#artist').val(),
								type: $('#type').val(),
								method: $('#method').val(),
								link: $('#link').val()
							},
							success : function (result)
							{
								$('#loading').fadeOut('fast',function () {
									if (result['error'] != 0)
									{
										if (result['error'] == 1) {
											$('#errorContent').text('Chưa nhập đủ thông tin');
										} else if (result['error'] == 2) {
											$('#errorContent').text('Nơi tìm kiếm không đúng');
										} else if (result['error'] == 3) {
											$('#errorContent').text('Không tìm thấy bài hát');
										} else if (result['error'] == 4) {
											$('#errorContent').text('Phương thức không đúng');
										}
										$('#errorAlert').show();
									} else
									{
										$('#resultTitle').text(result['title']);
										$('#resultTitleLink').attr('href',result['link']);
										$('#resultArtist').text(result['artist']);
										if (result['artistLink'] != null) { 
											$('#resultArtist').attr('href',result['artistLink']);
										} else {
											$('#resultArtist').removeAttr('href');
										}
										
										if (result['artistLink'] != null) {
											$('#resultArtistLink').attr('href',result['artistLink']);
											if (result['artistImage'] != null) {
												$('#resultArtistImage').attr('src',result['artistImage']);
											} else {
												$('#resultArtistImage').attr('src','noimage.jpg');
											}
											$('#resultArtistImage').show();
										} else {
											$('#resultArtistImage').hide();
										}
										
										if (result['videoLink'] != null) {
											$('#playerSourceVideo').attr('src', result['download128']);
											$('#playerSourceVideo').attr("poster", result['videoImage'])
											$('#resultVideoLink').attr('href',result['videoLink']);
											if (result['videoImage'] != null) {
												$('#resultVideoImage').attr('src',result['videoImage']);
											} else {
												$('#resultVideoImage').attr('src','noimage.jpg');
											}
											$('#resultVideoImage').show();
										} else {
											$('#resultVideoImage').hide();
											$('#playerSource').attr('src', result['download128']);
										}
										
										if (result['albumLink'] != null) {
											$('#resultAlbumLink').attr('href',result['albumLink']);
											if (result['albumImage'] != null) {
												$('#resultAlbumImage').attr('src',result['albumImage']);
											} else {
												$('#resultAlbumImage').attr('src','noimage.jpg');
											}
											$('#resultAlbumImage').show();
										} else {
											$('#resultAlbumImage').hide();
										}
										
										
										// if (result['type'] === 'video') {
										// 	$('#playerSource').replaceWith("<video id=\"playerSource\" src=\"" + result['download128'] + "\" preload=\"auto\" loop type=\"video/mp4\"></video>")
										// } else {
										// 	$('#playerSource').replaceWith("<video id=\"playerSource\" src=\"" + result['download128'] + "\" preload=\"auto\" loop type=\"video/mp4\"></video>")

											
										// }
										
										$('#result128').val(result['download128']);
										$('#a128').attr('href',result['download128']);
										
										if (result['download320'] != '')
										{
											$('#a320').attr('href',result['download320']);
											$('#result320').val(result['download320']);
											$('#form320').show();
										} else {
											$('#form320').hide();
										}
										
										if (result['downloadLl'] != '')
										{
											$('#aLl').attr('href',result['downloadLl']);
											$('#resultLl').val(result['downloadLl']);
											$('#formLl').show();
										} else{
											$('#formLl').hide();
										}
										
										if (result['lyricFile'] != '' && result['lyricFile'] != 'http://lrc.nct.nixcdn.com/null')
										{
											$('#resultLyricFile').val(result['lyricFile']);
											$('#aLyricFile').attr('href',result['lyricFile']);
											$('#formLyricFile').show();
										}
										else
										{
											$('#formLyricFile').hide();
										}

										if (result['lyricText'] != '')
										{
											$('#resultlyricText').html(result['lyricText']);
											$('#lyricTextDiv').show();
										} else{
											$('#lyricTextDiv').hide();
										}

										$('#resultEmbed').val(result['embed']);
										$('#resultContainer').show();
										$('#playPause').click(); // Play music
									}
								});
							}
						});
					});
				});
			});
		});
		return false;
	}
    function copyEmbed() {
        $('#resultEmbed').focus();
        $('#resultEmbed').select();
        alert("Nhấn Ctrl + C để Copy");
    }
