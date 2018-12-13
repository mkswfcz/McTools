<?php

class voltFun
{
    static function form($elements)
    {
        $form = '';
        foreach ($elements as $element => $value) {
            if ($element == 'style') {
                $form .= 'style=';
                foreach ($value as $k => $v) {
                    $form .= "{$k}:{$v};";
                }
            } else {
                $form .= " {$element}='{$value}' ";
            }
        }
        $form = "<form {$form}>";
        return $form;
    }


    static function confirm($head = '', $content = '', $uri, $target)
    {
        $html = "<div class='modal fade' id=$target tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>
                         &times;
                    </button>
                    <h4 class='modal-title' id='myModalLabel'>
                        {$head}
                    </h4>
                    <div class='modal-body' style='text-align: center'>
                        {$content}
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>取消</button>
                        <button type='button' class='btn btn-primary' style='background-color: #bce8f1'> <a style='text-decoration: none' href={$uri}>确定<a></button> 
                    </div>
                    </div>
                </div>
            </div>
        </div>";
        return $html;
    }

    static function modalForm($method, $action, $id, $onSubmit, $title)
    {
        $form_html = self::form(['method' => $method, 'action' => $action, 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'form_data', 'onsubmit' => $onSubmit]);
        $form_html .= "
<div class='modal fade' id='{$id}' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='float: left;'>
    <div class='modal-dialog'>
        <div class='modal-content' style='float: left;'>
            <div class='modal-header' style='height: 10%'>
                <button id='modal_close' type='button' class='close' data-dismiss='modal' aria-hidden='true'>
                    &times;
                </button>
                 <h4 class='modal-title' id='myModalLabel'>
                 {$title}
                 </h4>
            </div>
            <div id='modal_body' >
                <form>";
        return $form_html;
    }

    static function modalText($label, $name, $content, $placeholder)
    {
        return " <div id='modal_text'>
                       <label id='modal_label'for='{$name}' class='col-sm-3 control-label'>{$label}</label>
                        <div class='col-sm-9'><textarea style='height: 100%; width: 100%;'class='form-control' name='{$name}' value='{$content}' id='{$name}'
                                          placeholder='{$placeholder}'>
                                </textarea>
                        </div>
                 </div>";
    }

    static function modalInput($label, $type, $name, $value, $placeholder, $styles = array())
    {
        $html = "<div id='modal_input' > ";
        if (!empty($styles)) {
            $style = '';
            foreach ($styles as $k => $v) {
                $style .= $k . ':' . $v . ';';
            }
            $html = "<div id='modal_input'>";
        }
        $html .= "  
                        <label id='modal_label'for='{$name}' class='col-sm-3 control-label'>{$label}</label>
                        <div class='col-sm-9'>
                            <input type='{$type}' class='form-control' id='{$name}' name='{$name}' value='{$value}'
                                   placeholder='{$placeholder}'>
                        </div>
                    </div>";
        return $html;
    }

    static function modalFooter($operate)
    {
        $html = " 
             </form>
            </div>
              <div id='form_error'></div>
            <div id='modal_foot'>
                <button id='modal_submit' type='submit' class='btn btn-primary' style='margin-bottom: 10px;'>
                    {$operate}
                </button><span id='tip'> </span>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
</form>";
        return $html;
    }

    static function input($elements, $show_value = '')
    {
        $input = '';
        foreach ($elements as $element => $value) {
            if ($element == 'style') {
                $input .= 'style=';
                foreach ($value as $k => $v) {
                    $input .= "{$k}:{$v};";
                }
            } else {
                $input .= " {$element}='{$value}' ";
            }
        }
        if ($show_value) {
            $input .= ' value=' . $show_value . ' ';
        }
        debug('inp: ', $input);
        $input = "<input {$input}>";
        return $input;
    }

    static function text($elements, $value = '')
    {
        $input = '';
        foreach ($elements as $element => $value) {
            if ($element == 'style') {
                $input .= 'style=';
                foreach ($value as $k => $v) {
                    $input .= "{$k}:{$v};";
                }
            } else {
                $input .= " {$element}='{$value}' ";
            }
        }
        $input = "<textarea {$input}>";
        debug('tex: ', $input);
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
            if (0 === strpos($title, 'ajax_link_')) {
                $title = str_replace('ajax_link_', '', $title);
                $real_link .= self::ajax_link($title, $link);
            } elseif (0 === strpos($title, 'modal_link_')) {
                $title = str_replace('modal_link_', '', $title);
                $real_link .= self::modal_link($title, $link);
            } else {
                $real_link .= self::link($link, $title);
            }
        }
        return $real_link;
    }

    static function ajax_link($link_name, $target)
    {
        $html = "<a class='list-group-item' style='text-decoration: none' data-toggle='modal' data-target=#{$target}>
        {$link_name}</a>";
        return $html;
    }

    static function modal_link($link_name, $target)
    {
        return "<a  data-toggle='modal'  data-target=#{$target}>{$link_name}</a>";
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

    static function getText($object, $property)
    {
        $text_method = lcfirst(camelize($property)) . 'Text';
        if (method_exists($object, $text_method)) {
            return $object->$text_method();
        }
        return false;
    }

    static function textReplace($stack, $needle)
    {
        if (strpos($stack, $needle)) {
            return str_replace($needle, '', $stack);
        }
        return $stack;
    }

    static function parseHref($links)
    {
        $result = [];
        foreach ($links as $link_name => $link_params) {
            $result [$link_name] = $link_params;
        }
        return $result;
    }

    static function buildHref($batch_links, $object)
    {
        $href = '';
        foreach ($batch_links as $link_name => $links) {
            foreach ($links as $url => $params) {
                $url .= '?';
                foreach ($params as $value) {
                    $url .= $value . '=' . $object->$value . '&';
                }
                $url = substr($url, 0, strlen($url) - 1);
                $href .= "<a href='{$url}'>{$link_name} </a><br>";
            }
        }
        return $href;
    }

    static function th($text)
    {
        return "<th id='modal_th'scope='col'>{$text}</th>";
    }

    static function td($value)
    {
        return "<td id='modal_td'>{$value}</td>";
    }

    static function setTableTitle($table_head, $objects, $show_properties)
    {
        $object = $objects[0];
        $vars = get_object_vars($object);

        foreach ($show_properties as $property => $show_word) {
            $real_key = self::textReplace($property, '_text');
            $get_method = 'get' . ucwords($real_key);
            $get_method = lcfirst(camelize($get_method));
            if (in_array($real_key, array_keys($vars)) || method_exists($object, $get_method)) {
                $table_head .= self::th($show_word);
            }
        }
        return $table_head;
    }

    static function setTableLinks($table, $row_links)
    {
        $link_titles = array_keys($row_links);
        foreach ($link_titles as $title) {
            $table .= self::th($title);
        }
        $table .= "</tr></thead><tbody><tr>";
        return $table;
    }

    static function setTableTd($table, $object, $properties)
    {
        $vars = get_object_vars($object);
        foreach ($properties as $property => $show_word) {
            $real_property = self::textReplace($property, '_text');
            $real_value = $vars[$real_property];
            if (!property_exists($object, $real_property)) {
                $get_method = 'get' . ucwords($real_property);
                $get_method = lcfirst(camelize($get_method));
                if (method_exists($object, $get_method)) {
                    $real_value = $object->$get_method();
                }
            }
            if (strpos($property, '_text')) {
                $result = self::getText($object, $property);
                $real_value = $result ? $result : date('Y-m-d H:i:s', $real_value);
            }

            $table .= self::td($real_value);
        }
        return $table;
    }

    static function modalTable($objects, $properties, $row_links = array())
    {
        $table = "<table id='modal_table' class='table table-striped table-hover'>";
        $table .= "<thead> <tr>";
        $table .= self::setTableTitle($table, $objects, $properties);
        $table = self::setTableLinks($table, $row_links);

        foreach ($objects as $object) {

            $table = self::setTableTd($table, $object, $properties);
            if (count($row_links) > 0) {

                foreach ($row_links as $title => $links) {
                    $result = self::parseHref($links);
                    $real_link = self::buildHref($result, $object);
                    $table .= self::td($real_link);
                }

            }
            $table .= "</tr>";
        }
        $table .= "</tbody></table>";
        return $table;
    }
}