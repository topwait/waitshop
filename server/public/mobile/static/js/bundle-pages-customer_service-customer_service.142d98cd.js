(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["bundle-pages-customer_service-customer_service"],{"01f1":function(e,t,i){"use strict";i.r(t);var n=i("3445"),r=i.n(n);for(var a in n)"default"!==a&&function(e){i.d(t,e,(function(){return n[e]}))}(a);t["default"]=r.a},1785:function(e,t,i){var n=i("24fb");t=n(!1),t.push([e.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */.u-image[data-v-c22e95ae]{position:relative;transition:opacity .5s ease-in-out}.u-image__image[data-v-c22e95ae]{width:100%;height:100%}.u-image__loading[data-v-c22e95ae], .u-image__error[data-v-c22e95ae]{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;flex-direction:row;align-items:center;justify-content:center;background-color:#f3f4f6;color:#909399;font-size:%?46?%}',""]),e.exports=t},1915:function(e,t,i){"use strict";var n=i("7ec9"),r=i.n(n);r.a},"29ec":function(e,t,i){"use strict";i.r(t);var n=i("55be"),r=i.n(n);for(var a in n)"default"!==a&&function(e){i.d(t,e,(function(){return n[e]}))}(a);t["default"]=r.a},3445:function(e,t,i){"use strict";i("a9e3"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n={name:"u-image",props:{src:{type:String,default:""},mode:{type:String,default:"aspectFill"},width:{type:[String,Number],default:"100%"},height:{type:[String,Number],default:"auto"},shape:{type:String,default:"square"},borderRadius:{type:[String,Number],default:0},lazyLoad:{type:Boolean,default:!0},showMenuByLongpress:{type:Boolean,default:!0},loadingIcon:{type:String,default:"photo"},errorIcon:{type:String,default:"error-circle"},showLoading:{type:Boolean,default:!0},showError:{type:Boolean,default:!0},fade:{type:Boolean,default:!0},webp:{type:Boolean,default:!1},duration:{type:[String,Number],default:500},bgColor:{type:String,default:"#f3f4f6"}},data:function(){return{isError:!1,loading:!0,opacity:1,durationTime:this.duration,backgroundStyle:{}}},watch:{src:{immediate:!0,handler:function(e){e?this.isError=!1:(this.isError=!0,this.loading=!1)}}},computed:{wrapStyle:function(){var e={};return e.width=this.$u.addUnit(this.width),e.height=this.$u.addUnit(this.height),e.borderRadius="circle"==this.shape?"50%":this.$u.addUnit(this.borderRadius),e.overflow=this.borderRadius>0?"hidden":"visible",this.fade&&(e.opacity=this.opacity,e.transition="opacity ".concat(Number(this.durationTime)/1e3,"s ease-in-out")),e}},methods:{onClick:function(){this.$emit("click")},onErrorHandler:function(e){this.loading=!1,this.isError=!0,this.$emit("error",e)},onLoadHandler:function(){var e=this;if(this.loading=!1,this.isError=!1,this.$emit("load"),!this.fade)return this.removeBgColor();this.opacity=0,this.durationTime=0,setTimeout((function(){e.durationTime=e.duration,e.opacity=1,setTimeout((function(){e.removeBgColor()}),e.durationTime)}),50)},removeBgColor:function(){this.backgroundStyle={backgroundColor:"transparent"}}}};t.default=n},"3a82":function(e,t,i){"use strict";var n=i("7b44"),r=i.n(n);r.a},"49d5":function(e,t,i){"use strict";i.r(t);var n=i("73b1"),r=i("29ec");for(var a in r)"default"!==a&&function(e){i.d(t,e,(function(){return r[e]}))}(a);i("3a82");var o,s=i("f0c5"),d=Object(s["a"])(r["default"],n["b"],n["c"],!1,null,"e1d97e2e",null,!1,n["a"],o);t["default"]=d.exports},"55be":function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n={data:function(){return{isFirstLoading:!0,detail:[]}},onShow:function(){this.getCustomerService()},methods:{getCustomerService:function(){var e=this;this.$u.api.apiCustomerService().then((function(t){0===t.code?(e.detail=t.data,e.$nextTick((function(){e.isFirstLoading=!1}))):e.$showToast(t.msg)}))}}};t.default=n},"73b1":function(e,t,i){"use strict";i.d(t,"b",(function(){return r})),i.d(t,"c",(function(){return a})),i.d(t,"a",(function(){return n}));var n={uImage:i("c146").default},r=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("v-uni-view",{staticClass:"container"},[i("v-uni-view",{staticClass:"index-service-widget"},[i("u-image",{staticClass:"u-margin-top-60",attrs:{width:"390rpx",height:"390rpx",src:e.detail.image}}),i("v-uni-view",{staticClass:"u-font-32 u-font-weight u-margin-top-20"},[e._v("扫码添加客服")]),i("v-uni-view",{staticClass:"saveQrCode"},[e._v("保存二维码")]),i("v-uni-view",{staticClass:"info"},[i("v-uni-view",{staticClass:"line"},[e._v("服务时间："+e._s(e.detail.business_hours))]),i("v-uni-view",{staticClass:"line"},[e._v("服务电话："+e._s(e.detail.phone))]),i("v-uni-view",{staticClass:"line"},[e._v("服务Q Q："+e._s(e.detail.qq))])],1)],1),i("v-uni-view",{staticClass:"u-font-24 u-color-white u-margin-top-50 u-text-center"},[e._v("无法添加或疑难问题请联系人工客服电话")])],1)},a=[]},"7b44":function(e,t,i){var n=i("ce70");"string"===typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);var r=i("4f06").default;r("607dc325",n,!0,{sourceMap:!1,shadowMode:!1})},"7ec9":function(e,t,i){var n=i("1785");"string"===typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);var r=i("4f06").default;r("5c8c4ce7",n,!0,{sourceMap:!1,shadowMode:!1})},c146:function(e,t,i){"use strict";i.r(t);var n=i("d20e"),r=i("01f1");for(var a in r)"default"!==a&&function(e){i.d(t,e,(function(){return r[e]}))}(a);i("1915");var o,s=i("f0c5"),d=Object(s["a"])(r["default"],n["b"],n["c"],!1,null,"c22e95ae",null,!1,n["a"],o);t["default"]=d.exports},ce70:function(e,t,i){var n=i("24fb");t=n(!1),t.push([e.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */uni-page-body[data-v-e1d97e2e]{background:linear-gradient(180deg,#ffb86a,#fb7a4b,#f94e35,#f73327,#f61b1c)}.index-service-widget[data-v-e1d97e2e]{display:flex;flex-direction:column;align-items:center;margin:0 %?60?%;margin-top:%?200?%;height:%?900?%;border-radius:%?12?%;background-color:#fff}.index-service-widget .saveQrCode[data-v-e1d97e2e]{color:#fff;font-size:%?32?%;font-weight:700;text-align:center;width:%?460?%;height:%?88?%;line-height:%?88?%;margin-top:%?80?%;border-radius:%?60?%;background:linear-gradient(180deg,#ffa200,#ff5e44)}.index-service-widget .info[data-v-e1d97e2e]{margin-top:%?40?%}.index-service-widget .info .line[data-v-e1d97e2e]{color:#333;line-height:%?46?%;font-size:%?26?%;font-weight:400}body.?%PAGE?%[data-v-e1d97e2e]{background:linear-gradient(180deg,#ffb86a,#fb7a4b,#f94e35,#f73327,#f61b1c)}',""]),e.exports=t},d20e:function(e,t,i){"use strict";i.d(t,"b",(function(){return r})),i.d(t,"c",(function(){return a})),i.d(t,"a",(function(){return n}));var n={uIcon:i("e4d2").default},r=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("v-uni-view",{staticClass:"u-image",style:[e.wrapStyle,e.backgroundStyle],on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.onClick.apply(void 0,arguments)}}},[e.isError?e._e():i("v-uni-image",{staticClass:"u-image__image",style:{borderRadius:"circle"==e.shape?"50%":e.$u.addUnit(e.borderRadius)},attrs:{src:e.src,mode:e.mode,"lazy-load":e.lazyLoad,"show-menu-by-longpress":e.showMenuByLongpress},on:{error:function(t){arguments[0]=t=e.$handleEvent(t),e.onErrorHandler.apply(void 0,arguments)},load:function(t){arguments[0]=t=e.$handleEvent(t),e.onLoadHandler.apply(void 0,arguments)}}}),e.showLoading&&e.loading?i("v-uni-view",{staticClass:"u-image__loading",style:{borderRadius:"circle"==e.shape?"50%":e.$u.addUnit(e.borderRadius),backgroundColor:this.bgColor}},[e.$slots.loading?e._t("loading"):i("u-icon",{attrs:{name:e.loadingIcon,width:e.width,height:e.height}})],2):e._e(),e.showError&&e.isError&&!e.loading?i("v-uni-view",{staticClass:"u-image__error",style:{borderRadius:"circle"==e.shape?"50%":e.$u.addUnit(e.borderRadius)}},[e.$slots.error?e._t("error"):i("u-icon",{attrs:{name:e.errorIcon,width:e.width,height:e.height}})],2):e._e()],1)},a=[]}}]);