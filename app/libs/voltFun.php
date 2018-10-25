<?php

class voltFun
{
    static function input($elements)
    {
        $input = '';
        foreach ($elements as $element => $value) {
            $input .= " {$element}='{$value}' ";
        }
        $input = "<input {$input}><br>";
//        debug('input: ',$input);
        return $input;
    }

    static function link($uri, $title)
    {
        return "<a class ='list-group-item' style='text-decoration: none' href=$uri >$title</a>";
    }

    static function rowLink($links)
    {
        $real_link = '';
        foreach ($links as $title => $link) {
            $real_link .= self::link($link, $title);
        }
        return $real_link;
    }

    /**
     * @param array $contents
     * ['Title1':['link_title':'link_1','Title2':...]]
     * @param $panel_class
     * default-success warning/danger/info/default
     * @return string
     */
    static function dirLink($contents = array(), $panel_class = 'success')
    {
        $toggle = "\"collapse\"";
        $parent = "\"#according\"";
        $in_class = "\"panel-collapse collapse out\"";
        $panel_body = "\"panel-body\"";
        $panel_line = "\"panel panel-{$panel_class}\"";
        $collapses = [1 => 'collapseOne', 2 => 'collapseTwo', 3 => 'collapseThree',
            4 => 'collapseFour', 5 => 'collapseFive', 6 => 'collapseSix', 7 => 'collapseSeven', 8 => 'collapseEight', 9 => 'CollapseNine', 10 => 'CollapseTen'];
        $begin_div = '<div class="panel-group" id="accordion">';
        $end_div = '</div>';

        $line_start = '';
        $line_in = '';
        $i = 1;
        foreach ($contents as $title => $links) {
            $real_link = self::rowLink($links);
            $line_start .= "  <div class=$panel_line>" . '
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