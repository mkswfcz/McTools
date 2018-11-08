<label>标题</label>
{{ form(['method':'post','action':'/admin/articles/update','style':['width':'240px','height':'500px']]) }}
<input name="id" type="hidden" value="{{ id }}">
{{ input(['type':'text','name':'title','value': title ]) }}
<br>
<div>
    <label>内容</label></div>
{{ text(['name':'content','style':['width':'200px','height':'400px']]) }}
    {{ content }}
</textarea>
<br>
{{ input(['type':'submit','value':'保存']) }}
<br>
</form>
