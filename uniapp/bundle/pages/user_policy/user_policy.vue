<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 内容部件 -->
		<view class="index-policy-widget u-margin-20">
			<u-parse :html="content"></u-parse>
		</view>

	</view>
</template>

<script>
	// +----------------------------------------------------------------------
	// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
	// +----------------------------------------------------------------------
	// | 欢迎阅读学习程序代码
	// | gitee:   https://gitee.com/wafts/WaitShop
	// | github:  https://github.com/topwait/waitshop
	// | 官方网站: https://www.waitshop.cn
	// +----------------------------------------------------------------------
	// | 禁止对本系统程序代码以任何目的、任何形式再次发布或出售
	// | WaitShop并不是自由软件,未经许可不能去掉WaitShop相关版权
	// | WaitShop系统产品必须购买商业授权后,方可去除前后端官方标识
	// | WaitShop团队版权所有并拥有最终解释权
	// +----------------------------------------------------------------------
	// | Author: WaitShop Team <2474369941@qq.com>
	// +----------------------------------------------------------------------
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 内容类型
				type: null,
				// 内容数据
				content: ''
			}
		},
		onLoad(options) {
			this.type = options.type
			if (this.type === 'service') {
				uni.setNavigationBarTitle({
					title: '服务协议'
				})
			} else if (this.type === 'privacy') {
				uni.setNavigationBarTitle({
					title: '隐私政策'
				})
			} else if (this.type === 'ensure') {
				uni.setNavigationBarTitle({
					title: '售后保障'
				})
			}
			
			this.getPolicyFun()
		},
		methods: {
			/**
			 * 获取政策协议
			 */
			getPolicyFun() {
				const parma = {type: this.type}
				this.$u.api.apiPolicy(parma).then(result => {
					if (result.code === 0) {
					this.content = result.data.content
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading policy error')
					}
				})
			}
		}
	}
</script>
