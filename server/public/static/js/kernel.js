/**
 * 核心JS
 * 未经允许请勿传播
 * @author windy
 * @email 544889826@qq.com
 */
layui.use(["jquery", "element"], function() {
    let $ = layui.jquery;                          //加载JQuery
    let tplBodyNode    = $(".tpl-body");           //主体内容节点
    let tplBodyShade   = $(".tpl-body-shade");     //辅助遮罩节点
    let tplHeaderNode  = $(".tpl-header");         //头部内容节点
    let tplSidebarNode = $(".tpl-sidebar");        //侧边内容节点
    let iframeNode     = $(".tpl-body iframe");    //框架内容节点
    let iframeUrls     = iframeNode.attr("src");   //页面加载地址

    /** 初始化菜单(判断第一个是否存在子菜单) **/
    let firstMenuNode = $(".tpl-sidebar .tpl-menu-item");
    if (firstMenuNode.eq(0).length > 0) {
        if (firstMenuNode.eq(0).children("dl.tpl-second-menu").length > 0) {
            firstMenuNode.eq(0).children("dl.tpl-second-menu").show();
            tplBodyNode.css({"left":"240px"})
        }
    }

    /** 主菜单 **/
    tplSidebarNode.on("click", ".tpl-menu-item",  function () {
        if (!$(this).children("a").hasClass("active")) {
            // 切换菜单
            $(this).siblings().find("a").removeClass("active");
            $(this).children("a").addClass("active");
            // 子级菜单
            $(this).siblings().find(".tpl-second-menu").removeClass("activate");
            $(this).siblings().find(".tpl-second-menu").removeAttr("style");
            if ($(this).children(".tpl-second-menu").length) {
                // 预载节点
                let Node        = $(this).children(".tpl-second-menu").find("a");
                let subMenuNode = $(this).children(".tpl-second-menu");
                let shrinkNode  = $(".tpl-side-shrink").length;
                // 显示子菜单
                subMenuNode.addClass("activate");
                if ($(window).width() < 768 || layui.device().mobile) {
                    subMenuNode.css("display", "block");
                }
                Node.eq(0).children("i").length ? Node.eq(1).addClass("active") : Node.eq(0).addClass("active");
                iframeUrls = Node.eq(0).children("i").length ? Node.eq(1).attr("data-url") : Node.eq(0).attr("data-url");
                // 图标菜单
                shrinkNode ? tplBodyNode.css({"left": "180px"}) : tplBodyNode.css({"left": "240px"});
            } else {
                iframeUrls = $(this).children("a").attr("data-url");
                $(".tpl-side-shrink").length ? tplBodyNode.css({"left":"60px"}) : tplBodyNode.removeAttr("style");
            }
            // 跳转页面
            iframeNode.attr("src", iframeUrls);
            return true;
        }
    });

    /** 子级菜单 **/
    tplSidebarNode.on("click", ".tpl-second-menu dd a", function () {
        if ($(this).attr("href")) {
            if ($(this).children("i").hasClass("layui-icon-triangle-d")) {
                $(this).children("i").removeClass("layui-icon-triangle-d");
                $(this).children("i").addClass("layui-icon-triangle-r");
                $(this).next().stop().slideUp();
            } else {
                $(this).children("i").removeClass("layui-icon-triangle-r");
                $(this).children("i").addClass("layui-icon-triangle-d");
                $(this).next().stop().slideDown();
            }
        } else {
            $(".tpl-sidebar .tpl-second-menu dd a").removeClass("active");
            $(this).addClass("active");
            iframeUrls = $(this).attr("data-url");
            iframeNode.attr("src", iframeUrls);
        }
    });

    /** 图标菜单 **/
    tplHeaderNode.on("click", ".stretch i", function () {
        // 获取节点
        let appNode     = $("#app");
        let shrinkNode  = $(".tpl-side-shrink");
        let submenuNode = $(".tpl-sidebar .tpl-menu-item > a.active").next();

        if ($(window).width() < 768 || layui.device().mobile) {
            // 小屏幕或手机端菜单
            if ($(this).hasClass("layui-icon-shrink-right")) {
                $(this).removeClass("layui-icon-shrink-right");
                $(this).addClass("layui-icon-spread-left");
                tplBodyShade.addClass("activate");
                tplSidebarNode.addClass("develop-sidebar");
                tplHeaderNode.children(".layui-layout-left").addClass("develop-sidebar");
                if (submenuNode.length) {
                    submenuNode.addClass("activate");
                    submenuNode.css("display", "block");
                }
            }
            else {
                $(this).removeClass("layui-icon-spread-left");
                $(this).addClass("layui-icon-shrink-right");
                tplBodyShade.removeClass("activate");
                tplSidebarNode.removeClass("develop-sidebar");
                tplHeaderNode.children(".layui-layout-left").removeClass("develop-sidebar");
                $(".tpl-sidebar .tpl-menu-item .tpl-second-menu").removeClass("activate");
            }
        } else {
            // 判断伸缩小图标菜单
            if (!shrinkNode.length) {
                appNode.addClass("tpl-side-shrink");
                submenuNode.length ? tplBodyNode.css({"left":"180px"}) : tplBodyNode.css({"left":"60px"});
            } else {
                appNode.removeClass("tpl-side-shrink");
                submenuNode.length ? tplBodyNode.css({"left":"240px"}) : tplBodyNode.css({"left":"120px"});
            }
        }
    });

    /** 关闭遮罩 **/
    tplBodyShade.on("click", "", function () {
        $(".tpl-header a.stretch i").removeClass("layui-icon-spread-left");
        $(".tpl-header .stretch i").addClass("layui-icon-shrink-right");
        $(".tpl-sidebar").removeClass("develop-sidebar");
        $(".tpl-header .layui-layout-left").removeClass("develop-sidebar");
        if ($(window).width() < 770 || layui.device().mobile) {
            if ($(".tpl-sidebar .tpl-menu-item a.active").next().length) {
                $(".tpl-sidebar .tpl-menu-item .tpl-second-menu").removeClass("activate");
                $(".tpl-sidebar .tpl-menu-item .active").next().removeAttr("style");
            }
        }
        $(this).removeClass("activate");
    });

    /** 监听页面宽度改变 **/
    $(window).resize(function(){
        if ($(window).width() > 770 || !layui.device().mobile) {
            $(".tpl-sidebar .tpl-menu-item .tpl-second-menu").removeAttr("style");
            if ($(".tpl-sidebar .tpl-menu-item a.active").next()) {
                $(".tpl-sidebar .tpl-menu-item .active").next().addClass("activate");
            }
        }
        if ($(window).width() <= 768 || layui.device().mobile) {
            if (tplSidebarNode.hasClass("develop-sidebar")) {
                tplSidebarNode.removeClass("develop-sidebar");
            }
            if (tplHeaderNode.children(".layui-layout-left").hasClass("develop-sidebar")) {
                tplHeaderNode.children(".layui-layout-left").removeClass("develop-sidebar");
            }
        }
        return true;
    });

    /** 刷新当前页面 **/
    tplHeaderNode.on("click", ".refresh", function () {
        iframeNode.attr("src", iframeNode.attr("src"))
    });

    /** 点击按钮全屏或退出全屏 **/
    tplHeaderNode.on("click", ".fullscreen", function () {
        let docElm = document.documentElement;
        if ($(this).children("i").hasClass("layui-icon-screen-restore")) {
            document.exitFullscreen();
            $(this).children("i").eq(0).removeClass("layui-icon-screen-restore");
        } else {
            docElm.requestFullscreen();
            $(this).children("i").eq(0).addClass("layui-icon-screen-restore");
        }
    });

    /** 按键盘全屏或退出全屏 **/
    tplHeaderNode.on("keydown", "", function (event) {
        event = event || window.event || arguments.callee.caller.arguments[0];
        //按Esc
        if (event && event.keyCode === 27) {
            $(".fullscreen").children("i").eq(0).removeClass("layui-icon-screen-restore");
        }
        //按F11
        if (event && event.keyCode === 122) {
            $(".fullscreen").children("i").eq(0).addClass("layui-icon-screen-restore");
        }
    });
});