<?php
// Function To Display BBCodes And Smilies
function textbbcode($form, $name, $content = "")
{
    //$form = form name
    //$name = textarea name
    //$content = textarea content (only for edit pages etc)
    ?>
<script type="text/javascript">
function BBTag(tag,s,text,form){
switch(tag)
    {
    case '[quote]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[quote]" + body.substring(start, end) + "[/quote]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[quote][/quote]";
	}
        break;
    case '[img]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[img]" + body.substring(start, end) + "[/img]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[img][/img]";
	}
        break;
    case '[url]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[url]" + body.substring(start, end) + "[/url]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[url][/url]";
	}
        break;
    case '[*]':
        document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[*]";
        break;
    case '[b]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[b]" + body.substring(start, end) + "[/b]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[b][/b]";
	}
        break;
    case '[i]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[i]" + body.substring(start, end) + "[/i]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[i][/i]";
	}
        break;
    case '[hide]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substring(0, start);
		var middle = "[hide]" + body.substring(start, end) + "[/hide]";
		var rightpos = start + body.substring(start, end).length;
		var right = body.substring(rightpos, end);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[hide][/hide]";
	}
        break;
		case '[code]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substring(0, start);
		var middle = "[code]" + body.substring(start, end) + "[/code]";
		var rightpos = start + body.substring(start, end).length;
		var right = body.substring(rightpos, end);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[code][/code]";
	}
        break;
    case '[u]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[u]" + body.substring(start, end) + "[/u]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[u][/u]";
	}
        break;
    }
    document.forms[form].elements[text].focus();
}
</script>

<div class="container">

<div class="row justify-content-md-center">
        <div class="col-md-6">
	<input type="button" class="btn btn-primary btn-sm" name="code" value="CODE " onclick="javascript: BBTag('[code]','code','<?php echo $name; ?>','<?php echo $form; ?>')" />
    <input type="button" class="btn btn-primary btn-sm" name="bold" value="B " onclick="javascript: BBTag('[b]','bold','<?php echo $name; ?>','<?php echo $form; ?>')" />
    <input type="button" class="btn btn-primary btn-sm" name="italic" value="I " onclick="javascript: BBTag('[i]','italic','<?php echo $name; ?>','<?php echo $form; ?>')" />
    <input type="button" class="btn btn-primary btn-sm" name="underline" value="U " onclick="javascript: BBTag('[u]','underline','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
    <input type="button" class="btn btn-primary btn-sm" name="li" value="List " onclick="javascript: BBTag('[*]','li','<?php echo $name; ?>','<?php echo $form; ?>')" />
    <input type="button" class="btn btn-primary btn-sm" name="quote" value="QUOTE " onclick="javascript: BBTag('[quote]','quote','<?php echo $name; ?>','<?php echo $form; ?>')" />
    <input type="button" class="btn btn-primary btn-sm" name="url" value="URL " onclick="javascript: BBTag('[url]','url','<?php echo $name; ?>','<?php echo $form; ?>')" />
    <input type="button" class="btn btn-primary btn-sm" name="img" value="IMG " onclick="javascript: BBTag('[img]','img','<?php echo $name; ?>','<?php echo $form; ?>')" />
	<input type="button" class="btn btn-primary btn-sm" name="hide" value="HIDE " onclick="javascript: BBTag('[hide]','hide','<?php echo $name; ?>','<?php echo $form; ?>')" />
	<a  onclick="myFunction()"><img src='<?php echo URLROOT; ?>/assets/images/smilies/grin.png' alt='' /></a>
        </div>
</div>

    <div class="row justify-content-md-center">
        <div class="col-md-10">
<div id="myDIVsmileytog" style="display:none">

	<a href="javascript:SmileIT(':)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/smile.png" border="0" alt=':)' title=':)' /></a>
    <a href="javascript:SmileIT(':(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sad.png" border="0" alt=':(' title=':(' /></a>
    <a href="javascript:SmileIT(':D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/grin.png" border="0" alt=':D' title=':D' /></a>
    <a href="javascript:SmileIT(':P','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/razz.png" border="0" alt=':P' title=':P' /></a>
    <a href="javascript:SmileIT(':-)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/smile-big.png" border="0" alt=':-)' title=':-)' /></a>
    <a href="javascript:SmileIT('B)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/cool.png" border="0" alt='B)' title='B)' /></a>
    <a href="javascript:SmileIT('8o','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/eek.png" border="0" alt='8o' title='8o' /></a>
    <a href="javascript:SmileIT(':?','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/confused.png" border="0" alt=':?' title=':?' /></a>
    <a href="javascript:SmileIT('8)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/glasses.png" border="0" alt='8)' title='8)' /></a>
    <a href="javascript:SmileIT(';)z','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/wink.png" border="0" alt=';)z' title=';)' /></a>
    <a href="javascript:SmileIT(':-*','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/kiss.png" border="0" alt=':-*' title=':-*' /></a>
    <a href="javascript:SmileIT(':-(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/crying.png" border="0" alt=':-(' title=':-(' /></a>
    <a href="javascript:SmileIT(':|','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/plain.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT('O:-D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/angel.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':-@','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/devilish.png" border="0" alt=':-@' title=':-@' /></a>
    <a href="javascript:SmileIT(':o)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/monkey.png" border="0" alt=':o)' title=':o)' /></a>
    <a href="javascript:SmileIT('brb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT;; ?>/assets/images/smilies/brb.png" border="0" alt='brb' title='brb' /></a>
    <a href="javascript:SmileIT(':warn','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/warn.png" border="0" alt=':warn' title=':warn' /></a>
    <a href="javascript:SmileIT(':help','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/help.png" border="0" alt=':help' title=':help' /></a>
    <a href="javascript:SmileIT(':bad','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/bad.png" border="0" alt=':bad' title=':bad' /></a>
    <a href="javascript:SmileIT(':love','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/love.png" border="0" alt=':love' title=':love' /></a>
    <a href="javascript:SmileIT(':idea','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/idea.png" border="0" alt=':idea' title=':idea' /></a>
    <a href="javascript:SmileIT(':bomb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/bomb.png" border="0" alt=':bomb' title=':bomb' /></a>
    <a href="javascript:SmileIT(':!','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/important.png" border="0" alt=':!' title=':!' /></a>
    <a href="javascript:SmileIT(':gigg','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/giggle.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT(':rofl','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/roflmao.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':slep','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sleep.png" border="0" alt=':-@' title=':-@' /></a>
    <a href="javascript:SmileIT(':thum','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/thumbsup.png" border="0" alt=':o)' title=':o)' /></a>
    <a href="javascript:SmileIT(':0_0','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/zpo.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT(':poop','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/poop.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':spechles','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/speechless.png" border="0" alt=':-@' title=':-@' /></a>
    <a href="javascript:SmileIT(':unsure','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/unsure.png" border="0" alt=':o)' title=':o)' /></a>
    <a href="javascript:SmileIT(':mad','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/mad.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT(':roll','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/rolleyes.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':sick','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sick.png" border="0" alt=':-@' title=':-@' /></a>
    <a href="javascript:SmileIT(':crylol','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/crylaugh.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT(':confos','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/confound.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':fire','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/fire.png" border="0" alt=':-@' title=':-@' /></a>
</div>
		 <textarea class="form-control" name="<?php echo $name; ?>" rows="13"><?php echo $content; ?></textarea>
        </div>
    </div>

</div>
<?php
}

function shoutbbcode($form, $name, $content = "")
{
    //$form = form name
    //$name = textarea name
    //$content = textarea content (only for edit pages etc)
    ?>
<script type="text/javascript">
// Function To Replace BBCode Tags In Text Zones //
// BBCode write function
	function bbcomment(repdeb, repfin){
		var input = document.forms["<?php echo $form ;?>"].elements["<?php echo $name ;?>"];
		input.focus();
		if(typeof document.selection != 'undefined'){
			var range = document.selection.createRange();
			var insText = range.text;
			range.text = repdeb + insText + repfin;
			range = document.selection.createRange();
			if (insText.length == 0){	
				range.move('character', -repfin.length);
				} 
			else{
				range.moveStart('character', repdeb.length + insText.length + repfin.length);
				}
			range.select();
			}
		else if(typeof input.selectionStart != 'undefined'){
			var start = input.selectionStart;
			var end = input.selectionEnd;
			var insText = input.value.substring(start, end);
			input.value = input.value.substr(0, start) + repdeb + insText + repfin + input.value.substr(end);
			var pos;
			if (insText.length == 0){
				pos = start + repdeb.length;
				} 
			else{
				pos = start + repdeb.length + insText.length + repfin.length;
				}
			input.selectionStart = pos;
			input.selectionEnd = pos;
			}
		else{
			var pos;
			var re = new RegExp('^[0-9]{0,3}$');
			while(!re.test(pos)){
				pos = prompt("Insertion Ã  la position (0.." + input.value.length + "):", "0");
				}
			if(pos > input.value.length){
				pos = input.value.length;
				}
			var insText = prompt("Veuillez entrer le texte Ã  formater:");
			input.value = input.value.substr(0, pos) + repdeb + insText + repfin + input.value.substr(pos);
			}
		}
	// Fonction Couleur De Police
	function bbcouleur(couleur){
		bbcomment("[color="+couleur+"]", "[/color]");
		}
	// Fonction Police
	function bbfont(font){
		bbcomment("[font="+font+"]", "[/font]");
		}
	// Fonction Taille De Police
	function bbsize(taille){
		bbcomment("[size="+taille+"]", "[/size]");
		}
	// Fonctions De Remplacement De Caractères
	function deblaie(reg,t){
		texte=new String(t);
		return texte.replace(reg,'$1\n');
		}
	function remblaie(t){
		texte=new String(t);
		return texte.replace(/\n/g,'');
		}
	function remplace_tag(reg,rep,t){
		texte=new String(t);
		return texte.replace(reg,rep);
		}
	function nl2br(t){
		texte=new String(t);
		return texte.replace(/\n/g,'<br/>');
		}
	function nl2khol(t){
		texte=new String(t);
		return texte.replace(/\n/g,ptag);
		}
	function unkhol(t){
		texte=new String(t);
		return texte.replace(new RegExp(ptag,'g'),'\n');
		}	
	var timer=0;
	var ptag=String.fromCharCode(5,6,7);
	// Fonction Preview
	function  visualisation() {
		t=document.forms["<?php echo $form ;?>"].elements["<?php echo $name ;?>"].value  
		t=code_to_html(t)
		if (document.getElementById) document.getElementById("previsualisation").innerHTML=t
		if (document.formu.auto.checked) timer=setTimeout(visualisation,1000)
		}
	// Transform BBCode in HTML
	function code_to_html(t){
		t=nl2khol(t)
		// balise Center
			t=deblaie(/(\[\/center\])/g,t)
			t=remplace_tag(/\[center\](.+)\[\/center\]/g,'<center>$1</center>',t)  
			t=remblaie(t)		
		// balise Gras
			t=deblaie(/(\[\/b\])/g,t)
			t=remplace_tag(/\[b\](.+)\[\/b\]/g,'<strong>$1</strong>',t)  
			t=remblaie(t)
		// balise Italique
			t=deblaie(/(\[\/i\])/g,t)
			t=remplace_tag(/\[i\](.+)\[\/i\]/g,'<em>$1</em>',t)  
			t=remblaie(t)
		// balise SOuligné
			t=deblaie(/(\[\/u\])/g,t)
			t=remplace_tag(/\[u\](.+)\[\/u\]/g,'<u>$1</u>',t)  
			t=remblaie(t)
		// balise Barré
			t=deblaie(/(\[\/s\])/g,t)
			t=remplace_tag(/\[s\](.+)\[\/s\]/g,'<span style="text-decoration:line-through;">$1</span>',t)  
			t=remblaie(t)
		// balise Citation
			t=deblaie(/(\[\/quote\])/g,t)
			t=remplace_tag(/\[quote\](.+)\[\/quote\]/g,'<p class=sub><b> Citation : </b></p><table class=main border=1 cellspacing=0 cellpadding=10><tr><td style="border: 1px black dotted">$1</td></tr></table>',t)  
			t=remblaie(t)
			t=deblaie(/(\[\/quote\])/g,t)
			t=remplace_tag(/\[quote=([a-zA-Z]+)\]((\s|.)+?)\[\/quote\]/g,'<p class=sub><b>$1 a écrit : </b></p><table class=main border=1 cellspacing=0 cellpadding=10><tr><td style="border: 1px black dotted">$2</td></tr></table>',t)  
			t=remblaie(t)
		// Balise Multi Citation Pour Message Privé
			t=deblaie(/(\[\/reponse\])/g,t)
			t=remplace_tag(/\[reponse(.*)\](.+)\[\/reponse\]/g,'<table class=main border=1 cellspacing=0 cellpadding=10><tr><td style="border: 1px black dotted">$2</td></tr></table>',t)  
			t=remblaie(t)
		// balise code	
			t=deblaie(/(\[\/code\])/g,t)
			t=remplace_tag(/\[code\](.+)\[\/code\]/g,'<p class=sub><b>Extrait De Code : </b></p><table class=main border=1 cellspacing=0 cellpadding=10><tr><td style="border: 1px black dotted">$1</td></tr></table>',t)  
			t=remblaie(t)
		// balise blink	
			t=deblaie(/(\[\/blink\])/g,t)
			t=remplace_tag(/\[blink\](.+)\[\/blink\]/g,'<div id="blink">$1</div>',t)  
			t=remblaie(t)
		// balise df	
			t=deblaie(/(\[\/df\])/g,t)
			t=remplace_tag(/\[df\](.+)\[\/df\]/g,'<marquee>$1</marquee>',t)  
			t=remblaie(t)
		// balise [audio]..[/audio]
			t=deblaie(/(\[\/audio\])/g,t)
			t=remplace_tag(/\[audio\]((www.|http:\/\/|https:\/\/)[^\s]+(\.mp3))\[\/audio\]/g,'<param name=movie value=$1/><embed width=470 height=310 src=$1></embed>',t)  
			t=remblaie(t)	
		//****************
		//* Partie Vidéo *
		//****************
		// balise [video]..[/video] pour youtube
			t=deblaie(/(\[\/video\])/g,t)
			t=remplace_tag(/\[video\][^\s'\"<>]*youtube.com.*v=([^\s'\"<>]+)\[\/video\]/img,'<object width="680" height="440"><param name="movie" value="http://www.youtube.com/v/$1"></param><embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" width="680" height="440"></embed></object>',t)  
			t=remblaie(t)
		// balise [video=...] pour youtube
			t=deblaie(/(\[\/video\])/g,t)
			t=remplace_tag(/\[video=[^\s'\"<>]*youtube.com.*v=([^\s'\"<>]+)\]/img,'<object width="680" height="440"><param name="movie" value="http://www.youtube.com/v/$1"></param><embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" width="680" height="440"></embed></object>',t)  
			t=remblaie(t)
		// balise [video]..[/video] pour mp4
			t=deblaie(/(\[\/video\])/g,t)
			t=remplace_tag(/\[video\]((www.|http:\/\/|https:\/\/)[^\s]+(\.mp4))\[\/video\]/g,'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="680" height="440" id="player1" name="player1"><param name="movie" value="$1"><param name="allowfullscreen" value="true"><param name="allowscriptaccess" value="always"><embed  src="$1" name="player1"  width="680"  height="440" allowscriptaccess="always" allowfullscreen="true"></embed></object>',t)  
			t=remblaie(t)
		// balise [video]..[/video] pour wmv
			t=deblaie(/(\[\/video\])/g,t)
			t=remplace_tag(/\[video\]((www.|http:\/\/|https:\/\/)[^\s]+(\.wmv))\[\/video\]/g,'<param name=filename value=$1/><embed width=680 height=440 src=$1></embed>',t)  
			t=remblaie(t)	
		// balise [video]..[/video] pour dailymotion
			t=deblaie(/(\[\/video\])/g,t)
			t=remplace_tag(/\[video\][^\s'\"<>]*dailymotion.com\/video\/([^\s'\"<>]+)\[\/video\]/img,'<object width="680" height="440"><param name="movie" value="http://www.dailymotion.com/swf/$1"></param><embed src="http://www.dailymotion.com/swf/$1" type="application/x-shockwave-flash" width="680" height="440"></embed></object>',t)  
			t=remblaie(t)	
		// balise [video]..[/video] pour google video
			t=deblaie(/(\[\/video\])/g,t)
			t=remplace_tag(/\[video\][^\s'\"<>]*video.google.com.*docid=(-?[0-9]+).*\[\/video\]/img,'<embed style="width:680px; height:440px;" id="VideoPlayback" align="middle" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=$1" allowScriptAccess="sameDomain" quality="best" bgcolor="#ffffff" scale="noScale" wmode="window" salign="TL"  FlashVars="playerMode=embedded"></embed>',t)  
			t=remblaie(t)	
		// balise font	
			t=deblaie(/(\[\/font\])/g,t)
			t=remplace_tag(/\[font=(#[a-fA-F0-9]{6})\](.+)\[\/font\]/g,'<font face="$1">$2</font>',t)
			t=remblaie(t)
			t=deblaie(/(\[\/font\])/g,t)
			t=remplace_tag(/\[font=([a-zA-Z].*?)\]((\s|.)+?)\[\/font\]/g,'<font face="$1">$2</font>',t)
			t=remblaie(t)
		// balise Img
			t=deblaie(/(\[\/img\])/g,t)
			t=remplace_tag(/\[img\](.+)\[\/img\]/g,'<img src="$1" />',t)
			t=remblaie(t)
		// balise URL	
			t=deblaie(/(\[\/url\])/g,t)
			t=remplace_tag(/\[url=([^\s<>]+)\](.+)\[\/url\]/g,'<a href="$1" target="_blank">$2</a>',t)
			t=remblaie(t)
			t=deblaie(/(\[\/url\])/g,t)
			t=remplace_tag(/\[url\]([^\s<>]+)\[\/url\]/g,'<a href="$1" target="_blank">$1</a>',t)
			t=remblaie(t)
			t=remplace_tag(/\[\/url\]/g,'</a>',t)
			t=remblaie(t)
		// balise Couleur	
			t=deblaie(/(\[\/color\])/g,t)
			t=remplace_tag(/\[color=(#[a-fA-F0-9]{6})\](.+)\[\/color\]/g,'<font color="$1">$2</font>',t)
			t=remblaie(t)
			t=deblaie(/(\[\/color\])/g,t)
			t=remplace_tag(/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/g,'<font color="$1">$2</font>',t)
			t=remblaie(t)
		// alignement
			t=deblaie(/(\[\/align\])/g,t)
			t=remplace_tag(/\[align=([a-zA-Z]+)\]((\s|.)+?)\[\/align\]/g,'<div style="text-align:$1">$2</div>',t)
			t=remblaie(t)
		// balise size	
			t=deblaie(/(\[\/size\])/g,t)
			t=remplace_tag(/\[size=([+-]?[0-9])\](.+)\[\/size\]/g,'<font size="$1">$2</font>',t)
			t=remblaie(t)
		// Balise HR
			t=deblaie(/(\[\/hr\])/g,t)
			t=remplace_tag(/\[hr=(#[a-fA-F0-9]{6})\]/g,'<hr color="$1" />',t)
			t=remblaie(t)
			t=deblaie(/(\[\/hr\])/g,t)
			t=remplace_tag(/\[hr=([a-zA-Z]+)\]/g,'<hr color="$1" />',t)
			t=remblaie(t)
			t=deblaie(/(\[\/hr\])/g,t)
			t=remplace_tag(/\[hr\]/g,'<hr />',t)
			t=remblaie(t)
		// smilies
			t=remplace_tag(/:sm10:/g,'<img src="/assets/images/smilies/smile.png" alt="" />',t) 
			t=remplace_tag(/:sm11:/g,'<img src="/assets/images/smilies/sad.png" alt="" />',t) 
			t=remplace_tag(/:sm12:/g,'<img src="/assets/images/smilies/wink.png" alt="" />',t) 
			t=remplace_tag(/:sm13:/g,'<img src="/assets/images/smilies/razz.png" alt="" />',t) 
			t=remplace_tag(/:sm14:/g,'<img src="/assets/images/smilies/grin.png" alt="" />',t) 
			t=remplace_tag(/:sm15:/g,'<img src="/assets/images/smilies/plain.png" alt="" />',t) 
			t=remplace_tag(/:sm16:/g,'<img src="/assets/images/smilies/suprise.png" alt="" />',t) 
			t=remplace_tag(/:sm17:/g,'<img src="/assets/images/smilies/confused.png" alt="" />',t) 
			t=remplace_tag(/:sm18:/g,'<img src="/assets/images/smilies/glasses.png" alt="" />',t)
			t=remplace_tag(/:sm19:/g,'<img src="/assets/images/smilies/eek.png" alt="" />',t) 
			t=remplace_tag(/:sm20:/g,'<img src="/assets/images/smilies/cool.png" alt="" />',t) 
			t=remplace_tag(/:sm21:/g,'<img src="/assets/images/smilies/smile-big.png" alt="" />',t)
			t=remplace_tag(/:sm22:/g,'<img src="/assets/images/smilies/crying.png" alt="" />',t) 
			t=remplace_tag(/:sm23:/g,'<img src="/assets/images/smilies/kiss.png" alt="" />',t) 
			t=remplace_tag(/O:-D/g,'<img src="/assets/images/smilies/angel.png" alt="" />',t) 	
			t=remplace_tag(/:sm25:/g,'<img src="/assets/images/smilies/devilish.png" alt="" />',t) 
			t=remplace_tag(/:sm26:/g,'<img src="/assets/images/smilies/important.png" alt="" />',t) 
			t=remplace_tag(/:sm27:/g,'<img src="/assets/images/smilies/brb.png" alt="" />',t) 
			t=remplace_tag(/:sm28:/g,'<img src="/assets/images/smilies/bomb.png" alt="" />',t) 
			t=remplace_tag(/:sm29:/g,'<img src="/assets/images/smilies/warn.png" alt="" />',t) 	
			t=remplace_tag(/:sm30:/g,'<img src="/assets/images/smilies/idea.png" alt="" />',t)
			t=remplace_tag(/:help/g,'<img src="/assets/images/smilies/help.png" alt="" />',t) 
			t=remplace_tag(/:sm32:/g,'<img src="/assets/images/smilies/love.png" alt="" />',t) 	
			t=remplace_tag(/:sm33:/g,'<img src="/assets/images/smilies/bad.png" alt="" />',t) 	
			t=remplace_tag(/:sm34:/g,'<img src="/assets/images/smilies/monkey.png" alt="" />',t) 	
			t=remblaie(t)	
		t=unkhol(t)
		t=nl2br(t)
		return t
		}
</script>

<?php
print ("<center><input id='BBCode' type='button' name='Bold' 			value='' style=\"background: url('assets/images/bbcodes/dold.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[b]', '[/b]')\" 					alt='Bold' 				title='Bold' 				/>");
	print ("<input id='BBCode' type='button' name='Italique' 			value='' style=\"background: url('assets/images/bbcodes/Italique.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[i]', '[/i]')\" 					alt='Italique' 			title='Italique' 			/>");
	print ("<input id='BBCode' type='button' name='Highlight' 			value='' style=\"background: url('assets/images/bbcodes/Highlight.png');  height:25px; width:25px;\" 				onclick=\"bbcomment('[u]', '[/u]')\" 					alt='Highlight' 			title='Highlight'			/>");
	print ("<input id='BBCode' type='button' name='Barré' 				value='' style=\"background: url('assets/images/bbcodes/Barrer.png');  height:25px; width:25px;\"			 		onclick=\"bbcomment('[s]', '[/s]')\" 					alt='Ligne Barrée' 		title='Ligne Barrée'		/>&nbsp;&nbsp;&nbsp;");
	print ("<input id='BBCode' type='button' name='List' 				value='' style=\"background: url('assets/images/bbcodes/List.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[list]', '[/list]')\" 				alt='List' 				title='List'				/>");
	print ("<input id='BBCode' type='button' name='Quote' 			       value='' style=\"background: url('assets/images/bbcodes/quote.gif');  height:25px; width:25px;\"			 		onclick=\"bbcomment('[quote]', '[/quote]')\" 			alt='Quote' 			title='Quote'			/>");
	print ("<input id='BBCode' type='button' name='Code' 				value='' style=\"background: url('assets/images/bbcodes/code.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[code]', '[/code]')\" 			alt='Code' 			title='Code'				/>");
	print ("<input id='BBCode' type='button' name='Url' 				value='' style=\"background: url('assets/images/bbcodes/Link.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[url]', '[/url]')\"				alt='Lnk' 				title='Link'				/>");
	print ("<input id='BBCode' type='button' name='Image' 				value='' style=\"background: url('assets/images/bbcodes/Image.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[img]', '[/img]')\"				alt='Image' 			title='Image'				/>");
	print ("<input id='BBCode' type='button' name='Video' 				value='' style=\"background: url('assets/images/bbcodes/Video.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[video]', '[/video]')\" 			alt='Vidéo' 			title='Vidéo'				/>");
	print ("<input id='BBCode' type='button' name='Audio' 				value='' style=\"background: url('assets/images/bbcodes/Audio.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[audio]', '[/audio]')\"  			alt='Audio' 			title='Audio'				/>");
	print ("<input id='BBCode' type='button' name='Blink'		               value='' style=\"background: url('assets/images/bbcodes/Blink.png');  height:25px; width:25px;\" 				        onclick=\"bbcomment('[blink]','[/blink]')\" 				alt='Blink' 			title='Blink'		/>");
	print ("<input id='BBCode' type='button' name='scroller' 			value='' style=\"background: url('assets/images/bbcodes/scroller.png');  height:25px; width:25px;\"					onclick=\"bbcomment('[df]', '[/df]')\" 				alt='scroller' 			title='scroller'			/>&nbsp;&nbsp;&nbsp;&nbsp;");
	print ("<input id='BBCode' type='button' name='Align Right'            	value='' style=\"background: url('assets/images/bbcodes/right.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[align=left]','[/align]')\" 			alt='Align Right' 		title='Align Right' 	/>");
	print ("<input id='BBCode' type='button' name='Align Center'        	value='' style=\"background: url('assets/images/bbcodes/center.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[align=center]','[/align]')\" 		alt='Align Center' 		title='Align Center' 	/>");
	print ("<input id='BBCode' type='button' name='Align Left'             	value='' style=\"background: url('assets/images/bbcodes/left.gif');  height:25px; width:25px;\" 					onclick=\"bbcomment('[align=right]','[/align]')\" 		alt='Align Left' 		       title='Align Left' 	/></center>");
	// Choose the colour
	print ("<center><a href=" . URLROOT . "/shoutbox/history  target='_blank'><b>History</b></a>
	&nbsp;&nbsp;<a  onclick='myFunction()'><img src='".URLROOT."/assets/images/smilies/grin.png' alt='' /></a>&nbsp;&nbsp;");
	print ("<select name='color' style='padding-bottom:3px;' onChange='bbcouleur(this.value);' title='Couleur'>");
	print ("<option value='0' name='color'>Colour</option>");	
	print ("<option value='#000000' style='BACKGROUND-COLOR:#000000'>Black</option>");	
	print ("<option value='#686868' style='BACKGROUND-COLOR:#686868'>Grey</option>");	
	print ("<option value='#708090' style='BACKGROUND-COLOR:#708090'>Dark Grey</option>");	
	print ("<option value='#C0C0C0' style='BACKGROUND-COLOR:#C0C0C0'>Light Grey</option>");	
	print ("<option value='#FFFFFF' style='BACKGROUND-COLOR:#FFFFFF'>White</option>");	
	print ("<option value='#FFFFE0' style='BACKGROUND-COLOR:#FFFFE0'>Beech</option>");	
	print ("<option value='#880000' style='BACKGROUND-COLOR:#880000'>Dark Red</option>");	
	print ("<option value='#B82428' style='BACKGROUND-COLOR:#B82428'>Light Red</option>");	
	print ("<option value='#FF0000' style='BACKGROUND-COLOR:#FF0000'>Red</option>");	
	print ("<option value='#FF1490' style='BACKGROUND-COLOR:#FF1490'>Dark Pink</option>");	
	print ("<option value='#FF68B0' style='BACKGROUND-COLOR:#FF68B0'>Pink</option>");	
	print ("<option value='#FFC0C8' style='BACKGROUND-COLOR:#FFC0C8'>Light Pink</option>");	
	print ("<option value='#FF4400' style='BACKGROUND-COLOR:#FF4400'>Dark Orange</option>");	
	print ("<option value='#FF6448' style='BACKGROUND-COLOR:#FF6448'>Redish Orange</option>");	
	print ("<option value='#FFA800' style='BACKGROUND-COLOR:#FFA800'>Orange</option>");	
	print ("<option value='#FFD800' style='BACKGROUND-COLOR:#FFD800'>Dark Yellow</option>");	
	print ("<option value='#FFFF00' style='BACKGROUND-COLOR:#FFFF00'>Yellow</option>");	             
	print ("<option value='#FF00FF' style='BACKGROUND-COLOR:#FF00FF'>Light Purple</option>");	
	print ("<option value='#C01480' style='BACKGROUND-COLOR:#C01480'>Dark Purple</option>");	
	print ("<option value='#B854D8' style='BACKGROUND-COLOR:#B854D8'>Dark Violet</option>");	
	print ("<option value='#D8A0D8' style='BACKGROUND-COLOR:#D8A0D8'>Light Violet</option>");	
	print ("<option value='#000080' style='BACKGROUND-COLOR:#000080'>Darkest Blue</option>");	           
	print ("<option value='#0000FF' style='BACKGROUND-COLOR:#0000FF'>Dark Blue</option>");	            
	print ("<option value='#2090FF' style='BACKGROUND-COLOR:#2090FF'>Ble</option>");	 
	print ("<option value='#00BCFF' style='BACKGROUND-COLOR:#00BCFF'>Light Blue</option>");	
	print ("<option value='#B0E0E8' style='BACKGROUND-COLOR:#B0E0E8'>Faint Blue</option>");	
	print ("<option value='#A02828' style='BACKGROUND-COLOR:#A02828'>Brown</option>");	
	print ("<option value='#F0A460' style='BACKGROUND-COLOR:#F0A460'>Brown Creme</option>");	  
	print ("<option value='#D0B488' style='BACKGROUND-COLOR:#D0B488'>Light Brown</option>");	               
	print ("<option value='#B8B868' style='BACKGROUND-COLOR:#B8B868'>Brown/Green</option>");	
	print ("<option value='#008000' style='BACKGROUND-COLOR:#008000'>Dark Green</option>");	
	print ("<option value='#30CC30' style='BACKGROUND-COLOR:#30CC30'>Green</option>");	
	print ("<option value='#00FF80' style='BACKGROUND-COLOR:#00FF80'>Light Green</option>");	
	print ("<option value='#98FC98' style='BACKGROUND-COLOR:#98FC98'>Light Lime</option>");	
	print ("<option value='#98CC30' style='BACKGROUND-COLOR:#98CC30'>Dark Lime</option>");	
	print ("<option value='#40E0D0' style='BACKGROUND-COLOR:#40E0D0'>Turquois</option>");	
	print ("<option value='#20B4A8' style='BACKGROUND-COLOR:#20B4A8'>Aquarium</option></select>");
	print ("&nbsp;&nbsp;</center>");
	?>
<div class="container">
<div class="row justify-content-md-center">
<div class="col col-lg-10">
<div id="myDIVsmileytog" style="display:none">
    <a href="javascript:SmileIT(':)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/smile.png" border="0" alt=':)' title=':)' /></a>
    <a href="javascript:SmileIT(':(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sad.png" border="0" alt=':(' title=':(' /></a>
    <a href="javascript:SmileIT(':D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/grin.png" border="0" alt=':D' title=':D' /></a>
    <a href="javascript:SmileIT(':P','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/razz.png" border="0" alt=':P' title=':P' /></a>
    <a href="javascript:SmileIT(':-)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/smile-big.png" border="0" alt=':-)' title=':-)' /></a>
    <a href="javascript:SmileIT('B)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/cool.png" border="0" alt='B)' title='B)' /></a>
    <a href="javascript:SmileIT('8o','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/eek.png" border="0" alt='8o' title='8o' /></a>
    <a href="javascript:SmileIT(':?','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/confused.png" border="0" alt=':?' title=':?' /></a>
    <a href="javascript:SmileIT('8)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/glasses.png" border="0" alt='8)' title='8)' /></a>
    <a href="javascript:SmileIT(';)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/wink.png" border="0" alt=';)' title=';)' /></a>
    <a href="javascript:SmileIT(':-*','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/kiss.png" border="0" alt=':-*' title=':-*' /></a>
    <a href="javascript:SmileIT(':-(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/crying.png" border="0" alt=':-(' title=':-(' /></a>
    <a href="javascript:SmileIT(':|','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/plain.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT('O:-D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/angel.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':-@','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/devilish.png" border="0" alt=':-@' title=':-@' /></a>
    <a href="javascript:SmileIT(':o)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/monkey.png" border="0" alt=':o)' title=':o)' /></a>
    <a href="javascript:SmileIT('brb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT;; ?>/assets/images/smilies/brb.png" border="0" alt='brb' title='brb' /></a>
    <a href="javascript:SmileIT(':warn','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/warn.png" border="0" alt=':warn' title=':warn' /></a>
    <a href="javascript:SmileIT(':help','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/help.png" border="0" alt=':help' title=':help' /></a>
    <a href="javascript:SmileIT(':bad','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/bad.png" border="0" alt=':bad' title=':bad' /></a>
    <a href="javascript:SmileIT(':love','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/love.png" border="0" alt=':love' title=':love' /></a>
    <a href="javascript:SmileIT(':idea','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/idea.png" border="0" alt=':idea' title=':idea' /></a>
    <a href="javascript:SmileIT(':bomb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/bomb.png" border="0" alt=':bomb' title=':bomb' /></a>
    <a href="javascript:SmileIT(':!','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/important.png" border="0" alt=':!' title=':!' /></a>
    <a href="javascript:SmileIT(':gigg','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/giggle.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT(':rofl','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/roflmao.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':slep','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sleep.png" border="0" alt=':-@' title=':-@' /></a>
    <a href="javascript:SmileIT(':thum','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/thumbsup.png" border="0" alt=':o)' title=':o)' /></a>
    <a href="javascript:SmileIT(':0_0','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/zpo.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT(':poop','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/poop.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':spechles','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/speechless.png" border="0" alt=':-@' title=':-@' /></a>
    <a href="javascript:SmileIT(':unsure','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/unsure.png" border="0" alt=':o)' title=':o)' /></a>
    <a href="javascript:SmileIT(':mad','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/mad.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT(':roll','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/rolleyes.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':sick','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sick.png" border="0" alt=':-@' title=':-@' /></a>
    <a href="javascript:SmileIT(':crylol','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/crylaugh.png" border="0" alt=':|' title=':|' /></a>
    <a href="javascript:SmileIT(':confos','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/confound.png" border="0" alt='O:-D' title='0:-D' /></a>
    <a href="javascript:SmileIT(':fire','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/fire.png" border="0" alt=':-@' title=':-@' /></a>
</div></div>

<div class="row">
<div class="col-md-11">
<input  class="form-control shoutbox_msgbox" type='text' size='100%' name="<?php echo $name; ?>"><?php echo $content; ?>

</div>

        <div class="col-md-1">
        <center><input type='submit' name='submit' value='<?php echo Lang::T("SHOUT") ?>' class='btn btn-sm btn-primary' /></center>
        </div>
     </div>

</div>
</div>
<?php
}
?>