<?php

class voltFun
{
    static function form()
    {
    }

    static function link($uri, $title)
    {
        return "<a href=$uri >$title</a>";
    }

    /**
     * @param array $contents
     * ['title'=>['title'='uri','title'=>'uri']]
     * @return string
     */
    static function dirLink($contents = array())
    {
        $begin_div = '<div class="panel-group" id="accordion">';
        $end_div = '</div>';
        $line_start = '<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion"
                       href="#collapseOne">';

        foreach ($contents as $title => $links) {
            $real_link = '';
            foreach ($links as $uri_title => $uri) {
                $real_link .=self::link($uri,$uri_title);
            }
            $line_end = '               </a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in">
                <div class="panel-body">
                    ' . $real_link . '
                </div>
            </div>
        </div>';
            $begin_div .= $line_start . $title . $line_end;
        }
        $html = $begin_div.$end_div;
        return $html;
    }
}