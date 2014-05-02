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

class TOC
{
    public static function action($text, $options)
    {
        //match group index.
        $mgiAllText = 0;
        $mgiMarks = 1;
        $mgiMarkedupText = 2;
        $mgiBreaks = 3;

        $topLevel = 1;
        $bottomLevel = 6;

        if (count($options) > 0)
        {
            $topLevel = intval($options[0]);
        }

        if (count($options) > 1)
        {
            $bottomLevel = intval($options[1]);
        }

        $newText = "";
        $previousLevel = 0;
        $match = null;
        $nextMatchIndex = 0;

        while (true)
        {
            $matchIsSuccess = preg_match
                (KARAS\KARAS::RegexHeading, $text, $match, PREG_OFFSET_CAPTURE, $nextMatchIndex);

            if ($matchIsSuccess === 0)
            {
                for($i = 0; $i < $previousLevel; $i += 1)
                {
                    $newText .= "</li>\n</ul>\n";
                }

                break;
            }

            if (strlen($match[$mgiMarks][0]) <= $bottomLevel)
            {
                $level = strlen($match[$mgiMarks][0]);
                $level = $level - $topLevel + 1;

                if ($level > 0)
                {
                    $levelDiff = $level - $previousLevel;
                    $previousLevel = $level;

                    if ($levelDiff > 0)
                    {
                        for($i = 0; $i < $levelDiff; $i += 1)
                        {
                            $newText .= "\n<ul>\n<li>";
                        }
                    }
                    else if($levelDiff < 0)
                    {
                        for($i = 0; $i < -$levelDiff; $i += 1)
                        {
                            $newText .= "</li>\n</ul>\n";
                        }

                        $newText .= "<li>";
                    }
                    else
                    {
                        $newText .= "</li>\n<li>";
                    }

                    $markedupText = KARAS\KARAS::convertInlineMarkup($match[$mgiMarkedupText][0]);
                    $markedupTexts = KARAS\KARAS::splitOptions($markedupText);
                    $itemText = $markedupTexts[0];

                    if (count($markedupTexts) > 1)
                    {
                        $itemText = "<a href=\"#" . trim($markedupTexts[1]) . "\">"
                                    . $itemText . "</a>";
                    }

                    $newText .= $itemText;
                }
            }

            $nextMatchIndex = $match[$mgiAllText][1]
                              + strlen($match[$mgiAllText][0])
                              - strlen($match[$mgiBreaks][0]);
        }

        return trim($newText);
    }
}

?>