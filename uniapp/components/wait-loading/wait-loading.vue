<template>
	<!-- 全局加载动画部件 -->
	<view class="index-loading-widget" :style="{backgroundColor}">
		<u-loading :mode="mode" :size="size" :color="color || ''"></u-loading>
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
		props: {
			backgroundColor: {
				type: String,
				default: "#FFFFFF"
			},
			mode: {
				type: String,
				default: "circle"
			},
			size: {
				type: Number,
				default: 60
			},
			color: {
				type: String,
				default: "#FF5058"
			}
		}
	}
</script>

<style lang="scss">
	// 全局加载动画部件样式
	.index-loading-widget {
		position: fixed;
		top: 0;
		left: 0;
		width: 100vw;
		height: 100vh;
		z-index: 9999;
		display: flex;
		justify-content: center;
		align-items: center;
	}
</style>
