<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();

$uploadcare_js = $_SESSION[ 'raw_js' ];

if ( isset( $uploadcare_js ) && ! empty( $uploadcare_js ) ) {
	echo "jQuery(document).ready(function(){";
	echo "if(UPLOADCARE_PUBLIC_KEY != ''){";
	echo $uploadcare_js;
	echo "}});";
} else {
	echo "error";
}
                 
