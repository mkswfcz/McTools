<!DOCTYPE HTML>
<html>
<head>
    <script>
        function exchange() {
            var source = $('#language_source');
            var target = $('#language_target');
            var origin_val = source.val();
            source.text(target.val());
            target.text(origin_val);
        }

        function trans() {
            var lang = $('#modal_select option:selected').val();
            var url = '/admin/translate/translate';
            var content = $('#language_source').val();
            $.ajax({
                async: false,
                type: 'POST',
                url: url,
                data: {"lang": lang, "content": content},
                success: function (result) {
                    var res = JSON.parse(result);
                    var target = res.data.target_content;
                    $('#language_target').text(target);
                }
            });
        }
    </script>
</head>
{{ select(name,langs) }}<br><br>
<div id="translate">
    {{ text(['id':'language_source','style':['height':'120px']]) }}
    你好!
    </textarea>
    <button style="text-align: center;" onclick="exchange()"> switch</button>
    {{ text(['id':'language_target','name':'target_language','style':['height':'120px']]) }}
    </textarea>
</div>

<button id="trans_button" onclick="trans()"> translate</button>
