
//jQuery doc ready
//See http://digwp.com/2011/09/using-instead-of-jquery-in-wordpress/
jQuery(document).ready(function($) {
    //Set pin-it button click logic
    $(".pin-it-btn").click(function(event) {
        event.preventDefault();
        //Use jQuery function to retrieve and execute JavaScript from Pinterest
        $.getScript("http://assets.pinterest.com/js/pinmarklet.js?r=" + Math.random()*99999999);
    });
});

/* Old JavaScript from Pinterest
function exec_pinmarklet() {
    var e=document.createElement('script');
    e.setAttribute('type','text/javascript');
    e.setAttribute('charset','UTF-8');
    e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r=' + Math.random()*99999999);
    document.body.appendChild(e);
}
*/
