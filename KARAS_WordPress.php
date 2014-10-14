<?php

//Plugin Name: KARAS for WordPress
//Plugin URI: http://lightweightmarkuplanguage.com/
//Description: Convert KARAS syntax to HTML. This plugin works as filter and ignores any others except prepend_attachement.
//Author: XJINE
//Version: 0.9.1beta
//Author URI: http://lightweightmarkuplanguage.com/
//License: BSD

//Need PHP Version 5.2 or later.

define("KARASPluginDirectory", WP_PLUGIN_DIR . "/KARAS_WordPress/plugins");

require_once("KARAS.php");

add_action("admin_menu", "add_convert_KARAS_checkbox");
add_action("save_post", "save_convert_KARAS_custom_field");
remove_filter("the_content", "wpautop" );
remove_filter("the_excerpt", "wpautop" );
add_filter("the_content", "convert_KARAS");
add_filter("the_excerpt", "convert_KARAS");

function add_convert_KARAS_checkbox()
{
    add_meta_box("convert_KARAS",
                 "KARAS",
                 "html_source_for_convert_KARAS",
                 "post",
                 "side",
                 "high");
    add_meta_box("convert_KARAS",
                 "KARAS",
                 "html_source_for_convert_KARAS",
                 "page",
                 "side",
                 "high");
}
 
function html_source_for_convert_KARAS()
{
    $post_id = get_the_ID();
    $is_checked = get_post_meta($post_id, "convert_KARAS", true);
 
    print ("<label for=\"convet_KARAS\">Convert KARAS.</label><p>");

    if($is_checked == "true")
    {
        print("<input type=\"checkbox\" name=\"convert_KARAS\" checked>");
    }
    else
    {
        print("<input type=\"checkbox\" name=\"convert_KARAS\">");
    }

    print "</p>";
}
 
function save_convert_KARAS_custom_field($post_id)
{
    $is_checked = isset($_POST["convert_KARAS"]) ? "true" : "false";

    if (get_post_meta($post_id, "convert_KARAS", true) == "")
    {
        add_post_meta($post_id, "convert_KARAS", $is_checked, true);
    }
    else if ($is_checked != get_post_meta($post_id, "convert_KARAS", true))
    {
        update_post_meta($post_id, "convert_KARAS", $is_checked);
    }
    else if ($is_checked == "")
    {
        delete_post_meta($post_id, "convert_KARAS");
    }
}

function print_filters_for($filtername)
{
    global $wp_filter;
    print "<pre>";
    print_r($wp_filter[$filtername]);
    print "</pre>";
}

function convert_KARAS($content)
{
    $post_id = get_the_ID();
    $is_checked = get_post_meta($post_id, "convert_KARAS", true);

    if($is_checked == "true")
    {
        $content = get_the_content();
        $content = KARAS\KARAS::convert($content, KARASPluginDirectory, 2);
        apply_filters("prepend_attachment", $content);
        return $content;
    }
    else
    {
        return $content;
    }
}

// Copyright (c) 2014, Daiki Umeda(XJINE) - lightweightmarkuplanguage.com
// All rights reserved.
// 
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
// 
// * Redistributions of source code must retain the above copyright notice, this
//   list of conditions and the following disclaimer.
// 
// * Redistributions in binary form must reproduce the above copyright notice,
//   this list of conditions and the following disclaimer in the documentation
//   and/or other materials provided with the distribution.
// 
// * Neither the name of the copyright holder nor the names of its
//   contributors may be used to endorse or promote products derived from
//   this software without specific prior written permission.
// 
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
// AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
// IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
// DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
// FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
// DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
// SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
// CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
// OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
// OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

?>