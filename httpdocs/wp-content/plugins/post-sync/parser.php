<?php


class parser {
	
	/**
	 * This var describes if the parser run in restricted mode (only *simple* bbcode is parsed). It's set by the construct function and its default value is false.
	 * @var boolean restrict
	 * @access private
	 */
	private $restrict = false;
	
	/**
	 * This var describes if the parser will parse the smiles. It's set by the construct function and its default value is true.
	 * @var boolean smile
	 * @access private
	 */
	private $smile = true;
	
	/**
	 * This var contains the url of the smile's directory. It's set by the construct function and its default value is 'smile/' (no problem if it not exists).
	 * @var string smile_dir
	 * @access private
	 */
	private $smile_dir = 'smile/';	
	
	/**
	 * This array is the list (name => url) of the smiles contained in $this->smile_dir. It's populate by the construct function (only if the directory of smile exists).
	 * @var array smiles
	 * @access private
	 */
	private $smiles = array();

	/**
	 * This array contains the main static bbcode
	 * @var array restrict_static_bbcode
	 * @access private
	 */
	private $restrict_static_bbcode = array(
											'[b]', '[/b]',
											'[i]', '[/i]',
											'[u]', '[/u]',
											'[s]','[/s]',
											);

	/**
	 * This array contains the main static bbcode's html
	 * @var array restrict_static_html
	 * @access private
	 */
	private $restrict_static_html = array(
											'<b>', '</b>',
											'<i>', '</i>',
											'<u>', '</u>',
											'<s>', '</s>',
						 					);
	
	/**
	 * This array contains the advanced static bbcode
	 * @var array advanced_static_bbcode 
	 * @access private
	 */
	private $advanced_static_bbcode = array(											
											'[center]', '[/center]',
											'[left]', '[/left]',
											'[right]', '[/right]',
											);
	
	/**
	 * This array contains the advanced static bbcode's html
	 * @var array advanced_static_html
	 * @access private
	 */
	private $advanced_static_html = array(
											'<div style="text-align: center;">', '</div>',
											'<div style="text-align: left;">', '</div>',
											'<div style="text-align: right;">', '</div>',
						 					);
	
	/**
	 * This array contains the special symbols, listed as 'name' => 'code_entity_html'
	 * @var array special_symbol
	 * @access private
	 */
	private $special_symbol = array(
									'bull' => '&bull;',
									'copy' => '&copy;',
									'reg' => '&reg;',
									'tm' => '&trade;',									
									);
	
	/**
	 * This function builds the parser. 
	 * 
	 * This function builds the parser and accepts two parameters. The 
	 * first sets the class parameter $this->restrict (if true only some
	 * bbcode are considerated by the parser). The second sets the class 
	 * parameter $this->smile (if true, the smiles are parsed).
	 * 
	 * @param boolean restrict Restrict mode?
	 * @param boolean smile Parse the smile?
	 * @return void
	 */
	function __construct( $restrict = false, $smile = true ){

		/** For a non-logical error the statics array must have the same
		 *  size.
		 */ 
		if ( count($this->restrict_static_bbcode) != count($this->restrict_static_html) 
				OR (count($this->restrict_static_bbcode) % 2) != 0
				OR count($this->advanced_static_bbcode) != count($this->advanced_static_html) 
				OR (count($this->advanced_static_bbcode) % 2) != 0 )
			exit("Error array static bbcode/html");
			
		if ($restrict) $this->restrict = true;
		
		/**
		 * If the class parameter is true, the parse scan the $this->smile_dir
		 * and insert every file in the array $this->smiles as 'name' 
		 * (without the last extension) => 'url_of_smile'.
		 */ 
		if (!$smile) $this->smile = false;
		else {
		
			if ( $this->smile_dir != '' && file_exists($this->smile_dir) ){
				
				if ($handle = opendir( $this->smile_dir )) {
					
    				while (false !== ($file = readdir($handle))) 
        				if ($file != "." && $file != ".."){
							
							$exts = split("[/\\.]", $file); 
							$n = count($exts)-1;
							$ext = $exts[$n];
							$position = strpos($file, ".".$extension);
							$name = substr($file, 0, $position);  					
        					$this->smiles[ $name ] = $this->smile_dir.$file;
						
						}
				
					//print_r($this->smiles);
				
				} else $this->smile = false;
				
			} else $this->smile = false;
			
		}
		
	}
	
	/**
	 * This function parses BBcode tag to HTML code (XHTML transitional 1.0)
	 * 
	 * It parses (only if it is in valid format e.g. an email must to be 
	 * as example@example.ext or similar) the text with BBcode and 
	 * translates in the relative html code. It converts every <, >, ',
	 * ", & in html entity.
	 * 
	 * @param string $text
	 * @return string
	 */
	function bb_to_html($text){
		
		$text = htmlspecialchars($text, ENT_QUOTES);
		
		/**
		 * Parse the static restrict bbcode.
		 */ 
		for ($i = 0; $i < count($this->restrict_static_bbcode); $i += 2){
		
			$pattern = '#'.preg_quote($this->restrict_static_bbcode[$i], '#').'(.+)'.preg_quote($this->restrict_static_bbcode[$i+1], '#').'#Usi';
			$replace = $this->restrict_static_html[$i].'$1'.$this->restrict_static_html[$i+1];
			$text = preg_replace($pattern, $replace, $text);
			
		}
		
		/**
		 * Parse the advande static and non-static (array pattern) bbcode.
		 */ 
		if ( !$this->restrict ){
		
			for ($i = 0; $i < count($this->advanced_static_bbcode); $i += 2){
			
				$pattern = '#'.preg_quote($this->advanced_static_bbcode[$i], '#').'(.+)'.preg_quote($this->advanced_static_bbcode[$i+1], '#').'#Usi';
				$replace = $this->advanced_static_html[$i].'$1'.$this->advanced_static_html[$i+1];
				$text = preg_replace($pattern, $replace, $text);
			
			}
			
			$text = preg_replace("#\[url\](?!http://)(.+)\[\/url\]#si", "[url=http://$1]$1[/url]", $text);
			
			$pattern = array('#\[color=([a-zA-Z]*|\#?[0-9a-fA-F]{6})](.+)\[/color\]#Usi',
							 '#\[size=([0-9][0-9]?)](.+)\[/size\]#Usi',
							 '#\[quote](\r\n)?(.+?)\[/quote]#si',
							 '#\[quote=(.*?)](\r\n)?(.+?)\[/quote]#si',
							 '#\[email]([\w\.\-]+@[a-zA-Z0-9\-]+\.?[a-zA-Z0-9\-]*\.\w{1,4})\[/email]#Usi',
							 '#\[url](.+)\[/url]#Usi',
							 '#\[email=([\w\.\-]+@[a-zA-Z0-9\-]+\.?[a-zA-Z0-9\-]*\.\w{1,4})](.+)\[/email]#Usi',
							 '#\[url=(.+)](.+)\[/url\]#Usi',
							 '#\[urloff](.+)\[/urloff]#Usi',
							 '#\[urloff=(.+)\](.+)\[/urloff\]#Usi',
							 '#\[img](.+)\[/img]#Usi',
							 '#\[img=(.+)](.+)\[/img]#Usi',
							 '#\[code](\r\n)?(.+?)(\r\n)?\[/code]#si',
							 '#\[youtube]http://[a-z]{0,3}.youtube.com/watch\?v=([0-9a-zA-Z]{1,11})\[/youtube]#Usi',
							 '#\[youtube]([0-9a-zA-Z]{1,11})\[/youtube]#Usi',
							 '#\[dmmedium]([0-9a-zA-Z]{1,20})\[/dmmedium]#Usi',
							 '#\[dmsmall]([0-9a-zA-Z]{1,20})\[/dmsmall]#Usi',
							 '#\[vimeo]http://www.vimeo.com/([0-9]{1,10})\[/vimeo]#Usi',
							 '#\[vimeo]([0-9]{1,10})\[/vimeo]#Usi',
							 );

			$replace = array('<span style="color: $1">$2</span>',
							 '<span style="font-size: $1px">$2</span>',
							 "<div class=\"quote\"><span class=\"quoteby\">Disse:</span>\r\n$2</div>",
							 "<div class=\"quote\"><span class=\"quoteby\">Disse <b>$1</b>:</span>\r\n$3</div>",
							 '<a href="mailto: $1">$1</a>',
							 '<a href="$1">$1</a>',
							 '<a href="mailto: $1">$2</a>',
							 '<a href="$1">$2</a>',
							 '<a href="$1" target="_blank">$1</a>',
							 '<a href="$1" target="_blank">$2</a>',
							 '<img src="$1" alt="$1" />',
							 '<img src="$1" alt="$2" />',
							 '<div class="code">$2</div>',
							 '<object type="application/x-shockwave-flash" style="width: 450px; height: 366px;" data="http://www.youtube.com/v/$1"><param name="movie" value="http://www.youtube.com/v/$1" /><param name="wmode" value="transparent" /></object>',
							 '<object type="application/x-shockwave-flash" style="width: 450px; height: 366px;" data="http://www.youtube.com/v/$1"><param name="movie" value="http://www.youtube.com/v/$1" /><param name="wmode" value="transparent" /></object>',
							 '<object type="application/x-shockwave-flash" style="width: 420px; height: 365px;" data="http://dailymotion.alice.it/swf/$1"><param name="movie" value="http://dailymotion.alice.it/swf/$1" /><param name="allowScriptAccess" value="always" /></object>',
							 '<object type="application/x-shockwave-flash" style="width: 220px; height: 185px;" data="http://dailymotion.alice.it/swf/$1"><param name="movie" value="http://dailymotion.alice.it/swf/$1" /><param name="allowScriptAccess" value="always" /></object>',
							 '<object type="application/x-shockwave-flash" style="width: 400px; height: 327px;" data="http://vimeo.com/moogaloop.swf?clip_id=$1"><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=$1" /><param name="allowScriptAccess" value="always" /></object>',
							 '<object type="application/x-shockwave-flash" style="width: 400px; height: 327px;" data="http://vimeo.com/moogaloop.swf?clip_id=$1"><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=$1" /><param name="allowScriptAccess" value="always" /></object>',
							 );
			
			/**
			 * Recursive parse...
			 */ 
			for ($i = 0; $i < count($pattern); $i++){
			
				while ( preg_match( $pattern[$i], $text ) > 0 )	
					$text = preg_replace($pattern, $replace, $text);
					
			}	
		
		}
		
		/**
		 * Parse the smiles
		 */
		if ( $this->smile ){
		
			foreach($this->smiles as $name => $url) 
				$text = str_ireplace(':'.$name, '<img src="'.$url.'" alt="'.$name.'" />', $text);
			
		}
		
		/**
		 * Parse symbols
		 */
		foreach($this->special_symbol as $name => $html_code) 
			$text = str_ireplace(':'.$name, $html_code, $text);
		
		/**
		 * Transform newline in <br /> (html newline)
		 */ 
		$text = nl2br($text);
		
		return $text;
	
	}

	/**
	 * This function parses HTML code to bbcode tag
	 * 
	 * It parses (also if the argument is not in the rigth format e.g. 
	 * <b></b> is transforms in [b][/b]) the text in html code and 
	 * translates in the relative BBCode. It not decodes &amp;, 
	 * &quot;, &#039;, &lt;, &gt; (uncomments the first instruction of
	 * the function for this). This function is thougths to re-convert
	 * a parsed (by bb_to_html()) text.
	 * 
	 * @param string $text
	 * @return string
	 */
	function html_to_bb($text){
		
		/**
		 * Un-comment (remove //) this to allow the decode of &amp;, 
	 	 * &quot;, &#039;, &lt;, &gt;
		 */ 
		//$text = htmlspecialchars_decode($text, ENT_QUOTES);
		
		/**
		 * Parse static restric html code.
		 */ 
		for ($i = 0; $i < count($this->restrict_static_bbcode); $i += 2){
		
			$pattern = '#'.preg_quote($this->restrict_static_html[$i], '#').'(.*)'.preg_quote($this->restrict_static_html[$i+1], '#').'#Usi';
			$replace = $this->restrict_static_bbcode[$i].'$1'.$this->restrict_static_bbcode[$i+1];
			$text = preg_replace($pattern, $replace, $text);
			
		}
		
		/**
		 * Parse the smiles.
		 */ 
		if ( $this->smile ){
		
			foreach($this->smiles as $name => $url) 
				$text = str_ireplace('<img src="'.$url.'" alt="'.$name.'" />', ':'.$name, $text);
			
		}
		
		/**
		 * Parse the advanced static and non-static (array pattern in 
		 * the function) html code.
		 */ 
		if ( !$this->restrict ){
		
			for ($i = 0; $i < count($this->advanced_static_bbcode); $i += 2){
			
				$pattern = '#'.preg_quote($this->advanced_static_html[$i], '#').'(.*)'.preg_quote($this->advanced_static_html[$i+1], '#').'#Usi';
				$replace = $this->advanced_static_bbcode[$i].'$1'.$this->advanced_static_bbcode[$i+1];
				$text = preg_replace($pattern, $replace, $text);
				
			}
				
			$pattern = array('#<span style="color: (.*)">(.*)</span>#Usi',
							 '#<span style="font-size: (.*)px">(.*)</span>#Usi',
							 '#<div class="quote"><span class="quoteby">Disse:</span><br />(.*)</div>#Usi',
							 '#<div class="quote"><span class="quoteby">Disse \[b](.*)\[/b]:</span><br />(.*)</div>#Usi',
							 '#<a href="mailto: (.*)">(.*)</a>#Usi',
							 '#<a href="(.*)" target="_blank">(.*)</a>#Usi',
							 '#<a href="(.*)">(.*)</a>#Usi',
							 '#<img src="(.*)" alt="(.*)" />#Usi',
							 '#<img (.*) src="(.*)" (.*) />#Usi',
							 '#<div class="code">(.*)</div>#Usi',
							 '#<object type="application/x-shockwave-flash" style="width: 450px; height: 366px;" data="http://www.youtube.com/v/(.*)"><param name="movie" value="http://www.youtube.com/v/(.*)" /><param name="wmode" value="transparent" /></object>#Usi',
							 '#<object type="application/x-shockwave-flash" style="width: 420px; height: 365px;" data="http://dailymotion.alice.it/swf/(.*)"><param name="movie" value="http://dailymotion.alice.it/swf/(.*)" /><param name="allowScriptAccess" value="always" /></object>#Usi',
							 '#<object type="application/x-shockwave-flash" style="width: 220px; height: 185px;" data="http://dailymotion.alice.it/swf/(.*)"><param name="movie" value="http://dailymotion.alice.it/swf/(.*)" /><param name="allowScriptAccess" value="always" /></object>#Usi',
							 '#<object type="application/x-shockwave-flash" style="width: 400px; height: 327px;" data="http://vimeo.com/moogaloop.swf\?clip_id=(.*)"><param name="movie" value="http://vimeo.com/moogaloop.swf\?clip_id=(.*)" /><param name="allowScriptAccess" value="always" /></object>#Usi',
							 );

			$replace = array('[color=$1]$2[/color]',
							 '[size=$1]$2[/size]',
							 '[quote]$1[/quote]',
							 '[quote=$1]$2[/quote]',
							 '[email=$1]$2[/email]',
							 '[urloff=$1]$2[/urloff]',
							 '[url=$1:5m0wto18]$2[/url:5m0wto18]',
							 '[img=$1]$2[/img]',
							 '[img:5m0wto18]$2[/img:5m0wto18]',
							 "[code]\r\n$1[/code]",
							 '[youtube]$1[/youtube]',
							 '[dmmedium]$1[/dmmedium]',
							 '[dmsmall]$1[/dmsmall]',
							 '[vimeo]$1[/vimeo]',
							 );

			/**
			 * Recursive parsing...
			 */ 
			for ($i = 0; $i < count($pattern); $i++){
			
				while ( preg_match( $pattern[$i], $text ) > 0 )	
					$text = preg_replace($pattern, $replace, $text);
					
			}	
		
		}
		
		/**
		 * Parse symbols
		 */ 
		foreach($this->special_symbol as $name => $html_code) 
			$text = str_ireplace($html_code, ':'.$name, $text);
		
		/**
		 * Remove <br />
		 */ 
		$text = str_replace('<br />','',$text);
		
		return $text;
	
	}
	

}

?>
