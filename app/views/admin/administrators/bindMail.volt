{{ form(['action':'/admin/administrators/authCode','method':'post']) }}
<div style="margin-bottom: 2px; color: #9d9d9d;"> 邮箱</div>
<div style="border:#8c8c8c 1px solid; width: 280px; height: 50px; float: left; text-align: center;">
    {{ input(['id':'bind_mail_id','type':'hidden','name':'id','value':admin.id]) }}
    {{ input(['id':'bind_mail_type','type':'hidden','name':'type','value':type]) }}
    {{ input(['type':'text','name':'mail_address','id':'div_input','style':['float':'left']]) }}
    {{ input(['type':'button','id':'ajax_send','value':'发送','style':['border-style':'none','background-color':'whitesmoke','margin-top':'5%','outline':'none','color':'#2e6da4']]) }}
</div>
<div style="margin-bottom: 2px; color: #9d9d9d;"> 验证码</div>
{{ input(['type':'text','name':'code','style':['height':'50px','width':'280px','outline':'none']]) }}<br><br>
{{ input(['id':'mail_bind_submit','type':'submit','value':'提交']) }}
</form>
