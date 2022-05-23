import {baseURL} from './config.js'
import {isClient, toLogin} from '@/utils/tools.js'

const install = (Vue, vm) => {
	// 参数配置
	Vue.prototype.$u.http.setConfig({
		baseUrl: baseURL,
		loadingText: '努力加载中~',
		loadingTime: 5000
	});
	
	// 请求拦截配置
	Vue.prototype.$u.http.interceptor.request = (config) => {
		config.header.token  = vm.$store.state.token;
		config.header.client = isClient()
		if(config.url == '/user/login') config.header.noToken = true;
		return config;
	}
	
	// 响应拦截配置
	Vue.prototype.$u.http.interceptor.response = (res) => {
		if (res.code === 1009) {
			toLogin()
		}
		
		return res
	}
}

export default {
	install
}
