<h1>Sample plugin for CityIndex</h1>

<p>
	When building plugins in wordpress I try to stick to MVC architecture and code to phpDoc standard as much as possible. This means the seperation of
	front end code from backend and I use wordpress's wpdb class as the modal. It also allows programmers who are not familiar with the wordpress core
	to easily navigate the plugin codebase and for the easy creation of documentation.
</p>

<p>
	I try sticking to strict namespacing conventions. For this plugin the main namespace is:<br/>
	<u>EmailOnlineUsers</u><br/>
	Therefore the two modules for 'dashboard' and 'widget' are:<br/>
	<u>EmailOnlineUsersDashboard</u> and <u>EmailOnlineUsersWidget</u> respectfully.
</p>

<p>
	The Directory structure is as follows:
<ul>
	<li>
		<u>/index.php</u><br/>
		This file acts as the 'router' in the MVC architecture.
	</li>
	<li>
		<u>/application</u><br/>
		Contains the main controller for the project. This class will have any global methods defined, it is always constructed before any modules and after any 3rd party files are included. An example of this for this
		plugin would be:<br/>
		EmailOnlineUsers::send_message()
	</li>
	<li>
		<u>/application/includes</u><br/>
		Contains any 3rd party files
	</li>
	<li>
		<u>/application/modules</u><br/>
		Contains the class files for any modules. The two modules in this plugin are the dashboard admin and the front end widget
	</li>
	<li>
		<u>/public_html</u><br/>
		Contains the view files for the main controller and modules. Any code required to be executed here are defined by "shortcodes"
		these are in the format &lt;!--[--shortcode name--]-->. In the relevant modules class (defined in /application or /application/modules)
		the values are defined in the array $class->shortcodes['shortcode name'].
	</li>
	<li>
		<u>/public_html/js</u><br/>
		Contains any javascripts for the modules
	</li>
	<li>
		<u>/public_html/css</u><br/>
		Contains any css for the modules
	</li>
</ul>
</p>