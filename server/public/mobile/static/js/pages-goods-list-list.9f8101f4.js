(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-goods-list-list"],{"07df":function(t,e,i){var a=i("6d96");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("3e2664a7",a,!0,{sourceMap:!1,shadowMode:!1})},3315:function(t,e,i){"use strict";i.r(e);var a=i("8905"),n=i("9ba7");for(var r in n)"default"!==r&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("cd42");var o,c=i("f0c5"),s=Object(c["a"])(n["default"],a["b"],a["c"],!1,null,"dbaed82c",null,!1,a["a"],o);e["default"]=s.exports},"34c7":function(t,e,i){"use strict";i("a9e3"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={name:"u-loading",props:{mode:{type:String,default:"circle"},color:{type:String,default:"#c7c7c7"},size:{type:[String,Number],default:"34"},show:{type:Boolean,default:!0}},computed:{cricleStyle:function(){var t={};return t.width=this.size+"rpx",t.height=this.size+"rpx","circle"==this.mode&&(t.borderColor="#e4e4e4 #e4e4e4 #e4e4e4 ".concat(this.color?this.color:"#c7c7c7")),t}}};e.default=a},"3ada":function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */.index-header-widget[data-v-dbaed82c]{position:fixed;left:0;right:0;z-index:9999;background:#fff}.index-header-widget .pack-search-widget[data-v-dbaed82c]{display:flex;align-items:center;justify-content:space-between;padding:%?20?% 0}.index-header-widget .pack-search-widget .search-input[data-v-dbaed82c]{flex:1;height:%?70?%;line-height:%?70?%;padding:0 %?20?%;margin:0 %?20?%;border-radius:%?60?%;background:#f4f4f4}.index-header-widget .pack-filter-widget[data-v-dbaed82c]{display:flex;align-items:center;justify-content:space-between;width:100%;padding:%?20?% 0;font-size:%?28?%;text-align:center;border-top:1px solid #eee;border-bottom:1px solid #eee}.index-header-widget .pack-filter-widget .filter-item[data-v-dbaed82c]{flex:1;max-width:100%;font-size:%?28?%}.index-header-widget .pack-filter-widget .filter-item .active[data-v-dbaed82c]{color:#e93323}.index-header-widget .pack-filter-widget .filter-item .icon[data-v-dbaed82c]{width:%?14?%;height:%?22?%;margin-left:%?12?%}',""]),t.exports=e},"41e0":function(t,e,i){var a=i("f80b");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("e41cbeca",a,!0,{sourceMap:!1,shadowMode:!1})},"45d8":function(t,e,i){"use strict";var a;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",["row"==t.layout?i("v-uni-view",{staticClass:"index-row-widget"},t._l(t.list,(function(e,a){return i("v-uni-view",{key:a,staticClass:"product-item",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.onJump("/pages/goods/detail/detail?goods_id="+e.id)}}},[i("v-uni-view",{staticStyle:{width:"100%",height:"300rpx"}},[i("v-uni-image",{staticClass:"u-equal-bfb u-radius-tl-14 u-radius-tr-14",attrs:{"lazy-load":!0,src:e.image}})],1),i("v-uni-view",{staticClass:"u-padding-lr-10"},[i("v-uni-view",{staticClass:"u-margin-tb-10 u-line-2 u-font-26"},[t._v(t._s(e.name))]),i("v-uni-view",{staticClass:"u-font-34 u-font-weight u-color-major"},[i("v-uni-text",{staticClass:"u-font-24"},[t._v("￥")]),i("v-uni-text",[t._v(t._s(e.min_price))])],1),i("v-uni-view",{staticClass:"u-flex u-row-between u-margin-tb-10 u-font-24 u-color-muted"},[i("v-uni-view",{staticClass:"u-line-through"},[t._v("￥"+t._s(e.market_price))]),i("v-uni-view",[t._v("已售"+t._s(e.sales_volume)+"件")])],1)],1)],1)})),1):t._e(),"col"==t.layout?i("v-uni-view",{staticClass:"index-col-widget"},t._l(t.list,(function(e,a){return i("v-uni-view",{key:a,staticClass:"product-item",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.onJump("/pages/goods/detail/detail?goods_id="+e.id)}}},[i("v-uni-view",{staticStyle:{width:"180rpx",height:"180rpx","flex-shrink":"0"}},[i("v-uni-image",{staticClass:"u-equal-bfb u-radius-tl-14 u-radius-14",attrs:{"lazy-load":!0,src:e.image}})],1),i("v-uni-view",{staticClass:"info"},[i("v-uni-view",{staticClass:"u-line-2 u-font-30"},[t._v(t._s(e.name))]),i("v-uni-view",{staticClass:"u-margin-top-10 u-font-34 u-font-weight u-color-major"},[i("v-uni-text",{staticClass:"u-font-24"},[t._v("￥")]),i("v-uni-text",[t._v(t._s(e.min_price))])],1),i("v-uni-view",{staticClass:"u-flex u-row-between u-margin-tb-10 u-font-24 u-color-muted"},[i("v-uni-view",{staticClass:"u-line-through"},[t._v("￥"+t._s(e.market_price))]),i("v-uni-view",[t._v("已售"+t._s(e.sales_volume)+"件")])],1)],1)],1)})),1):t._e()],1)},r=[]},"4b62":function(t,e,i){"use strict";var a=i("07df"),n=i.n(a);n.a},5198:function(t,e,i){"use strict";i.r(e);var a=i("c358"),n=i("53ec");for(var r in n)"default"!==r&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("8637");var o,c=i("f0c5"),s=Object(c["a"])(n["default"],a["b"],a["c"],!1,null,"481623f8",null,!1,a["a"],o);e["default"]=s.exports},"53ec":function(t,e,i){"use strict";i.r(e);var a=i("9884"),n=i.n(a);for(var r in a)"default"!==r&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a},"6d96":function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */.u-loading-circle[data-v-60256ec8]{display:inline-flex;vertical-align:middle;width:%?28?%;height:%?28?%;background:0 0;border-radius:50%;border:2px solid;border-color:#e5e5e5 #e5e5e5 #e5e5e5 #8f8d8e;-webkit-animation:u-circle-data-v-60256ec8 1s linear infinite;animation:u-circle-data-v-60256ec8 1s linear infinite}.u-loading-flower[data-v-60256ec8]{width:20px;height:20px;display:inline-block;vertical-align:middle;-webkit-animation:a 1s steps(12) infinite;animation:u-flower-data-v-60256ec8 1s steps(12) infinite;background:transparent url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHBhdGggZmlsbD0ibm9uZSIgZD0iTTAgMGgxMDB2MTAwSDB6Ii8+PHJlY3Qgd2lkdGg9IjciIGhlaWdodD0iMjAiIHg9IjQ2LjUiIHk9IjQwIiBmaWxsPSIjRTlFOUU5IiByeD0iNSIgcnk9IjUiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgLTMwKSIvPjxyZWN0IHdpZHRoPSI3IiBoZWlnaHQ9IjIwIiB4PSI0Ni41IiB5PSI0MCIgZmlsbD0iIzk4OTY5NyIgcng9IjUiIHJ5PSI1IiB0cmFuc2Zvcm09InJvdGF0ZSgzMCAxMDUuOTggNjUpIi8+PHJlY3Qgd2lkdGg9IjciIGhlaWdodD0iMjAiIHg9IjQ2LjUiIHk9IjQwIiBmaWxsPSIjOUI5OTlBIiByeD0iNSIgcnk9IjUiIHRyYW5zZm9ybT0icm90YXRlKDYwIDc1Ljk4IDY1KSIvPjxyZWN0IHdpZHRoPSI3IiBoZWlnaHQ9IjIwIiB4PSI0Ni41IiB5PSI0MCIgZmlsbD0iI0EzQTFBMiIgcng9IjUiIHJ5PSI1IiB0cmFuc2Zvcm09InJvdGF0ZSg5MCA2NSA2NSkiLz48cmVjdCB3aWR0aD0iNyIgaGVpZ2h0PSIyMCIgeD0iNDYuNSIgeT0iNDAiIGZpbGw9IiNBQkE5QUEiIHJ4PSI1IiByeT0iNSIgdHJhbnNmb3JtPSJyb3RhdGUoMTIwIDU4LjY2IDY1KSIvPjxyZWN0IHdpZHRoPSI3IiBoZWlnaHQ9IjIwIiB4PSI0Ni41IiB5PSI0MCIgZmlsbD0iI0IyQjJCMiIgcng9IjUiIHJ5PSI1IiB0cmFuc2Zvcm09InJvdGF0ZSgxNTAgNTQuMDIgNjUpIi8+PHJlY3Qgd2lkdGg9IjciIGhlaWdodD0iMjAiIHg9IjQ2LjUiIHk9IjQwIiBmaWxsPSIjQkFCOEI5IiByeD0iNSIgcnk9IjUiIHRyYW5zZm9ybT0icm90YXRlKDE4MCA1MCA2NSkiLz48cmVjdCB3aWR0aD0iNyIgaGVpZ2h0PSIyMCIgeD0iNDYuNSIgeT0iNDAiIGZpbGw9IiNDMkMwQzEiIHJ4PSI1IiByeT0iNSIgdHJhbnNmb3JtPSJyb3RhdGUoLTE1MCA0NS45OCA2NSkiLz48cmVjdCB3aWR0aD0iNyIgaGVpZ2h0PSIyMCIgeD0iNDYuNSIgeT0iNDAiIGZpbGw9IiNDQkNCQ0IiIHJ4PSI1IiByeT0iNSIgdHJhbnNmb3JtPSJyb3RhdGUoLTEyMCA0MS4zNCA2NSkiLz48cmVjdCB3aWR0aD0iNyIgaGVpZ2h0PSIyMCIgeD0iNDYuNSIgeT0iNDAiIGZpbGw9IiNEMkQyRDIiIHJ4PSI1IiByeT0iNSIgdHJhbnNmb3JtPSJyb3RhdGUoLTkwIDM1IDY1KSIvPjxyZWN0IHdpZHRoPSI3IiBoZWlnaHQ9IjIwIiB4PSI0Ni41IiB5PSI0MCIgZmlsbD0iI0RBREFEQSIgcng9IjUiIHJ5PSI1IiB0cmFuc2Zvcm09InJvdGF0ZSgtNjAgMjQuMDIgNjUpIi8+PHJlY3Qgd2lkdGg9IjciIGhlaWdodD0iMjAiIHg9IjQ2LjUiIHk9IjQwIiBmaWxsPSIjRTJFMkUyIiByeD0iNSIgcnk9IjUiIHRyYW5zZm9ybT0icm90YXRlKC0zMCAtNS45OCA2NSkiLz48L3N2Zz4=) no-repeat;background-size:100%}@-webkit-keyframes u-flower-data-v-60256ec8{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@keyframes u-flower-data-v-60256ec8{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@-webkit-keyframes u-circle-data-v-60256ec8{0%{-webkit-transform:rotate(0);transform:rotate(0)}100%{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}',""]),t.exports=e},"7cde":function(t,e,i){var a=i("3ada");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("2320e692",a,!0,{sourceMap:!1,shadowMode:!1})},"7fac":function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */.index-row-widget[data-v-4ce47015]{display:flex;flex-wrap:wrap;justify-content:space-between;padding:0 %?20?%}.index-row-widget .product-item[data-v-4ce47015]{width:49%;margin-top:%?20?%;border-radius:%?14?%;background-color:#fff}.index-col-widget[data-v-4ce47015]{background-color:#fff}.index-col-widget .product-item[data-v-4ce47015]{display:flex;margin:0 %?20?%;padding:%?20?% 0;border-bottom:1px solid #f6f6f6}.index-col-widget .product-item[data-v-4ce47015]:last-child{border-bottom:0}.index-col-widget .product-item .info[data-v-4ce47015]{display:flex;flex-direction:column;justify-content:space-between;flex:1;padding:0 %?20?%}',""]),t.exports=e},8637:function(t,e,i){"use strict";var a=i("41e0"),n=i.n(a);n.a},8905:function(t,e,i){"use strict";i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var a={waitLoading:i("5198").default,uIcon:i("e4d2").default,waitGoodsList:i("decb").default},n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"container"},[t.isFirstLoading?i("wait-loading"):t._e(),i("v-uni-view",{staticClass:"index-header-widget"},[i("v-uni-view",{staticClass:"pack-search-widget"},[i("v-uni-view",{staticClass:"search-input",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.$toPage("/pages/goods/search/search")}}},[i("u-icon",{attrs:{name:"search",size:"28",color:"#999"}}),i("v-uni-text",{staticClass:"u-margin-lr-10 u-font-26 u-color-placeholder"},[t._v(t._s(t.keyword?t.keyword:"请输入要搜索的商品名"))])],1),i("v-uni-view",{staticClass:"u-margin-right-30"},["row"==t.layout?i("u-icon",{attrs:{"custom-prefix":"custom-icon",name:"row",size:"42",color:"#999"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onSwitchLayout.apply(void 0,arguments)}}}):t._e(),"col"==t.layout?i("u-icon",{attrs:{"custom-prefix":"custom-icon",name:"col",size:"42",color:"#999"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onSwitchLayout.apply(void 0,arguments)}}}):t._e()],1)],1),i("v-uni-view",{staticClass:"pack-filter-widget"},t._l(t.tabList,(function(e,a){return i("v-uni-view",{key:a,staticClass:"filter-item",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onSwitchTab(a)}}},[i("v-uni-text",{class:e.selected?"active":""},[t._v(t._s(e.name))]),e.image?i("v-uni-image",{staticClass:"icon",attrs:{src:"/static/"+e.image[e.type]}}):t._e()],1)})),1)],1),i("mescroll-body",{ref:"mescrollRef",attrs:{top:186,up:t.upOption},on:{init:function(e){arguments[0]=e=t.$handleEvent(e),t.mescrollInit.apply(void 0,arguments)},up:function(e){arguments[0]=e=t.$handleEvent(e),t.upCallback.apply(void 0,arguments)},down:function(e){arguments[0]=e=t.$handleEvent(e),t.downCallback.apply(void 0,arguments)}}},[i("wait-goods-list",{attrs:{layout:t.layout,list:t.goodsList}})],1)],1)},r=[]},9884:function(t,e,i){"use strict";i("a9e3"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={props:{backgroundColor:{type:String,default:"#FFFFFF"},mode:{type:String,default:"circle"},size:{type:Number,default:60},color:{type:String,default:"#FF5058"}}};e.default=a},"9ba7":function(t,e,i){"use strict";i.r(e);var a=i("b390"),n=i.n(a);for(var r in a)"default"!==r&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a},"9e22":function(t,e,i){"use strict";i.r(e);var a=i("e1771"),n=i("f889");for(var r in n)"default"!==r&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("4b62");var o,c=i("f0c5"),s=Object(c["a"])(n["default"],a["b"],a["c"],!1,null,"60256ec8",null,!1,a["a"],o);e["default"]=s.exports},a05c:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=i("ada8"),n={props:{layout:{type:String,default:"row"},list:{type:Array,default:[]}},methods:{onJump:function(t){(0,a.toPage)(t)}}};e.default=n},b390:function(t,e,i){"use strict";var a=i("4ea4");i("99af"),i("4160"),i("e25e"),i("159b"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=a(i("98b0")),r=a(i("5464")),o={mixins:[n.default],components:{MescrollBody:r.default},data:function(){return{isFirstLoading:!0,layout:"row",tabIndex:0,tabList:[{name:"综合",alias:"all",type:0,selected:!0},{name:"销量",alias:"sales",type:0,selected:!1,image:["ic_sort_horn.png","ic_sort_up.png","ic_sort_down.png"]},{name:"价格",alias:"price",type:0,selected:!1,image:["ic_sort_horn.png","ic_sort_up.png","ic_sort_down.png"]},{name:"新品",alias:"news",type:0,selected:!1}],cid:0,keyword:"",goodsList:[],upOption:{noMoreSize:1,page:{size:20},empty:{icon:"/static/empty/empty_goods.png",tip:"暂无商品"}}}},onLoad:function(t){this.cid=parseInt(t.cid||0),this.keyword=t.keyword?t.keyword:""},methods:{upCallback:function(t){var e=this,i=this.tabList[this.tabIndex];this.$u.api.apiGoodsList({cid:this.cid,filter:i.alias,sort:0===i.type||2===i.type?"desc":"asc",keyword:this.keyword,page:t.num}).then((function(i){var a=i.data.data,n=i.data.total;1==t.num&&(e.goodsList=[]),e.goodsList=e.goodsList.concat(a),e.mescroll.endBySize(a.length,n),e.$nextTick((function(){e.isFirstLoading=!1}))})).catch((function(){e.mescroll.endErr()}))},onSwitchTab:function(t){var e=this.tabList[t];if(this.tabIndex=t,e.image){this.tabList.forEach((function(e,i){i!==t&&(e.type=0,e.selected=!1)}));var i=0;0===e.type&&(i=2),1===e.type&&(i=2),2===e.type&&(i=1),this.tabList[t].type=i,this.tabList[t].selected=!0}else this.tabList.forEach((function(t,e){t.type=0,t.selected=!1})),this.tabList[t].selected=!0;this.mescroll.scrollTo(0,0),this.mescroll.resetUpScroll(),this.upCallback({num:1})},onSwitchLayout:function(){var t="row"===this.layout?"col":"row";this.layout=t}}};e.default=o},b7cf:function(t,e,i){"use strict";i.r(e);var a=i("a05c"),n=i.n(a);for(var r in a)"default"!==r&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a},c358:function(t,e,i){"use strict";i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var a={uLoading:i("9e22").default},n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"index-loading-widget",style:{backgroundColor:t.backgroundColor}},[i("u-loading",{attrs:{mode:t.mode,size:t.size,color:t.color||""}})],1)},r=[]},c790:function(t,e,i){var a=i("7fac");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("319fc4ca",a,!0,{sourceMap:!1,shadowMode:!1})},cd42:function(t,e,i){"use strict";var a=i("7cde"),n=i.n(a);n.a},da3d:function(t,e,i){"use strict";var a=i("c790"),n=i.n(a);n.a},decb:function(t,e,i){"use strict";i.r(e);var a=i("45d8"),n=i("b7cf");for(var r in n)"default"!==r&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("da3d");var o,c=i("f0c5"),s=Object(c["a"])(n["default"],a["b"],a["c"],!1,null,"4ce47015",null,!1,a["a"],o);e["default"]=s.exports},e1771:function(t,e,i){"use strict";var a;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return t.show?i("v-uni-view",{staticClass:"u-loading",class:"circle"==t.mode?"u-loading-circle":"u-loading-flower",style:[t.cricleStyle]}):t._e()},r=[]},f80b:function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */.index-loading-widget[data-v-481623f8]{position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;justify-content:center;align-items:center}',""]),t.exports=e},f889:function(t,e,i){"use strict";i.r(e);var a=i("34c7"),n=i.n(a);for(var r in a)"default"!==r&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a}}]);