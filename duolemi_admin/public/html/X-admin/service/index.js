$(function () {

    $.ajax({
        url: "/admin/index",
        type: "GET",
        dataType: "json",
        success: function (res){
            if (res.code == 0) {
                //对页面进行赋值
                $("body").attr("style","dispaly:block");
                $("#username").html(res.data.username);
            } else {
                location.href = './login.html'
            }
        },
        error: function (data) {
            //$.messager.alert('错误', data.msg);
        }
    });

    //退出
    $("#login").click(function () {

        layer.confirm('确认要退出吗?', {icon: 3, title:'提示'}, function(index){
            $.ajax({
                url: "/admin/logout",
                type: "GET",
                dataType: "json",
                success:function(data) {
                    location.href = './login.html'
                }
            });
        });
    });
});