import Vue from 'vue'
import App from './App'
import store from './store'
import uView from '@/components/uview-ui'
import mpShare from '@/components/uview-ui/libs/mixin/mpShare.js'
import httpApi from '@/common/http.api.js'
import httpInterceptor from '@/common/http.interceptor.js'
import {showToast, showSuccess, showError, px2rpx, toPage} from './utils/tools'

Vue.config.productionTip = false
Vue.prototype.$px2rpx = px2rpx
Vue.prototype.$toPage = toPage
Vue.prototype.$showToast = showToast
Vue.prototype.$showSuccess = showSuccess
Vue.prototype.$showError = showError
Vue.mixin(mpShare)
Vue.use(uView)

App.mpType = 'app'

const app = new Vue({
	store,
    ...App
})

Vue.use(httpApi, app)
Vue.use(httpInterceptor, app)
app.$mount()
