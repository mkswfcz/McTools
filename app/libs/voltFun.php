<?php

class voltFun
{
    static function form()
    {
    }

    static function link($uri, $title)
    {
        return "<a style='text-decoration: none' href=$uri >$title</a>";
    }

    static function rowLink($links)
    {
        $real_link = '';
        foreach ($links as $title => $link) {
            $real_link .= '<p style="border: solid 1px;border-radius: 5px;">'.self::link($link, $title).'</p>';
        }
        return $real_link;
    }

    /**
     * @param array $contents
     * ['title'=>['title'='uri','title'=>'uri']]
     * @return string
     */
    static function dirLink($contents = array())
    {
        $toggle = "\"collapse\"";
        $parent = "\"#according\"";
        $in_class = "\"panel-collapse collapse in\"";
        $panel_body = "\"panel-body\"";
        $collapses = [1 => 'collapseOne', 2 => 'collapseTwo', 3 => 'collapseThree'];
        $begin_div = '<div class="panel-group" id="accordion">';
        $end_div = '</div>';

        $line_start = '';
        $line_in = '';
        $i = 1;
        foreach ($contents as $title => $links) {
            $real_link = self::rowLink($links);
            $line_start .= '  <div class="panel panel-default">
        <div class="panel-heading">
                <h4 class="panel-title">';
            $line_start .= "<a style='text-decoration: none' data-toggle=$toggle data-parent=$parent href=#$collapses[$i]>$title</a>" . '</h4>
            </div>' . "
            <div id=$collapses[$i] class=$in_class>
                <div class=$panel_body>
                     $real_link 
                </div>
            </div>
        </div>";
            $i += 1;
        }
        $begin_div .= $line_start . $line_in;
        $html = $begin_div . $end_div;
        return $html;
    }
}