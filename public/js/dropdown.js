$(document).ready(function(){
	$("#menu li").hover(
		function(){ $("ul", this).fadeIn("fast"); },
		function() { $("ul", this).fadeOut("fast");}
	);
	if (document.all) {
		$("#menu li").hoverClass ("sfHover");
	}
});

$.fn.hoverClass = function(c) {
	return this.each(function(){
		$(this).hover(
			function() { $(this).addClass(c);  },
			function() { $(this).removeClass(c); }
		);
	});
};
/*
Code Highlighting
Courtesy of Dean Edwards star-light
http://dean.edwards.name/my/behaviors/#star-light.htc
	- with jQuery methods added, of course
*/