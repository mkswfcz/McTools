<div id="admin_login">
    <div id="login_title">
        登录
    </div>
    {{ form(['action':'/admin/administrators/login','method':'post','class':'ajax_form','style':['margin-top':'10%','height':'50%']]) }}
    {{ input(['type':'text','name':'username','placeholder':'用户名','class':'admin_input','style':['background-color':'whitesmoke']]) }}
    {{ input(['type':'password','name':'password','placeholder':'密 码','class':'admin_input','style':['background-color':'whitesmoke']]) }}
    {{ input(['type':'submit','value':'登录','class':'login_submit']) }}
</div>