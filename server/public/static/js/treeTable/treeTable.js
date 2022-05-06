layui.define(["layer", "table"], function (exports) {
    let $ = layui.jquery;
    let layer = layui.layer;
    let table = layui.table;

    let treeTable = {
        // 渲染树形表格
        render: function (param) {
            // 检查参数
            if (!treeTable.checkParam(param)) {
                return;
            }
            // 获取数据
            if (param.data) {
                treeTable.init(param, param.data);
            } else {
                $.getJSON(param.url, param.where, function (res) {
                    treeTable.init(param, res.data);
                });
            }
        },
        // 渲染表格
        init: function (param, data) {
            let mData = [];
            let doneCallback = param.done;
            let tNodes = data;
            // 补上id和pid字段
            for (let i = 0; i < tNodes.length; i++) {
                let tt = tNodes[i];
                if (!tt.id) {
                    if (!param.treeIdName) {
                        layer.msg('参数treeIdName不能为空', {icon: 5});
                        return;
                    }
                    tt.id = tt[param.treeIdName];
                }
                if (!tt.pid) {
                    if (!param.treePidName) {
                        layer.msg('参数treePidName不能为空', {icon: 5});
                        return;
                    }
                    tt.pid = tt[param.treePidName];
                }
            }

            // 对数据进行排序
            let sort = function (s_pid, data) {
                for (let i = 0; i < data.length; i++) {
                    if (data[i].pid === s_pid) {
                        let len = mData.length;
                        if (len > 0 && mData[len - 1].id === s_pid) {
                            mData[len - 1].isParent = true;
                        }
                        mData.push(data[i]);
                        sort(data[i].id, data);
                    }
                }
            };
            sort(param.treeSpid, tNodes);

            // 重写参数
            param.url = undefined;
            param.data = mData;
            param.page = {
                count: param.data.length,
                limit: param.data.length
            };
            param.cols[0][param.treeColIndex].templet = function (d) {
                let mId = d.id;
                let mPid = d.pid;
                let isDir = d.isParent;
                let emptyNum = treeTable.getEmptyNum(mPid, mData);
                let iconHtml = '';
                for (let i = 0; i < emptyNum; i++) {
                    iconHtml += '<span class="treeTable-empty"></span>';
                }
                //是否显示文件夹图标
                if (param.treeDirIcon) {
                    if (isDir) {
                        iconHtml += '<i class="layui-icon layui-icon-triangle-d"></i> <i class="layui-icon layui-icon-layer"></i>';
                    } else {
                        iconHtml += '<i class="layui-icon layui-icon-file"></i>';
                    }
                } else {
                    if (isDir) {
                        iconHtml += '<i class="layui-icon layui-icon-triangle-d"></i>';
                    } else {
                        iconHtml += '';
                    }
                }
                iconHtml += '&nbsp;&nbsp;';
                let ttype = isDir ? 'dir' : 'file';
                let vg = '<span class="treeTable-icon open" lay-tid="' + mId + '" lay-tpid="' + mPid + '" lay-ttype="' + ttype + '">';
                return vg + iconHtml + d[param.cols[0][param.treeColIndex].field] + '</span>'
            };

            param.done = function (res, curr, count) {
                $(param.elem).next().addClass('treeTable');
                $('.treeTable .layui-table-page').css('display', 'none');
                $(param.elem).next().attr('treeLinkage', param.treeLinkage);
                if (param.treeDefaultClose) {
                    treeTable.foldAll(param.elem);
                }
                if (doneCallback) {
                    doneCallback(res, curr, count);
                }
            };

            // 渲染表格
            table.render(param);
        },
        // 计算缩进的数量
        getEmptyNum: function (pid, data) {
            let num = 0;
            if (!pid) {
                return num;
            }
            let tPid;
            for (let i = 0; i < data.length; i++) {
                if (pid === data[i].id) {
                    num += 1;
                    tPid = data[i].pid;
                    break;
                }
            }
            return num + treeTable.getEmptyNum(tPid, data);
        },
        // 展开/折叠行
        toggleRows: function ($dom, linkage) {
            let type = $dom.attr('lay-ttype');
            if ('file' === type) {
                return;
            }
            let mId = $dom.attr('lay-tid');
            let isOpen = $dom.hasClass('open');
            if (isOpen) {
                $dom.removeClass('open');
            } else {
                $dom.addClass('open');
            }
            $dom.closest('tbody').find('tr').each(function () {
                let $ti = $(this).find('.treeTable-icon');
                let pid = $ti.attr('lay-tpid');
                let ttype = $ti.attr('lay-ttype');
                let tOpen = $ti.hasClass('open');
                if (mId === pid) {
                    if (isOpen) {
                        $(this).hide();
                        if ('dir' === ttype && tOpen === isOpen) {
                            $ti.trigger('click');
                        }
                    } else {
                        $(this).show();
                        if (linkage && 'dir' === ttype && tOpen === isOpen) {
                            $ti.trigger('click');
                        }
                    }
                }
            });
        },
        // 检查参数
        checkParam: function (param) {
            if (!param.treeSpid && param.treeSpid !== 0) {
                layer.msg('参数treeSpid不能为空', {icon: 5});
                return false;
            }

            if (!param.treeColIndex && param.treeColIndex !== 0) {
                layer.msg('参数treeColIndex不能为空', {icon: 5});
                return false;
            }
            return true;
        },
        // 展开所有
        expandAll: function (dom) {
            $(dom).next('.treeTable').find('.layui-table-body tbody tr').each(function () {
                let $ti = $(this).find('.treeTable-icon');
                let ttype = $ti.attr('lay-ttype');
                let tOpen = $ti.hasClass('open');
                if ('dir' === ttype && !tOpen) {
                    $ti.trigger('click');
                }
            });
        },
        // 折叠所有
        foldAll: function (dom) {
            $(dom).next('.treeTable').find('.layui-table-body tbody tr').each(function () {
                let $ti = $(this).find('.treeTable-icon');
                let ttype = $ti.attr('lay-ttype');
                let tOpen = $ti.hasClass('open');
                if ('dir' === ttype && tOpen) {
                    $ti.trigger('click');
                }
            });
        }
    };

    layui.link(layui.cache.base + 'treeTable.css');

    // 给图标列绑定事件
    $('body').on('click', '.treeTable .treeTable-icon', function () {
        let treeLinkage = $(this).parents('.treeTable').attr('treeLinkage');
        if ('true' === treeLinkage) {
            treeTable.toggleRows($(this), true);
        } else {
            treeTable.toggleRows($(this), false);
        }
    });

    exports('treeTable', treeTable);
});
