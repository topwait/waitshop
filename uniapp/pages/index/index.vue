<template>
	<view class="container">

		<block v-for="(item, index) in diyItems" :key="index">
			
			<!-- 搜索框 -->
			<block v-if="item.type === 'search'">
				<diy-search :itemIndex="index" :itemConfig="item.config" :domain="diyItems"></diy-search>
			</block>
			
			<!-- 轮播图 -->
			<block v-if="item.type === 'banner' && item.data.length > 0">
				<diy-banner :itemIndex="index" :itemConfig="item.config" :itemData="item.data"></diy-banner>
			</block>
			
			<!-- 单图组 -->
			<block v-if="item.type === 'image' && item.data.length > 0">
				<diy-image-single :itemIndex="index" :itemConfig="item.config" :itemData="item.data"></diy-image-single>
			</block>
			
			<!-- 图片橱窗 -->
			<block v-if="item.type === 'window' && item.data.length > 0">
				<diy-image-magic :itemIndex="index" :itemConfig="item.config" :itemData="item.data"></diy-image-magic>
			</block>
			
			<!-- 导航组 -->
			<block v-if="item.type === 'navBar' && item.data.length > 0">
				<diy-nav-menu :itemIndex="index" :itemConfig="item.config" :itemData="item.data"></diy-nav-menu>
			</block>
			
			<!-- 公告组 -->
			<block v-if="item.type === 'notice' && item.data.length > 0">
				<diy-notice :itemIndex="index" :itemConfig="item.config" :itemData="item.data"></diy-notice>
			</block>
			
			<!-- 商品组 -->
			<block v-if="item.type === 'goods' && item.data.length > 0">
				<diy-goods :itemIndex="index" :itemConfig="item.config" :itemData="item.data"></diy-goods>
			</block>
			
			<!-- 优惠券组 -->
			<block v-if="item.type === 'coupon' && item.data.length > 0">
				<diy-coupon :itemIndex="index" :itemConfig="item.config" :itemData="item.data"></diy-coupon>
			</block>
			
			<!-- 拼团商品组 -->
			<block v-if="item.type === 'teamGoods' && item.data.length > 0">
				<diy-teamGoods :itemIndex="index" :itemConfig="item.config" :itemData="item.data"></diy-teamGoods>
			</block>
			
			<!-- 秒杀商品组 -->
			<block v-if="item.type === 'seckillGoods' && item.data.lenght > 0">
				<diy-seckillGoods :itemIndex="index" :itemConfig="item.config" :itemData="item.data"></diy-seckillGoods>
			</block>
			
			<!-- 辅助空白组 -->
			<block v-if="item.type === 'blank'">
				<diy-blank :itemIndex="index" :itemConfig="item.config"></diy-blank>
			</block>
			
			<!-- 辅助线组 -->
			<block v-if="item.type === 'guide'">
				<diy-guide :itemIndex="index" :itemConfig="item.config"></diy-guide>
			</block>
			
		</block>
		
		<wait-copyright v-if="!isFirstLoading"></wait-copyright>
		
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
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 装修组件
				diyItems: []
			}
		},
		onLoad() {
			this.getDiyPage()
		},
		methods: {
			/**
			 * 获取DIY页面数据 
			 */
			getDiyPage() {
				this.$u.api.apiDiyPage().then((result) => {
					if (result.code === 0) {
						this.setPageNavBar(result.data.page)
						this.diyItems = result.data.items
						this.isFirstLoading = false
					} else {
						this.$showToast('loading index error')
					}
				})
			},
			
			/**
			 * 设置定义导航栏
			 */
			setPageNavBar(page) {
				uni.setNavigationBarTitle({
					title: page.config.title
				})
				uni.setNavigationBarColor({
					frontColor: page.config.titleTextColor === 'white' ? '#ffffff' : '#000000',
					backgroundColor: page.config.titleBackgroundColor
				})
			}
		}
	}
</script>

<style lang="scss">

</style>
