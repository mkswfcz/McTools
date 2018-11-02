<?php

class voltFun
{
    static function form($elements)
    {
        $form = '';
        foreach ($elements as $element => $value) {
            if ($element == 'style') {
                foreach ($value as $k => $v) {
                    $form .= "style={$k}:{$v};";
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

    static function input($elements)
    {
        $input = '';
        foreach ($elements as $element => $value) {
            if ($element == 'style') {
                foreach ($value as $k => $v) {
                    $input .= "style={$k}:{$v};";
                }
            } else {
                $input .= " {$element}='{$value}' ";
            }
        }
        $input = "<input {$input}><br>";
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
        $reflect = new ReflectionClass($object);
        $methods = $reflect->getMethods();
        foreach ($methods as $method) {
            $text_method = lcfirst(camelize($property)) . 'Text';
            debug('method name: ',$method->name,$method->class, $text_method);
            if ($text_method == $method->name) {
                debug('text: ', $text_method);
                return $object->$text_method();
            }
        }
        return false;
    }

    static function modalTable($objects, $properties)
    {
        $table = "<table id='modal_table' class='table table-striped table-hover'>";
        $table .= "<thead> <tr>";
        $i = 1;
        foreach ($objects as $key => $object) {
            $vars = get_object_vars($object);
            if (1 === $i) {
                $keys = array_keys($vars);
                foreach ($properties as $k => $v) {
                    $temp_key = '';
                    if (strpos($k, '_text')) {
                        $temp_key = $k;
                        $k = str_replace('_text', '', $k);
                    }
                    if (in_array($k, $keys)) {
                        if (!isNull($temp_key)) {
                            $k = $temp_key;
                        }
                        $table .= "<th id='modal_th'scope='col'>{$properties[$k]}</th>";
                    }

                }
                $table .= "</tr></thead><tbody>";
            }
            $table .= "<tr>";
            foreach ($vars as $key => $value) {
                if (isset($properties[$key . '_text'])) {
                    #todo 若存在propertyText 则调用该方法
                    $result = self::getText($object, $key);
                    debug('res: ', $result);
                    $value = $result ? $result : date('Y-m-d H:i:s', $value);
                }
                $table .= "<td id='modal_td'>{$value}</td>";
            }
            $table .= "</tr>";
            $i++;
        }
        $table .= "</tbody></table>";
        return $table;
    }
}