window.alert = function (str) {
    var shield = document.createElement("DIV");
    shield.id = "shield";
    shield.style.position = "absolute";
    shield.style.left = "50%";
    shield.style.top = "50%";
    shield.style.width = "280px";
    shield.style.height = "150px";
    shield.style.marginLeft = "-140px";
    shield.style.marginTop = "-110px";
    shield.style.zIndex = "25";
    var alertFram = document.createElement("DIV");
    alertFram.id = "alertFram";
    alertFram.style.position = "absolute";
    alertFram.style.width = "280px";
    alertFram.style.height = "150px";
    alertFram.style.left = "50%";
    alertFram.style.top = "20%";
    alertFram.style.marginLeft = "-140px";
    alertFram.style.marginTop = "-110px";
    alertFram.style.textAlign = "center";
    alertFram.style.lineHeight = "150px";
    alertFram.style.zIndex = "300";
    strHtml = "<ul style=\"list-style:none;margin:0px;padding:0px;width:100%\">\n";
    strHtml += " <li style=\"background:#626262;text-align:left;padding-left:20px;font-size:14px;font-weight:bold;height:25px;line-height:25px;border:1px solid #F9CADE;color:white\">Prompt</li>\n";
    strHtml += " <li style=\"background:#787878;text-align:center;font-size:12px;height:95px;line-height:95px;border-left:1px solid #F9CADE;border-right:1px solid #F9CADE;color:#DCC722\">" + str + "</li>\n";
    strHtml += " <li style=\"background:#626262;text-align:center;font-weight:bold;height:30px;line-height:25px; border:1px solid #F9CADE;\"><input type=\"button\" value=\"确 定\" onclick=\"doOk()\" style=\"width:80px;height:20px;background:#626262;color:white;border:1px solid white;font-size:14px;line-height:20px;outline:none;margin-top: 4px\"/></li>\n";
    strHtml += "</ul>\n";
    alertFram.innerHTML = strHtml;
    document.body.appendChild(alertFram);
    document.body.appendChild(shield);
    this.doOk = function () {
        alertFram.style.display = "none";
        shield.style.display = "none";
    }
    alertFram.focus();
    document.body.onselectstart = function () {
        return false;
    };
}

function encrypt(value) {
    var date = new Date();
    var timestamp = Date.parse(date.toLocaleDateString()) / 1000;
    var time_md5 = CryptoJS.MD5(timestamp.toString()).toString();
    var iv = time_md5.slice(0, 16);

    var key_hash = CryptoJS.MD5(time_md5).toString();
    var new_key = CryptoJS.enc.Utf8.parse(key_hash);
    var new_iv = CryptoJS.enc.Utf8.parse(iv);
    var encrypted = CryptoJS.AES.encrypt(value, new_key, {
        iv: new_iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    return encrypted.toString();
}

$(document).on('submit', ".ajax_form", function (event) {
    event.preventDefault();
    var self = $(this);
    var url = self.attr("action");
    var map = {};
    $('input').each(function (index, item) {
        var k = $(this).attr('name');
        var v = $("input[name=" + "'" + k + "']").val();
        if (k !== undefined) {
            map[k] = encrypt(v);
        }
    });
    console.log(map);
    self.ajaxSubmit({
        async: false,
        type: 'POST',
        url: url,
        data: map,
        success: function (result) {
            result = JSON.parse(result);
            if (0 === result.error_code) {
                url = result.data.redirect_url;
                console.log(result.error_reason);
                // window.open(url);
                top.window.location.href = url;
                return;
            } else {
                alert(result.error_reason);
            }

            return false;
        }
    });
})


$(document).on('click', '#modal_submit', function (event) {
    event.preventDefault();
    var url = $('.form-horizontal').attr('action');
    var map = {};
    $('input').each(function (index, item) {
        var k = $(this).attr('name');
        var v = $("input[name=" + "'" + k + "']").val();
        if (k !== undefined) {
            console.log('k:' + k + ' v:' + v);
            map[k] = encrypt(v);
        }
    });
    $('textarea').each(function (index, item) {
        var k = $(this).attr('name');
        var v = $("textarea[name=" + "'" + k + "']").val();
        if (k !== undefined) {
            console.log('k:' + k + ' v:' + v);
            map[k] = encrypt(v);
        }
    })
    console.log(map);
    $.ajax({
        async: false,
        type: 'POST',
        url: url,
        data: map,
        success: function (result) {
            var error_area = document.getElementById('form_error');
            result = JSON.parse(result);
            $("#form_error").empty();
            console.log(result.error_reason)
            error_area.append(result.error_reason);
            return false;
        }
    });
})
