<?php
eval(Page::load_class('lib/Text_Wiki/Wiki'));

/*
 class Parser
 All wiki parser work functions (non model)
 mReschke 2010-09-08
*/
class Parser {
    private $php_tokens;

    /*
     function parse_wiki(Info $info, Topic $topic, $data, $display=true)
     Main wiki parser function.  Call this to do a full and complete parse on wiki content
     mReschke 2010-09-07
    */
    public static function parse_wiki(Info $info, Topic $topic, $data, $display=true, $disable_rules=null) {
        GLOBAL $files, $view;

        //Tokens is a multi-dimensional array of items pulled out of the raw wiki usually before parsing, then put back in later
        //Tokens[0][] is all <php></php> items pulled out, each one is replaced with a [php x] where x is the occurance integer
        $tokens = array();

        //Pre Text_Wiki Parse
        $data = Parser::pre_parse($info, $topic, $data, $tokens);
        
        //Text-Wiki Parse
        $data = Parser::text_wiki_parser($info, $topic, $data, $disable_rules);

        //Post Text_Wiki Parse
        $data = Parser::post_parse($info, $topic, $data, $tokens);

        //Display or Return Parsed HTML
        if ($display) { echo $data; } else { return $data; }
    }

    /*
     function pre_parse($info, $topic, $data, &$tokens)
     Parse extra rules from the raw text before Text_Wiki has parsed it
     mReschke 2011-10-03
    */
    public static function pre_parse(Info $info, Topic $topic, $data, &$tokens) {
        //Pre Parse <include>

        //Must do <phpw> first because it could evan to a bunch of <include> statements which we would want to execute
        //Notice also in pre_parse_include we also recursively eval all <phpw> also
        //We also parse all phpw, includes and files first because all other pre_parse rules with work on their output
        //Files MUST come before <php> because its output is actually <php> which then need evaled

        //Pre Parse <phpw></phpw>
        //Since we want this php return parsed we don't "pull" them out with tokens like we would <php>, we just eval them and let the wiki parse the output
        //NOTICE, this function is duplicated in pre_parse_include also for recursive <phpw> and <include> features
        $data = preg_replace_callback('"<phpw>.*?</phpw>"sim',
            function($matches) {
                ob_start();
                eval(preg_replace('"<phpw>|</phpw>"', '', $matches[0]));
                $return = ob_get_contents();
                ob_end_clean();
                return $return;
            }, $data);

        $data = preg_replace('"<include (.*?)>"ie', "Parser::pre_parse_include(\"\\1\")", $data);
        $data = preg_replace('"<exclude>|</exclude>"', '', $data);
        $data = preg_replace('"<section(.*)>|</section>"', '', $data);

        //Pre Parse <files> (must be before the <php> tag because <files> returns php data which needs evaluated)
        $data = preg_replace('"<files>"ie', "Parser::pre_parse_files(\"\\1\")", $data);
        $data = preg_replace('"<files (.*?)>"ie', "Parser::pre_parse_files(\"\\1\")", $data);

        //Pre Parse <search> (must be before the <php> tag because <search> returns php data which needs evaluated)
        $data = preg_replace('"<search>"ie', "Parser::pre_parse_search(\"\\1\")", $data);
        $data = preg_replace('"<search (.*?)>"ie', "Parser::pre_parse_search(\"\\1\")", $data);

        //Pre Parse <php></php> (must be AFTER <files> because <files> produces <php> tags)
        //This removes all <php>...</php> content into a &$tokens[0] variable to be re-injected later on post parse (basically don't parse php)
        $i = 0;
        $data = preg_replace_callback('"<php>.*?</php>"sim',
            function($matches) use (&$tokens, &$i) {
                $tokens[0][] = preg_replace('"<php>|</php>"', '', $matches[0]);
                $return = "[php_token $i]";
                $i++;
                return $return;
            }, $data);

        //Pre Parse <cmd>
        #$data = preg_replace('"<cmd (.*?)>"ie', "Parser::pre_parse_cmd(\"\\1\")", $data);
        $data = preg_replace_callback('"<cmd (.*?)>"sim',
            function ($matches) {
                GLOBAL $topic;
                $cmd = preg_replace('"<cmd |>"', '', $matches[0]);
                if ($cmd != '') {
                    $cmd = preg_replace('"\#"', Config::FILES_DIR.'/'.$topic->topic_id, $cmd); //Wildcards
                    exec($cmd, $return);
                    $return = implode("\r\n", $return);
                }
                return $return;
            }, $data);

        //Pre Parse <embed xxx yyy>
        $data = preg_replace('"<embed (.*?)>"ie', "Parser::pre_parse_embed(\"\\1\")", $data);
        
        //Pre Parse <textarea></textarea>
        $data = preg_replace('"</!textarea>"', "</textarea>", $data);
        
        //Pre Parse <codepress type height title></codepress>
        //CODE PRESS IS OBSOLETE, use <code> with Geshi types!
        //$data = preg_replace('"<codebox (.*?) (.*?) (.*?)>"', "<html>\n<div class='codebox_outer'><span class='codebox_header'>$3</span><textarea class='codepress $1' id='id1' style='width:100%;height:$2px;'>\n", $data);
        //$data = preg_replace('"<codebox (.*?) (.*?)>"', "<html>\n<div class='codebox_outer'><span class='codebox_header'>Script Snippet</span><textarea class='codepress $1' id='id1' style='width:100%;height:$2px;'>\n", $data);
        //$data = preg_replace('"<codebox (.*?)>"', "<html>\n<div class='codebox_outer'><span class='codebox_header'>Script Snippet</span><textarea class='codepress $1' id='id1' style='width:100%;height:400px;'>\n", $data);
        //$data = preg_replace('"<codebox>"', "<html>\n<div class='codebox_outer'><span class='codebox_header'>Script Snippet</span><textarea class='codepress text' id='id1' style='width:100%;height:400px;'>\n", $data);
        //$data = preg_replace('"</codebox>"', "</textarea></div>\n</html>\n", $data);
                
        //Pre Parse, remove my <teaser> and </teaser>
        $data = preg_replace('"<teaser>|</teaser>"', "", $data);
        
        //Pre Parse, comment tags
        $data = preg_replace('"<#(.*?)>"', "", $data);
        
        //Pre Parse Remove <auth> if not allowed (pre parse so headers and TOC are removed)
        if (!$info->is_authenticated) {
            $data = preg_replace('"<auth>.*?</auth>"sim', '', $data);
        }
        
        //Pre Parse Remove <private> if not allowed (pre parse so headers and TOC are removed)
        if ($info->is_authenticated && ($info->admin || $topic->tbl_post->created_by == $info->user_id)) {
        } else {
            $data = preg_replace('"<priv>.*?</priv>"sim', '', $data);
        }
        
        //Pre Parse ++++++ (headers)
        $data = preg_replace('"</\+\+\+\+\+\+>"', '++++++<endheader>', $data);
        $data = preg_replace('"</\+\+\+\+\+>"', '+++++<endheader>', $data);
        $data = preg_replace('"</\+\+\+\+>"', '++++<endheader>', $data);
        $data = preg_replace('"</\+\+\+>"', '+++<endheader>', $data);
        $data = preg_replace('"</\+\+>"', '++<endheader>', $data);
        $data = preg_replace('"</\+>"', '+<endheader>', $data);

        return $data;
    }

    /*
     function post_parse($info, $topic, $data, &$tokens)
     Parse extra rules from the parsed HTML after Text_Wiki has parsed it
     mReschke 2010-09-08
    */
    public static function post_parse(Info $info, Topic $topic, $data, &$tokens) {
        //Post Parse <php></php> (replace my php_tokens with the unparsed PHP in the $tokens[0] array)
        $data = preg_replace_callback('"\[php_token .*?]"sim',
            function($matches) use ($tokens) {
                GLOBAL $files, $search; #this is what makes <files> and <search> global
                $i = preg_replace('"\[php_token |\]"','', $matches[0]);
                ob_start();
                eval($tokens[0][$i]);
                $return = ob_get_contents();
                $return = preg_replace('"</!textarea>"', "</textarea>", $return);
                ob_end_clean();
                return $return;
            }, $data);
    
        //Post Parse <box></box>
        $data = preg_replace('"&lt;box&gt;<br />"', '<div class="box"><table><tr><td><div>', $data);
        $data = preg_replace('"&lt;box&gt;"', '<div class="box"><table><tr><td><div>', $data);
        $data = preg_replace('"&lt;box (.*?) &gt;<br />"', '<div class="box"><table><tr><td><div class="box_left">$1</div></td><td><div class="box_right">', $data);
        $data = preg_replace('"&lt;box (.*?) &gt;"', '<div class="box"><table><tr><td><div class="box_left">$1</div></td><td><div class="box_right">', $data);
        $data = preg_replace('"&lt;box (.*?)&gt;<br />"', '<div class="box"><table><tr><td><div class="box_left">$1</div></td><td><div class="box_right">', $data);
        $data = preg_replace('"&lt;box (.*?)&gt;"', '<div class="box"><table><tr><td><div class="box_left">$1</div></td><td><div class="box_right">', $data);
        $data = preg_replace('"&lt;/box&gt;"', '</div></td></table></div>', $data);


        //Post parse <highlight></highlight>
        $data = preg_replace('"&lt;highlight(.*?)&gt;(.*?)&lt;/highlight&gt;"ie', "Parser::post_parse_highlight(true, \"\\1\", \"\\2\")", $data); //Own Line
        $data = preg_replace('"&lt;highlight(.*?)&gt;(<br />|</p>)"ie', "Parser::post_parse_highlight(false, \"\\1\")", $data); //Own Line
        $data = preg_replace('"&lt;/highlight&gt;"', '</div>', $data);


        //Post parse ending header tags
        //The </+> has been moved to pre_parse, and changed to replace with +<endheader>, to maintain accurate divs in Text_Wiki/Render/Heading.php
        #$data = preg_replace('"&lt;/\+\+\+\+\+\+&gt;"', '</div>', $data);
        #$data = preg_replace('"&lt;/\+\+\+\+\+&gt;"', '</div></div>', $data);
        #$data = preg_replace('"&lt;/\+\+\+\+&gt;"', '</div></div></div>', $data);
        #$data = preg_replace('"&lt;/\+\+\+&gt;"', '</div></div></div></div>', $data);
        #$data = preg_replace('"&lt;/\+\+&gt;"', '</div></div></div></div></div>', $data);
        #$data = preg_replace('"&lt;/\+&gt;"', '</div></div></div></div></div></div>', $data);
        $data = preg_replace('"&lt;endheader&gt;"', '',$data);
        
        //Post parse remove <expand> tag, I just remove it here, its used in my Text_Wiki headers additions
        //I reversed the logic, I don't want <expand> anymore, but still exists in some topics.  I want all expanded by default, and use the <-> tag
        $data = preg_replace('" &lt;expand&gt; "', '', $data);
        $data = preg_replace('" &lt;expand&gt;"', '', $data);
        $data = preg_replace('"&lt;expand&gt;"', '', $data);
        
        $data = preg_replace('" &lt;-&gt; "', '', $data);
        $data = preg_replace('" &lt;-&gt;"', '', $data);
        $data = preg_replace('"&lt;-&gt;"', '', $data);

        $data = preg_replace('" &lt;notoc&gt; "', '', $data);
        $data = preg_replace('" &lt;notoc&gt;"', '', $data);
        $data = preg_replace('"&lt;notoc&gt;"', '', $data);

        //Post Parse <pagebreak>, I just remove it here, its used in my Text_Wiki headers additions
        $data = preg_replace('" &lt;pagebreak&gt; "', '', $data);
        $data = preg_replace('" &lt;pagebreak&gt;"', '', $data);
        $data = preg_replace('"&lt;pagebreak&gt;"', '', $data);
        #$data = preg_replace('"&lt;pagebreak&gt;"', '<span style="page-break-before: always;"></span>', $data);
        
        //Post Parse <noprint>
        $data = preg_replace('"&lt;noprint&gt;<br />"', '<div class="noprint">', $data);
        $data = preg_replace('"&lt;noprint&gt;"', '<div class="noprint">', $data);
        $data = preg_replace('"&lt;/noprint&gt;"', '</div>', $data);
        
        //Post parse <info>...</info> box
        $data = preg_replace('"&lt;info&gt;"', '<div class="info">', $data);
        $data = preg_replace('"&lt;/info&gt;"', '</div>', $data);

        //Post Parse, fix <auth>...</auth> tags
        //I do this post parse because for pre parse I have to do <html><div>... which is fine
        //But text_wiki wants <html> on its own line, so will not work the inline <auth->
        //I autodetect inline or own line by /n, so I can use div or span, because div/span make things look soo much different
        if ($info->is_authenticated) {
            //The first inline regex will not catch multi line <auth> but the second line will, so this works great!
            $data = preg_replace('"&lt;auth&gt;(.*?)&lt;/auth&gt;"', '<span class="auth_inline">$1</span>', $data); //Own Line
            $data = preg_replace('"&lt;auth&gt;(<br />|</p>)"', '<div class="auth">', $data); //Own Line
            $data = preg_replace('"&lt;auth&gt;"', '<div class="auth">', $data); //Own Line
            $data = preg_replace('"&lt;/auth&gt;"', '</div>', $data);
        }
        
        //Post Parse, fix <private>...</private> tags
        if ($info->is_authenticated && ($info->admin || $topic->tbl_post->created_by == $info->user_id)) {
            //The first inline regex will not catch multi line <priv> but the second line will, so this works great!
            $data = preg_replace('"&lt;priv&gt;(.*?)&lt;/priv&gt;"', '<span class="private_inline">$1</span>', $data); //Own Line
            $data = preg_replace('"&lt;priv&gt;(<br />|</p>)"', '<div class="private">', $data); //Own Line
            $data = preg_replace('"&lt;priv&gt;"', '<div class="private">', $data); //Own Line
            $data = preg_replace('"&lt;/priv&gt;"', '</div>', $data);
        }
        
        //Post Parse [\n] (new line, like <br />)
        $data = preg_replace('"\[\\\n\]"', '<br />', $data);

        //Post Parse <link url> tag
        #$data = preg_replace('"&lt;link (.*?)\|(.*?)&gt;"', "<a href='$1'>$2</a>", $data);

        return $data;
    }

    /*
     function pre_parse_include($matches)
     Replace <include> with actual raw wiki text
     mReschke 2010-09-07
    */
    public static function pre_parse_include($topic_id) {
        GLOBAL $info;

        $section = null;
        if (preg_match('" "', $topic_id)) {
            $tmp = explode(" ", $topic_id);
            $topic_id = $tmp[0];
            $section = $tmp[1];
        }
        
        if (is_numeric($topic_id)) {
            //Include another topic, if permissible
            $topic = new Tbl_topic;
            $topic->topic_id = $topic_id;
            $topic->tbl_post = Tbl_post::get_topic($info, $topic_id);
            $topic->perms = Tbl_perm::get_permissions($info, $topic->topic_id);            
            if (!Topic::has_perm($topic, 'READ') && $topic->tbl_post->created_by != $info->tbl_user->user_id) {
                return "";
            } else {
                //Remove any <exclude>...</exclude> content
                $topic->tbl_post->body = preg_replace('"<exclude>.*?</exclude>"sim', '', $topic->tbl_post->body);

                //Recursively process more <phpw> and <includes>
                //Parse for <phpw> here too because included text may recursively contain phpw which we want to expand
                //We do phpw here because what if a <phpw> item build a bunch of <includes>, we want to eval the <phpw>
                //first, then parse its <include> output, ah
                $topic->tbl_post->body = preg_replace_callback('"<phpw>.*?</phpw>"sim',
                    function($matches) {
                        ob_start();
                        eval(preg_replace('"<phpw>|</phpw>"', '', $matches[0]));
                        $return = ob_get_contents();
                        ob_end_clean();
                        return $return;
                    }, $topic->tbl_post->body);

                if (!$section) {
                    $body = $topic->tbl_post->body;
                } else {
                    if (preg_match_all('"<section '.$section.'>.*?</section>"sim', $topic->tbl_post->body, $matches)) {
                        $body = implode($matches[0]);
                    }
                }

                $body = preg_replace('"<include (.*?)>"ie', "Parser::pre_parse_include(\"\\1\")", $body);
                return $body;
            }
        }
    }

    /*
     function pre_parse_files($cmd)
     Create the PHP required to embed the filemanager.  Must run before the <php> rule is evaled
     mReschke 2010-10-01
    */
    public static function pre_parse_files($cmd) {
        GLOBAL $topic;
        
        $ret = '';
        $type = '';
        
        if ($cmd == '') {
            //Using just <files>
            $ret = "<php>Files::reset_default_view();
                \$files->embed = true;
                \$files->path = $topic->topic_id;
                \$files->hide_selection = true;
                load_files_code();
                load_files_view();</php>";
        
        } else {
            //Using <files parm1=x; parm2=x; ...>
            if (stristr($cmd, 'file=')) {
                //Just one file
                $type = 'single';
            } else {
                //Multip files, use file manager
                $type = 'multi';
                $ret = "<php>Files::reset_default_view();
                    \$files->embed = true;
                    \$files->path = $topic->topic_id;";
            }
    
            $params = explode(";", $cmd);
            if (count($params) > 0) {
                foreach ($params as $param) {
                    $param = trim($param);
                    if (stristr($param, "=")) {
                        $tmp = explode("=", $param);
                        $key = $tmp[0];
                        $value = $tmp[1];
                    } else {
                        $key = $param;
                        $value = null;
                    }
                    $key = strtolower($key);
                    
                    //Wildcards
                    #$value = eregi_replace("\#", $topic->topic_id, $value); //# with topic_id
                    $value = preg_replace('"\#"', $topic->topic_id, $value);
                    
                    
                    if ($type == 'multi') {
                        if ($key == 'path')          $ret .= "\n\$files->path = '$value';";
                        if ($key == 'filter')        $ret .= "\n\$files->filter = '$value';";
                        if ($key == 'noheader')      $ret .= "\n\$files->hide_header = true;";
                        if ($key == 'nomenu')        $ret .= "\n\$files->hide_menu = true;";
                        if ($key == 'nocontextmenu') $ret .= "\n\$files->hide_contextmenu = true;";
                        if ($key == 'nosubfolders')  $ret .= "\n\$files->hide_subfolders = true;";
                        if ($key == 'nonav')         $ret .= "\n\$files->hide_nav = true;";
                        if ($key == 'showhidden')    $ret .= "\n\$files->show_hidden = true;";
                        if ($key == 'nocolumns')     $ret .= "\n\$files->hide_columns = true;";
                        if ($key == 'noselection')   $ret .= "\n\$files->hide_selection = true;";
                        if ($key == 'nobackground')  $ret .= "\n\$files->hide_background = true;";
                        if ($key == 'view')          $ret .= "\n\$files->view = '$value';";
                    } elseif ($type == 'single') {
                        if ($key == 'file')          $single_file = $value;
                        if ($key == 'view')          $single_view = strtolower($value);
                        if ($key == 'title')         $single_title = $value;
                    }
                }
            }
            if ($type == 'multi') {
                $ret .= "
                    load_files_code();
                    load_files_view();</php>";
            } elseif ($type == 'single') {
                if ($single_file) {
                    if (!isset($single_view)) $single_view = 'link'; //Default single view
                    if (!isset($single_title)) $single_title = $single_file; //Default single title
                    if ($single_view == 'link') {
                        //Display file as link
                        $ret = "[local:/files/".preg_replace('" "', '+', $single_file)." $single_title]";
                    } elseif ($single_view == 'inline') {
                        //Innline file, include its contents before wiki parser
                        if (is_file(urldecode(Config::FILES_DIR.'/'.$single_file))) {
                            $fh = fopen(urldecode(Config::FILES_DIR.'/'.$single_file), 'r');
                            $ret = fread($fh, filesize(urldecode(Config::FILES_DIR.'/'.$single_file)));
                            fclose($fh);
                        } else {
                            $ret = 'File Not Found';
                        }
                    }
                }
            }
        }
        return $ret;
    }

    /*
     function pre_parse_search($query)
     Create the PHP required to embed the search page
     mReschke 2012-11-17
    */
    public static function pre_parse_search($query) {
        $ret = "<php>eval(Page::load_part('search'));
            \$search->uri = '$query';
            \$search->embed = true;
            load_search_code();
            load_search_view();</php>";
        return $ret;
    }

    /*
     function pre_parse_embed($matches)
     Replace <embed xxx yyy> with embed content
     mReschke 2013-05-03
    */
    public static function pre_parse_embed($param) {
        $return = "";
        $tmp = explode(" ", $param);
        if (count($tmp) >= 2) {
            $type = strtolower($tmp[0]);
            $location = $tmp[1];
        }

        if ($type && $location) {
            if ($type == 'url') {
                $rand = rand(1,999);
                $id = "embed_url_$rand";
                $return = "<html>\r\n<iframe id='$id' onload=\"resize_embed_url('$id')\" src='$location' style='width:100%'></iframe>\r\n</html>\r\n";
                #$return = "<html><object id='$id' type='text/html' onload=\"resize_embed_url('$id')\" data='$location' style='width:100%;' /></html>";
                
            }
        }
        return $return;
    }

    /*
     function post_parse_highlight($inline, $color, $data)
     Replace <highlight xxx>yyy</highlight> with embed content
     mReschke 2013-07-16
    */
    public static function post_parse_highlight($inline, $color, $data='') {
        $color = trim(strtolower($color));
        $default        = 'rgba(255,255,0,0.6)';
        $rgb['red']     = 'rgba(255,0,0,0.5)';
        $rgb['green']   = 'rgba(0,255,0,0.5)';
        $rgb['blue']    = 'rgba(0,0,255,0.5)';
        $rgb['yellow']  = 'rgba(255,255,0,0.6)';
        $rgb['cyan']    = 'rgba(0,255,255,0.5)';
        $rgb['magenta'] = 'rgba(255,0,255,0.5)';
        if ($color <> '') {
            if (isset($rgb[$color])) {
                $color = $rgb[$color];
            } elseif (preg_match('"#"', $color)) {
                $color = $color;
            }
        }
        if ($color == '') $color = $default;
        if ($inline) {
            return "<span class='highlight' style='background-color:$color'>$data</span>";
        } else {
            return "<div class='highlight' style='background-color:$color'>";
        }
    }

    /*
     function text_wiki_parser(Info $info, Topic $topic, $data)
     Parse raw wiki using Text_Wiki.  Data should be pre_parsed before being parsed by Text_Wiki and post_parsed after being parsed by Text_Wiki.
     mReschke 2010-09-07
    */
    public static function text_wiki_parser(Info $info, Topic $topic, $data, $disable_rules) {
        GLOBAL $files, $search, $view;
        # Text_wiki is an awesome wiki markup parser
        # It accepts wiki language and converts it into XHTML
        # Visit: http://wiki.ciaweb.net/yawiki/index.php?area=Text_Wiki&page=SamplePage
        
        // instantiate a Text_Wiki object with the default rule set
        $wiki = new Text_Wiki();
        
        // when rendering XHTML, make sure wiki links point to a
        // specific base URL
        $baseURL = "/topic/";
        
        $wiki->setRenderConf('xhtml', 'wikilink', 'view_url', $baseURL);
        $wiki->setRenderConf('xhtml', 'wikilink', 'new_url', $baseURL);

        # the url when viewing an article
        $wiki->setRenderConf('xhtml', 'freelink', 'view_url', $baseURL.'%s');

        # the url when a link is cliked to make a new article %s is the article name
        $wiki->setRenderConf('xhtml', 'freelink', 'new_url', $baseURL.'%s');
                
        #This allows embeding of PHP documents without parsing them
        #syntax: [[embed path/to/file.php]]
        #NOTE: includes cannot have / at the beginning, so make the 'base', '') for root
        #$wiki->setParseConf('embed', 'base', '');

        #$wiki->setParseConf('include', 'base', '/');
        #$wiki->enableRule('embed');     #disable for non domains admins
        $wiki->enableRule('xhtml');
        $wiki->enableRule('html');
        $wiki->disableRule('wikilink'); //Disable the PascalCase auto wikilink
        #$wiki->disableRule('break');

        // Disable Rules
        if (isset($disable_rules)) {
            foreach($disable_rules as $disable_rule) {
                $wiki->disableRule($disable_rule);
            }
        }

        # Set a bunch of CSS Style Sheets ID's and classes to use with mrticles.css
        #TOC
        $wiki->setRenderConf('xhtml', 'toc', 'div_id', 'toc');
        $wiki->setRenderConf('xhtml', 'toc', 'css_list', 'tocList');
        $wiki->setRenderConf('xhtml', 'toc', 'css_item', 'tocItems');
        $wiki->setRenderConf('xhtml', 'toc', 'title', "<div class='tocTitle'>Topic Content</div>");
        $wiki->setRenderConf('xhtml', 'toc', 'collapse', false);

        //WikiLink (Standard PascalCase links)
        $topic_ids = Tbl_topic::get_topic_ids_array();
        #$wiki->setRenderConf('xhtml', 'wikilink', 'pages', $topic_ids);     # for PascalCased article names
        #$wiki->setRenderConf('xhtml', 'wikilink', 'css', 'wikiLink');
        #$wiki->setRenderConf('xhtml', 'wikilink', 'css_new', 'wikiLink_new');
        #$wiki->setRenderConf('xhtml', 'wikilink', 'new_text', '');

        //FreeLink (like ((1)) or ((Some Article)) or ((1|display)) or ((Some Article|display))
        $wiki->setRenderConf('xhtml', 'freelink', 'pages', $topic_ids);     # for spaces in article names
        $wiki->setRenderConf('xhtml', 'freelink', 'css', 'freeLink');
        $wiki->setRenderConf('xhtml', 'freelink', 'css_new', 'freeLink_new');
        $wiki->setRenderConf('xhtml', 'freelink', 'new_text', '');
        $wiki->setRenderConf('xhtml', 'freelink', 'new_url', '/edit/newtopic/');

        #foreach ($NonStandardPages as $dd)  {
        #    echo $dd."<BR>";
        #}

        #Headings
        $wiki->setRenderConf('xhtml', 'heading', 'css_h1', 'heading1');
        $wiki->setRenderConf('xhtml', 'heading', 'css_h2', 'heading2');
        $wiki->setRenderConf('xhtml', 'heading', 'css_h3', 'heading3');
        $wiki->setRenderConf('xhtml', 'heading', 'css_h4', 'heading4');
        $wiki->setRenderConf('xhtml', 'heading', 'css_h5', 'heading5');
        $wiki->setRenderConf('xhtml', 'heading', 'css_h6', 'heading6');
        
        #BlockQuote
        $wiki->setRenderConf('xhtml', 'blockquote', 'css', 'blockQuote');

        #Code
        $wiki->setRenderConf('xhtml', 'code', 'css', 'code');
        $wiki->setRenderConf('xhtml', 'code', 'css_outer', 'code_outer');
        $wiki->setRenderConf('xhtml', 'code', 'css_header', 'code_header');
        
        #Textbox (mReschke custom rule)
        $wiki->setRenderConf('xhtml', 'textbox', 'css', 'textbox');
        $wiki->setRenderConf('xhtml', 'textbox', 'css_outer', 'textbox_outer');
        $wiki->setRenderConf('xhtml', 'textbox', 'css_header', 'textbox_header');
        
        #Image
        $wiki->setRenderConf('xhtml', 'image', 'base', '/');
        $wiki->setRenderConf('xhtml', 'image', 'css', 'image');
        $wiki->setRenderConf('xhtml', 'image', 'css_link', 'image_link');

        #Table
        $wiki->setRenderConf('xhtml', 'table', 'css_table', 'datatable');
        $wiki->setRenderConf('xhtml', 'table', 'css_table_simple', 'table_table');
        $wiki->setRenderConf('xhtml', 'table', 'css_tr', 'table_tr');
        $wiki->setRenderConf('xhtml', 'table', 'css_th', 'table_th');
        $wiki->setRenderConf('xhtml', 'table', 'css_td', 'table_td');

        #Table2
        #$wiki->setRenderConf('xhtml', 'table2', 'css_table', 'table_table');
        #$wiki->setRenderConf('xhtml', 'table2', 'css_tr', 'table_tr');
        #$wiki->setRenderConf('xhtml', 'table2', 'css_th', 'table_th');
        #$wiki->setRenderConf('xhtml', 'table2', 'css_td', 'table_td');
        
        #URL
        $wiki->setRenderConf('xhtml', 'url', 'images', false);  #display link, not actual image
        $wiki->setRenderConf('xhtml', 'url', 'target', '_blank');
        $wiki->setRenderConf('xhtml', 'url', 'css_inline', 'urlLink');  #url link CSS when useing just http://...
        $wiki->setRenderConf('xhtml', 'url', 'css_descr', 'urlLink'); #url link CSS when using [http://... name]
        #$wiki->setRenderConf('xhtml', 'url', 'css_img', 'urlLink');  #image link CSS
        #$wiki->setRenderConf('xhtml', 'url', 'css_footnote', 'urlLink');
        
        #Smiley
        #$wiki->setRenderConf('xhtml', 'smiley', 'prefix', Config::WEB_BASE_IMAGE_URL.'/theme/'.Config::THEME.'/images/smileys/icon_');
        $wiki->setRenderConf('xhtml', 'smiley', 'prefix', 'ignored-mreschke');
        #$wiki->setRenderConf('xhtml', 'smiley', 'extension', 'xx');
        #$wiki->setRenderConf('xhtml', 'smiley', 'css', 'xx');
        
        // transform the wiki text into XHTML
        $data = $wiki->transform($data, 'Xhtml');

        return $data;
    }

    /*
     function pre_save($body) string
     just before insert/update post body, call this function
     adjusts some wiki stuff for proper formatting
     mReschke 2010-09-07
    */
    public static function pre_save($body) {
        //Try to fix some main MS Office pasted strange characters
        $body = preg_replace('"…"', '...', $body);
        $body = preg_replace('"“"', '"', $body);
        $body = preg_replace('"”"', '"', $body);
        $body = preg_replace('"’"', "'", $body);
		$body = preg_replace('"–"', '-', $body);
        $body = preg_replace('"®"', '(r)', $body);
        $body = preg_replace('"©"', '(c)', $body);

        //Dont every allow </textarea>, it will break the edit page
        $body = preg_replace('"</textarea>"', '</!textarea>', $body);

        //Adjust inline <code> to its own line
        #$body = preg_replace('"(.)<code>"', "$1\n<code>", $body);
        #$body = preg_replace('"(.)</code>"', "$1\n</code>", $body);
        #$body = preg_replace('"<code>."', "<code>\n$1", $body);
        #$body = preg_replace('"</code>."', "</code>\n$1", $body);
        
        //Convert </textarea> to </!textarea>
        #$body = preg_replace('"<textarea (.*?)>"', "<!--textarea $1>-->", $body);
        #$body = preg_replace('"</textarea>"', "<!--</textarea>-->", $body);
        
        #$body = preg_replace('"\[\{(.*?)\}\](.*?)\[\{(.*?)\}\]"', "\n<html>\n<$1>\n<$2>\n<$3>\n</html>\n", $body);
        #$body = preg_replace('"\[\{(.|\n*?)\}\](.|\n*?)\[\{(.|\n*?)\}\]"', "\n<html>\n<$1>\n<$2>\n<$3>\n</html>\n", $body);
        #$body = preg_replace('"\[\{([A-Z][A-Z0-9]*)\b[^>]*\}\](.*?)\[\{/\1\}\]"', "\n<html>\n<$1>\n<$2>\n<$3>\n</html>\n", $body);
        
        #$body = preg_replace('"\[\{(.*?)\}\]"', "\n<html>\n<$1>\n", $body);
        #$body = preg_replace('"\[\{\/(.*?)\}\]"', "\n<\/$1>\n<html>\n", $body);
        
        #$data = preg_replace('"<(.|\n)*? \ >"', '', $data); //Strip HTML
        #<([A-Z][A-Z0-9]*)\b[^>]*>(.*?)</\1>
        return $body;
    }
}
