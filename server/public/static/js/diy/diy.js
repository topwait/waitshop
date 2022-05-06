(function () {

    // 解决火狐浏览器拖动新增tab
    document.body.ondrop = function (event) {
        event.preventDefault();
        event.stopPropagation();
    };

    // 默认数据
    let defaultData = {};

    /***
    /***
     * 前端可视化diy
     * @constructor
     */
    function diyPhone(initData, diyData, opts) {
        defaultData = initData;
        this.init(diyData, opts);
    }

    diyPhone.prototype = {
        init: function (data, opts) {
            new Vue({
                el: '#diyView'
                ,error: ''
                ,data: {
                    diyData: data        // diy数据
                    ,selectedIndex: -1   // 当前选中的元素（下标）
                    ,curItem: {}         // 当前选中的diy元素
                    ,opts: opts          // 外部数据
                }
                ,methods: {
                    /**
                     * 新增Diy组件
                     * @param key
                     */
                    onAddItem: function (key) {
                        // 复制默认diy组件数据
                        let data = $.extend(true, {}, defaultData[key]);
                        // 新增到diy列表数据
                        this.diyData.items.push(data);
                        // 编辑当前选中的元素
                        this.onEditer(this.diyData.items.length - 1);
                    },

                    /**
                     * 编辑Diy组件
                     * @param index
                     */
                    onEditer: function (index) {
                        // 记录当前选中元素的索引
                        this.selectedIndex = index;
                        // 当前选中的元素数据
                        this.curItem = this.selectedIndex === 'page' ? this.diyData.page
                            : this.diyData.items[this.selectedIndex];
                        // 注册编辑器事件
                        // this.initEditor();
                    },

                    /**
                     * 删除diy元素
                     * @param index
                     */
                    onDeleteItem: function (index) {
                        let _this = this;
                        layer.confirm('确定要删除吗？', function (layIdx) {
                            _this.diyData.items.splice(index, 1);
                            _this.selectedIndex = -1;
                            layer.close(layIdx);
                        });
                    },

                    /**
                     * 编辑器：选择图片
                     * @param source
                     * @param index
                     */
                    onEditorSelectImage: function (source, index) {
                        wait.imageUpload({
                            that: $(this),
                            filed: "image",
                            limit: 1,
                            isTpl: false,
                            isOpenWindows: false
                        }).then((res) => {
                            console.log(res)
                            source[index] = res[0];
                        });
                    },

                    /**
                     * 拖动diy元素更新当前索引
                     * @param e
                     */
                    onDragItemEnd: function (e) {
                        this.onEditer(e.newIndex);
                    },

                    /**
                     * 编辑器：重置颜色
                     * @param holder
                     * @param attribute
                     * @param color
                     */
                    onEditorResetColor: function (holder, attribute, color) {
                        holder[attribute] = color;
                    },

                    /**
                     * 编辑器：删除data元素
                     * @param index
                     * @param selectedIndex
                     */
                    onEditorDeleteData: function (index, selectedIndex) {
                        if (this.diyData.items[selectedIndex].data.length <= 1) {
                            layer.msg("至少保留一个", {anim: 6});
                            return false;
                        }
                        this.diyData.items[selectedIndex].data.splice(index, 1);
                    },

                    /**
                     * 编辑器：添加data元素
                     */
                    onEditorAddData: function () {
                        let newDataItem = $.extend(true, {}, defaultData[this.curItem.type].data[0]);
                        this.curItem.data.push(newDataItem);
                    },

                    /**
                     * 编辑器：商品选择
                     * @param item
                     */
                    onSelectGoods: function (item) {
                        let uris = {
                            goods: "/admin/data/goods?scene=normal",
                            teamGoods: "/admin/data/teamGoods?scene=normal",
                            bargainGoods: "bargain.goods/lists&status=10",
                            sharpGoods: "sharp.goods/lists&status=10"
                        };
                        layer.open({
                            type: 2
                            , title: "选择商品"
                            , area: ["850px", "630px"]
                            , offset: "auto"
                            , anim: 1
                            , closeBtn: 1
                            , shade: 0.3
                            , btn: ["确定", "取消"]
                            , content: uris[item.type]
                            , yes: function (index, layero) {
                                let iframeWin = window[layero.find("iframe")[0]["name"]];
                                let data = iframeWin.getSelectedData();
                                data.forEach(function (itm) {
                                    item.data.push(itm);
                                });
                                layer.close(index);
                            }
                        });
                    },

                    /**
                     * 提交后端保存
                     * @returns {boolean}
                     */
                    onSubmit: function () {
                        if (this.diyData.items.length <= 0) {
                            layer.msg("至少存在一个组件", {anim: 6});
                            return false;
                        }
                        $.post("", {data: data}, function (result) {
                            if (result.code === 0) {
                                let index = parent.layer.getFrameIndex(window.name);
                                parent.layer.close(index);
                                parent.layer.msg(result.msg, {icon: 1});
                                return true;
                            }
                            layer.msg(result.msg, {icon: 2})  ;
                            return false;
                        });
                    }

                }
            });
        }

    };

    window.diyPhone = diyPhone;

})(window);