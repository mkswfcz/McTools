{{ modalForm('post','/admin/articles/create','addUserModal','return submit()','创建') }}
{{ modalInput('标题','text','title','','请输入标题') }}
{{ modalText('内容','content','','请输入内容') }}
{{ modalFooter('保存') }}
