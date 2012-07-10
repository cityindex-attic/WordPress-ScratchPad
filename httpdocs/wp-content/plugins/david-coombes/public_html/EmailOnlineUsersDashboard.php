<h1>Email online user</h1>

<!--[--get messages--]-->

<form method="post" id="emailonline-form">
	
	<input type="hidden" name="_wpnonce" value="<!--[--nonce send message--]-->"/>
	<input type="hidden" name="emailonline_action" value="send_message"/>
	
	<ul>
		<li><select name="email"><!--[--users online select--]--></select></li>
		<li><label for="subject">Subject</label><input type="text" name="subject" id="subject" class="required"/></li>
		<li>
			<textarea name="message" class="required"></textarea>
		</li>
		<li>
			<input type="submit" value="Send Message"/>
		</li>
	</ul>
</form>