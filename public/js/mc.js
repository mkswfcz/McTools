function encrypt(value) {
    var date = new Date();
    var t = CryptoJS.MD5(date.toLocaleDateString());
    var i = t.toString().slice(0, 16);

    var key_hash = CryptoJS.MD5(t.toString()).toString();
    var new_key = CryptoJS.enc.Utf8.parse(key_hash);
    var new_iv = CryptoJS.enc.Utf8.parse(i);
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

    $.ajax({
        async: false,
        type: 'POST',
        url: url,
        data: map,
        success: function (result) {
            result = JSON.parse(result);
            if (0 === result.error_code) {
                alert(result.error_code);
            }
        }
    });
})
