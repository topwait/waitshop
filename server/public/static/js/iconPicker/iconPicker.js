
layui.define(['laypage', 'form'], function (exports) {
    'use strict';

    let IconPicker = function () { this.v = '0.1.beta'; };
    let $    = layui.jquery;
    let _MOD = 'iconPicker';
    let BODY = 'body';

    /**
     * 渲染组件
     */
    IconPicker.prototype.render = function(options) {
        let opts = options,
            // DOM选择器
            elem = opts.elem,
            // 是否分页：true/false
            page = opts.page,
            // 每页显示数量
            limit = opts.limit == null ? 12 : opts.limit,
            // 是否开启搜索：true/false
            search = opts.search == null ? true : opts.search,
            // 点击回调
            click = opts.click,
            // json数据
            data = {},
            // 唯一标识
            tmp = new Date().getTime(),
            // 定义类名
            TITLE        = 'layui-select-title',
            TITLE_ID     = 'layui-select-title-' + tmp,
            ICON_BODY    = 'layui-iconPicker-' + tmp,
            PICKER_BODY  = 'layui-iconPicker-body-' + tmp,
            PAGE_ID      = 'layui-iconPicker-page-' + tmp,
            LIST_BOX     = 'layui-iconPicker-list-box',
            selected     = 'layui-form-selected',
            unselect     = 'layui-unselect';

        let a = {
            /**
             * 初始化
             */
            init: function () {
                data = common.getData['fontClass']();
                a.hideElem().createSelect().createBody().toggleSelect();
                return a;
            },

            /**
             * 隐藏elem
             */
            hideElem: function () {
                $(elem).hide();
                return a;
            },

            /**
             * 绘制select下拉选择框
             */
            createSelect: function () {
                let selectHtml = '<div class="layui-iconPicker layui-unselect layui-form-select" id="'+ ICON_BODY +'">' +
                    '<div class="'+ TITLE +'" id="'+ TITLE_ID +'">' +
                        '<div class="layui-iconPicker-item">'+
                            '<span class="layui-iconPicker-icon layui-unselect">' +
                                '<i class="layui-icon layui-icon-circle-dot"></i>' +
                            '</span>'+
                            '<i class="layui-edge"></i>' +
                        '</div>'+
                    '</div>' +
                    '<div class="layui-anim layui-anim-upbit"></div>';
                $(elem).after(selectHtml);
                return a;
            },

            /**
             * 展开/折叠下拉框
             */
            toggleSelect: function () {
                let item = '#' + TITLE_ID + ' .layui-iconPicker-item,#' + TITLE_ID + ' .layui-iconPicker-item .layui-edge';
                a.event('click', item, function (e) {
                    let $icon = $('#' + ICON_BODY);
                    if ($icon.hasClass(selected)) {
                        $icon.removeClass(selected).addClass(unselect);
                    } else {
                        $icon.addClass(selected).removeClass(unselect);
                    }
                    e.stopPropagation();
                });
                return a;
            },

            /**
             * 绘制主体部分
             */
            createBody: function () {
                // 获取数据
                let searchHtml = '';
                if (search) {
                    searchHtml = '<div class="layui-iconPicker-search">' +
                        '<input class="layui-input">' +
                        '<i class="layui-icon">&#xe615;</i>' +
                        '</div>';
                }

                // 组合dom
                let bodyHtml = '<div class="layui-iconPicker-body" id="'+ PICKER_BODY +'">' +
                    searchHtml +
                        '<div class="'+ LIST_BOX +'"></div> '+
                     '</div>';
                $('#' + ICON_BODY).find('.layui-anim').eq(0).html(bodyHtml);
                a.search().createList().check().page();

                return a;
            },

            /**
             * 绘制图标列表
             * @param text 模糊查询关键字
             * @returns {any}
             */
            createList: function (text) {
                let d        = data,       //图标数组
                    l        = d.length,   //图标数组长度
                    _limit   = limit,      //每页显示多少个图标
                    pageHtml = '',         //分页HTML
                    icons    = [],         //图标DOM数组
                    listHtml = $('<div class="layui-iconPicker-list">'); //图标列表HTML


                for (let i = 0; i < l; i++) {
                    let obj = d[i];

                    // 判断是否模糊查询
                    if (text && obj.indexOf(text) === -1) continue;
                  
                    // 每个图标DOM
                    let icon = '<div class="layui-iconPicker-icon-item" title="'+ obj +'">';
                    icon += '<i class="'+ obj +'"></i>';
                    icon += '</div>';
                    icons.push(icon);
                }

                // 查询出图标后再分页
                l = icons.length;
                let _pages = l % _limit === 0 ? l / _limit : parseInt((l / _limit + 1).toString());
                for (let i = 0; i < _pages; i++) {
                    // 按limit分块
                    let lm = $('<div class="layui-iconPicker-icon-limit" id="layui-iconPicker-icon-limit-'+ (i+1) +'">');

                    for (let j = i * _limit; j < (i+1) * _limit && j < l; j++) {
                        lm.append(icons[j]);
                    }

                    listHtml.append(lm);
                }

                // 无数据
                if (l === 0) {
                    listHtml.append('<p class="layui-iconPicker-tips">无数据</p>');
                }

                // 判断是否分页
                if (page){
                    $('#' + PICKER_BODY).addClass('layui-iconPicker-body-page');
                    pageHtml = '<div class="layui-iconPicker-page" id="'+ PAGE_ID +'">' +
                        '<div class="layui-iconPicker-page-count">' +
                        '<span id="'+ PAGE_ID +'-current">1</span>/' +
                        '<span id="'+ PAGE_ID +'-pages">'+ _pages +'</span>' +
                        '(<span id="'+ PAGE_ID +'-length">'+ l +'</span>)' +
                        '</div>' +
                        '<div class="layui-iconPicker-page-operate">' +
                        '<i class="layui-icon" id="'+ PAGE_ID +'-prev" data-index="0" prev>&#xe603;</i> ' +
                        '<i class="layui-icon" id="'+ PAGE_ID +'-next" data-index="2" next>&#xe602;</i> ' +
                        '</div>' +
                        '</div>';
                }

                $('#' + ICON_BODY).find('.layui-anim').find('.' + LIST_BOX).html('').append(listHtml).append(pageHtml);
                return a;
            },

            // 分页
            page: function () {
                let icon = '#' + PAGE_ID + ' .layui-iconPicker-page-operate .layui-icon';

                $(icon).unbind('click');
                a.event('click', icon, function (e) {
                   let elem = e.currentTarget,
                       total = parseInt($('#' +PAGE_ID + '-pages').html()),
                       isPrev = $(elem).attr('prev') !== undefined,
                       // 按钮上标的页码
                       index = parseInt($(elem).attr('data-index')),
                       $cur = $('#' +PAGE_ID + '-current'),
                       // 点击时正在显示的页码
                       current = parseInt($cur.html());

                    // 分页数据
                    if (isPrev && current > 1) {
                        current=current-1;
                        $(icon + '[prev]').attr('data-index', current);
                    } else if (!isPrev && current < total){
                        current=current+1;
                        $(icon + '[next]').attr('data-index', current);
                    }
                    $cur.html(current);

                    // 图标数据
                    $('.layui-iconPicker-icon-limit').hide();
                    $('#layui-iconPicker-icon-limit-' + current).show();
                    e.stopPropagation();
                });
                return a;
            },

            /**
             * 搜索
             */
            search: function () {
                let item = '#' + PICKER_BODY + ' .layui-iconPicker-search .layui-input';
                a.event('input propertychange', item, function (e) {
                    var elem = e.target,
                        t = $(elem).val();
                    a.createList(t);
                });
                a.event('click', item, function (e) {
                    e.stopPropagation();
                });
                return a;
            },

            /**
             * 点击选中图标
             */
            check: function () {
                let item = '#' + PICKER_BODY + ' .layui-iconPicker-icon-item';
                a.event('click', item, function (e) {
                    let el = $(e.currentTarget).find('i');
                    let icon =  el.attr('class');
                            
                    $('#' + TITLE_ID).find('.layui-iconPicker-item .layui-iconPicker-icon i').html('').attr('class', icon);
                    $('#' + ICON_BODY).removeClass(selected).addClass(unselect);
                    $(elem).attr('value', icon);

                    // 回调
                    if (click) {
                        click({ icon: icon  });
                    }

                });

                return a;
            },

            event: function (evt, el, fn) {
                $(BODY).on(evt, el, fn);
            }
        };

        let common = {
            /**
             * 获取数据
             */
            getData: {
                fontClass: function () {
                    return [
                        "layui-icon layui-icon-circle-dot",
                        
                        "iconfont icon-alipay", "iconfont icon-mnp", "iconfont icon-amount", 
                        "iconfont icon-wallet",  "iconfont icon-kanJia", "iconfont icon-seckill",
                        "iconfont icon-cart", "iconfont icon-location","iconfont icon-box", 
                        "iconfont icon-gift", "iconfont icon-coupon", "iconfont icon-video", 
                        "iconfont icon-notice", "iconfont icon-pinTuan", "iconfont icon-broom", 
                        "iconfont icon-kefu", "iconfont icon-haoping", 
                        "iconfont icon-application","iconfont icon-goods", "iconfont icon-set", 
                        "iconfont icon-order", "iconfont icon-store", "iconfont icon-up_arrow",
                        "iconfont icon-down_arrow", "iconfont icon-home", "iconfont icon-user", 
                        "iconfont icon-text", "iconfont icon-administrators", "iconfont icon-weixin", 
                        "iconfont icon-marketing", "iconfont icon-statistics", "iconfont icon-shutdown",

                        "layui-icon layui-icon-heart-fill", "layui-icon layui-icon-heart", "layui-icon layui-icon-light",
                        "layui-icon layui-icon-time", "layui-icon layui-icon-bluetooth", "layui-icon layui-icon-at",
                        "layui-icon layui-icon-mute", "layui-icon layui-icon-mike", "layui-icon layui-icon-key",
                        "layui-icon layui-icon-gift", "layui-icon layui-icon-email", "layui-icon layui-icon-rss",
                        "layui-icon layui-icon-wifi", "layui-icon layui-icon-logout", "layui-icon layui-icon-android",
                        "layui-icon layui-icon-ios", "layui-icon layui-icon-windows", "layui-icon layui-icon-transfer",
                        "layui-icon layui-icon-service", "layui-icon layui-icon-subtraction", "layui-icon layui-icon-addition",
                        "layui-icon layui-icon-slider", "layui-icon layui-icon-print", "layui-icon layui-icon-export",
                        "layui-icon-cols", "layui-icon layui-icon-screen-restore", "layui-icon layui-icon-screen-full",
                        "layui-icon layui-icon-rate-half", "layui-icon layui-icon-rate", "layui-icon layui-icon-rate-solid",
                        "layui-icon layui-icon-cellphone", "layui-icon layui-icon-vercode", "layui-icon layui-icon-login-wechat",
                        "layui-icon layui-icon-login-qq", "layui-icon layui-icon-login-weibo", "layui-icon layui-icon-password",
                        "layui-icon layui-icon-username", "layui-icon layui-icon-refresh-3", "layui-icon layui-icon-auz",
                        "layui-icon layui-icon-spread-left", "layui-icon layui-icon-shrink-right", "layui-icon layui-icon-snowflake",
                        "layui-icon layui-icon-tips", "layui-icon layui-icon-note", "layui-icon layui-icon-home",
                        "layui-icon layui-icon-senior", "layui-icon layui-icon-refresh", "layui-icon layui-icon-refresh-1",
                        "layui-icon layui-icon-flag", "layui-icon layui-icon-theme", "layui-icon layui-icon-notice", 
                        "layui-icon layui-icon-website", "layui-icon layui-icon-console", "layui-icon layui-icon-face-surprised",
                        "layui-icon layui-icon-set", "layui-icon layui-icon-template-1", "layui-icon layui-icon-app", 
                        "layui-icon layui-icon-template", "layui-icon layui-icon-praise", "layui-icon layui-icon-tread",
                        "layui-icon layui-icon-male", "layui-icon layui-icon-female", "layui-icon layui-icon-camera",
                        "layui-icon layui-icon-camera-fill", "layui-icon layui-icon-more", "layui-icon layui-icon-more-vertical",
                        "layui-icon layui-icon-rmb", "layui-icon layui-icon-dollar", "layui-icon layui-icon-diamond",
                        "layui-icon layui-icon-fire", "layui-icon layui-icon-return", "layui-icon layui-icon-location",
                        "layui-icon layui-icon-read", "layui-icon layui-icon-survey", "layui-icon layui-icon-face-smile", 
                        "layui-icon layui-icon-face-cry", "layui-icon layui-icon-cart-simple", "layui-icon layui-icon-cart",
                        "layui-icon layui-icon-next", "layui-icon layui-icon-prev", "layui-icon layui-icon-upload-drag",
                        "layui-icon layui-icon-upload", "layui-icon layui-icon-download-circle", "layui-icon layui-icon-component",
                        "layui-icon layui-icon-file-b", "layui-icon layui-icon-user", "layui-icon layui-icon-find-fill", "layui-icon layui-icon-add-1",
                        "layui-icon layui-icon-play", "layui-icon layui-icon-pause", "layui-icon layui-icon-headset", "layui-icon layui-icon-video",
                        "layui-icon layui-icon-voice", "layui-icon layui-icon-speaker",
                        "layui-icon layui-icon-fonts-del", "layui-icon layui-icon-fonts-code", "layui-icon layui-icon-unlink", "layui-icon layui-icon-picture",
                        "layui-icon layui-icon-link", "layui-icon layui-icon-tabs", "layui-icon layui-icon-radio", "layui-icon layui-icon-circle",
                        "layui-icon layui-icon-edit", "layui-icon layui-icon-share", "layui-icon layui-icon-delete", "layui-icon layui-icon-form",
                        "layui-icon layui-icon-cellphone-fine", "layui-icon layui-icon-dialogue", "layui-icon layui-icon-fonts-clearv",
                        "layui-icon layui-icon-layer", "layui-icon layui-icon-date", "layui-icon layui-icon-water", "layui-icon layui-icon-code-circle",
                        "layui-icon layui-icon-carousel", "layui-icon layui-icon-prev-circle", "layui-icon layui-icon-layouts",
                        "layui-icon layui-icon-util", "layui-icon layui-icon-templeate-1", "layui-icon layui-icon-upload-circle", "layui-icon layui-icon-tree",
                        "layui-icon layui-icon-table", "layui-icon layui-icon-chart", "layui-icon layui-icon-chart-screen", "layui-icon layui-icon-engine",
                        "layui-icon layui-icon-file", "layui-icon layui-icon-set-sm", "layui-icon layui-icon-reduce-circle", "layui-icon layui-icon-add-circle",
                        "layui-icon layui-icon-404", "layui-icon layui-icon-about", "layui-icon layui-icon-search", "layui-icon layui-icon-set-fill", "layui-icon layui-icon-group",
                        "layui-icon layui-icon-friends", "layui-icon layui-icon-reply-fill", "layui-icon layui-icon-menu-fill", "layui-icon layui-icon-log", "layui-icon layui-icon-picture-fine",
                        "layui-icon layui-icon-face-smile-fine", "layui-icon layui-icon-list", "layui-icon layui-icon-release", "layui-icon layui-icon-ok", "layui-icon layui-icon-help",
                        "layui-icon layui-icon-chat", "layui-icon layui-icon-top", "layui-icon layui-icon-star", "layui-icon layui-icon-star-fill", "layui-icon layui-icon-close-fill",
                        "layui-icon layui-icon-close", "layui-icon layui-icon-ok-circle", "layui-icon layui-icon-add-circle-fine"
                    ];
                   
                }
            }
        };

        a.init();
        return new IconPicker();
    };

    /**
     * 选中图标
     * @param filter lay-filter
     * @param iconName 图标名称，自动识别fontClass/unicode
     */
    IconPicker.prototype.checkIcon = function (filter, iconName){
        let p = $('*[lay-filter='+ filter +']').next().find('.layui-iconPicker-item .layui-icon');
            p.html('').attr('class', iconName);
    };

    layui.link(layui.cache.base + 'iconPicker/iconPicker.css');
    exports(_MOD, new IconPicker());
});