/**
 * 工具JS
 * 未经允许请勿传播
 * @author windy
 * @email 544889826@qq.com
 */
layui.use(["form", "element"]);

let lock = {};
let load = {};
let wait = {
    /**
     * 异步请求封装
     * @author windy
     * @param options
     * @returns {Promise<unknown>}
     */
    ajax: function (options) {
        let loading = undefined;
        return new Promise((resolve, reject) => {
            $.ajax({
                url: options.url,
                data: options.data || {},
                type: options.type || "GET",
                async: options.async || true,
                timeout: options.timeout || 3000,
                dataType: "json",
                beforeSend: function () {
                    if (options.beforeSend === undefined) {
                        if (lock[options.url.replace("/", "_")] !== undefined) {
                            return layer.msg("操作频繁,请稍后再试", {time: 1500});
                        }
                        lock[options.url.replace("/", "_")] = true;
                        load[options.url.replace("/", "_")] = setTimeout(function () {
                            loading = layer.load(1, {shade: [0.1, "#fff"]})
                        }, 1500)
                    }
                },
                success: function (res) {
                    resolve(res);
                },
                error: function(jqXHR, error, errorThrown) {
                    layer.msg("网络错误:"+errorThrown, {icon: 2, time: 1500}, function () {
                        reject(errorThrown);
                        return false;
                    })
                },
                complete: function (xhr, status) {
                    if (status === "timeout") {
                        layer.msg("请求超时，请重试", {icon: 2, time: 1500}, function () {
                            if (loading !== undefined) {
                                layer.close(loading)
                            }
                            return false;
                        });
                    }

                    clearTimeout(load[options.url.replace("/", "_")]);
                    delete load[options.url.replace("/", "_")];
                    delete lock[options.url.replace("/", "_")];
                    if (loading !== undefined) {
                        layer.close(loading)
                    }
                }
            });
        });
    }
    /**
     * 弹出窗口
     * @author windy
     * @param options
     */
    ,popup: function (options) {
        let width = options.area[0] || "90%";
        let height = options.area[1] || "90%";

        if (width.endsWith('px')) {
            if ($(window).width() <= width.replace("/px/", "")) {
                width = "90%";
            }
        }

        if (height.endsWith('px')) {
            if ($(window).height() <= height.replace("/px/", "")) {
                height = "90%";
            }
        }

        if ($(window).width() < 768 || layui.device().mobile) {
            width = "90%";
            height = "90%";
        }
        layer.open({
            type: options.type || 2
            ,title: options.title || "弹窗标题"
            ,shadeClose: options.shadeClose || true
            ,area: [width, height]
            ,content: options.url,
            success: function(layero, index){
                let iframeWindow  = window["layui-layer-iframe" + index]
                let iframeElement = $(layero).find("iframe").contents();
                iframeElement.find("#closePopupWindow").click(function(){
                    layer.close(index)
                });
                options.success(iframeWindow, index, iframeElement)
            }
        });
    }
    /**
     * 绑定操作事件(需提前引入layui)
     * 包含:[表格工具, 选项卡切换, 表单事件, 普通按钮]
     * @author windy
     * @param active
     * @constructor
     */
    ,BindEvents: function (active) {
        // 绑定自定义事件
        $(document).on("click", ".layEvent", function () {
            let type = $(this).attr("lay-event");
            let obj  = $(this).attr("lay-data");
            active[type] ? active[type].call(this, obj) : "";
        });

        layui.use(["element", "table", "form"], function () {
            // 监听表格头部事件
            layui.table.on("toolbar(wait-table-lists)", function(obj){
                let type = obj.event;
                active[type] ? active[type].call(this, obj) : "";
            });

            // 监听表格右侧事件
            layui.table.on("tool(wait-table-lists)", function(obj){
                let type = obj.event;
                active[type] ? active[type].call(this, obj) : "";
            });

            // 监听是否切换
            layui.form.on("switch(switch-status)", function(obj){
                active["change"] ? active["change"].call(this, obj) : "";
            });
        });
        return active;
    }
    /**
     * 渲染数据表格
     * @author windy
     * @param options
     */
    ,table: function (options) {
        layui.table.render({
            elem: options.elem
            ,url: options.url
            ,method: options.method || "GET"
            ,toolbar: options.toolbar === false ? false : options.toolbar || "#toolbar"
            ,defaultToolbar: ["filter", "exports", "print"]
            ,cols: options.cols
            ,page: options.page || true
            ,limit: options.limit || 20
            ,limits: [20, 40, 60, 80, 100, 120, 140, 160]
            ,parseData: function(res){
                return {
                    "code": res.code,
                    "msg": res.msg,
                    "count": res.data.count,
                    "data": res.data.list,
                };
            },
            done: function(){
                setTimeout(function () {
                    $(".layui-table-main tr").each(function (index, val) {
                        $($(".layui-table-fixed-l .layui-table-body tbody tr")[index]).height($(val).height());
                        $($(".layui-table-fixed-r .layui-table-body tbody tr")[index]).height($(val).height());
                    });
                }, 200);
            }
        });
    }
    /**
     * 删除上传文件
     * @author windy
     */
    ,delUploadFile: function () {
        $(document).on("click", ".del-upload-btn", function () {
            $(this).parent().parent().find(".add-upload-elem").show();
            $(this).parent().remove();
        });
    }
    /**
     * 上传图片
     * @author windy
     * @param options
     * @returns {Promise<unknown>}
     */
    ,imageUpload: function (options) {
        let that   = options.that;
        let limit  = options.limit || 1;
        let field  = options.field || "image";
        let delBtn = options.delBtn || "del-upload-btn";
        let width  = options.width || "780px";
        let height = options.height || "600px";
        let relative = options.relative || false;
        if (layui.device().mobile) { width = "90%"; height = "90%"; }
        return new Promise((resolve) => {
            parent.layer.open({
                type: 2
                , title: "上传图片"
                , shadeClose: true
                , maxmin: true
                , anim: 1
                , shade: 0.3
                , area: [width, height]
                , content: "/admin/file/lists?type=1",
                success: function (layero, index) {
                    let iframeNode = $(layero).find("iframe").contents();
                    iframeNode.find("#okFile").click(function () {
                        let urls = [];
                        let relativeUrls = [];
                        let thumbnail = that.parent().find(".thumbnail");
                        iframeNode.find(".warehouse li.active").each(function () {
                            urls.push($(this).attr("title"));
                            relativeUrls.push($(this).children("img").attr("alt"))
                        });

                        if (urls.length <= 0) {
                            parent.layer.msg("请至少选择一张图片");
                            return false;
                        }

                        if (thumbnail.length >= limit && options.verify === undefined) {
                            parent.layer.msg("您已达到可选限制");
                            return false;
                        }

                        if (urls.length > limit) {
                            parent.layer.msg("限制只能选" + limit + "张");
                            return false;
                        }

                        if (urls.length + thumbnail.length > limit && options.verify === undefined) {
                            parent.layer.msg("您最多只能选" + (limit - thumbnail.length) + "张");
                            return false;
                        }

                        urls.forEach(function (url) {
                            let template = '<div class="thumbnail">';
                                template += '<img src="' + url + '" alt="img">';
                                template += '<input type="hidden" name="' + field + '" value="' + url + '">';
                                template += '<i class="' + delBtn + '">x</i>';
                                template += '</div>';
                                that.parent().prepend(template);
                        });

                        if (that.parent().find(".thumbnail").length >= limit) {
                            that.hide();
                        }
                        parent.layer.close(index);
                        resolve(relative ? relativeUrls : urls);
                    });
                }
            });
        });
    }
    /**
     * 上传视频
     * @author windy
     * @param options
     * @returns {Promise<unknown>}
     */
    ,videoUpload: function (options) {
        let that   = options.that;
        let limit  = options.limit || 1;
        let field  = options.field || "video";
        let delBtn = options.delBtn || "del-upload-btn";
        let width  = options.width || "780px";
        let height = options.height || "500px";
        if (layui.device().mobile) { width = "90%"; height = "90%"; }
        return new Promise((resolve) => {
            parent.layer.open({
                type: 2
                , title: "上传视频"
                , shadeClose: true
                , maxmin: true
                , anim: 1
                , shade: 0.3
                , area: [width, height]
                , content: "/admin/file/lists?type=2",
                success: function (layero, index) {
                    let iframeNode = $(layero).find("iframe").contents();
                    iframeNode.find("#okFile").click(function () {
                        let urls = [];
                        let thumbnail = that.parent().find(".thumbnail");
                        iframeNode.find(".warehouse li.active").each(function () {
                            urls.push($(this).attr("title"));
                        });

                        if (urls.length <= 0) {
                            parent.layer.msg("请至少选择一个视频");
                            return false;
                        }

                        if (thumbnail.length >= limit) {
                            parent.layer.msg("您已达到可选限制");
                            return false;
                        }

                        if (urls.length > limit) {
                            parent.layer.msg("限制只能选" + limit + "个");
                            return false;
                        }

                        if (urls.length + thumbnail.length > limit) {
                            parent.layer.msg("您最多只能选" + (limit - thumbnail.length) + "个");
                            return false;
                        }

                        urls.forEach(function (url) {
                            let template = '<div class="thumbnail">';
                                template += '<video width="100" height="100" src="'+url+'" controls="" autoplay="false"></video>';
                                template += '<input type="hidden" name="' + field + '" value="' + url + '">';
                                template += '<i class="' + delBtn + '">x</i>';
                                template += '</div>';
                                that.parent().prepend(template);
                        });

                        if (that.parent().find(".thumbnail").length >= limit) {
                            that.hide();
                        }
                        parent.layer.close(index);
                        resolve(urls);
                    });
                }
            });
        });
    }
};
