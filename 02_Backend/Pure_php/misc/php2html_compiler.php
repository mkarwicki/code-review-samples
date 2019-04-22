<?php
	/******************/
	/** START TIMER **/
	/****************/
	$time_start = microtime_float();
	$baseURL='xxx';
	/**************************************/
	/** DIRECTIORIES AND FILES SETTINGS **/
	/************************************/
	deleteDirectory('dist');
	/*CREATE DIRECTIORIES*/
	/*COPY ASSETS AND DEPENDENCIES FOR PACKAGE COMPERE PAGES*/
	mkdir('dist');
	mkdir('dist/assets');
	mkdir('dist/assets/images');
	mkdir('dist/assets/css');
	mkdir('dist/assets/js');
	mkdir('dist/assets/bower_components');
	mkdir('dist/assets/bower_components/jquery');
	mkdir('dist/assets/bower_components/jquery/dist');
	mkdir('dist/assets/bower_components/modernizr');
	mkdir('dist/assets/bower_components/foundation');
	mkdir('dist/assets/bower_components/foundation/js');
	mkdir('dist/assets/bower_components/fastclick');
	mkdir('dist/assets/bower_components/fastclick/lib');
	recurse_copy('assets/images','dist/assets/images');
	recurse_copy('assets/css','dist/assets/css');
	recurse_copy('assets/js','dist/assets/js');
	copy('assets/bower_components/jquery/dist/jquery.min.js','dist/assets/bower_components/jquery/dist/jquery.min.js');
	copy('assets/bower_components/modernizr/modernizr.js','dist/assets/bower_components/modernizr/modernizr.js');
	copy('assets/bower_components/foundation/js/foundation.min.js','dist/assets/bower_components/foundation/js/foundation.min.js');
	copy('assets/bower_components/fastclick/lib/fastclick.js','dist/assets/bower_components/fastclick/lib/fastclick.js');
	copy('favicon.ico','dist/favicon.ico');
	copy('pushServiceWorker.js','dist/pushServiceWorker.js');
	/*COPY ASSETS AND DEPENDENCIES FOR PACKAGE OFFER PAGE*/





	/*HTACCESS FOR HTML PAGES*/
	copy('compilation-data/.htaccess','dist/.htaccess');
	/**********************/
	/** PAGES SETTINGS **/
	/*******************/
    $pages=array(
	    1=>array(
		    'title'=>'Strona Główna',
		    'localUrl'=>'',
		    'rewrite'=>'index.html',
	    ),
	    3=>array(
		    'title'=>'Funkcjonalności',
		    'localUrl'=>'funkcjonalnosci',
		    'rewrite'=>'funkcjonalnosci.html',
	    ),
	    4=>array(
		    'title'=>'Pakiet - Start',
		    'localUrl'=>'start',
		    'rewrite'=>'start.html',
	    ),
	    5=>array(
		    'title'=>'Pakiet - Start Extra',
		    'localUrl'=>'start-extra',
		    'rewrite'=>'start-extra.html',
	    ),
	    6=>array(
		    'title'=>'Pakiet - Start',
		    'localUrl'=>'best-tv',
		    'rewrite'=>'best-tv.html',
	    ),
	    7=>array(
		    'title'=>'Pakiet - Start',
		    'localUrl'=>'canal-plus',
		    'rewrite'=>'canal-plus.html',
	    ),
	    8=>array(
		    'title'=>'Pakiet - Max',
		    'localUrl'=>'max',
		    'rewrite'=>'max.html',
	    ),
	    9=>array(
		    'title'=>'Pakiet - Eleven',
		    'localUrl'=>'eleven',
		    'rewrite'=>'eleven.html',
	    ),
	    10=>array(
		    'title'=>'Pakiet - HBO',
		    'localUrl'=>'hbo',
		    'rewrite'=>'hbo.html',
	    ),
	    11=>array(
		    'title'=>'Canal+ Seriale Extra',
		    'localUrl'=>'canal-plus-seriale-extra',
		    'rewrite'=>'canal-plus-seriale-extra.html',
	    ),
    );

	foreach($pages as $key=>$page):
		copy($baseURL.$page['localUrl'], 'dist/'.$page['rewrite']);
	endforeach;







	/**************************************/
	/** CLEAN SESSION DATA AND LOG TIME **/
	/************************************/
	$time_end = microtime_float();
	$time = $time_end - $time_start;
	echo "Compiled in $time seconds\n";




	function deleteDirectory($dir) {
		if (!file_exists($dir)) {
			return true;
		}
		if (!is_dir($dir)) {
			return unlink($dir);
		}
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}
			if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
				return false;
			}
		}
		return rmdir($dir);
	}


	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}



	function recurse_copy($src,$dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
