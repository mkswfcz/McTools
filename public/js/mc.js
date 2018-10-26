function urlEncode($url) {
}

$(document).on('submit', ".ajax_form", function (event) {
    event.preventDefault();
    var self = $(this);
    var url = self.attr("action");
    var map = {};
    var input = $('input').each(function (index, item) {
        var k = $(this).attr('name');
        var v = $("input[name=" + "'" + k + "']").val();
        if (k !== undefined) {
            map[k]=v;
        }
    });
    alert(map);
    $.ajax({
        async: false,
        type: 'POST',
        url: url,
        data: map
    });
})
