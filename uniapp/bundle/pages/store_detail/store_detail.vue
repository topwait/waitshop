<template>
	<view class="container">
		
		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 地图预览部件 -->
		<map style="width: 100%; height: 600rpx;" :latitude="latitude" :longitude="longitude" :markers="covers"></map>
		
		<!-- 门店信息部件 -->
		<view class="u-flex u-bg-white u-padding-20">
			<u-image src="http://www.wait.top/uploads/image/20211215103510ecc566984.jpg" width="160rpx" height="160rpx" border-radius="12rpx"></u-image>
			<view class="u-margin-left-20">
				<view class="u-font-28 u-font-weight u-margin-bottom-10">{{storeData.name}}</view>
				<view class="u-margin-bottom-10">
					<text class="u-font-26 u-color-lighter">营业时间:</text>
					<text class="u-font-26 u-color-lighter u-margin-left-10">{{storeData.business_hours}}</text>
				</view>
				<view class="u-margin-bottom-10">
					<text class="u-font-26 u-color-lighter">联系电话:</text>
					<text class="u-font-26 u-color-lighter u-margin-left-10">{{storeData.mobile}}</text>
				</view>
				<view>
					<text class="u-font-26 u-color-lighter">门店地址:</text>
					<text class="u-font-26 u-color-lighter u-margin-left-10">{{storeData.region}}{{storeData.address}}</text>
				</view>
			</view>
		</view>

		<!-- 导航到店部件 -->
		<view class="index-footer-widget">
			<view class="go-btn" @tap="goMap">导航到店</view>
		</view>

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
				// 门店ID
				storeId: 0,
				// 门店数据
				storeData: {},
				// 门店经度
				longitude: 0,
				// 门店维度
				latitude: 0,
				// 门店坐标
				covers: [],
				// 我的经度
				lng: 0,
				// 我的维度
				lat: 0
			}
		},
		onLoad(options) {
			this.lng = options.lng
			this.lat = options.lat
			this.storeId = parseInt(options.id)
			this.getStoreDetail()
		},
		methods: {
			/**
			 * 获取店铺详情
			 */
			getStoreDetail() {
				const param = {id: this.storeId, lng: this.lng, lat: this.lat}
				this.$u.api.apiStoreDetail(param).then(result => {
					if (result.code === 0) {
						this.storeData = result.data
						this.longitude = result.data.longitude
						this.latitude  = result.data.latitude
						this.covers = [
							{
								longitude: this.longitude,
								latitude: this.latitude,
							}, {
								longitude: this.longitude,
								latitude: this.latitude,
							}
						]
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading store error')
					}
				})
			},
			
			/**
			 * 导航到店
			 */
			goMap() {
				let addr = this.storeData.region + this.storeData.address
				uni.openLocation({
					name: this.storeData.name,
					longitude: parseFloat(this.longitude),
					latitude: parseFloat(this.latitude),
					address: addr,
					scale: 15,
					fail: function (e) {
						console.log(e)
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	// 底部部件样式
	.index-footer-widget {
		position: absolute;
		bottom: 0;
		z-index: 1000;
		width: 100%;
		background-color: #FFF;
		.go-btn {
			padding: 26rpx;
			margin: 20rpx 30rpx;
			font-size: 30rpx;
			font-weight: bold;
			text-align: center;
			color: #FFFFFF;
			border-radius: 8rpx;
			background-color: #FF2C3C;
		}
	}
</style>
