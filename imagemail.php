<?php

function textToImage($text, $font_name, $font_size, $text_color, $bg_color, $transparent=false)
{
	// initializing varibles
	$padding = 2;
	$font_dir = "fonts/";
	$font = $font_dir . $font_name;
	$text_color_r = 0;
	$text_color_g = 0;
	$text_color_b = 0;
	$bg_color_r = 255;
	$bg_color_g = 255;
	$bg_color_b = 255;
	
	// tranforming hexadecimal colors into RGB
	list($text_color_r, $text_color_g, $text_color_b) = getHexColors($text_color);
	list($bg_color_r, $bg_color_g, $bg_color_b) = getHexColors($bg_color);
	
	// positioning and sizing
	$box = ImageTTFBbox($font_size,0,$font,$text);
	$box_width = $box[4];
	$box_height = abs($box[3]) + abs($box[5]);
	$text_width = $box_width + ($padding * 2) + 1;
	$text_height = $box_height + ($padding * 2) + 0;
	$text_x = $padding;
	$text_y = ($box_height - abs($box[3])) + $padding;
	
	// creating image and palette for only 2 colors
	$img = ImageCreateTrueColor($text_width, $text_height);
	ImageTrueColorToPalette($img, true, 2);
	
	// getting RGB colors
	$text_color = ImageColorAllocate($img, $text_color_r, $text_color_g, $text_color_b);
	$bg_color = ImageColorAllocate($img, $bg_color_r, $bg_color_g, $bg_color_b);

	// painting background	
	ImageFill($img, 0, 0, $bg_color);
	
	// writing text
	ImageTTFText($img, $font_size, 0, $text_x, $text_y, $text_color, $font, $text);
	
	// applying transparency if wanted
	if ($transparent)
		ImageColorTransparent($img, $bg_color);
	
	return $img;
}

function getHexColors($c) 
{
	$c = preg_replace("/[^a-f0-9]/i", "", $c);
	return array(
		hexdec(substr($c, 0, 2)),
		hexdec(substr($c, 2, 2)),
		hexdec(substr($c, 4, 2))
	);
}

if ($_GET["action"] == "font_preview")
{
	// creating image from text
	$img = textToImage("This is a font preview.", $_GET["font_name"], $_GET["font_size"] > 0 ? $_GET["font_size"] : 10, "#000000", "#FFFFFF", false);
	
	// sending image to browser
	header("content-type: image/png");
	ImagePNG($img);
	
	// destroying image handle
	ImageDestroy($img);
	
	exit();
}

if ($_POST["email"] != null)
{
	// creating image from text
	$img = textToImage($_POST["email"], $_POST["font_name"], $_POST["font_size"], $_POST["text_color"], $_POST["bg_color"], $_POST["transparent"]);

	// creating file name
	$filename = "imagemail_".date("Ymd")."_".time();
		
	// writing image to disk
	ImagePNG($img, "data/$filename.png", 9);
	
	// destroying image handle
	ImageDestroy($img);
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="js/prototype.js"></script>
<script src="js/effects.js"></script>
<script src="js/colors.js"></script>
<link rel="stylesheet" type="text/css" href="css/imagemail.css" />
<title>ImagEmail</title>
<?
if ($_POST["email"] == null)
{
?>
<script language="javascript">
	function testForm()
	{
		if ($("email").value == "" 
			|| $("text_color").value == "" 
			|| $("bg_color").value == "" 
			|| $("font_size").value == "" 
			|| parseInt($("font_size").value) <= 0)
		{
			alert("Please fill in all fields correctly.");
			return false;
		}
		return true;
	}
	
	function fontPreview()
	{
		$("imgFontPreview").src = "imagemail.php?action=font_preview&font_name=" + $("font_name").value + "&font_size=" + $("font_size").value;
	}
	
	function loadColorPickers()
	{
		fontPreview();
		create_color_dropdown();
		$("text_color_div").color_menu = true;
		$("bg_color_div").color_menu = true;
		
		if($("text_color").value)
				$("text_color_div").style.backgroundColor = $("text_color").value;
		if($("bg_color").value)
				$("bg_color_div").style.backgroundColor = $("bg_color").value;
				
		Event.observe("text_color_div", "click", function() {
				$("ttiw_color_dropdown").callback = function(v) {
					$("text_color").value = v; //.substr(1, 6);
					$("text_color_div").style.backgroundColor = v;
				}
				$("ttiw_color_dropdown").open($("text_color_div"));
			});

		Event.observe("bg_color_div", "click", function() {
				$("ttiw_color_dropdown").callback = function(v) {
					$("bg_color").value = v; //.substr(1, 6);
					$("bg_color_div").style.backgroundColor = v;
				}
				$("ttiw_color_dropdown").open($("bg_color_div"));
			});
			
	}
	
	window.onload = loadColorPickers;
</script>
<?
}
?>
</head>
<body bgcolor="#FFFFFF">
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-376711-1";
urchinTracker();
</script>
<h1>ImagEmail</h1>

<div id="google_ad">
<script type="text/javascript"><!--
google_ad_client = "pub-6671721013404297";
google_ad_slot = "0449777844";
google_ad_width = 120;
google_ad_height = 240;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>

<h3>Protect your e-mail from spam by using image instead of plain text</h3>
<h4>by <img align="absmiddle" src="data/imagemail_20100527_1274993186.png" alt="ImagEmail" title="ImagEmail"></h4>
<?
if ($_POST["email"] == null)
{
?>
<form name="formImagEmail" id="formImagEmail" action="imagemail.php" method="post" onsubmit="return testForm();">
	<table>
		<tr>
			<td style="text-align: right;">E-mail:</td>
	
			<td colspan="2"><input type="text" name="email" id="email" value="" size="32" /></td>
		</tr>
		<tr>
			<td style="text-align: right;">Font name:</td>
			<td colspan="2">
				<select name="font_name" id="font_name" onchange="fontPreview();">
					<option value='ARIAL.TTF'>Arial</option>
					<option value='COMIC.TTF'>Comic Sans</option>
					<option value='TAHOMA.TTF'>Tahoma</option>
					<option value='TIMES.TTF'>Times New Roman</option>
					<option value='verdana.TTF'>Verdana</option>
				</select>
				<img src="" id="imgFontPreview" align="absmiddle" />
			</td>
		</tr>
		<tr>
			<td style="text-align: right;">Font size:</td>
			<td colspan="2"><input type="text" name="font_size" id="font_size" onblur="fontPreview();" value="10" size="4" /> pixels</td>
		</tr>
		<tr>
			<td style="text-align: right;">Text color:</td>
			<td style="width:60px">
				<input type="text" name="text_color" id="text_color" value="#000000" size="7" />
			</td>
			<td style="text-align: left; width:400px">
				<div id="text_color_div" style="width: 16px; height: 16px; background-color: #000000; border: 1px solid #000;">&nbsp;</div>
			</td>
		</tr>
		<tr>
			<td style="text-align: right;">Background color:</td>
			<td>
				<input type="text" name="bg_color" id="bg_color" value="#FFFFFF" size="7" />
			</td>
			<td style="text-align: left;">
				<div id="bg_color_div" style="width: 16px; height: 16px; background-color: #FFFFFF; border: 1px solid #000;">&nbsp;</div>
			</td>
		</tr>
		<tr>
			<td style="text-align: right;">&nbsp;</td>
			<td colspan="2"><input id="transparent" type="checkbox" name="transparent" value="1" /><label for="transparent">transparent</label></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">
				<br />
				<input type="submit" name="submit" value="Create ImagEmail" />
			</td>
		</tr>
	</table>
</form>
<?
}
else
{
?>
<table>
	<tr>
		<td style="text-align: right;">Your ImagEmail:</td>
		<td>
			<img src="data/<?=$filename.".png"?>" alt="ImagEmail" title="ImagEmail" />
		</td>
	</tr>
	<tr>
		<td style="text-align: right;">HTML code to use:</td>
		<td>
			<br />
			<textarea cols="60" rows="3"><img src="http://cetorres.com/imagemail/data/<?=$filename.".png"?>" alt="ImagEmail" title="ImagEmail" /></textarea>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><br /><input type="button" value="Back" onclick="location.href='imagemail.php'" /></td>
	</tr>
</table>
<?
}
?>
<div id="footer">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
This is a free service provided by <a href="http://cetorres.com" style="color:#000000">Carlos Eugênio Torres</a>. If you wish to make a donation, please click here <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=carloseugeniotorres%40gmail%2ecom&item_name=Carlos%20Eug%c3%aanio%20Torres&no_shipping=0&no_note=1&tax=0&currency_code=EUR&lc=PT&bn=PP%2dDonationsBF&charset=UTF%2d8"><img src="http://cetorres.com/imagens/paypal.gif" border="0" align="absmiddle" alt="Make payments with PayPal - it's fast, free and secure!" title="Make payments with PayPal - it's fast, free and secure!" /></a>&nbsp;
<a href="http://whos.amung.us/show/q2aa0lt6"><img src="http://whos.amung.us/swidget/q2aa0lt6.gif" title="visitors online" alt="visitors online" width="80" height="15" border="0" align="absmiddle" /></a>
</div>
</body>
</html>