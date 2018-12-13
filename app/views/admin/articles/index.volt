{{ modal_link('新建','addUserModal') }}
{% include 'admin/articles/form.volt' %}

{#{{ modalForm('post','/admin/articles/update','updateArticle','return submit()','创建') }}#}
{#<input type="text" name="id" value="{{ id }}" hidden>#}
{#{{ modalInput('标题','text','title',{{ title}},'请输入标题') }}#}
{#{{ modalText('内容','content',{{ content}},'请输入内容') }}#}
{#{{ modalFooter('保存') }}#}

{{ modalTable(articles,['id':'序号','title':'标题','content':'内容','author':'作者','created_at_text':'创建','updated_at_text':'更新'],
    ['操作':['edit':['/admin/articles/edit':['id']],'show':['/admin/articles/show':['id']]]]) }}
<div><a href="/admin/articles/index?page={{ last_page }}" style="float: left">上一页</a> <a href="/admin/articles/index?page={{ next_page }}" style="float: right">下一页</a></div>