<template>
	<view :style="{
		color: fontColor,
		fontWeight: fontWeight,
		textDecoration: lineThrough ? 'line-through' : ''}">
		
		<text :style="{fontSize: minorSize}" v-if="prefix">{{ prefix }}</text>
		<text :style="{fontSize: mainSize}">{{ amount }}</text>		
	</view>
</template>

<script>
	// +----------------------------------------------------------------------
	// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
	// +----------------------------------------------------------------------
	// | 欢迎阅读学习程序代码
	// | gitee:   https://gitee.com/wafts/WaitShop
	// | github:  https://github.com/miniWorlds/waitshop
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
			// 金额
			amount: {
				type: String | Number,
				required: true
			},
			// 前缀
			prefix: {
				type: String,
				default: '￥'
			},
			// 字体颜色
			fontColor: {
				type: String,
				default: '#FF2C3C'
			},
			// 字体加粗
			fontWeight: {
				type: String | Number,
				default: 'normal'
			},
			// 字体贯穿线
			lineThrough: {
				type: Boolean,
				default: false
			},
			// 主要字体大小
			mainSize: {
				type: String,
				default: '28rpx'
			},
			// 次要字体大小
			minorSize: {
				type: String,
				default: '22rpx'
			}
		}
	}
</script>
