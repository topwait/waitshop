layui.define(["layer", "form"], function (exports) {
    let $ = layui.jquery;
    let layer = layui.layer;
    let form = layui.form;
    let spec_table_data = []; // 本地存储值
    let spec_name_index = 0;  // 记录规格项目的下标
    let spec_value_temp_id_number = 0; // 记录input下标累计数
    let create_table_by_spec = null; // 触发表格生成定时器(1000毫秒触发生成)

    /** ======== 多规格实例start ======== **/
    let active = {
        // 单规格初始化入口(elem=容器ID)
        initSingleSpec: function (elem) {
            if ($(elem).find("div").length > 0) return true;
            $(elem).append(active.template("initSingleSpec"));
        },
        // 多规格初始化入口(elem=容器ID)
        initBatchSpec: function (elem) {
            // 初始化模板
            if ($(elem).find("div").length > 0) return true;
            $(elem).append(active.template("initMultiSpec"));

            // 添加规格“项目”
            $(document).on("click", "#addSpecProject", function () {
                active.addSpecProject();
            });

            // 添加规格“值”
            $(document).on("click", ".addSpecValue", function () {
                let that = $(this);
                layer.prompt({title: "输入规格值，多个请换行", formType: 2}, function (text, index) {
                    layer.close(index);
                    let specs = text.split("\n");
                    specs = active.unique(specs);
                    active.addSpecValues(that, specs);
                });
            });

            // 删除规格项目
            $(document).on("click", ".deleteSpecItem", function () {
                $(this).parent().remove();
                active.triggerCreateTableBySpec();
            });

            // 删除规格值
            $(document).on("click", ".deleteSpecValue", function () {
                let that = $(this).parent().parent().find(".addSpecValue");
                $(this).parent().remove();
                active.laterSpec(that);
                active.triggerCreateTableBySpec();
            });

            // 修改规格名称
            $(document).on("input", ".spec_name", function () {
                active.triggerCreateTableBySpec();
            });

            // 改变规格值名称
            $(document).on("input", ".goods-spec-value input", function () {
                let that = $(this).parent().parent().find(".addSpecValue");
                active.triggerCreateTableBySpec();
                active.laterSpec(that);
            });

            // 本地化数据
            $(document).on("input", "#more-spec-lists-table input", function () {
                let key = $(this).attr("name") + $(this).parent().parent().attr("spec-value-temp-ids");
                spec_table_data[key] = $(this).val();
            });

            // 批量填充
            $(document).on("click", ".batch-spec-content", function () {
                let title = $(this).text();
                let input_name = $(this).attr("input-name");
                layer.prompt({formType: 3, title: "批量填写" + title},function(value, index, elem){
                    $('input[name="'+input_name+'[]"]').val(value);
                    // 保存值到本地
                    $("#more-spec-lists-table input").each(function(){
                        let key = $(this).attr("name") + $(this).parent().parent().attr('spec-value-temp-ids');
                        spec_table_data[key] = $(this).val();
                    });
                    layer.close(index);
                });
            });
        },
        // 添加规格"项目"(id=规格ID, value=规格的名称)
        addSpecProject: function (id="0", value="") {
            let div  = $(".goods-spec-div");
            let that = $("#goods-spec-project");
            let tpl  = active.template("SpecProject");
            let index = spec_name_index += 1;
            let template = tpl.replace("{spec_id_index}", (index).toString());
            template = template.replace("{spec_name_index}", (index).toString());
            template = template.replace("{spec_id}", id);
            template = template.replace("{value}", value);
            if (div.length > 2) { layer.msg('最多添加3个规格项目'); return; }
            that.append(template);
        },
        // 添加规格"值"
        addSpecValues: function (that, specs) {
            // 查询本类下所有规格值
            let added_specs = [];
            that.parent().parent().find(".goods-spec-value input").each(function () {
                added_specs.push($(this).val().trim());
            });
            // 查询规格值(如果存在则不插入)
            specs.forEach(function (item) {
                let id = item.id === undefined ? '0' : item.id;
                let spec = item.value === undefined ? item.trim() : item.value;
                if (spec !== "" && !added_specs.includes(spec)) {
                    let tpl  = active.template("SpecValues");
                    let template_html = tpl.replace("{spec_value_temp_id}", (spec_value_temp_id_number--).toString());
                    template_html = template_html.replace("{spec_value}", spec);
                    template_html = template_html.replace("{spec_value_id}", id);
                    that.parent().before(template_html);
                }
            });
            // 后续操作
            active.laterSpec(that);
            active.triggerCreateTableBySpec();
        },
        // 规格属性"后续"
        laterSpec: function (that) {
            let spec_values = "";
            let spec_ids = "";
            that.parent().parent().find(".goods-spec-value .spec_value_name").each(function () {
                spec_values += $(this).val().trim() + ",";
            });
            that.parent().parent().find(".goods-spec-value .spec_value_id").each(function () {
                spec_ids += $(this).val().trim() + ",";
            });
            that.prev().val(spec_values.substring(0, spec_values.lastIndexOf(",")));
            that.prev().prev().val(spec_ids.substring(0, spec_ids.lastIndexOf(",")));
        },
        // 触发生成表格
        triggerCreateTableBySpec: function () {
            clearTimeout(create_table_by_spec);
            create_table_by_spec = setTimeout(active.createSkuTable, 1000);
        },
        // 创建表格
        createSkuTable: function () {
            let table_html = "";  //表格
            let table_head = [];  //表格头
            let table_data = [];  //表格值
            let spec_value_temp_arr = []; //规格值下标
            let th_html = active.template("TableThead");
            let tr_html = active.template("TableTr");

            // 1、遍历获取规格项目
            $(".goods-spec-div").each(function (index) {
                let spec_name = $(this).find(".spec_name").val();
                if (active.isEmptyString(spec_name)) {
                    return true;
                }
                table_head[index] = spec_name;
                table_data[index] = [];
                spec_value_temp_arr[index] = [];

                $(this).find(".goods-spec-value .spec_value_name").each(function (i) {
                    table_data[index][i] = $(this).val();
                    spec_value_temp_arr[index][i] = $(this).parent().attr('spec-value-temp-id');
                });
            });

            // 2、组装表格头
            let spec_table_th = "";
            table_head.forEach(function (item) {
                spec_table_th += "<th width='40'>"+ item +"</th>"
            });
            table_html = th_html.replace("{table_th}", spec_table_th);

            // 3、组装表格行
            spec_value_temp_arr = active.cartesianProduct(spec_value_temp_arr);
            table_data = active.cartesianProduct(table_data);
            table_data.forEach(function (item, i) {
                let spec_tr_html = "";
                let specs = "";
                if (Array.isArray(item)) {
                    // 创建tr
                    let spec_value_temp_ids = "";
                    spec_value_temp_arr[i].forEach(function(tempIds){ spec_value_temp_ids += tempIds + ","; });
                    spec_value_temp_ids = spec_value_temp_ids.substring(0, spec_value_temp_ids.lastIndexOf(","));
                    spec_tr_html += "<tr spec-value-temp-ids='" +spec_value_temp_ids +"'>";
                    // 创建td
                    item.forEach(function (value) {
                        spec_tr_html += "<td>"+ value +"</td>";
                        specs += value.replace(",", "") + ",";
                    })
                } else {
                    let spec_value_temp_ids = spec_value_temp_arr[i];
                    spec_tr_html = '<tr spec-value-temp-ids="'+spec_value_temp_ids+'">';
                    spec_tr_html += "<td>"+ item +"</td>";
                    specs += item.replace(",", "") + ",";
                }
                specs = specs.substring(0, specs.lastIndexOf(","));
                let tr_html_str = tr_html.replace("{spec_value_str}", specs);
                tr_html_str = tr_html_str.replace("{sku_id}", "0");
                table_html += tr_html_str.replace("{table_td}", spec_tr_html);
            });
            $("#more-spec-lists-table").html(table_html);
            active.setTableValue();
        },
        // 动态渲染已保存的值
        setTableValue: function () {
            $("#more-spec-lists-table").find("input").each(function () {
                let key = $(this).attr("name") + $(this).parent().parent().attr("spec-value-temp-ids");
                if(spec_table_data[key]!== undefined){
                    $(this).val(spec_table_data[key]);
                }
            });
            $(".goods-spec-img-div").each(function(){
                let key = $(this).parent().parent().attr('spec-value-temp-ids');
                if(spec_table_data["spec_image[]"+key]){
                    if (spec_table_data["spec_image[]"+key]) {
                        $(this).html(`
                            <img src="/static/images/ic_add_enc.png" class="multi-spec-img-add" alt="image" style="display:none;">
                            <div class="thumbnail">
                                <img src="${spec_table_data["spec_image[]" + key]}" alt="image">
                                <input type="hidden" name="spec_image[]" value="${spec_table_data["spec_image[]" + key]}">
                                <div class="goods-spec-img-del-x"><i class="layui-icon layui-icon-close"></i></div>
                            </div>
                        `);
                    } else {
                        $(this).html('<img src="/static/images/ic_add_enc.png" class="multi-spec-img-add" alt="image">');
                    }
                }
            });
        },
        // 设置表格持久化数据
        setTableData: function (filed, key, value) {
            spec_table_data[filed + key] = value;
        },
        // 数组去重
        unique: function (arr) {
            let hash=[];
            for (let i = 0; i < arr.length; i++) {
                if(hash.indexOf(arr[i]) === -1){
                    hash.push(arr[i]);
                }
            }
            return hash;
        },
        // 字符串判空
        isEmptyString: function (str) {
            str = str.replace(/(^\s*)|(\s*$)/g, "");
            return str.length === 0;
        },
        // 笛卡尔积生成
        cartesianProduct: function (arr) {
            if (arr.length < 2) return arr[0] || [];
            return [].reduce.call(arr, function (col, set) {
                let res = [];
                col.forEach(c => {
                    set.forEach(s => {
                        let t = [].concat(Array.isArray(c) ? c : [c]);
                        t.push(s);
                        res.push(t);
                    })
                });
                return res;
            });
        },
        // 切换选项卡
        switchTab: function (number) {
            let tabsElem    = $(".layui-tab .layui-tab-title li");
            let contentElem = $(".layui-tab .layui-tab-content .layui-tab-item");
            tabsElem.removeClass("layui-this");
            tabsElem.eq(number).addClass("layui-this");
            contentElem.removeClass("layui-show");
            contentElem.eq(number).addClass("layui-show");
        },
        // 模板引擎
        template: function (type) {
            let tpl = "";
            switch (type) {
                case "SpecProject":
                    tpl = `
                        <div class="goods-spec-div">
                            <div class="deleteSpecItem">x</div>
                            <!-- 规格项 -->
                            <div class="layui-form-item">
                                <label class="layui-form-label">规格项：</label>
                                <div class="layui-input-inline">
                                    <input type="hidden" name="spec_id[{spec_id_index}]" value="{spec_id}">
                                    <input type="text" name="spec_name[{spec_name_index}]" value="{value}" placeholder="请填写规格名" class="layui-input spec_name" autocomplete="off" switch-tab="1" lay-verType="tips"  lay-verify="more_spec_required" verify-msg="请填写规格名">
                                </div>
                            </div>
                            <!-- 规格值 -->
                            <div class="layui-form-item">
                                <label class="layui-form-label"></label>
                                <div class="layui-input-block">
                                    <div class="layui-input-inline">
                                        <input type="hidden" name="spec_value_ids[]">
                                        <input type="hidden" name="spec_value_names[]">
                                        <a href="javascript:" class="addSpecValue">+ 添加规格值</a>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    break;
                case "SpecValues":
                    tpl = `
                        <div class="layui-input-inline goods-spec-value" spec-value-temp-id="{spec_value_temp_id}">
                            <div class="deleteSpecValue">x</div>
                            <input type="text"  value="{spec_value}" placeholder="规格值" class="layui-input spec_value_name" autocomplete="off" switch-tab="1" lay-vertype="tips" lay-verify="more_spec_required" verify-msg="规格名称不可留空">
                            <input type="hidden" class="spec_value_id" value="{spec_value_id}">
                        </div>`;
                    break;
                case "TableThead":
                    tpl = `
                        <thead>
                            <tr style="background-color: #F3F5F9">
                                {table_th}
                                <th width="50">规格图片</th>
                                <th><span class="form-label-asterisk">*</span>市场价(元)</th>
                                <th><span class="form-label-asterisk">*</span>价格(元)</th>
                                <th><span class="form-label-asterisk">*</span>成本价(元)</th>
                                <th><span class="form-label-asterisk">*</span>库存</th>
                                <th><span class="form-label-asterisk">*</span>体积(m3)</th>
                                <th><span class="form-label-asterisk">*</span>重量(kg)</th>
                                <th>条码</th>
                            </tr>
                        </thead>
                    `;
                    break;
                case "TableTr":
                    tpl = `
                        {table_td}
                        <td style="display:none;">
                            <input type="hidden" name="spec_value_str[]" value="{spec_value_str}">
                            <input type="hidden" name="sku_id[]" value="{sku_id}">
                        </td>
                        <td>
                            <div class="goods-spec-img-div">
                                <div class="thumbnail"><input type="hidden" name="spec_image[]"></div>
                                <img src="/static/images/ic_add_enc.png" class="multi-spec-img-add" alt="image">
                            </div>
                        </td>
                        <td><input type="number" name="market_price[]" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="more_spec_required|more_market_price" verify-msg="请填写市场价格"></td>
                        <td><input type="number" name="sell_price[]" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="more_spec_required|more_sell_price" verify-msg="请填写售卖价格"></td>
                        <td><input type="number" name="cost_price[]" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="more_spec_required|more_cost_price" verify-msg="请填写成本价格"></td>
                        <td><input type="number" name="stock[]" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="more_spec_required|more_stock" verify-msg="请填写库存数量"></td>
                        <td><input type="number" name="volume[]" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="more_spec_required|more_volume" verify-msg="请填写体积"></td>
                        <td><input type="number" name="weight[]" class="layui-input"  autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="more_spec_required|more_weight" verify-msg="请填写重量"></td>
                        <td><input type="number" name="bar_code[]" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips"></td>
                    </tr>
                    `;
                    break;
                case "initMultiSpec":
                    tpl = `
                        <!-- 规格容器 -->
                        <div class="layui-form-item">
                            <div class="layui-input-block" id="goods-spec-project"></div>
                        </div>
                        <!-- 规格添加 -->
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <a class="layui-btn layui-btn-sm" id="addSpecProject">添加规格项目</a>
                            </div>
                        </div>
                        <!-- 规格表格 -->
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="form-label-asterisk">*</span>规格明细：</label>
                            <div class="layui-input-block">
                                <div class="batch-div">
                                    <span class="batch-spec-title">批量设置：</span>
                                    <div>
                                        <span class="batch-spec-content click-a" input-name="market_price">市场价</span>
                                        <span class="batch-spec-content click-a" input-name="sell_price">价格</span>
                                        <span class="batch-spec-content click-a" input-name="cost_price">成本价</span>
                                        <span class="batch-spec-content click-a" input-name="stock">库存</span>
                                        <span class="batch-spec-content click-a" input-name="volume">体积</span>
                                        <span class="batch-spec-content click-a" input-name="weight">重量</span>
                                        <span class="batch-spec-content click-a" input-name="bar_code">条码</span>
                                    </div>
                                </div>
                                <table id="more-spec-lists-table" class="layui-table" lay-size="sm">
                                    <thead> 
                                        <tr style="background-color: #F3F5F9">
                                            <th>规格图片</th>
                                            <th><span class="form-label-asterisk">*</span>市场价(元)</th>
                                            <th><span class="form-label-asterisk">*</span>价格(元)</th>
                                            <th><span class="form-label-asterisk">*</span>成本价(元)</th>
                                            <th><span class="form-label-asterisk">*</span>库存</th>
                                            <th><span class="form-label-asterisk">*</span>体积(m3)</th>
                                            <th><span class="form-label-asterisk">*</span>重量(kg)</th>
                                            <th>条码</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    `;
                    break;
                case "initSingleSpec": //单规格模板
                    tpl = `
                        <label class="layui-form-label"><span class="form-label-asterisk">*</span>规格明细：</label>
                        <div class="layui-input-block">
                            <table id="one-spec-lists-table" class="layui-table" lay-size="sm">
                                <thead>
                                    <tr style="background-color: #F3F5F9">
                                        <th width="50">规格图片</th>
                                        <th><span class="form-label-asterisk">*</span>市场价(元)</th>
                                        <th><span class="form-label-asterisk">*</span>价格(元)</th>
                                        <th><span class="form-label-asterisk">*</span>成本价(元)</th>
                                        <th><span class="form-label-asterisk">*</span>库存</th>
                                        <th><span class="form-label-asterisk">*</span>体积(m3)</th>
                                        <th><span class="form-label-asterisk">*</span>重量(kg)</th>
                                        <th>条码</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="goods-spec-img-div">
                                                <div class="thumbnail"><input type="hidden" name="one_spec_image"></div>
                                                <img src="/static/images/ic_add_enc.png" class="one-spec-img-add" alt="image">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="one_sku_id" value="0" class="layui-input" autocomplete="off" switch-tab="1">
                                            <input type="number" name="one_market_price" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="one_spec_required|one_market_price" verify-msg="请填写市场价"></td>
                                        <td><input type="number" name="one_sell_price" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="one_spec_required|one_sell_price" verify-msg="请填写售卖价"></td>
                                        <td><input type="number" name="one_cost_price" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="one_spec_required|one_cost_price" verify-msg="请填写成本价格"></td>
                                        <td><input type="number" name="one_stock" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="one_spec_required|one_stock" verify-msg="请填写库存"></td>
                                        <td><input type="number" name="one_volume" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="one_spec_required|one_volume" verify-msg="请填写体积"></td>
                                        <td><input type="number" name="one_weight" class="layui-input"  autocomplete="off" switch-tab="1" lay-verType="tips" lay-verify="one_spec_required|one_weight" verify-msg="请填写重量"></td>
                                        <td><input type="number" name="one_bar_code" class="layui-input" autocomplete="off" switch-tab="1" lay-verType="tips"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `;
                    break;
            }
            return tpl;
        },
    };



    /** ======== 表单验证器start ======== **/
    form.verify({
        // 基础
        must: function (value, item) {
            if (!value || value === "") {
                active.switchTab($(item).attr("switch-tab"));
                return $(item).attr("verify-msg") ?? "必填项不可为空";
            }
        }
        ,negative: function (value, item) {
            if (value && value < 0) {
                active.switchTab($(item).attr("switch-tab"));
                return $(item).attr("verify-msg") ?? "该值不允许小于0";
            }
        }
        ,image: function(value, item) {
            let image = $(item).find('input[name="image"]').val();
            if(!image){
                active.switchTab($(item).attr("switch-tab"));
                return $(item).attr("verify-msg") ?? "请选择图片";
            }
        }
        ,category: function(value, item) {
            let category = $(item).find('input[type="text"]').val();
            if (!category || category === "") {
                active.switchTab($(item).attr("switch-tab"));
                return $(item).attr("verify-msg") ?? "请选择分类";
            }
        }
        ,banner: function(value, item) {
            let banner = $(item).find('input[type="hidden"]').val();
            if (!banner || banner === "") {
                active.switchTab($(item).attr("switch-tab"));
                return $(item).attr("verify-msg") ?? "请至少选择一张轮播图";
            }
        }
        // 多规格
        ,more_spec_required: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "2") {
                if (!value) {
                    active.switchTab($(item).attr("switch-tab"));
                    return $(item).attr("verify-msg");
                }
            }
        }
        ,more_market_price: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "2") {
                if (value && value < 0.01) {
                    active.switchTab($(item).attr('switch-tab'));
                    return '市场价必须大于或等于0.01';
                }
            }
        }
        ,more_sell_price: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "2") {
                if (value && value < 0.01) {
                    active.switchTab($(item).attr("switch-tab"));
                    return "售卖价格必须大于或等于0.01";
                }
            }
        }
        ,more_cost_price: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "2") {
                if (value && value < 0.01) {
                    active.switchTab($(item).attr("switch-tab"));
                    return "成本价格必须大于或等于0.01";
                }
            }
        }
        ,more_stock: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "2") {
                if (value && value < 0) {
                    active.switchTab($(item).attr("switch-tab"));
                    return "库存必须大于或等于0";
                }
            }
        }
        ,more_volume: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "2") {
                if (value && value < 0) {
                    active.switchTab($(item).attr("switch-tab"));
                    return "体积必须大于0";
                }
            }
        }
        ,more_weight: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "2") {
                if (value && value < 0) {
                    active.switchTab($(item).attr("switch-tab"));
                    return '重量必须大于0';
                }
            }
        }
        // 单规格
        ,one_spec_required: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "1") {
                if (!value || value === "") {
                    active.switchTab($(item).attr("switch-tab"));
                    return $(item).attr("verify-msg");
                }
            }
        }
        ,one_market_price: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "1") {
                if (value && value < 0.01) {
                    active.switchTab($(item).attr('switch-tab'));
                    return '市场价必须大于或等于0.01';
                }
            }
        }
        ,one_sell_price: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "1") {
                if (value && value < 0.01) {
                    active.switchTab($(item).attr("switch-tab"));
                    return "售卖价格必须大于或等于0.01";
                }
            }
        }
        ,one_cost_price: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "1") {
                if (value && value < 0.01) {
                    active.switchTab($(item).attr("switch-tab"));
                    return "成本价格必须大于或等于0.01";
                }
            }
        }
        ,one_stock: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "1") {
                if (value && value < 0) {
                    active.switchTab($(item).attr("switch-tab"));
                    return "库存必须大于或等于0";
                }
            }
        }
        ,one_volume: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "1") {
                if (value && value < 0) {
                    active.switchTab($(item).attr("switch-tab"));
                    return "体积必须大于0";
                }
            }
        }
        ,one_weight: function (value, item) {
            if ($('input[name="spec_type"]:checked').val() === "1") {
                if (value && value < 0) {
                    active.switchTab($(item).attr('switch-tab'));
                    return '重量必须大于0';
                }
            }
        }
    });

    /** ======== 模块导出start ======== **/
    layui.link(layui.cache.base + 'goodsSku/goodsSku.css');
    exports("goodsSku", active);
});