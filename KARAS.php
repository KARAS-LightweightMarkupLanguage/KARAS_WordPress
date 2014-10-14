<?php

// Copyright (c) 2014, Daiki Umeda (XJINE) - lightweightmarkuplanguage.com
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

//Need PHP version 5.2 or later.

namespace KARAS;

class KARAS
{
    //Block group
    const RegexBlockGroup
        = "/^(?:(\\{\\{)(.*?)|\\}\\}.*?)$/mu";
    const RegexFigcaptionSummary
        = "/(?:^|\n)(=+)(.*?)(\n(?:(?:(?:\\||\\!)[\\|\\=\\>\\<]|\\=|\\-|\\+|\\;|\\>|\\<)|\n)|$)/su";

    //Block markup
    const RegexBlockquote
        = "/(?:^|\n)(\\>+)(.*?)(?:(\n\\>)|(\n(?:\n|\\=|\\-|\\+|\\;|(?:(?:\\||\\!)[\\|\\=\\>\\<])))|$)/su";
    const RegexTableBlock
        = "/(?:^|\n)((?:\\||\\!)(?:\\||\\>|\\<|\\=).*?)(\n(?!(?:\\||\\!)[\\|\\=\\>\\<])|$)/su";
    const RegexTableCell
        = "/(\\\\*)(\\||\\!)(\\||\\>|\\<|\\=)/su";
    const RegexList
        = "/(?:^|\n)((?:\\-|\\+)+)(.*?)(?:(\n(?:\\-|\\+))|(\n(?:\n|\\;|\\=))|$)/su";
    const RegexDefList
        = "/(?:^|\n)(\\;+)(.*?)(?:(\n\\;)|(\n(?:\n|\\=))|$)/su";
    const RegexHeading
        = "/(?:^|\n)(\\=+)(.*?)(\n\\=|\n{2,}|$)/su";
    //It is important to check '\n{2,}' first, to exclude \n chars.
    const RegexBlockLink
        = "/(?:\n{2,}|^\n*)\\s*\\({2,}.+?(?:\n{2,}|$)/su";
    const RegexParagraph
        = "/(?:\n{2,}|^\n*)(\\s*(<*).+?)(?:\n{2,}|$)/su";

    //Inline markup
    const RegexInlineMarkup
        = "/(\\\\*)(\\*{2,}|\\/{2,}|\\_{2,}|\\%{2,}|\\@{2,}|\\?{2,}|\\\${2,}|`{2,}|\\'{2,}|\\,{2,}|\"{2,}|\\({2,}[\t\v\f\x{0020}\x{00A0}]*\\(*|\\){2,}[\t\v\f\x{0020}\x{00A0}]*\\)*|<{2,}[\x{0020}\x{00A0}]*<*|>{2,}[\x{0020}\x{00A0}]*>*)/su";

    const RegexLineBreak
        = "/(\\\\*)(\\~(?:\n|$))/u";

    //Other syntax
    const RegexPlugin
         = "/(\\\\*)((\\[{2,}[\x{0020}\x{00A0}]*\\[*)|(\\]{2,}[\x{0020}\x{00A0}]*\\]*))/u";
    const RegexCommentOut
        = "/(\\\\*)(\\#{2,})/su";
    const RegexSplitOption
         = "/(\\\\*)(\\:{2,3})/su";

    //Other
    const RegexEscape
        = "/\\\\+/su";
    const RegexProtocol
        = "/:{1,1}(\/{2,})/su";
    const RegexWhiteSpace
        = "/\\s/u";
    const RegexWhiteSpaceLine
        = "/^[\t\v\f\x{0020}\x{00A0}]+$/mu";
    const RegexLineBreakCode
        = "/\r\n|\r|\n/u";
    const RegexBlankLine
        = "/^\n/mu";
    const RegexPreElement
        = "/(<pre\\s*.*?>)|<\\/pre>/imu";
    const RegexLinkElement
        = "/(?:<a.*?>.*?<\\/a>)|(?:<img.*?>)|(?:<video.*?>.*?<\\/video>)|(?:<audio.*?>.*?<\\/audio>)|<object.*?>.*?<\\/object>/iu";
    const RegexStringTypeAttribute
        = "/([^\\s]+?)\\s*=\\s*\"(.+?)\"/u";
    const RegexFileExtension
        = "/.+\\.(.+?)$/u";

    const BlockGroupTypeUndefined = -1;
    const BlockGroupTypeDiv = 0;
    const BlockGroupTypeDetails = 8;
    const BlockGroupTypeFigure = 9;
    const BlockGroupTypePre = 10;
    const BlockGroupTypeCode = 11;
    const BlockGroupTypeKbd = 12;
    const BlockGroupTypeSamp = 13;

    public static $ReservedBlockGroupTypes = 
    [
        "div", "header", "footer", "nav",
        "article", "section", "aside", "address",
        "details", "figure",
        "pre", "code", "kbd", "samp"
    ];

    const InlineMarkupTypeDefAbbr = 5;
    const InlineMarkupVarCode = 6;
    const InlineMarkupKbdSamp = 7;
    const InlineMarkupTypeSupRuby = 8;
    const InlineMarkupTypeLinkOpen = 11;
    const InlineMarkupTypeLinkClose = 12;
    const InlineMarkupTypeInlineGroupOpen = 13;
    const InlineMarkupTypeInlineGroupClose = 14;

    public static $InlineMarkupSets = array
    (    
        array("*", "b", "strong"),
        array("/", "i", "em"),
        array("_", "u", "ins"),
        array("%", "s", "del"),
        array("@", "cite", "small"),
        array("?", "dfn", "abbr"),
        array("$", "kbd", "samp"),
        array("`", "var", "code"),
        array("'", "sup", "ruby"),
        array(",", "sub"),
        array("\"", "q"),
        array("(",  "a"),
        array(")",  "a"),
        array("<",  "span"),
        array(">",  "span"),
    );

    const MediaTypeImage = 0;
    const MediaTypeAudio = 1;
    const MediaTypeVideo = 2;
    const MediaTypeUnknown = 3;

    public static $MediaExtensions = array
    (
        "/bmp|bitmap|gif|jpg|jpeg|png/i",
        "/aac|aiff|flac|mp3|ogg|wav|wave/i",
        "/asf|avi|flv|mov|movie|mpg|mpeg|mp4|ogv|webm/i"
    );

    public static $ReservedObjectAttributes = array
    (
        "width", "height", "type", "typemustmatch", "name", "usemap", "form" 
    );

    const ListTypeUl = true;
    const ListTypeOl = false;

    const PluginDirectory = "./plugins";
    const DefaultEscapeCode = "escpcode";





    public static function convert
        ($text, $pluginDirectory, $startLevelOfHeading)
    {
        $escapeCode = KARAS::generateSafeEscapeCode($text, KARAS::DefaultEscapeCode);
        $lineBreakCode = KARAS::getDefaultLineBreakCode($text);
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);

        $text = KARAS::replaceTextInPluginSyntax($text, ">", $escapeCode . ">");
        $text = KARAS::replaceTextInPluginSyntax($text, "{", $escapeCode . "{");
        $text = KARAS::replaceTextInPreElement($text, "\n", $escapeCode . "\n" . $escapeCode);
        $hasUnConvertedBlockquote = true;
        while ($hasUnConvertedBlockquote)
        {
            $text = KARAS::convertBlockGroup($text, $escapeCode);
            KARAS::convertBlockquote($text, $hasUnConvertedBlockquote, $escapeCode);
        }

        $pluginManager = new PluginManager($pluginDirectory);
        $text = str_replace($escapeCode, "", $text);
        $text = KARAS::replaceTextInPreElement($text, "[",  $escapeCode . "[");
        $text = KARAS::convertPlugin($text, $pluginManager);

        $text = KARAS::replaceTextInPreElement($text, "\n", $escapeCode . "\n" . $escapeCode);
        $hasUnConvertedBlockquote = true;
        while ($hasUnConvertedBlockquote)
        {
            $text = KARAS::convertBlockGroup($text, $escapeCode);
            KARAS::convertBlockquote($text, $hasUnConvertedBlockquote, $escapeCode);
        }

        $text = KARAS::replaceTextInPreElement($text, "#", $escapeCode . "#");
        $text = KARAS::convertCommentOut($text);
        $text = KARAS::convertWhiteSpaceLine($text);
        $text = KARAS::convertProtocol($text);
        $text = KARAS::convertTable($text);
        $text = KARAS::convertList($text);
        $text = KARAS::convertDefList($text);
        $text = KARAS::convertHeading($text, $startLevelOfHeading);
        $text = KARAS::convertBlockLink($text);
        $text = KARAS::convertParagraph($text);
        $text = KARAS::reduceBlankLine($text);

        $text = str_replace($escapeCode, "", $text);
        $text = KARAS::replaceTextInPreElement($text, "\\",  $escapeCode);
        $text = KARAS::reduceEscape($text);
        $text = KARAS::replaceTextInPreElement($text, $escapeCode, "\\");
        $text = str_replace("\n", $lineBreakCode, $text);

        return $text;
    }

    public static function generateSafeEscapeCode($text, $escapeCode)
    {
        while (true)
        {
            if (mb_strpos($text, $escapeCode) === false)
            {
                break;
            }

            $escapeCode = mb_substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 8);
        }

        return $escapeCode;
    }

    public static function getDefaultLineBreakCode($text)
    {
        $match;
        $matchIsSuccess = preg_match
            (KARAS::RegexLineBreakCode, $text, $match, PREG_OFFSET_CAPTURE);

        if ($matchIsSuccess === 1)
        {
            return $match[0][0];
        }
        else
        {
            return "\n";
        }
    }

    public static function replaceTextInPluginSyntax($text, $oldText, $newText)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiEscapes = 1;
        $mgiMarks = 2;
        $mgiOpenMarks = 3;
        //$mgiCloseMarks = 4;

        $matchStack = array();
        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexPlugin, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            if (strlen($match[$mgiEscapes][0]) % 2 == 1)
            {
                $nextMatchIndex = $match[$mgiMarks][1] + 1;
                continue;
            }

            if (strlen($match[$mgiOpenMarks][0]) != 0)
            {
                $pluginMatch  = new PluginMatch();
                $pluginMatch->index = $match[$mgiMarks][1];
                array_unshift($matchStack, $pluginMatch);
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            if (count($matchStack) == 0)
            {
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            $preMatch = array_shift($matchStack);
            $markedupText = substr
                ($text, $preMatch->index, $match[$mgiMarks][1] - $preMatch->index);
            $markedupText = str_replace($oldText, $newText, $markedupText);
            $text = KARAS::removeAndInsertText($text,
                                               $preMatch->index,
                                               $match[$mgiMarks][1] - $preMatch->index,
                                               $markedupText);
            $nextMatchIndex = $preMatch->index + strlen($markedupText);
        }

        return $text;
    }

    public static function replaceTextInPreElement($text, $oldText, $newText)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiOpenPreElement = 1;

        $matchStack = array();
        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexPreElement, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            if (empty($match[$mgiOpenPreElement][0]) == false)
            {
                $index = $match[$mgiOpenPreElement][1] + strlen($match[$mgiOpenPreElement][0]);
                array_unshift($matchStack, $index);
                $nextMatchIndex = $index;
                continue;
            }

            if (count($matchStack) == 0)
            {
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            $preTextStart = array_shift($matchStack);
            $preTextEnd = $match[$mgiAllText][1];
            $preText = substr($text, $preTextStart, $preTextEnd - $preTextStart);
            $preText = str_replace($oldText, $newText, $preText);
            $text = KARAS::removeAndInsertText
                ($text, $preTextStart, $preTextEnd - $preTextStart, $preText);
            $nextMatchIndex = $preTextStart + strlen($newText) + strlen($match[$mgiAllText][0]);
        }

        return $text;
    }





    public static function encloseWithLinebreak($text)
    {
        return "\n" . $text . "\n";
    }

    public static function escapeHTMLSpecialCharacters($text)
    {
        $text = str_replace("&", "&amp;", $text);
        $text = str_replace("\"", "&#34;", $text);
        $text = str_replace("'", "&#39;", $text);
        $text = str_replace("<", "&lt;", $text);
        $text = str_replace(">", "&gt;", $text);
        return $text;
    }

    public static function removeAndInsertText($text, $index, $removeLength, $newText)
    {
        return substr($text, 0, $index) . $newText . substr($text, $index + $removeLength);
    }

    public static function removeWhiteSpace($text)
    {
        return preg_replace(KARAS::RegexWhiteSpace, "", $text);
    }

    public static function splitOption($text, &$isSpecialOption)
    {
        //match group index.
        //$mgiAllText = 0;
        $mgiEscapes = 1;
        $mgiMarks = 2;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match(KARAS::RegexSplitOption,
                                         $text,
                                         $match,
                                         PREG_OFFSET_CAPTURE,
                                         $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                return array(trim($text));
            }

            if (strlen($match[$mgiEscapes][0]) % 2 == 1)
            {
                $nextMatchIndex = $match[$mgiMarks][1] + 1;
                continue;
            }

            if(strlen($match[$mgiMarks][0]) == 3)
            {
                $isSpecialOption = true;
            }
            else
            {
                $isSpecialOption = false;   
            }

            return array(trim(substr($text, 0, $match[$mgiMarks][1])),
                         trim(substr($text, $match[$mgiMarks][1] + strlen($match[$mgiMarks][0]))));
        }
    }

    public static function splitOptions($text, &$hasSpecialOption)
    {
        $options = array();
        $restText = trim($text);

        while (true)
        {
            $isSpecialOption = false;
            $splitResult = KARAS::splitOption($restText, $isSpecialOption);
            
            if (count($splitResult) == 1)
            {
                $options[] = $restText;
                break;
            }

            if($isSpecialOption == true)
            {
                $options[] = $splitResult[0];
                $options[] = $splitResult[1];
                $hasSpecialOption = true;
                break;
            }

            $options[] = $splitResult[0];
            $restText = $splitResult[1];
        }

        return $options;
    }





    public static function convertPlugin($text, $pluginManager)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiEscapes = 1;
        $mgiMarks = 2;
        $mgiOpenMarks = 3;
        $mgiCloseMarks = 4;

        $matchStack = array();
        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexPlugin, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            if (strlen($match[$mgiEscapes][0]) % 2 == 1)
            {
                $nextMatchIndex = $match[$mgiMarks][1] + 1;
                continue;
            }

            if (strlen($match[$mgiOpenMarks][0]) != 0)
            {
                $pluginMatch = new PluginMatch();
                $pluginMatch->index = $match[$mgiMarks][1];
                $pluginMatch->marks = $match[$mgiMarks][0];
                array_unshift($matchStack, $pluginMatch);
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            if (count($matchStack) == 0)
            {
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            $preMatch = array_shift($matchStack);
            $markedupTextIndex = $preMatch->index + strlen($preMatch->marks);
            $markedupText = substr
                ($text, $markedupTextIndex, $match[$mgiAllText][1] - $markedupTextIndex);
            $openMarks = KARAS::removeWhiteSpace($preMatch->marks);
            $closeMarks = KARAS::removeWhiteSpace($match[$mgiCloseMarks][0]);
            $newText = KARAS::constructPluginText
                ($text, $markedupText, $openMarks, $closeMarks, $pluginManager);
            $markDiff = strlen($openMarks) - strlen($closeMarks);

            if ($markDiff > 0)
            {
                $openMarks = substr($openMarks, 0, $markDiff);
                $closeMarks = "";

                if ($markDiff > 1)
                {
                    $preMatch->marks = substr($openMarks, 0, $markDiff);
                    array_unshift($matchStack, $preMatch);
                }
            }
            else
            {
                $openMarks = "";
                $closeMarks = substr($closeMarks, 0, -$markDiff);
            }

            $newText = $openMarks . $newText . $closeMarks;
            //It is important to trim close marks to exclude whitespace out of syntax.
            $text = KARAS::removeAndInsertText($text,
                                               $preMatch->index,
                                               $match[$mgiAllText][1]
                                               + strlen(trim($match[$mgiAllText][0]))
                                               - $preMatch->index,
                                               $newText);
            $nextMatchIndex = $preMatch->index + strlen($newText) - strlen($closeMarks);
        }

        return $text;
    }

    private static function constructPluginText
        ($text, $markedupText, $openMarks, $closeMarks, $pluginManager)
    {
        $hasSpecialOption = false;
        $markedupTexts = KARAS::splitOptions($markedupText, $hasSpecialOption);
        $markedupText = null;
        $pluginName = $markedupTexts[0];
        $options = array();

        //Remove plugin name from option.
        if (count($markedupTexts) > 1)
        {
            $options = array_slice($markedupTexts, 1);
        }

        if($hasSpecialOption == true)
        {
            $markedupText = array_pop($options);
        }

        if (strlen($openMarks) > 2 && strlen($closeMarks) > 2)
        {
            return KARAS::constructActionTypePluginText
                        ($pluginManager, $pluginName, $options, $markedupText, $text);
        }
        else
        {
            return KARAS::constructConvertTypePluginText
                        ($pluginManager, $pluginName, $options, $markedupText);
        }
    }

    private static function constructActionTypePluginText
        ($pluginManager, $pluginName, $options, $markedupText, $text)
    {
        $plugin = $pluginManager->getPlugin($pluginName);

        if ($plugin == null)
        {
            return " Plugin \"" . $pluginName . "\" has wrong. ";
        }

        try
        {
            if($plugin->hasMethod("action") == true)
            {
                return $plugin->getMethod("action")->invoke(null, $options, $markedupText, $text);
            }
            else
            {
                return " Plugin \"" . $pluginName . "\" has wrong. ";
            }
        }
        catch(Exception $e)
        {
            return " Plugin \"" . $pluginName . "\" has wrong. ";
        }
    }

    private static function constructConvertTypePluginText
        ($pluginManager, $pluginName, $options, $markedupText)
    {
        $plugin = $pluginManager->getPlugin($pluginName);

        if ($plugin == null)
        {
            return " Plugin \"" . $pluginName . "\" has wrong. ";
        }

        try
        {
            if($plugin->hasMethod("convert") == true)
            {
                return $plugin->getMethod("convert")->invoke(null, $options, $markedupText);
            }
            else
            {
                return " Plugin \"" . $pluginName . "\" has wrong. ";
            }
        }
        catch(Exception $e)
        {
            return " Plugin \"" . $pluginName . "\" has wrong. ";
        }
    }





    public static function convertBlockGroup($text, $escapeCode)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiOpenMarks = 1;
        $mgiOption = 2;

        $match;
        $nextMatchIndex = 0;
        $matchStack = array();
        $unhandledGroupClose = null;
        $groupsInPreCodeKbdSamp = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexBlockGroup, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                if ($groupsInPreCodeKbdSamp > 0 && $unhandledGroupClose != null)
                {
                    $match = $unhandledGroupClose;
                    $groupsInPreCodeKbdSamp = 0;
                }
                else
                {
                    break;
                }
            }

            if (empty($match[$mgiOpenMarks][0]) == false)
            {
                $blockGroupMatch = KARAS::constructBlockGroupMatch
                                                            ($match[$mgiAllText][1],
                                                            strlen($match[$mgiAllText][0]),
                                                            $match[$mgiOption][0]);

                if ($blockGroupMatch->type >= KARAS::BlockGroupTypePre)
                {
                    $groupsInPreCodeKbdSamp += 1;

                    if ($groupsInPreCodeKbdSamp == 1)
                    {
                        array_unshift($matchStack, $blockGroupMatch);
                    }
                }
                else
                {
                    //if pre or code group is open.
                    if ($groupsInPreCodeKbdSamp > 0)
                    {
                        $groupsInPreCodeKbdSamp += 1;
                    }
                    else
                    {
                        array_unshift($matchStack, $blockGroupMatch);
                    }
                }

                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            if (count($matchStack) == 0)
            {
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            if ($groupsInPreCodeKbdSamp > 1)
            {
                $groupsInPreCodeKbdSamp -= 1;
                $unhandledGroupClose = $match;
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            $preMatch = array_shift($matchStack);
            $newOpenText = "";
            $newCloseText = "";
            KARAS::constructBlockGroupText($preMatch, $newOpenText, $newCloseText);

            //Note, it is important to exclude linebreak code.
            $markedutTextIndex = $preMatch->index + $preMatch->length + 1;
            $markedupText = substr
                ($text, $markedutTextIndex, $match[$mgiAllText][1] - $markedutTextIndex - 1);

            switch ($preMatch->type)
            {
                case KARAS::BlockGroupTypeDetails:
                    {
                        $markedupText = 
                            KARAS::convertFigcaptionSummary($markedupText, "summary");
                        break;
                    }
                case KARAS::BlockGroupTypeFigure:
                    {
                        $markedupText = 
                            KARAS::convertFigcaptionSummary($markedupText, "figcaption");
                        break;
                    }
                case KARAS::BlockGroupTypePre:
                case KARAS::BlockGroupTypeCode:
                case KARAS::BlockGroupTypeKbd:
                case KARAS::BlockGroupTypeSamp:
                    {
                        $markedupText = KARAS::escapeHTMLSpecialCharacters($markedupText);
                        $markedupText = str_replace("\n", $escapeCode . "\n" . $escapeCode, $markedupText);
                        $groupsInPreCodeKbdSamp = 0;
                        break;
                    }
            }

            $newText = $newOpenText . $markedupText . $newCloseText;
            $text = KARAS::removeAndInsertText($text,
                                               $preMatch->index,
                                               $match[$mgiAllText][1]
                                               + strlen($match[$mgiAllText][0])
                                               - $preMatch->index,
                                               $newText);
            $nextMatchIndex = $preMatch->index + strlen($newText);
        }

        return $text;
    }

    private static function constructBlockGroupMatch($index, $textLength, $optionText)
    {
        $blockGroupMatch = new BlockGroupMatch();
        $blockGroupMatch->index = $index;
        $blockGroupMatch->length = $textLength;

        $hasSpecialOption = false;
        $options = KARAS::splitOptions($optionText, $hasSpecialOption);

        if (count($options) > 0)
        {
            $groupType = $options[0];
            $blockGroupMatch->type = KARAS::getGroupType($groupType);

            if ($blockGroupMatch->type == KARAS::BlockGroupTypeUndefined)
            {
                $blockGroupMatch->type = KARAS::BlockGroupTypeDiv;
                $blockGroupMatch->option = $groupType;
            }
        }

        if (count($options) > 1)
        {
            $blockGroupMatch->option = $options[1];
        }

        return $blockGroupMatch;
    }

    private static function getGroupType($groupTypeText)
    {
        for ($i = 0; $i < count(KARAS::$ReservedBlockGroupTypes); $i += 1)
        {
            if (strcasecmp($groupTypeText, KARAS::$ReservedBlockGroupTypes[$i]) == 0)
            {
                return $i;
            }
        }

        return KARAS::BlockGroupTypeUndefined;
    }

    private static function constructBlockGroupText
        ($groupOpen, &$newOpenText, &$newCloseText)
    {
        $newCloseText = "</" . KARAS::$ReservedBlockGroupTypes[$groupOpen->type] . ">";
        $optionText = "";

        if (strlen($groupOpen->option) != 0)
        {
            $optionText = " class=\"" . $groupOpen->option . "\"";
        }

        $newOpenText = "<" . KARAS::$ReservedBlockGroupTypes[$groupOpen->type] . $optionText . ">";

        if ($groupOpen->type >= KARAS::BlockGroupTypePre)
        {
            if ($groupOpen->type >= KARAS::BlockGroupTypeCode)
            {
                $newOpenText = "<pre" . $optionText . ">" . $newOpenText;
                $newCloseText .= "</pre>";
            }

            $newOpenText = "\n" . $newOpenText;
            $newCloseText = $newCloseText . "\n";
        }
        else
        {
            $newOpenText = KARAS::encloseWithLinebreak($newOpenText) . "\n";
            $newCloseText = "\n" . KARAS::encloseWithLinebreak($newCloseText);
        }
    }

    private static function convertFigcaptionSummary($text, $element)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiMarks = 1;
        $mgiMarkedupText = 2;
        $mgiBreaks = 3;

        $maxLevelOfHeading = 6;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match(KARAS::RegexFigcaptionSummary,
                                         $text,
                                         $match,
                                         PREG_OFFSET_CAPTURE,
                                         $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $newText = "";
            $level = strlen($match[$mgiMarks][0]);

            if ($level >= $maxLevelOfHeading + 1)
            {
                $newText = KARAS::encloseWithLinebreak("<hr>");
            }
            else
            {
                //Note, it is important to convert inline markups first,
                //to convert inline markup's options first.
                $markedupText = KARAS::convertInlineMarkup($match[$mgiMarkedupText][0]);
                $hasSpecialOption = false;
                $markedupTexts = KARAS::splitOptions($markedupText, $hasSpecialOption);
                $id = "";

                if (count($markedupTexts) > 1)
                {
                    $id = " id=\"" . $markedupTexts[1] . "\"";
                }

                $newText = KARAS::encloseWithLinebreak
                    ("<" . $element . $id .">" . $markedupTexts[0] . "</" . $element . ">");
            }

            $nextMatchIndex = $match[$mgiAllText][1] + strlen($newText);
            $text = KARAS::removeAndInsertText($text,
                                               $match[$mgiAllText][1],
                                               strlen($match[$mgiAllText][0])
                                               - strlen($match[$mgiBreaks][0]),
                                               KARAS::encloseWithLinebreak($newText));
        }

        return $text;
    }

    public static function convertBlockquote(&$text, &$hasUnConvertedBlockquote, $escapeCode)
    {
        //match group index.
        $mgiAllText = 0;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexBlockquote, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                if ($nextMatchIndex == 0)
                {
                    $hasUnConvertedBlockquote = false;
                }
                else
                {
                    $hasUnConvertedBlockquote = true;
                }

                break;
            }

            $sequentialBlockquotes = array();
            $indexOfBlockquoteStart = $match[$mgiAllText][1];
            $indexOfBlockquoteEnd = KARAS::constructSequentialBlockquotes
                ($text, $indexOfBlockquoteStart, $sequentialBlockquotes);
            
            $newText = KARAS::constructBlockquoteText($sequentialBlockquotes);
            $newtext = KARAS::replaceTextInPreElement
                ($newText, "\n", $escapeCode . "\n" . $escapeCode);
            $newText = KARAS::encloseWithLinebreak($newText);
            $nextMatchIndex = $indexOfBlockquoteStart + strlen($newText);
            $text = KARAS::removeAndInsertText($text,
                                               $indexOfBlockquoteStart,
                                               $indexOfBlockquoteEnd - $indexOfBlockquoteStart,
                                               KARAS::encloseWithLinebreak($newText));
        }
    }

    private static function constructSequentialBlockquotes
        ($text, $indexOfBlockquoteStart, &$blockquotes)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiMarks = 1;
        $mgiMarkedupText = 2;
        $mgiNextMarks = 3;
        $mgiBreaks = 4;

        $match;
        $nextMatchIndex = $indexOfBlockquoteStart;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexBlockquote, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $level = strlen($match[$mgiMarks][0]);
            $markedupText = $match[$mgiMarkedupText][0];

            if (count($blockquotes) == 0)
            {
                $sequentialBlockquote = new SequentialBlockquote();
                $sequentialBlockquote->level = $level;
                $sequentialBlockquote->text = trim($markedupText);
                $blockquotes[] = $sequentialBlockquote;
            }
            else
            {
                $previousBlockquote = $blockquotes[count($blockquotes) - 1];
                $previousLevel = $previousBlockquote->level;

                if ($level != $previousLevel)
                {
                    $sequentialBlockquote = new SequentialBlockquote();
                    $sequentialBlockquote->level = $level;
                    $sequentialBlockquote->text = trim($markedupText);
                    $blockquotes[] = $sequentialBlockquote;
                }
                else
                {
                    if (strlen($previousBlockquote->text) != 0)
                    {
                        $previousBlockquote->text .= "\n";
                    }

                    $previousBlockquote->text .= trim($markedupText);
                }
            }

            if (empty($match[$mgiNextMarks][0]) == true)
            {
                return $match[$mgiAllText][1] 
                       + strlen($match[$mgiAllText][0])
                       - (empty($match[$mgiBreaks][0]) ? 0 : strlen($match[$mgiBreaks][0]));
            }

            $nextMatchIndex = $match[$mgiNextMarks][1];
        }

        return -1;
    }

    private static function constructBlockquoteText($sequentialBlockquotes)
    {
        $blockquoteText = "";

        for ($i = 0; $i < $sequentialBlockquotes[0]->level; $i += 1)
        {
            $blockquoteText .= "<blockquote>\n\n";
        }

        $blockquoteText .= $sequentialBlockquotes[0]->text;

        for ($i = 1; $i < count($sequentialBlockquotes); $i += 1)
        {
            $levelDiff = $sequentialBlockquotes[$i]->level
                         - $sequentialBlockquotes[$i - 1]->level;

            if ($levelDiff > 0)
            {
                for ($j = 0; $j < $levelDiff; $j += 1)
                {
                    $blockquoteText .= "\n\n<blockquote>";
                }
            }
            else
            {
                for ($j = $levelDiff; $j < 0; $j += 1)
                {
                    $blockquoteText .= "\n\n</blockquote>";
                }
            }

            $blockquoteText .= "\n\n" . $sequentialBlockquotes[$i]->text;
        }

        for ($i = 0; $i < $sequentialBlockquotes[count($sequentialBlockquotes) - 1]->level; $i += 1)
        {
            $blockquoteText .= "\n\n</blockquote>";
        }

        return $blockquoteText;
    }





    public static function convertCommentOut($text)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiEscapes = 1;
        $mgiMarks = 2;

        $match;
        $nextMatchIndex = 0;
        $indexOfOpenMarks = 0;
        $markIsOpen = false;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexCommentOut, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            if (strlen($match[$mgiEscapes][0]) % 2 == 1)
            {
                $nextMatchIndex = $match[$mgiMarks][1] + 1;
                continue;
            }

            if ($markIsOpen == false)
            {
                $markIsOpen = true;
                $indexOfOpenMarks = $match[$mgiMarks][1];
                $nextMatchIndex = $indexOfOpenMarks + strlen($match[$mgiMarks][0]);
                continue;
            }

            $text = substr($text, 0, $indexOfOpenMarks)
                    . substr($text, 
                                $indexOfOpenMarks
                                + $match[$mgiAllText][1]
                                + strlen($match[$mgiAllText][0])
                                - $indexOfOpenMarks);
            $markIsOpen = false;
            $nextMatchIndex = $indexOfOpenMarks;
        }

        return $text;
    }

    public static function convertWhiteSpaceLine($text)
    {
        //match group index.
        $mgiAllText = 0;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match(KARAS::RegexWhiteSpaceLine,
                                         $text,
                                         $match,
                                         PREG_OFFSET_CAPTURE,
                                         $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $newText = "\n";

            $nextMatchIndex = $match[$mgiAllText][1] + strlen($newText);
            $text = KARAS::removeAndInsertText
                ($text, $match[$mgiAllText][1], strlen($match[$mgiAllText][0]), $newText);
        }

        return $text;
    }

    public static function convertProtocol($text)
    {
        //match group index.
        //$mgiAllText = 0
        $mgiMarks = 1;

        $match = null;
        $nextMatchIndex = 0;

        while(true)
        {
            $matchIsSuccess = preg_match(KARAS::RegexProtocol,
                                         $text,
                                         $match,
                                         PREG_OFFSET_CAPTURE,
                                         $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $newText = "";

            for($i = 0; $i < strlen($match[$mgiMarks][0]); $i +=1)
            {
                $newText .= "\\/";
            }

            $nextMatchIndex = $match[$mgiMarks][1] + strlen($newText);
            $text = KARAS::removeAndInsertText($text,
                                               $match[$mgiMarks][1],
                                               strlen($match[$mgiMarks][0]),
                                               $newText);
        }

        return $text;
    }

    public static function convertTable($text)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiTableText = 1;
        $mgiBreaks = 2;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess =  preg_match
                (KARAS::RegexTableBlock, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $cells = KARAS::constructTableCells($match[$mgiTableText][0]);
            $newText = KARAS::constructTableText($cells);
            $newText = KARAS::encloseWithLinebreak($newText);
            $nextMatchIndex = $match[$mgiAllText][1] + strlen($newText);
            $text = KARAS::removeAndInsertText($text, 
                                               $match[$mgiAllText][1],
                                               strlen($match[$mgiAllText][0])
                                               - strlen($match[$mgiBreaks][0]),
                                               KARAS::encloseWithLinebreak($newText));
        }

        return $text;
    }

    private static function constructTableCells($tableBlock)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiEscapes = 1;
        $mgiCellType = 2;
        $mgiTextAlign = 3;

        //like '||' or any other...
        $tableCellMarkLength = 2;

        $tableLines = explode("\n", $tableBlock);
        $cells = array();

        for ($i = 0; $i < count($tableLines); $i += 1)
        {
            $tableLine = $tableLines[$i];
            $cells[$i] = array();
            $match;
            $markedupText = "";
            $nextMatchIndex = 0;

            while (true)
            {
                $matchIsSuccess = preg_match(KARAS::RegexTableCell,
                                             $tableLine,
                                             $match,
                                             PREG_OFFSET_CAPTURE,
                                             $nextMatchIndex);

                if ($matchIsSuccess === 0)
                {
                    $markedupText = substr($tableLine, $nextMatchIndex);
                    
                    if (count($cells[$i]) > 0)
                    {
                        KARAS::setTableCellTextAndBlank
                            ($cells[$i][count($cells[$i]) - 1], $markedupText);
                    }

                    break;
                }

                if (strlen($match[$mgiEscapes][0]) % 2 == 1)
                {
                    $nextMatchIndex = $match[$mgiCellType][1] + 1;
                    continue;
                }

                $cell = new TableCell();
                KARAS::setTableCellTypeAndAlign
                    ($cell, $match[$mgiCellType][0], $match[$mgiTextAlign][0]);
                $markedupText = substr
                    ($tableLine, $nextMatchIndex, $match[$mgiAllText][1] - $nextMatchIndex);

                if (count($cells[$i]) > 0)
                {
                    KARAS::setTableCellTextAndBlank
                        ($cells[$i][count($cells[$i]) - 1], $markedupText);
                }

                $cells[$i][] = $cell;
                $nextMatchIndex = $match[$mgiAllText][1] + $tableCellMarkLength;
            }
        }

        return $cells;
    }

    private static function setTableCellTypeAndAlign($cell, $cellTypeMark, $textAlignMark)
    {
        if ($cellTypeMark == "|")
        {
            $cell->type = "td";
        }
        else
        {
            $cell->type = "th";
        }

        switch ($textAlignMark)
        {
            case ">":
                {
                    $cell->textAlign = " style=\"text-align:right\"";
                    break;
                }
            case "<":
                {
                    $cell->textAlign = " style=\"text-align:left\"";
                    break;
                }
            case "=":
                {
                    $cell->textAlign = " style=\"text-align:center\"";
                    break;
                }
            default:
                {
                    $cell->textAlign = "";
                    break;
                }
        }
    }

    private static function setTableCellTextAndBlank($cell, $markedupText)
    {
        $markedupText = trim($markedupText);

        switch ($markedupText)
        {
            case "::":
                {
                    $cell->isCollSpanBlank = true;
                    break;
                }
            case ":::":
                {
                    $cell->isRowSpanBlank = true;
                    break;
                }
            default:
                {
                    $cell->text = KARAS::convertInlineMarkup($markedupText);
                    break;
                }
        }
    }

    private static function constructTableText($cells)
    {
        $tableText = "<table>\n";

        for ($row = 0; $row < count($cells); $row += 1)
        {
            $tableText .= "<tr>";

            for ($column = 0; $column < count($cells[$row]); $column += 1)
            {
                $cell = $cells[$row][$column];

                if ($cell->isCollSpanBlank || $cell->isRowSpanBlank)
                {
                    continue;
                }

                $columnBlank = KARAS::countBlankColumn($cells, $column, $row);
                $rowBlank = KARAS::countBlankRow($cells, $column, $row);
                $colspanText = "";
                $rowspanText = "";

                if ($columnBlank > 1)
                {
                    $colspanText = " colspan = \"" . $columnBlank . "\"";
                }
                
                if ($rowBlank > 1)
                {
                    $rowspanText = " rowspan = \"" . $rowBlank . "\"";
                }
                
                $tableText .= "<" . $cell->type . $colspanText
                              . $rowspanText . $cell->textAlign . ">"
                              . $cell->text . "</" . $cell->type . ">";
            }

            $tableText .= "</tr>\n";
        }

        $tableText .= "</table>";
        return $tableText;
    }

    private static function countBlankColumn($cells, $column, $row)
    {
        $blank = 1;
        $rightColumn = $column + 1;

        while ($rightColumn < count($cells[$row]))
        {
            $rightCell = $cells[$row][$rightColumn];

            if ($rightCell->isCollSpanBlank)
            {
                $blank += 1;
                $rightColumn += 1;
            }
            else
            {
                break;
            }
        }

        return $blank;
    }

    private static function countBlankRow($cells, $column, $row)
    {
        $blank = 1;
        $underRow = $row + 1;

        while ($underRow < count($cells))
        {
            //Note, sometimes there is no column in next row.
            if ($column >= count($cells[$underRow]))
            {
                break;
            }

            $underCell = $cells[$underRow][$column];

            if ($underCell->isRowSpanBlank)
            {
                $blank += 1;
                $underRow += 1;
            }
            else
            {
                break;
            }
        }

        return $blank;
    }

    public static function convertList($text)
    {
        //match group index.
        $mgiAllText = 0;
        
        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexList, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $sequentialLists = array();
            $listStartIndex = $match[$mgiAllText][1];
            $listEndIndex = KARAS::constructSequentialLists
                                ($text, $listStartIndex, $sequentialLists);

            $newText = KARAS::constructListText($sequentialLists);
            $newText = KARAS::encloseWithLinebreak($newText);
            $nextMatchIndex = $listStartIndex + strlen($newText);
            $text = KARAS::removeAndInsertText($text,
                                               $listStartIndex,
                                               $listEndIndex - $listStartIndex,
                                               KARAS::encloseWithLinebreak($newText));
        }

        return $text;
    }

    private static function constructSequentialLists
        ($text, $indexOfListStart, &$sequentialLists)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiMarks = 1;
        $mgiMarkedupText = 2;
        $mgiNextMarks = 3;
        $mgiBreaks = 4;

        $match = null;
        $nextMatchIndex = $indexOfListStart;
        $levelDiff = 0;
        $previousLevel = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexList, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            //Update
            $levelDiff = strlen($match[$mgiMarks][0]) - $previousLevel;
            $previousLevel = strlen($match[$mgiMarks][0]);

            //If start of the items. || If Level up or down.
            if ($levelDiff != 0)
            {
                $sequentialList = new SequentialList();

                if ($match[$mgiMarks][0][strlen($match[$mgiMarks][0]) - 1] == '-')
                {
                    $sequentialList->type = KARAS::ListTypeUl;
                }
                //If  == '+'
                else
                {
                    $sequentialList->type = KARAS::ListTypeOl;
                }

                $sequentialList->level = strlen($match[$mgiMarks][0]);
                $sequentialList->items[] = $match[$mgiMarkedupText][0];
                $sequentialLists[] = $sequentialList;
            }
             //If same Level.
            else
            {
                $previousSequentialList = $sequentialLists[count($sequentialLists) - 1];
                $listType = KARAS::ListTypeUl;

                if ($match[$mgiMarks][0][strlen($match[$mgiMarks][0]) - 1] == '-')
                {
                    $listType = KARAS::ListTypeUl;
                }
                //If == '+'
                else
                {
                    $listType = KARAS::ListTypeOl;
                }

                if ($listType != $previousSequentialList->type)
                {
                    $sequentialList = new SequentialList();
                    $sequentialList->type = $listType;
                    $sequentialList->level = strlen($match[$mgiMarks][0]);
                    $sequentialList->items[] = $match[$mgiMarkedupText][0];
                    $sequentialLists[] = $sequentialList;
                }
                //If same items type.
                else
                {
                    $previousSequentialList->items[] = $match[$mgiMarkedupText][0];
                }
            }

            if (empty($match[$mgiNextMarks][0]) == true)
            {
                return $match[$mgiAllText][1]
                       + strlen($match[$mgiAllText][0])
                       - (empty($match[$mgiBreaks][0]) ? 0 : strlen($match[$mgiBreaks][0]));
            }

            $nextMatchIndex = $match[$mgiNextMarks][1];
        }

        return -1;
    }

    private static function constructListText($sequentialLists)
    {
        //Note : key = level, value = isUL(true:ul, false:ol)
        $listTypeHash = KARAS::constructListTypeHash($sequentialLists);
        $listText = "";

        $listText .= KARAS::constructFirstSequentialListText
                        ($sequentialLists[0], $listTypeHash);

        //Write later lists.
        $previousLevel = $sequentialLists[0]->level;

        for ($i = 1; $i < count($sequentialLists); $i += 1)
        {
            $sequentialList = $sequentialLists[$i];

            //If level up.
            if ($previousLevel < $sequentialList->level)
            {
                $listText .= KARAS::constructUpLevelSequentialListText
                                ($previousLevel, $sequentialList, $listTypeHash);
            }
            //If level down.
            else if ($previousLevel > $sequentialList->level)
            {
                $listText .= KARAS::constructDownLevelSequentialListText
                                ($previousLevel, $sequentialList, $listTypeHash);
            }
            //If same level.(It means the list type is changed.)
            else
            {
                $listText .= KARAS::constructSameLevelSequentialListText
                                ($previousLevel, $sequentialList, $listTypeHash);
            }

            $previousLevel = $sequentialList->level;
        }

        $listText .= KARAS::constructListCloseText($previousLevel, $listTypeHash);

        return KARAS::encloseWithLinebreak($listText);
    }

    private static function constructListTypeHash($sequentialLists)
    {
        $listTypeHash = array();
        $maxLevel = 1;

        foreach ($sequentialLists as $list)
        {
            if ($maxLevel < $list->level)
            {
                $maxLevel = $list->level;
            }

            if (array_key_exists($list->level, $listTypeHash) == false)
            {
                $listTypeHash[$list->level] = $list->type;
            }
        }

        //If there is undefined level,
        //set the list type of the next higher defined level to it.
        //Note, the maximum level always has level type. 
        for ($level = 1; $level < $maxLevel; $level += 1)
        {
            if (array_key_exists($level, $listTypeHash) == false)
            {
                $typeUndefinedLevels = array();
                $typeUndefinedLevels[] = $level;

                for ($nextLevel = $level + 1; $nextLevel <= $maxLevel; $nextLevel += 1)
                {
                    if (array_key_exists($nextLevel, $listTypeHash))
                    {
                        foreach ($typeUndefinedLevels as $typeUndefinedLevel)
                        {
                            $listTypeHash[$typeUndefinedLevel] = $listTypeHash[$nextLevel];
                        }

                        //Skip initialized level.
                        $level = $nextLevel + 1;
                        break;
                    }

                    $typeUndefinedLevels[] = $nextLevel;
                }
            }
        }

        return $listTypeHash;
    }

    private static function constructFirstSequentialListText($sequentialList, &$listTypeHash)
    {
        $listText = "";

        for ($level = 1; $level < $sequentialList->level; $level += 1)
        {
            if ($listTypeHash[$level] == KARAS::ListTypeUl)
            {
                $listText .= "<ul>\n<li>\n";
            }
            else
            {
                $listText .= "<ol>\n<li>\n";
            }
        }

        if ($sequentialList->type == KARAS::ListTypeUl)
        {
            $listText .= "<ul>\n<li";
        }
        else
        {
            $listText .= "<ol>\n<li";
        }

        for ($i = 0; $i < count($sequentialList->items) - 1; $i += 1)
        {
            $listText .= KARAS::constructListItemText($sequentialList->items[$i])
                         . "</li>\n<li";
        }

        $listText .= KARAS::constructListItemText
                        ($sequentialList->items[count($sequentialList->items) - 1]);

        return $listText;
    }

    private static function constructUpLevelSequentialListText
        ($previousLevel, $sequentialList, &$listTypeHash)
    {
        $listText = "";

        for ($level = $previousLevel; $level < $sequentialList->level - 1; $level += 1)
        {
            if ($listTypeHash[$level] == KARAS::ListTypeUl)
            {
                $listText .= "\n<ul>\n<li>";
            }
            else
            {
                $listText .= "\n<ol>\n<li>";
            }
        }

        if ($sequentialList->level != 1)
        {
            $listText .= "\n";
        }

        if ($sequentialList->type == KARAS::ListTypeUl)
        {
            $listText .= "<ul>\n<li";
        }
        else
        {
            $listText .= "<ol>\n<li";
        }

        for ($i = 0; $i < count($sequentialList->items) - 1; $i += 1)
        {
            $listText .= KARAS::constructListItemText($sequentialList->items[$i])
                         . "</li>\n<li";
        }

        $listText .= KARAS::constructListItemText
                        ($sequentialList->items[count($sequentialList->items) - 1]);

        return $listText;
    }

    private static function constructDownLevelSequentialListText
        ($previousLevel, $sequentialList, &$listTypeHash)
    {
        //Close previous list item.
        $listText = "</li>\n";

        //Close previous level lists.
        for ($level = $previousLevel; $level > $sequentialList->level; $level -= 1)
        {
            if ($listTypeHash[$level] == KARAS::ListTypeUl)
            {
                $listText .= "</ul>\n";
            }
            else
            {
                $listText .= "</ol>\n";
            }

            $listText .= "</li>\n";
        }

        //if current level's list type is different from previous same level's list type.
        if ($listTypeHash[$sequentialList->level] != $sequentialList->type)
        {
            //Note, it is important to update hash.
            if ($listTypeHash[$sequentialList->level] == KARAS::ListTypeUl)
            {
                $listText .= "</ul>\n<ol>\n";
                $listTypeHash[$sequentialList->level] = KARAS::ListTypeOl;
            }
            else
            {
                $listText .= "</ol>\n<ul>\n";
                $listTypeHash[$sequentialList->level] = KARAS::ListTypeUl;
            }
        }

        for ($i = 0; $i < count($sequentialList->items) - 1; $i += 1)
        {
            $listText .= "<li" . KARAS::constructListItemText($sequentialList->items[$i])
                         . "</li>\n";
        }

        $listText .= "<li" . KARAS::constructListItemText
                                ($sequentialList->items[count($sequentialList->items) - 1]);

        return $listText;
    }

    private static function constructSameLevelSequentialListText
        ($previousLevel, $sequentialList, &$listTypeHash)
    {
        //Close previous list item.
        $listText = "";

        if ($listTypeHash[$previousLevel] == KARAS::ListTypeUl)
        {
            $listText .= "</li>\n</ul>\n";
        }
        else
        {
            $listText .= "</li>\n</ol>\n";
        }

        if ($sequentialList->type == KARAS::ListTypeUl)
        {
            $listText .= "<ul>\n";
        }
        else
        {
            $listText .= "<ol>\n";
        }

        for ($i = 0; $i < count($sequentialList->items) - 1; $i += 1)
        {
            $listText .= 
                "<li" . KARAS::constructListItemText($sequentialList->items[$i]) . "</li>\n";
        }

        $listText .= "<li" . KARAS::constructListItemText
                                ($sequentialList->items[count($sequentialList->items) - 1]);

        //Note, it is important to update hash.
        $listTypeHash[$sequentialList->level] = $sequentialList->type;

        return $listText;
    }

    private static function constructListItemText($listItemText)
    {
        $listItemText = KARAS::convertInlineMarkup($listItemText);
        $isSpecialOption = false;
        $listItemTexts = KARAS::splitOption($listItemText, $isSpecialOption);

        if (count($listItemTexts) > 1)
        {
            $listItemText = " value=\"" . $listItemTexts[1] . "\">";
        }
        else
        {
            $listItemText = ">";
        }

        $listItemText .= $listItemTexts[0];

        return $listItemText;
    }
    
    private static function constructListCloseText($previousLevel, &$listTypeHash)
    {
        //Close previous list item.
        $listText = "</li>\n";

        //Close all.
        for ($level = $previousLevel; $level > 1; $level -= 1)
        {
            if ($listTypeHash[$level] == KARAS::ListTypeUl)
            {
                $listText .= "</ul>\n";
            }
            else
            {
                $listText .= "</ol>\n";
            }

            $listText .= "</li>\n";
        }

        if ($listTypeHash[1] == KARAS::ListTypeUl)
        {
            $listText .= "</ul>";
        }
        else
        {
            $listText .= "</ol>";
        }

        return $listText;
    }

    public static function convertDefList($text)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiMarks = 1;
        $mgiMarkedupText = 2;
        $mgiNextMarks = 3;
        $mgiBreaks = 4;

        $match = null;
        $nextMatchIndex = 0;
        $indexOfDefListText = 0;
        $defListIsOpen = false;
        $newText = "";

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexDefList, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            if ($defListIsOpen == false)
            {
                $defListIsOpen = true;
                $indexOfDefListText = $match[$mgiAllText][1];
                $newText = "<dl>\n";
            }

            if (strlen($match[$mgiMarks][0]) == 1)
            {
                $newText .= "<dt>"
                            . trim(KARAS::convertInlineMarkup($match[$mgiMarkedupText][0]))
                            . "</dt>\n";
            }
            else
            {
                $newText .= "<dd>"
                            . trim(KARAS::convertInlineMarkup($match[$mgiMarkedupText][0]))
                            . "</dd>\n";
            }

            if (empty($match[$mgiNextMarks][0]))
            {
                $newText = KARAS::encloseWithLinebreak($newText . "</dl>");
                $nextMatchIndex = $indexOfDefListText + strlen($newText);
                $text = KARAS::removeAndInsertText
                            ($text,
                             $indexOfDefListText,
                             $match[$mgiAllText][1]
                             + strlen($match[$mgiAllText][0])
                             - (empty($match[$mgiBreaks][0]) ? 0 : strlen($match[$mgiBreaks][0]))
                             - $indexOfDefListText,
                             KARAS::encloseWithLinebreak($newText));
                $indexOfDefListText = 0;
                $defListIsOpen = false;
                $newText = "";
                continue;
            }
            
            $nextMatchIndex = $match[$mgiNextMarks][1];                
        }

        return $text;
    }

    public static function convertHeading($text, $startLevelOfHeading = 1)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiMarks = 1;
        $mgiMarkedupText = 2;
        $mgiBreaks = 3;

        $maxLevelOfHeading = 6;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexHeading, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $newText = "";
            $level = strlen($match[$mgiMarks][0]);
            $level = $level + $startLevelOfHeading - 1;

            if ($level >= $maxLevelOfHeading + 1)
            {
                $newText = KARAS::encloseWithLinebreak("<hr>");
            }
            else
            {
                //Note, it is important to convert inline markups first,
                //to convert inline markup's options first.
                $markedupText = KARAS::convertInlineMarkup($match[$mgiMarkedupText][0]);
                $isSpecialOption = false;
                $markedupTexts = KARAS::splitOption($markedupText, $isSpecialOption);
                $id = "";

                if (count($markedupTexts) > 1)
                {
                    $id = " id=\"" . $markedupTexts[1] . "\"";
                }

                $newText = "<h" . $level . $id . ">"
                           . $markedupTexts[0]
                           . "</h" . $level . ">";
                $newText = KARAS::encloseWithLinebreak($newText);
            }

            $nextMatchIndex = $match[$mgiAllText][1] + strlen($newText);
            $text = KARAS::removeAndInsertText($text,
                                               $match[$mgiAllText][1],
                                               strlen($match[$mgiAllText][0])
                                               - strlen($match[$mgiBreaks][0]),
                                               KARAS::encloseWithLinebreak($newText));
        }

        return $text;
    }

    public static function convertBlockLink($text)
    {
        //match group index.
        $mgiAllText = 0;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexBlockLink, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }
            
            $newText = trim(KARAS::convertInlineMarkup($match[$mgiAllText][0]));

            if (KARAS::textIsParagraph($newText))
            {
                $newText = "<p>" . $newText . "</p>";
            }
            
            $newText = KARAS::encloseWithLinebreak($newText);
            $nextMatchIndex = $match[$mgiAllText][1] + strlen($newText);
            $text = KARAS::removeAndInsertText($text,
                                               $match[$mgiAllText][1],
                                               strlen($match[$mgiAllText][0]),
                                               KARAS::encloseWithLinebreak($newText));
        }

        return $text;
    }

    private static function textIsParagraph($text)
    {
        $restText = preg_replace(KARAS::RegexLinkElement, "", $text);
        $restText = trim($restText);

        if (strlen($restText) == 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public static function convertParagraph($text)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiMarkedupText = 1;
        $mgiLTMarks = 2;

        //means \n\n length.
        $lineBreaks = 2;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexParagraph, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            if (strlen($match[$mgiLTMarks][0]) == 1)
            {
                //Note, it is important to exclude line breaks (like \n).
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiMarkedupText][0]);
                continue;
            }

            $markedupText = trim($match[$mgiMarkedupText][0]);

            if (mb_strlen($markedupText) == 0)
            {
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            $newText = "<p>" . KARAS::convertInlineMarkup($markedupText) . "</p>\n";
            $newText = KARAS::encloseWithLinebreak($newText);
            $nextMatchIndex = $match[$mgiAllText][1] + strlen($newText) - $lineBreaks;
            $text = KARAS::removeAndInsertText($text,
                                               $match[$mgiAllText][1],
                                               strlen($match[$mgiAllText][0]),
                                               $newText);
        }

        return $text;
    }

    public static function reduceBlankLine($text)
    {
        //match group index.
        $mgiLineBreakCode = 0;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexBlankLine, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $text = substr($text, 0, $match[$mgiLineBreakCode][1])
                    . substr($text,
                             $match[$mgiLineBreakCode][1]
                             + strlen($match[$mgiLineBreakCode][0]));
            $nextMatchIndex = $match[$mgiLineBreakCode][1];
        }

        return trim($text);
    }

    public static function reduceEscape($text)
    {
        //match group index.
        $mgiEscapes = 0;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexEscape, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $reduceLength = round((strlen($match[$mgiEscapes][0])) / 2);                
            $text = substr($text, 0, $match[$mgiEscapes][1])
                    . substr($text, $match[$mgiEscapes][1] + $reduceLength);

            $nextMatchIndex = $match[$mgiEscapes][1]
                             + strlen($match[$mgiEscapes][0])
                             - $reduceLength;
        }

        return $text;
    }





    public static function convertInlineMarkup($text)
    {
        //match group index.
        //$mgiAllText = 0;
        $mgiEscapes = 1;
        $mgiMarks = 2;

        $matchStack = array();
        $match;
        $nextMatchIndex = 0;

        $text = KARAS::convertLineBreak($text);

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexInlineMarkup, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            if (strlen($match[$mgiEscapes][0]) % 2 == 1)
            {
                $nextMatchIndex = $match[$mgiMarks][1] + 1;
                continue;
            }

            $inlineMarkupMatch = KARAS::constructInlineMarkupMatch
                                    ($match[$mgiMarks][1], $match[$mgiMarks][0]);
            $preMatch;
            $newText = "";
            $closeMarks = "";

            if ($inlineMarkupMatch->type >= KARAS::InlineMarkupTypeLinkOpen)
            {
                //InlieneMarkupType*Close - 1 = InlineMarkupType*Open
                $preMatch = KARAS::getPreMatchedInlineMarkup
                                ($matchStack, $inlineMarkupMatch->type - 1);
                KARAS::handleLinkAndInlineGroupMatch($text,
                                                     $preMatch,
                                                     $inlineMarkupMatch,
                                                     $matchStack,
                                                     $nextMatchIndex,
                                                     $newText,
                                                     $closeMarks);

                if ($nextMatchIndex != -1)
                {
                    continue;
                }
            }
            else
            {
                $preMatch = KARAS::getPreMatchedInlineMarkup
                                ($matchStack, $inlineMarkupMatch->type);
                KARAS::handleBasicInlineMarkupMatch($text,
                                                    $preMatch,
                                                    $inlineMarkupMatch,
                                                    $matchStack,
                                                    $nextMatchIndex,
                                                    $newText,
                                                    $closeMarks);

                if ($nextMatchIndex != -1)
                {
                    continue;
                }
            }

            //It is important to trim close marks to exclude whitespace out of syntax.
            $text = KARAS::removeAndInsertText($text, 
                                               $preMatch->index, 
                                               $inlineMarkupMatch->index
                                               + strlen(trim($inlineMarkupMatch->marks))
                                               - $preMatch->index,
                                               $newText);
            $nextMatchIndex = $preMatch->index + strlen($newText) - strlen($closeMarks);
        }

        return $text;
    }

    public static function convertLineBreak($text)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiEscapes = 1;
        $mgiLineBreak = 2;

        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS::RegexLineBreak, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            if (strlen($match[$mgiEscapes][0]) % 2 == 1)
            {
                $nextMatchIndex = $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]);
                continue;
            }

            $newText = "<br>\n";
            $text = KARAS::removeAndInsertText($text,
                                               $match[$mgiLineBreak][1],
                                               strlen($match[$mgiLineBreak][0]),
                                               $newText);
            $nextMatchIndex = $match[$mgiLineBreak][1] + strlen($newText);
        }

        return $text;
    }

    private static function constructInlineMarkupMatch($index, $marks)
    {
        $inlineMarkupMatch = new InlineMarkupMatch();

        for ($i = 0; $i < count(KARAS::$InlineMarkupSets); $i += 1)
        {
            if ($marks[0] == KARAS::$InlineMarkupSets[$i][0][0])
            {
                $inlineMarkupMatch->type = $i;
                $inlineMarkupMatch->index = $index;
                $inlineMarkupMatch->marks = $marks;
                break;
            }
        }

        return $inlineMarkupMatch;
    }

    private static function handleLinkAndInlineGroupMatch
        ($text, $openMatch, $closeMatch,
         &$matchStack, &$nextMatchIndex, &$newText, &$closeMarks)
    {
        if ($closeMatch->type == KARAS::InlineMarkupTypeLinkOpen
            || $closeMatch->type == KARAS::InlineMarkupTypeInlineGroupOpen)
        {
            array_unshift($matchStack, $closeMatch);
            $nextMatchIndex = $closeMatch->index + strlen($closeMatch->marks);
            return;
        }

        if ($openMatch == null)
        {
            $nextMatchIndex = $closeMatch->index + strlen($closeMatch->marks);
            return;
        }

        $markedupTextIndex = $openMatch->index + strlen($openMatch->marks);
        $markedupText = substr
            ($text, $markedupTextIndex, $closeMatch->index - $markedupTextIndex);
        $openMarks = KARAS::removeWhiteSpace($openMatch->marks);
        $closeMarks = KARAS::removeWhiteSpace($closeMatch->marks);

        if ($closeMatch->type == KARAS::InlineMarkupTypeLinkClose)
        {
            KARAS::constructLinkText($markedupText, $newText, $openMarks, $closeMarks);
        }
        else
        {
            KARAS::constructInlineGroupText($markedupText, $newText, $openMarks, $closeMarks);
        }
        
        if (strlen($openMarks) > 1)
        {
            $openMatch->marks = $openMarks;
        }
        else
        {
            while (true)
            {
                if (array_shift($matchStack)->type == $openMatch->type)
                {
                    break;
                }
            }
        }

        $nextMatchIndex = -1;
        return;
    }

    private static function handleBasicInlineMarkupMatch
        ($text, $openMatch, $closeMatch,
         &$matchStack, &$nextMatchIndex, &$newText, &$closeMarks)
    {
        if ($openMatch == null)
        {
            array_unshift($matchStack, $closeMatch);
            $nextMatchIndex = $closeMatch->index + strlen($closeMatch->marks);
            return;
        }

        $markedupTextIndex = $openMatch->index + strlen($openMatch->marks);
        $markedupText = trim(substr($text,
                                    $markedupTextIndex,
                                    $closeMatch->index - $markedupTextIndex));

        if ($openMatch->type <= KARAS::InlineMarkupTypeSupRuby
            && strlen($openMatch->marks) >= 3 && strlen($closeMatch->marks) >= 3)
        {
            KARAS::constructSecondInlineMarkupText
                ($markedupText, $openMatch, $closeMatch, $newText, $closeMarks);
        }
        else
        {
            KARAS::constructFirstInlineMarkupText
                ($markedupText, $openMatch, $closeMatch, $newText, $closeMarks);
        }

        while (true)
        {
            if (array_shift($matchStack)->type == $closeMatch->type)
            {
                break;
            }
        }

        $nextMatchIndex = -1;
        return;
    }

    private static function getPreMatchedInlineMarkup($matchStack, $inlineMarkupType)
    {
        //Note, check from latest $match.
        for ($i = 0; $i < count($matchStack); $i += 1)
        {
            if ($matchStack[$i]->type == $inlineMarkupType)
            {
                return $matchStack[$i];
            }
        }

        return null;
    }

    private static function constructLinkText
        ($markedupText, &$newText, &$openMarks, &$closeMarks)
    {
        $isSpecialOption = false;
        $markedupTexts = KARAS::splitOption($markedupText, $isSpecialOption);
        $url = $markedupTexts[0];

        if (strlen($openMarks) >= 5 && strlen($closeMarks) >= 5)
        {
            $newText = "<a href=\"" . $url . "\">"
                       . KARAS::constructMediaText($url, $markedupTexts) . "</a>";
        }
        else if (strlen($openMarks) >= 3 && strlen($closeMarks) >= 3)
        {
            $newText = KARAS::constructMediaText($url, $markedupTexts);
        }
        else
        {
            $aliasText = "";

            if (count($markedupTexts) > 1)
            {
                $aliasText = $markedupTexts[1];
            }
            else
            {
                $aliasText = $url;
            }

            $newText = "<a href=\"" . $url . "\">" . $aliasText . "</a>";
        }

        $markDiff = strlen($openMarks) - strlen($closeMarks);

        if ($markDiff > 0)
        {
            $openMarks = substr($openMarks, 0, $markDiff);
            $closeMarks = "";
        }
        else
        {
            $openMarks = "";
            $closeMarks = substr($closeMarks, 0, -$markDiff);
        }

        $newText = $openMarks . $newText . $closeMarks;
    }

    private static function constructMediaText($url, $markedupTexts)
    {
        $mediaText = "";
        $option = "";
        $reservedAttribute = "";
        $otherAttribute = "";
        $embedAttribute = "";
        $mediaType = KARAS::getMediaType(KARAS::getFileExtension($url));

        if (count($markedupTexts) > 1)
        {
            $option = $markedupTexts[1];
            KARAS::constructObjectAndEmbedAttributes
                ($option, $reservedAttribute, $otherAttribute, $embedAttribute);
            $option = " " . $markedupTexts[1];
        }

        switch ($mediaType)
        {
            case KARAS::MediaTypeImage:
                {
                    $mediaText = "<img src=\"" . $url . "\"" . $option . ">";
                    break;
                }
            case KARAS::MediaTypeAudio:
                {
                    $mediaText = "<audio src=\"" . $url . "\"" . $option . ">"
                                 . "<object data=\"" . $url . "\"" . $reservedAttribute . ">"
                                 . $otherAttribute
                                 . "<embed src=\"" . $url . "\"" . $embedAttribute
                                 . "></object></audio>";
                    break;
                }
            case KARAS::MediaTypeVideo:
                {
                    $mediaText = "<video src=\"" . $url . "\"" . $option . ">"
                                 . "<object data=\"" . $url . "\"" . $reservedAttribute . ">"
                                 . $otherAttribute
                                 . "<embed src=\"" . $url . "\"" . $embedAttribute
                                 . "></object></video>";
                    break;
                }
            default:
                {
                    $mediaText = "<object data=\"" . $url . "\"" . $reservedAttribute . ">"
                                 . $otherAttribute
                                 . "<embed src=\"" . $url . "\"" . $embedAttribute
                                 . "></object>";
                    break;
                }
        }

        return $mediaText;
    }

    public static function getMediaType($extension)
    {
        $match;

        for ($i = 0; $i < count(KARAS::$MediaExtensions); $i += 1)
        {
            $matchIsSuccess = preg_match
                (KARAS::$MediaExtensions[$i], $extension, $match, PREG_OFFSET_CAPTURE);

            if ($matchIsSuccess)
            {
                return $i;
            }
        }

        return KARAS::MediaTypeUnknown;
    }

    public static function getFileExtension($text)
    {
        //match group index.
        //$mgiAllText = 0;
        $mgiFileExtension = 1;

        $match;
        $matchIsSuccess = preg_match
            (KARAS::RegexFileExtension, $text, $match, PREG_OFFSET_CAPTURE);

        if ($matchIsSuccess)
        {
            return $match[$mgiFileExtension][0];
        }
        else
        {
            return "";
        }
    }

    private static function constructObjectAndEmbedAttributes
        ($option, &$reservedAttribute, &$otherAttribute, &$embedAttribute)
    {
        $parameterHash = KARAS::constructParameterHash($option);

        foreach (array_keys($parameterHash) as $name)
        {
            if (KARAS::attributeIsReserved($name))
            {
                $reservedAttribute .= $name . "=\"" . $parameterHash[$name] . "\" ";
            }
            else
            {
                $otherAttribute .= "<param name=\"" . $name
                                   . "\" value=\"" . $parameterHash[$name] . "\">";
            }

            $embedAttribute .= $name . "=\"" . $parameterHash[$name] . "\" ";
        }

        if (mb_strlen($reservedAttribute) > 0)
        {
            $reservedAttribute = " " . trim($reservedAttribute);
        }

        if(mb_strlen($embedAttribute) > 0)
        {
            $embedAttribute = " " . trim($embedAttribute);
        }
    }

    private static function constructParameterHash($option)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiName = 1;
        $mgiValue = 2;

        $parameterHash = array();
        $match;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match(KARAS::RegexStringTypeAttribute,
                                         $option,
                                         $match,
                                         PREG_OFFSET_CAPTURE,
                                         $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                break;
            }

            $parameterHash[$match[$mgiName][0]] = $match[$mgiValue][0];
            $option = substr($option, 0, $match[$mgiAllText][1])
                      . substr($option, $match[$mgiAllText][1] + strlen($match[$mgiAllText][0]));
            $nextMatchIndex = $match[$mgiAllText][1];
        }

        $logicalValues = mb_split("\s", $option);

        foreach ($logicalValues as $value)
        {
            if (mb_strlen($value) > 0)
            {
                $parameterHash[$value] = "true";
            }
        }

        return $parameterHash;
    }

    private static function attributeIsReserved($attribute)
    {
        foreach (KARAS::$ReservedObjectAttributes as $reservedAttribute)
        {
            if (strcasecmp($attribute, $reservedAttribute) == 0)
            {
                return true;
            }
        }

        return false;
    }

    private static function constructInlineGroupText
        ($markedupText, &$newText, &$openMarks, &$closeMarks)
    {
        $isSpecialOption = false;
        $markedupTexts = KARAS::splitOption($markedupText, $isSpecialOption);
        $idClass = "";

        if (strlen($openMarks) >= 3 && strlen($closeMarks) >= 3)
        {
            $idClass = " id=\"";
        }
        else
        {
            $idClass = " class=\"";
        }

        if(strlen($markedupTexts[0]) == 0)
        {
            $idClass = "";
        }
        else
        {
            $idClass .= $markedupTexts[0] . "\"";
        }

        if (count($markedupTexts) > 1)
        {
            $newText = $markedupTexts[1];
        }
        else
        {
            $newText = "";
        }

        $markDiff = strlen($openMarks) - strlen($closeMarks);

        if ($markDiff > 0)
        {
            $openMarks = substr($openMarks, 0, $markDiff);
            $closeMarks = "";
        }
        else
        {
            $openMarks = "";
            $closeMarks = substr($closeMarks, 0, -$markDiff);
        }

        $newText = $openMarks . "<span" . $idClass . ">" . $newText . "</span>" . $closeMarks;
    }

    private static function constructSecondInlineMarkupText
        ($markedupText, $openMatch, $closeMatch, &$newText, &$closeMarks)
    {
        $inlineMarkupSet = KARAS::$InlineMarkupSets[$openMatch->type];
        $openMarks = substr($openMatch->marks, 3);
        $closeMarks = substr($closeMatch->marks, 3);
        $openTag = "";
        $closeTag = "";

        if ($openMatch->type == KARAS::InlineMarkupTypeSupRuby)
        {
            $openTag = "<ruby>";
            $closeTag = "</ruby>";
            $hasSpecialOption = false;
            $markedupTexts = KARAS::splitOptions($markedupText, $hasSpecialOption);
            $markedupText = $markedupTexts[0];

            for ($i = 1; $i < count($markedupTexts); $i += 2)
            {
                $markedupText .= "<rp> (</rp><rt>" . $markedupTexts[$i] . "</rt><rp>) </rp>";

                if ($i + 1 < count($markedupTexts))
                {
                    $markedupText .= $markedupTexts[$i + 1];
                }
            }
        }
        else
        {
            $openTag = "<" . $inlineMarkupSet[2] . ">";
            $closeTag = "</" . $inlineMarkupSet[2] . ">";

            if ($openMatch->type == KARAS::InlineMarkupTypeDefAbbr)
            {
                $openTag = "<" . $inlineMarkupSet[1] . ">" . $openTag;
                $closeTag = $closeTag . "</" . $inlineMarkupSet[1] . ">";
            }

            if ($openMatch->type == KARAS::InlineMarkupKbdSamp
                || $openMatch->type == KARAS::InlineMarkupVarCode)
                $markedupText = KARAS::escapeHTMLSpecialCharacters($markedupText);
        }

        $newText = $openMarks . $openTag . $markedupText . $closeTag . $closeMarks;
    }

    private static function constructFirstInlineMarkupText
        ($markedupText, $openMatch, $closeMatch, &$newText, &$closeMarks)
    {
        $inlineMarkupSet = KARAS::$InlineMarkupSets[$openMatch->type];
        $openMarks = substr($openMatch->marks, 2);
        $closeMarks = substr($closeMatch->marks, 2);
        $openTag = "<" . $inlineMarkupSet[1] . ">";
        $closeTag = "</" . $inlineMarkupSet[1] . ">";

        if ($openMatch->type == KARAS::InlineMarkupVarCode
            || $openMatch->type == KARAS::InlineMarkupKbdSamp)
        {
            $markedupText = KARAS::escapeHTMLSpecialCharacters($markedupText);
        }

        $newText = $openMarks . $openTag . $markedupText . $closeTag . $closeMarks;
    }
}





class PluginMatch
{
    public $index;
    public $marks;

    public function __construct()
    {
        $this->index = -1;
        $this->marks = "";
    }
}

class PluginManager
{
    //Key = Lowercase plugin name, Value = Plugin
    private $loadedPluginHash;
    //Key = Original plugin name, Value = File path
    private $pluginFilePathHash;

    public function __construct($pluginDirectory)
    {
        $this->loadedPluginHash = array();
        $this->pluginFilePathHash = array();
        $this->loadPluginFilePaths(PluginManager::getSafeDirectoryPath($pluginDirectory));
    }

    public static function getSafeDirectoryPath($directory)
    {
         $lastchar = mb_substr($directory, -1);
         if($lastchar === "/")
         {
            return $directory;            
         }
         else
         {
            return $directory . "/";
         }
    }

    private function loadPluginFilePaths($pluginDirectory)
    {
        foreach (PluginManager::getPluginFilePaths($pluginDirectory) as $filePath)
        {
            $pathinfo = pathinfo($filePath);
            $pluginName = $pathinfo["filename"];
            $this->pluginFilePathHash[$pluginName] = $filePath;
        }
    }

    public static function getPluginFilePaths($pluginDirectory)
    {
        $plugins = array();

        if(file_exists($pluginDirectory) == false || is_dir($pluginDirectory) == false)
        {
            return $plugins;
        }

        $files = scandir($pluginDirectory);

        foreach ($files as $file)
        {
            $file = $pluginDirectory . $file;

            if (is_file($file) == true)
            {
                $pathinfo = pathinfo($file);
                
                if(strcasecmp($pathinfo["extension"], "php") == 0)
                {
                    $plugins[] = $file;
                }
            }
        }

        return $plugins;
    }

    public function getPlugin($pluginName)
    {
        $pluginName = mb_strtolower($pluginName);

        if (array_key_exists($pluginName, $this->loadedPluginHash))
        {
            return $this->loadedPluginHash[$pluginName];
        }
        else
        {
            return $this->loadAndChacePlugin($pluginName);
        }
    }

    public function loadAndChacePlugin($pluginName)
    {
        try
        {
            foreach(array_keys($this->pluginFilePathHash) as $key)
            {
                if(strcasecmp($key, $pluginName) == 0)
                {
                    require_once($this->pluginFilePathHash[$key]);
                    $plugin = new \ReflectionClass($pluginName);
                    $this->loadedPluginHash[$pluginName] = $plugin;
                    return $plugin;
                }
            }

            $this->loadedPluginHash[$pluginName] = null;
            return null;
        }
        catch(Exception $e)
        {
            $this->loadedPluginHash[$pluginName] = null;
            return null;
        }
    }
}

class BlockGroupMatch
{
    public $type;
    public $index;
    public $length;
    public $option;

    public function __construct()
    {
        $this->type = -1;
        $this->index = -1;
        $this->length = -1;
        $this->option = "";
    }
}

class SequentialBlockquote
{
    public $level;
    public $text;

    public function __construct()
    {
        $this->level = -1;
        $this->text = "";
    }
}

class TableCell
{
    public $isCollSpanBlank;
    public $isRowSpanBlank;
    public $type;
    public $textAlign;
    public $text;

    public function __construct()
    {
        $this->isCollSpanBlank = false;
        $this->isRowSpanBlank = false;
        $this->type = "";
        $this->textAlign = "";
        $this->text = "";
    }
}

class SequentialList
{
    public $type;
    public $level;
    public $items;

    public function __construct()
    {
        $this->type = false;
        $this->level = -1;
        $this->items = array();            
    }
}

class InlineMarkupMatch
{
    public $type;
    public $index;
    public $marks;

    public function __construct()
    {
        $this->type = -1;
        $this->index = -1;
        $this->marks = "";
    }
}

?>