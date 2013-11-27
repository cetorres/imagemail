var COLOR_DROPDOWN_CREATED = false;
var TABLE_DROPDOWN_CREATED = false;
var HYPERLINK_DROPDOWN_CREATED = false;

function create_color_dropdown() {
	if(COLOR_DROPDOWN_CREATED) return;
	COLOR_DROPDOWN_CREATED = true;
	var dropdown_colors = Array(
		"#FFFFFF", "#FFCCCC", "#FFCC99", "#FFFF99", "#FFFFCC", "#99FF99", 
		"#99FFFF", "#CCFFFF", "#CCCCFF", "#FFCCFF", "#CCCCCC", "#FF6666", 
		"#FF9966", "#FFFF66", "#FFFF33", "#66FF99", "#33FFFF", "#66FFFF", 
		"#9999FF", "#FF99FF", "#C0C0C0", "#FF0000", "#FF9900", "#FFCC66", 
		"#FFFF00", "#33FF33", "#66CCCC", "#33CCFF", "#6666CC", "#CC66CC", 
		"#999999", "#CC0000", "#FF6600", "#FFCC33", "#FFCC00", "#33CC00", 
		"#00CCCC", "#3366FF", "#6633FF", "#CC33CC", "#666666", "#990000", 
		"#CC6600", "#CC9933", "#999900", "#009900", "#339999", "#3333FF", 
		"#6600CC", "#993399", "#333333", "#660000", "#993300", "#996633", 
		"#666600", "#006600", "#336666", "#000099", "#333399", "#663366", 
		"#000000", "#330000", "#663300", "#663333", "#333300", "#003300", 
		"#003333", "#000066", "#330099", "#330033", "#FFD200");
	var dropdown_color_columns = 9;
	
	var color_dropdown = document.createElement("div");
	color_dropdown.id = "ttiw_color_dropdown";
	color_dropdown.style.position = "absolute";
	color_dropdown.style.display = "none";
	color_dropdown.callback = null;
	
	var div = document.createElement("div")
	div.style.padding = "0";
	div.style.margin = "0";
	div.style.clear = "both";
	for(var i = 0; i<dropdown_colors.length; i++) {
			var a = document.createElement("a");
			a.style.backgroundColor = dropdown_colors[i];
			a.backgroundColorValue = dropdown_colors[i];
			a.href = "javascript:void(0);";
			Event.observe(a, "click", function(e) { Event.element(e).parentNode.parentNode.callback(Event.element(e).backgroundColorValue); return false; } );
			if(i % dropdown_color_columns == 0 && i > 0) {
				color_dropdown.appendChild(div);
				div = document.createElement("div")
				div.style.padding = "0";
				div.style.margin = "0";
				div.style.clear = "both";
			}
			div.appendChild(a);
	}
	if(div.childNodes.length > 0)
		color_dropdown.appendChild(div);
	
	color_dropdown.open = function(elem, bottom) {
		pos = Position.page(elem);
		this.style.left = pos[0] + "px";
		if(bottom)
			this.style.top = (pos[1] - this.getHeight()) + "px";
		else
			this.style.top = (pos[1] + elem.getHeight()) + "px";
		try {
			if(!this.visible())
				Effect.BlindDown(this, {duration: 0.3, direction: "bottom-left", afterFinish: function(o) { o.element.setStyle({opacity: 1.0}) } });
			else
				this.show();
		} catch(e) {
			this.show();
		}
	}
	
	var body = document.getElementsByTagName("body")[0];
	body.appendChild(color_dropdown);
	
	Event.observe(document, "click", function(e) {
		var elem = Event.element(e);
		if(!elem.color_menu) {
			try {
				Effect.Fade("ttiw_color_dropdown", {duration: 0.25, afterFinish: function(o) {o.element.hide()} })
			} catch(e) {
				$("ttiw_color_dropdown").hide()
			}
		}
	});
}