<div id="admin_login">
    <div id="login_title">
        登录
    </div>
    {{ form(['action':'/admin/administrators/login','method':'post','style':['margin-top':'10%']]) }}
    {{ input(['type':'text','name':'username','placeholder':'用户名','style':['margin-top':'2%']]) }}
    {{ input(['type':'password','name':'password','placeholder':'密码','style':['margin-top':'2%']]) }}
    {{ input(['type':'submit','value':'登录','style':['margin-top':'2%','background-color':'#bce8f1'],'class':'btc btc-primary']) }}
</div>