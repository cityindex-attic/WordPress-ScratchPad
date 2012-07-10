<?php
/**
 * @package david-coombes
 */
/**
 * Debug function. Prints an array wrapped inside &lt;pre> tags for easy viewing in 
 * html browser. Will print the array variable name as well, this is taken from 
 * debug dump using preg_match. 
 * 
 * @param array $ar the array to print 
 * @link http://www.david-coombes.com 
 * @copyright open 
 */
if(!function_exists("ar_print")){
	function ar_print($ar) {

	//vars  
	$name = "";
	$caller_info = array_shift(debug_backtrace());
	$lines = file($caller_info['file']);
	$line = $lines[$caller_info['line'] - 1];

	//search debug dump for var name  
	if (preg_match('/ar_print\\s*\\(\$(\\w+)/', $line, $matches))
		$name = $matches[1];

	//print to stdout  
	print "\n<pre>\n";
	print "{$name}\t";
	print_r($ar);
	print "\n</pre>\n";
}
}

/**
 * Debug function. Prints debug_print_backtrace() between two pre tags. 
 * 
 * @link http://www.david-coombes.com 
 * @copyright open 
 */
if(!function_exists("debug_print")){
	function debug_print() {

	print "<pre>\n";
	debug_print_backtrace();
	print "</pre>\n";
}
}


if(!function_exists("log_file")){
	/**
	 * Logs a string to a file
	 *
	 * @param string $str 
	 */
	function log_file($str, $path=false, $mode='a'){
		
		($path) ? $file="{$path}/log.txt\n" : $file="log.txt\n";
		
		$fp = fopen($file, $mode);
		fwrite($fp, "$str\n");
		fclose($fp);
	}
}

if(!function_exists("ar_to_string")){
	function ar_to_string($ar,$tab=1){
		
		$str = "";
		$tabls = "";
		
		for($x=0;$x<$tab;$x++)
			$tabs .= "\t";
		
		foreach($ar as $key=>$val){
			if(is_array($ar[$key]))
				$str .= ar_to_string($ar, ($tab+1));
			else $str = "{$tabs}{$key}\t=>\t{$val}\n";
		}
			
		return $str;
	}
}
?>
