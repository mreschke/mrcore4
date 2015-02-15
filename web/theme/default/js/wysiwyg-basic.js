
/*
 * Javascript WYSIWYG HTML control
 * Version 0.2
 *
 * Copyright (c) 2004 Paul James
 * All rights reserved.
 *
 * This software is covered by the BSD License, please find a copy of this
 * license at http://peej.co.uk/sandbox/wysiwyg/
 *
 * Modified by mReschke for the text_wiki (sometimes called PearWiki) wiki syntax
 * 2010-09-07, thanks Paul
 */

// these are constants but IE doesn't like the const keyword
var WYSIWYG_VALUE_NONE = 0;
var WYSIWYG_VALUE_PROMPT = 1;
var WYSIWYG_VALUE_FUNCTION = 2;
var WYSIWYG_BUTTONS_AS_FORM_ELEMENTS = false;
var source_button_text = 'Source';
var view_button_text = 'WYSIWYG';
var default_to_source_view = true;

// define toolbar buttons
if (!wysiwyg_toolbarButtons) {
    var wysiwyg_toolbarButtons = new Array(
    //command, display name, value, title, prompt/function, default text
	["toggleview", "Source", "Compose", "Switch views"],
	["div"], // place a toolbar divider
	["formatblock", "Code", "<pre>", "Code Format"],
	["div"], // place a toolbar divider
	["formatblock", "H1", "<h1>", "Make 1st level heading"],
    ["formatblock", "H2", "<h2>", "Make 2nd level heading"],
    ["formatblock", "H3", "<h3>", "Make 3rd level heading"],
	["formatblock", "H4", "<h4>", "Make 4th level heading"],
	["formatblock", "H5", "<h5>", "Make 5th level heading"],
	["formatblock", "H6", "<h6>", "Make 6th level heading"],
	["inserthorizontalrule", "----", WYSIWYG_VALUE_NONE, "Horizontal Line"],
	["div"], // place a toolbar divider
    ["createlink", "((x))", WYSIWYG_VALUE_PROMPT, "Create a link to another topic", "Enter the topic ID:", ""],
	["createlink", "[http]", WYSIWYG_VALUE_PROMPT, "Create a link to external website", "Enter the external URL:", "http://"],
	["insertimage", "[[img]]", WYSIWYG_VALUE_PROMPT, "Insert an image", "Enter the URL of the image:", "http://"],
	["div"], // place a toolbar divider	
    ["bold", "B", WYSIWYG_VALUE_NONE, "Bold Text"],
	["italic", "I", WYSIWYG_VALUE_NONE, "Italicize Text"],
	["underline", "U", WYSIWYG_VALUE_NONE, "Underline Text"],
	["strikethrough", "S", "", "Strikethrough Text"],
	["superscript", "sup", "", "Superscript Text"],
	["subscript", "sub", "", "Subscript Text"],
	["forecolor", "C", WYSIWYG_VALUE_PROMPT, "Color Text", "Enter Named Color (red, blue, green...):", ""],
    ["div"], // place a toolbar divider
	["formatblock", "xCode", "<p>", "Remove Code Format"],
	["unlink", "xLink", WYSIWYG_VALUE_NONE, "Remove hyperlink"],
	["removeformat", "xFormat", "", "Unformat Text"]
	//["insertunorderedlist", "List", null, "Make an unordered list"],
	//["insertorderedlist", "Ordered List", null, "Make an ordered list"]
    );
}

// map control elements to desired elements


//        [/<span style="font-weight: normal;">(.*?)<\/span>/gm, "$1"],
//        [/<span style="font-weight: bold;">(.*?)<\/span>/gm, "**$1**"],
//        [/<span style="font-style: italic;">(.*?)<\/span>/gm, "<em>$1</em>"],
//        [/<span style="(font-weight: bold; ?|font-style: italic; ?){2}">(.*?)<\/span>/gm, "<strong><em>$2</em></strong>"],
//        [/<([a-z]+) style="font-weight: normal;">(.*?)<\/\1>/gm, "<$1>$2</$1>"],
//        [/<([a-z]+) style="font-weight: bold;">(.*?)<\/\1>/gm, "<$1><strong>$2</strong></$1>"],


//<span style="font-style: italic; font-weight: bold;">d</span>
//<br style="font-weight: bold; font-style: italic;">



//[/<(hr|HR)( style="width: 100%; height: 2px;")?>/g, "<hr />"]

if (!wysiwyg_elementMap) {
    var wysiwyg_elementMap = new Array(
	//Bold
//	[/<([a-z]+) style="font-weight: bold;">(.*?)<\/\1>/gim, "**$2**"],
//	[/<([a-z]+) style="font-weight: bold;">/gim, "\n"],
//	[/<(B|b|STRONG)>(.*?)<\/\1>/gim, "**$2**"],
	//Bold & Underline
//	[/<([a-z]+) style="(font-weight: bold; ?|text-decoration: underline; ?){2}">(.*?)<\/\1>/gim, "**__$3__**"],
//	[/<([a-z]+) style="(font-weight: bold; ?|text-decoration: underline; ?){2}">(.*?)/gim, "\n"],
	//Bold & Color
//	[/<([a-z]+) style="(font-weight: bold; ?|color: (.*?); ?){2}">(.*?)<\/\1>/gim, "##$3|**$4**##"],
//	[/<([a-z]+) style="(font-weight: bold; ?|color: (.*?); ?){2}">(.*?)/gim, "\n"],	
	//Bold & Underline & Italic
//	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline; ?){3}">(.*?)<\/\1>/gim, "**//__$3__//**"],
//	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline; ?){3}">(.*?)/gim, "\n"],
	//Bold & Underline & Italic & Strikethrough
//	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline line-through; ?){3}">(.*?)<\/\1>/gim, "**//__@@---$3@@__//**"],
//	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline line-through; ?){3}">(.*?)/gim, "\n"],
	//Bold & Underline & Italic & Strikethrough
	//[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline line-through; ?){3}">(.*?)<\/\1>/gim, "**//__@@---$3@@__//**"],
	//[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: underline line-through; ?){3}">(.*?)/gim, "\n"],		
	//Bold & Italic
//	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?){2}">(.*?)<\/\1>/gim, "**//$3//**"],
//	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?){2}">(.*?)/gim, "\n"],
	//Bold & Italic & Strikethrough
//	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: line-through; ?){3}">(.*?)<\/\1>/gim, "**//@@---$3@@//**"],
//	[/<([a-z]+) style="(font-weight: bold; ?|font-style: italic; ?|text-decoration: line-through; ?){3}">(.*?)/gim, "\n"],	
	//Bold & Strikethrough
//	[/<([a-z]+) style="(font-weight: bold; ?|text-decoration: line-through; ?){2}">(.*?)<\/\1>/gim, "**@@---$3@@**"],
//	[/<([a-z]+) style="(font-weight: bold; ?|text-decoration: line-through; ?){2}">(.*?)/gim, "\n"],
	//Underline
//	[/<([a-z]+) style="text-decoration: underline;">(.*?)<\/\1>/gim, "__$2__"],
//	[/<([a-z]+) style="text-decoration: underline;">/gim, "\n"],
//	[/<u>(.*?)<\/u>/gim, "__$1__"],
	//Underline & Color
//	[/<([a-z]+) style="(text-decoration: underline; ?|color: (.*?); ?){2}">(.*?)<\/\1>/gim, "##$3|__$4__##"],
//	[/<([a-z]+) style="(text-decoration: underline; ?|color: (.*?); ?){2}">(.*?)/gim, "\n"],		
	//Italics
//	[/<([a-z]+) style="font-style: italic;">(.*?)<\/\1>/gim, "//$2//"],
//	[/<([a-z]+) style="font-style: italic;">/gim, "\n"],
//	[/<(I|i|EM)>(.*?)<\/\1>/gim, "//$2//"],
	//Italic & Underline
//	[/<([a-z]+) style="(font-style: italic; ?|text-decoration: underline; ?){2}">(.*?)<\/\1>/gim, "//__$3__//"],
//	[/<([a-z]+) style="(font-style: italic; ?|text-decoration: underline; ?){2}">(.*?)/gim, "\n"],
	//Italic & Strikethrough
//	[/<([a-z]+) style="(font-style: italic; ?|text-decoration: line-through; ?){2}">(.*?)<\/\1>/gim, "//@@---$3@@//"],
//	[/<([a-z]+) style="(font-style: italic; ?|text-decoration: line-through; ?){2}">(.*?)/gim, "\n"],	
	//Strikethrough
//	[/<([a-z]+) style="text-decoration: line-through;">(.*?)<\/\1>/gim, "@@---$2@@"],
//	[/<([a-z]+) style="text-decoration: line-through;">/gim, "\n"],
//	[/<s>(.*?)<\/s>/gim, "@@---$1@@"],
	//Strikethrough & Underline
//	[/<([a-z]+) style="text-decoration: underline line-through;">(.*?)<\/\1>/gim, "__@@---$2@@__"],
//	[/<([a-z]+) style="text-decoration: underline line-through;">/gim, "\n"],
	//Color
//	[/<([a-z]+) style="color: (.*?);">(.*?)<\/\1>/gim, "##$2|$3##"],
//	[/<([a-z]+) style="color: (.*?);">/gim, "\n"],
	//[/<([a-z]+) style="(font-style: italic; ?|text-decoration: underline; ?){2}">(.*?)/gim, "\n"],	
	//Superscript
//	[/<sup>(.*?)<\/sup>/gim, "^^$1^^"],
	//Subscript
//	[/<sub>(.*?)<\/sub>/gim, ",,$1,,"],
	//
	[/<p>(.*?)<\/p>/gim, "$1\n"],
	//Headers
	[/<h1>(.*?)<\/h1>/gim, "\n+$1\n"],
	[/<h2>(.*?)<\/h2>/gim, "\n++$1\n"],
	[/<h3>(.*?)<\/h3>/gim, "\n+++$1\n"],
	[/<h4>(.*?)<\/h4>/gim, "\n++++$1\n"],
	[/<h5>(.*?)<\/h5>/gim, "\n+++++$1\n"],
	[/<h6>(.*?)<\/h6>/gim, "\n++++++$1\n"],
	//Spaces
	[/\&nbsp;/gim, " "],
	//Div (chrome return key prints a <div>)
	[/<div>/g, "\n"],
	[/<\/div>/g, ""],
	//Links
//	[/<a href=\"http(.*?)\"\>(.*?)<\/a>/gim, "[http$1 $2]"],
//	[/<a href=\"mailto\:(.*?)\"\>(.*?)<\/a>/gim, "mailto:$1"],
//	[/<a href=\"(.*?)\"\>(.*?)<\/a>/gim, "(($1|$2))"],
	//Image
//	[/<img src=\"(.*?)\">/gi, "[[image $1]]"],
//	[/<img\ (.*?)\ src=\"(.*?)\">/gi, "[[image $2 $1]]"],
	//
//	[/<li>(.*?)<\/li>/gim, "<li>$1</li>"],
//	[/<ul>(.*?)<\/ul>/gim, "<ul>$1</ul>"],
//	[/<font color=\"(.*?)\">(.*?)<\/font>/gim, "##$1|$2##"],
	[/<(br|BR)>/gim, "\n"],
//	[/<(hr|HR)( style="width: 100%; height: 2px;")?>/gim, "----\n"],
	//[/\[html\]/gm, "[html]"],
	[/<tt>(.*?)<\/tt>/gim, "$1"],
//	[/<pre>/gim, "<code>\n"],
//	[/<pre header=\"(.*?)\">/gim, "<code $1>\n"],
//	[/<\/pre>/gim, "</code>\n"],
	[/\&lt;/gim, "<"],
	[/\&gt;/gim, ">"],
	[/\%20/gim, " "],
	//[/\&#40;/gim, "("],
	//[/\&#41;/gim, ")"],
	//[/\&#47;/gim, "/"],	
	//[/\&quot;/gim, '"'],
	//Blockquote
//	[/<blockquote(.*?)>(.*?)<\/blockquote>/gim, "\n> $2\n"],
	[/<span class(.*?)tag_(.*?)>/gim, ""],
	[/<\/span>/gim, ""]
    );
//[/<pre>(.*?)<\/pre>/gm, "\n<code>\n$1\n</code>\n"]


    //This converts wiki syntax back to HTML for visual display
    //mReschke 2010-09-07
    //. will match any character except newline in javascript, use [\s\S] instead (http://jamesmckay.net/2008/05/how-to-match-any-character-including-newlines-in-a-javascript-regular-expression/)
    var wysiwyg_wikiMap = new Array(
	[/\</gim, "&lt;"],
	[/\>/gim, "&gt;"],
	[/\(/gim, "&#40;"],
	[/\)/gim, "&#41;"],
	[/\//gim, "&#47;"],
	[/\ /gim, "&nbsp;"],
	//html tag
	[/\&lt;html\&gt;/gim, "<span class='tag_html_data'><span class='tag_html'>&lt;html&gt;</span>"],
	[/\&lt;&#47;html\&gt;/gim, "<span class='tag_html'>&lt;/html&gt;</span></span>"],
	//[/\&lt;html\&gt;(\n|.?)*\&lt;\/html\&gt;/gim, "<span class='tag_html'>&lt;html&gt;$0$1$2$3$4$5&lt;/html&gt;</span>"],
	//php tag
	[/\&lt;php\&gt;/gim, "<span class='tag_php_data'><span class='tag_php'>&lt;php&gt;</span>"],
	[/\&lt;&#47;php\&gt;/gim, "<span class='tag_php'>&lt;/php&gt;</span></span>"],
	//codepress tag
	[/\&lt;codebox&nbsp;(.*?)\&gt;/gim, "<span class='tag_codebox_data'><span class='tag_codebox'>&lt;codebox $1&gt;</span>"],
	[/\&lt;&#47;codebox\&gt;/gim, "<span class='tag_codebox'>&lt;/codebox&gt;</span></span>"],
	//code tag
	[/\&lt;code(.*?)\&gt;/gim, "<span class='tag_code_data'><span class='tag_code'>&lt;code$1&gt;</span>"],
	[/\&lt;&#47;code\&gt;/gim, "<span class='tag_code'>&lt;/code&gt;</span></span>"],
	//box tag
	[/\&lt;box(.*?)\&gt;/gim, "<span class='tag_box_data'><span class='tag_box'>&lt;box$1&gt;</span>"],
	[/\&lt;&#47;box\&gt;/gim, "<span class='tag_box'>&lt;/box&gt;</span></span>"],
	//priv tag
	[/\&lt;priv(.*?)\&gt;/gim, "<span class='tag_priv'>&lt;priv$1&gt;</span>"],
	[/\&lt;&#47;priv(.*?)\&gt;/gim, "<span class='tag_priv'>&lt;/priv$1&gt;</span>"],
	//auth tag
	[/\&lt;auth(.*?)\&gt;/gim, "<span class='tag_auth'>&lt;auth$1&gt;</span>"],
	[/\&lt;&#47;auth(.*?)\&gt;/gim, "<span class='tag_auth'>&lt;/auth$1&gt;</span>"],
	//textbox tag
	[/\&lt;textbox&nbsp;(.*?)\&gt;/gim, "<span class='tag_textbox_data'><span class='tag_textbox'>&lt;textbox $1&gt;</span>"],
	[/\&lt;&#47;textbox\&gt;/gim, "<span class='tag_textbox'>&lt;/textbox&gt;</span></span>"],
	//link tag (obsolete)
	//[/\&lt;link&nbsp;(.*?)\|(.*?)\&gt;/gim, "<span class='tag_link'>&lt;link $1|$2&gt;</span>"],
	//teaser tag
	[/\&lt;teaser\&gt;/gim, "<span class='tag_teaser_data'><span class='tag_teaser'>&lt;teaser&gt;</span>"],
	[/\&lt;&#47;teaser\&gt;/gim, "<span class='tag_teaser'>&lt;/teaser&gt;</span></span>"],
	//Bold
//	[/\'\'\'(.*?)\'\'\'/gim, "<b>$1</b>"],
//	[/\'\'\'([\s\S]*)\'\'\'/gim, "<b>$1</b>"], //Multiline
//	[/\*\*(.*?)\*\*/gim, "<b>$1</b>"],
//	[/\*\*([\s\S]*)\*\*/gim, "<b>$1</b>"], //Multiline
	//Italic (add the ( |\n) becuase might be http://, don't want to pick that up)
//	[/(&nbsp;|\n)&#47;&#47;(.*?)&#47;&#47;/gim, "$1<i>$2</i>"],
	//[/&#47;&#47;(.*?)&#47;&#47;/gim, "<i>$1</i>"],
	//Underline
//	[/\_\_([\s\S]*?)\_\_/gim, "<u>$1</u>"], //Multiline
	//Strikethrough
//	[/\@\@\-\-\-([\s\S]*?)\@\@/gim, "<s>$1</s>"],
	//Superscript
//	[/\^\^([\s\S]*?)\^\^/gim, "<sup>$1</sup>"],
	//Subscript
//	[/,,([\s\S]*?),,/gim, "<sub>$1</sub>"], //Awesome, multiline with ? means nongreedy, so takes the first ,, after the start it sees, not the last one
	//Headers
	[/\n\+\+\+\+\+\+(.*?)\n/gim, "<h6>$1</h6>"],
	[/\n\+\+\+\+\+(.*?)\n/gim, "<h5>$1</h5>"],
	[/\n\+\+\+\+(.*?)\n/gim, "<h4>$1</h4>"],
	[/\n\+\+\+(.*?)\n/gim, "<h3>$1</h3>"],
	[/\n\+\+(.*?)\n/gim, "<h2>$1</h2>"],
	[/\n\+(.*?)\n/gim, "<h1>$1</h1>"],
//	[/\-\-\-\-\n/gim, "<hr />"],
	//Links
//	[/\&#40;\&#40;(.*?)\|(.*?)\&#41;\&#41;/gim, "<a href=\"$1\">$2</a>"],
//	[/\&#40;\&#40;(.*?)\&#41;\&#41;/gim, "<a href=\"$1\">$1</a>"],
//	[/\[http(.*?):&#47;&#47;(.*?)&nbsp;(.*?)\]/gim, "<a href=\"http$1://$2\">$3</a>"],
//	[/(&nbsp;|\n)http(.*?):&#47;&#47;(.*?)(&nbsp;|\n)/gim, "$1<a href=\"http$2://$3\">http$2://$3</a>$4"],
//	[/(&nbsp;|\n)mailto:(.*?)(&nbsp;|\n)/gi, "$1<a href=\"mailto:$2\">$2</a>$3"],
	//Image (fixme)
	//if have [[image]] [[image]] on same lines messes up...
	//[/\[\[image&nbsp;(.*?)\ (.*?)\]\]/gi, "<img $2 src=\"$1\">"],
	//[/\[\[image&nbsp;(.*?)\]\]/gi, "<img src=\"$1\">"],
	//Color
//	[/\#\#(.*?)\|(.*?)\#\#/gim, "<font color=\"$1\">$2</font>"],
//	[/\#\#(.*?)\|([\s\S]*)\#\#/gim, "<font color=\"$1\">$2</font>"],
	//Teletype
	[/\{\{(.*?)\}\}/gim, "<tt>{{$1}}</tt>"],
	//Blockquote
//	[/\n&gt;&nbsp;(.*?)\n/gim, "<blockquote class=\"blockQuote\">$1</blockquote>"],
	//[/<html>/gim, "<!--<html>-->"],
	//[/<&#47;html>/gim, "<!--</html>-->"],
	//Code
//	[/\&lt;code\&gt\;\n/gim, "<pre>"],
//	[/\&lt;code&nbsp;(.*?)\&gt;\n/gim, "<pre header='$1'>"],
//	[/\n\&lt;&#47;code\&gt;\n/gim, "\n</pre>"],
	//[/<(.*?)html>/gim, "&lt;$1html&gt;"]
	//Return (last)
	[/\n/gim, "<br />"]

    );
}

	//[/\n<code>\n/gim, "<pre>"],
	//[/\n<\/code>\n/gim, "</pre>"]
	//[/<code>/gim, "<pre>"],
	//[/<\/code>/gim, "</pre>"]	

// attach to window onload event
if (document.getElementById && document.designMode) {
    if (window.addEventListener){
        window.addEventListener("load", wysiwyg, false);
    } else if (window.attachEvent){
        window.attachEvent("onload", wysiwyg);
    } else {
        alert("Could not attach event to window.");
    }
}
    
// init wysiwyg
function wysiwyg() {
    var scroll_height;
    var carrot_pos;
    createWysiwygControls();
    setTimeout(initWysiwygControls, 1); // do this last and after a slight delay cos otherwise it can get turned off in Gecko
	
    // turn textareas into wysiwyg controls
    function createWysiwygControls() {
        var textareas = document.getElementsByTagName("textarea");
        for (var foo = 0; foo < textareas.length; foo++) {
            if (textareas[foo].className.indexOf("wysiwyg") > -1) {
		var wysiwyg = document.createElement("div");
                var control = document.createElement("iframe");
                var textarea = textareas[foo];
                wysiwyg.className = "wysiwyg";
		wysiwyg.id = 'edit_body_wysiwyg';
		//IFrame
		//control.src = textarea.className.match(/[a-zA-Z0-9_.-]+\.html/);
		control.src = textarea.className.match(/[a-zA-Z0-9_.-/]+\.html/); //mReschke, added path to textarea calss
		//alert(control.src);
                control.className = "";
		control.id = 'edit_body_iframe';
                wysiwyg.appendChild(control);
                wysiwyg.control = control;
                textarea.style.display = "none";
                textarea.className = "";
                textarea.parentNode.replaceChild(wysiwyg, textareas[foo]);
                wysiwyg.appendChild(textarea);
                wysiwyg.textarea = textarea;
                createToolbar(wysiwyg);
				
		//Default to Source View
		if (default_to_source_view) {
		    control.style.display='none';
		    textarea.style.display='block';
		    var toolbars = wysiwyg.getElementsByTagName("div");
		    for (var foo = 0; foo < toolbars.length; foo++) {
			for (var bar = 0; bar < toolbars[foo].childNodes.length; bar++) {
			    var button = toolbars[foo].childNodes[bar];
			    //alert(button.innerHTML);
			    if (button.command != "toggleview") {
				    button.style.display='none';
			    } else {
				    button.innerHTML = view_button_text;
			    }
			}
		    }
		}									
            }
        }
    }
    
    // get controls from DOM
    function getWysiwygControls() {
        var divs = document.getElementsByTagName("div");
        var wysiwygs = new Array();
        for (var foo = 0, bar = 0; foo < divs.length; foo++) {
            if (divs[foo].className == "wysiwyg") {
                wysiwygs[bar] = divs[foo];
                bar++;
            }
        }
        return wysiwygs;
    }
    
    // initiate wysiwyg controls
    function initWysiwygControls() {
        var wysiwygs = getWysiwygControls();
        if (!wysiwygs[0]) return; // no wysiwygs needed
        if (!wysiwygs[0].control.contentWindow) { // if not loaded yet, wait and try again
            setTimeout(initWysiwygControls, 1);
            return;
        }
        for (var foo = 0; foo < wysiwygs.length; foo++) {
            // turn on design mode for wysiwyg controls
            wysiwygs[foo].control.contentWindow.document.designMode = "on";
			//wysiwygs[foo].control.contentWindow.document.execCommand("styleWithCSS", false, false); //mreschke ??
			
            // attach submit method
            var element = wysiwygs[foo].control;
            while (element.tagName && element.tagName.toLowerCase() != "form") {
                if (!element.parentNode) break;
                element = element.parentNode;
            }
            if (element.tagName && element.tagName.toLowerCase() == "form" && !element.wysiAttached) {
                if (element.onsubmit) {
                    element.onsubmit = function() {
                        element.onsubmit();
                        wysiwygSubmit();
                    }
                } else {
                    element.wysiAttached = true;
                    element.onsubmit = wysiwygSubmit;
                }
            }
        }
        // schedule init of content (we do this due to IE)
        setTimeout(initContent, 1);
    }

    // set initial content    
    function initContent() {
        var wysiwygs = getWysiwygControls();
        for (var foo = 0; foo < wysiwygs.length; foo++) {
            wysiwygUpdate(wysiwygs[foo]);
        }
    }

    // create a toolbar for the control
    function createToolbar(wysiwyg) {
        var toolbar = document.createElement("div");
        var bar = 0;
        toolbar.className = "toolbar toolbar" + bar;
        for (var foo = 0; foo < wysiwyg_toolbarButtons.length; foo++) {
            if (wysiwyg_toolbarButtons[foo][0] == "toggleview") {
                var button = createButton(wysiwyg, foo);
                button.onclick = toggleView;
                button.htmlTitle = wysiwyg_toolbarButtons[foo][1];
                button.composeTitle = wysiwyg_toolbarButtons[foo][2];
                toolbar.appendChild(button);
            } else if (wysiwyg_toolbarButtons[foo].length >= 3) {
                var button = createButton(wysiwyg, foo);
                button.onclick = execCommand;
                toolbar.appendChild(button);
            } else if (wysiwyg_toolbarButtons[foo][0] == "div") {
                var divider = document.createElement("div");
                divider.className = "divider";
                toolbar.appendChild(divider);
            } else {
                bar++;
                wysiwyg.insertBefore(toolbar, wysiwyg.control);
                var toolbar = document.createElement("div");
                toolbar.className = "toolbar toolbar" + bar;
            }
        }
        wysiwyg.insertBefore(toolbar, wysiwyg.control);
    }
    
    // create a button for the toolbar
    function createButton(wysiwyg, number) {
        if (WYSIWYG_BUTTONS_AS_FORM_ELEMENTS) {
            var button = document.createElement("input");
            button.type = "button";
            button.value = wysiwyg_toolbarButtons[number][1];
        } else {
            if (document.all) { // IE needs the buttons to be anchors
                var button = document.createElement("a");
                button.href = "";
            } else {
                var button = document.createElement("span");
            }
            button.appendChild(document.createTextNode(wysiwyg_toolbarButtons[number][1]));
        }
        button.number = number;
        button.className = "toolbarButton toolbarButton" + number;
        button.command = wysiwyg_toolbarButtons[number][0];
        if (wysiwyg_toolbarButtons[number][2]) button.commandValue = wysiwyg_toolbarButtons[number][2];
        if (wysiwyg_toolbarButtons[number][3]) button.title = wysiwyg_toolbarButtons[number][3];
        button.wysiwyg = wysiwyg;
        return button;
    }
   
    // execute a toolbar command
    function execCommand() {
        var value = null;
        switch(this.commandValue) {
        case WYSIWYG_VALUE_NONE:
            value = null;
            break;
        case WYSIWYG_VALUE_PROMPT:
            if (wysiwyg_toolbarButtons[this.number][4]) var promptText = wysiwyg_toolbarButtons[this.number][4]; else var promptText = "";
            if (wysiwyg_toolbarButtons[this.number][5]) var defaultText = wysiwyg_toolbarButtons[this.number][5]; else var defaultText = "";
            var value = prompt(promptText, defaultText);
            if (!value) return false;
            break;
        case WYSIWYG_VALUE_FUNCTION:
        
        default:
            value = this.commandValue;
        }
        if (this.command instanceof Array) { // if command is array, execute all commands
            for (var foo = 0; foo < this.command.length; foo++) {
                if (this.command[foo] == "insertcontent") {
                    insertContent(this.wysiwyg, value);
                } else {
                    this.wysiwyg.control.contentWindow.document.execCommand(this.command[foo], false, value);
                }
            }
        } else {
            if (this.command == "insertcontent") {
                insertContent(this.wysiwyg, value);
            } else {
                this.wysiwyg.control.contentWindow.document.execCommand(this.command, false, value);
            }
        }
        textareaUpdate(this.wysiwyg);
        this.wysiwyg.control.contentWindow.focus();
        return false;
    }
    
    // insert HTML content into control
    function insertContent(wysiwyg, content) {
        var textarea = wysiwyg.textarea;
        var control = wysiwyg.control;
        if (document.selection) { // IE
            control.focus();
            sel = document.selection.createRange();
            sel.text = content;
        } else { // Mozilla
            var sel = control.contentWindow.getSelection();
            var range = sel.getRangeAt(0);
            sel.removeAllRanges();
            range.deleteContents();
            var oldContent = control.contentWindow.document.body.innerHTML;
            var inTag = false;
            var insertPos = 0;
            for (var foo = 0, pos = 0; foo < oldContent.length; foo++) {
                var aChar = oldContent.substr(foo, 1);
                if (aChar == "<") {
                    inTag = true;
                }
                if (!inTag) {
                    pos++;
                    if (pos == range.startOffset) {
                        insertPos = foo + 1;
                    }
                }
                if (aChar == ">") {
                    inTag = false;
                }
            }
            control.contentWindow.document.body.innerHTML = oldContent.substr(0, insertPos) + content + oldContent.substr(insertPos, oldContent.length);
        }
        textareaUpdate(wysiwyg);
    }
    
    // show textarea view
    function toggleView() {
        var control = this.wysiwyg.control;
        var textarea = this.wysiwyg.textarea;
        var toolbars = this.wysiwyg.getElementsByTagName("div");
        if (textarea.style.display == "none") {
            textareaUpdate(this.wysiwyg);
            control.style.display = "none";
            textarea.style.display = "block";
            for (var foo = 0; foo < toolbars.length; foo++) {
                for (var bar = 0; bar < toolbars[foo].childNodes.length; bar++) {
                    var button = toolbars[foo].childNodes[bar];
					
                    if (button.command != "toggleview") {
                        if (button.tagName != "DIV") button.disabled = true;
                        button.oldClick = button.onclick;
                        button.onclick = null;
                        button.oldClassName = button.className;
                        button.className += " disabled";
						button.style.display = 'none';
                    } else {
						button.innerHTML = view_button_text;
					}
                }
            }
		    if (scroll_height) { //Restore Last Scroll Height
				textarea.scrollTop = scroll_height;
		    }	    
        } else {
			scroll_height = textarea.scrollTop; //Save Scroll Height
            wysiwygUpdate(this.wysiwyg);
            textarea.style.display = "none";
            control.style.display = "block";
            control.contentWindow.document.designMode = "on";
            for (var foo = 0; foo < toolbars.length; foo++) {
                for (var bar = 0; bar < toolbars[foo].childNodes.length; bar++) {
                    var button = toolbars[foo].childNodes[bar];
                    if (button.command != "toggleview") {
                        if (button.tagName != "DIV") button.disabled = false;
                        if (button.oldClick) button.onclick = button.oldClick;
                        if (button.oldClassName) button.className = button.oldClassName;
						button.style.display = 'block';
                    } else {
						button.innerHTML = source_button_text;
					}
                }
            }
        }
        return false;
    }
    
    // update the textarea to contain the source for the wysiwyg control
    function textareaUpdate(wysiwyg) {
        var html = wysiwyg.control.contentWindow.document.body.innerHTML;
        for (var foo = 0; foo < wysiwyg_elementMap.length; foo++) {
            html = html.replace(wysiwyg_elementMap[foo][0], wysiwyg_elementMap[foo][1]);
        }
	html = html.replace(/&amp;/g, "&").replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, "\"");
        wysiwyg.textarea.value = html;
    }
    
    // update the wysiwyg to contain the source for the textarea control
    function wysiwygUpdate(wysiwyg) {
	//Original
        //wysiwyg.control.contentWindow.document.body.innerHTML = wysiwyg.textarea.value;

	//mReschke Wiki Version 2010-09-07
        var html = wysiwyg.textarea.value;
        for (var foo = 0; foo < wysiwyg_wikiMap.length; foo++) {
            html = html.replace(wysiwyg_wikiMap[foo][0], wysiwyg_wikiMap[foo][1]);
        }
	//return mystring.replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
        if (wysiwyg.control.contentWindow) {
	    wysiwyg.control.contentWindow.document.body.innerHTML = html;
	} else {
	    alert('crap');
	}
    }
    
    // update for upon submit
    function wysiwygSubmit() {
        var divs = this.getElementsByTagName("div");
        for (var foo = 0; foo < divs.length; foo++) {
            if (divs[foo].className == "wysiwyg") {
		//mReschke 2010-09-07, only update textarea if on wysiwyg display
		var textarea = divs[foo].textarea;
		if (textarea.style.display == "none") {
		    textareaUpdate(divs[foo]);
		}
		//Original
		//textareaUpdate(divs[foo]);
            }
        }
    }

}

