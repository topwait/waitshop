<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- “编辑”购物部件 -->
		<view style="height:60rpx;" v-if="invalidCart.length > 0 || normalCart.length > 0"></view>
		<view class="index-tools-widget" v-if="invalidCart.length > 0 || normalCart.length > 0">
			<view>共{{cartNum}}件宝贝</view>
			<view @click="onChangeManage">{{isManage ? '完成' : '编辑'}}</view>
		</view>
		
		<!-- “空的”购物车组件 -->
		<view class="index-empty-widget" v-if="invalidCart.length <= 0 && normalCart.length <= 0">
			<image class="img-null" src="/static/empty/empty_cart.png"></image>
			<view class="muted">{{isLogin ? '购物车空空如也' : '登录后才能查看哦！'}}</view>
			<navigator class="go-view" v-if="isLogin"
				open-type="switchTab" 
				url="/pages/index/index" hover-class="none"
			>去逛逛</navigator>
			<navigator class="go-view" v-else
				url="/pages/login/login" hover-class="none"
			>去登录</navigator>
		</view>
		
		<!-- “正常”购物车部件 -->
		<view class="index-cart-widget" v-if="normalCart.length > 0">
			<view class="cart-item" v-for="(item, index) in normalCart" :key="index">
				<view class="u-flex u-margin-right-20">
					<u-checkbox-group width="56rpx">
						<u-checkbox shape="circle" active-color="#FF5058"
							v-model="item.selected"
							@change="onChoiceCur(item.id, index)"
						></u-checkbox>
					</u-checkbox-group>
					<u-image width="180rpx" height="180rpx" border-radius="12" :lazy-load="true" :src="item.image"
						@click="$toPage('/pages/goods/detail/detail?goods_id='+item.goods_id)"></u-image>
				</view>
				<view style="overflow: hidden;">
					<view class="u-line-2 u-font-26" 
						@click="$toPage('/pages/goods/detail/detail?goods_id='+item.goods_id)">
						{{ item.name }}
					</view>
					<view class="u-margin-tb-16 u-line-1 u-font-24 u-color-muted" 
						@click="$toPage('/pages/goods/detail/detail?goods_id='+item.goods_id)">
						{{ item.spec_value_str }}
					</view>
					<view class="u-flex u-row-between">
						<view class="u-font-weight u-color-major">
							<text class="u-font-24">￥</text>
							<text class="u-font-32">{{ item.sell_price }}</text>
						</view>
						<u-number-box v-model="item.num" :color="'#323233'"
							:min="1" :max="item.stock || 1"
							@change="onCartChange(item.id, item.sku_id, index)"
						></u-number-box>
					</view>
				</view>
			</view>
		</view>
		
		<!-- “失效”购物车部件 -->
		<view class="index-invalid-widget" v-if="invalidCart.length > 0">
			<view class="cart-header">
				<view class="u-font-28">失效商品</view>
				<view @click="onCartInvalid">
					<u-icon name="trash" size="28" color="#999"></u-icon>
					<text class="u-font-26 u-margin-left-10">清空</text>
				</view>
			</view>
			<view class="cart-item" v-for="(item, index) in invalidCart" :key="index">
				<view class="left--item">
					<view class="left-product__status">失效</view>
					<view class="left-invalid__background">已失效</view>
					<u-image width="180rpx" height="180rpx" border-radius="12" :lazy-load="true" :src="item.image"></u-image>
				</view>
				<view style="overflow: hidden;">
					<view class="u-line-2 u-font-26 u-color-muted">{{ item.name }}</view>
					<view class="u-margin-tb-16 u-line-1 u-font-24 u-color-muted">{{ item.spec_value_str }}</view>
					<view class="u-flex u-row-between">
						<view class="u-font-weight u-color-muted">
							<text class="u-font-24">￥</text>
							<text class="u-font-32">{{ item.sell_price }}</text>
						</view>
						<u-number-box :color="'#323233'" v-model="item.stock" :min="1" :max="item.stock || 1" disabled></u-number-box>
					</view>
				</view>
			</view>
		</view>
				
		<!-- “商品”好物推荐部件 -->
		<wait-title text="精品推荐" topPadding="30rpx" bottomPadding="10rpx" v-if="goodsBestList.length>0"></wait-title>
		<wait-goods-list :list="goodsBestList" v-if="goodsBestList.length>0"></wait-goods-list>
		<view style="height:20rpx;" v-if="goodsBestList.length>0 && invalidCart.length <= 0 && normalCart.length <= 0"></view>
		
		<!-- “结算”购物车组件 -->
		<view style="height:120rpx;" v-if="invalidCart.length > 0 || normalCart.length > 0"></view>
		<view class="index-footer-widget" v-if="invalidCart.length > 0 || normalCart.length > 0">
			<u-checkbox-group>
				<u-checkbox shape="circle" active-color="#FF5058" v-model="choiceAll" @change="onChoiceAll()">
					<text class="u-font-26">全选</text>
				</u-checkbox>
			</u-checkbox-group>
			<view class="u-flex">
				<view class="u-flex u-padding-right-20">
					<text class="u-font-26 u-color-lighter u-padding-right-10">合计:</text>
					<text class="u-font-36 u-font-weight u-color-major">¥{{totalPrice}}</text>
				</view>
				<u-button type="error" shape="circle" hover-class="none" 
					v-if="!isManage"
					:disabled="choiceCount===0 ? true : false"
					:custom-style="{height: '74rpx', lineHeight: '74rpx'}"
					@click="onSettle"
				>去结算({{choiceCount}})</u-button>
				<u-button type="error" shape="circle" hover-class="none" plain
					v-if="isManage"
					:disabled="choiceCount===0 ? true : false"
					:custom-style="{height: '68rpx', lineHeight: '68rpx'}"
					@click="onCartDelete"
				>删除({{choiceCount}})</u-button>
			</view>
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
	import {mapState, mapMutations} from 'vuex'
	export default {
		data() {
			return {
				isFirstLoading: true, // 首次加载
				
				normalCart: [],     // 正常状态购物车
				invalidCart: [],    // 无效状态购物车
				
				cartNum: 0,         // 购物车产品数
				totalPrice: 0,		// 勾选的商品总价
				choiceCount: 0,		// 勾选的商品数量
				choiceAll: false,	// 是否全选
				isManage: false,	// 是否编辑
				
				goodsBestList: []   //好物推荐商品
			}
		},
		computed: {
			...mapState(['isLogin'])
		},
		onShow() {
			this.getGoodsBest()
			if (this.isLogin) {
				this.getCartList()
				this.getCartNums()
			} else {
				this.$nextTick(() => {
					this.isFirstLoading = false
				})
			}
		},
		methods: {
			/**
			 * 获取推荐商品
			 */
			getGoodsBest() {
				const param = {type: 'best'}
				this.$u.api.apiGoodsRecommend(param).then(result => {
					if (result.code === 0) {
						this.goodsBestList = result.data
					} else {
						this.$showToast('loading goodsBest error')
					}
				})
			},
			
			/**
			 * 获取购物车列表
			 */
			getCartList() {
				this.$u.api.apiCartList().then(result => {
					if (result.code === 0) {
						this.normalCart = result.data.normal
						this.invalidCart = result.data.invalid
						this.handleChoice()
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading cart error')
					}
				})
			},
			
			/**
			 * 获取购物车数量
			 */
			getCartNums() {
				this.$u.api.apiCartNum().then(result => {
					if (result.code === 0) {
						this.cartNum = result.data.count
					} else {
						this.$showToast('loading cartNum exception')
					}
				})
			},
			
			/**
			 * 销毁选择的产品
			 */
			onCartDelete() {
				const that = this
				let ids = []
				this.normalCart.forEach(function(item) {
					if (item["selected"]) {
						ids.push(item["id"])
					}
				})
				if (ids.length <= 0) {
					return this.$showToast('您还没选择产品哦~')
				}
				uni.showModal({
				    title: '温馨提示',
				    content: '您确定要删除吗？',
					cancelText: '再想一想',
					confirmText: '狠心删除',
					confirmColor: '#FF5058',
				    success: function (res) {
				        if (res.confirm) {
				            that.$u.api.apiCartDel({ids: ids}).then((result) => {
				            	if (result.code === 0) {
				            		that.getCartList()
				            		that.$showSuccess(result.msg)
				            	} else {
				            		that.$showToast(result.msg)
				            	}
				            })
				        }
				    }
				})
			},
			
			/**
			 * 清空无效产品
			 */
			onCartInvalid() {
				const that = this
				let ids = []
				this.invalidCart.forEach(function(item) {
					ids.push(item["id"])
				})
				if (ids.length <= 0) {
					return this.$showToast('当前没有失效商品哦~')
				}
				uni.showModal({
				    title: '温馨提示',
				    content: '您确定要清空吗？',
					cancelText: '再想一想',
					confirmText: '狠心清空',
					confirmColor: '#FF5058',
				    success: function (res) {
				        if (res.confirm) {
				            that.$u.api.apiCartDel({ids: ids}).then(result => {
				            	that.getCartList()
				            	that.$showToast(result.msg)
				            }) 
				        }
				    }
				})
			},
			
			/**
			 * 修改数量
			 */
			onCartChange(id, sku_id, index) {
				let num   = this.normalCart[index]['num']
				let stock = this.normalCart[index]['stock']
				
				if ((stock - num) < 0) {
					this.normalCart[index]['num'] = stock
					this.$showToast('该产品不能购买更多哦~')
					return false
				}
				
				let param = {id: id, sku_id: sku_id, num: num}
				this.$u.api.apiCartChange(param).then((result) => {
					if (result.code !== 0) {
						this.$showToast(result.msg)
					}
				})
				
				this.handleChoice()
			},
			
			/**
			 * 处理函数
			 */
			handleChoice() {
				let totalPrice  = 0
				let choiceCount = 0
				let isChoiceAll = true
				
				this.normalCart.forEach(function(item) {
					if (!item['selected'] && isChoiceAll===true) {
						isChoiceAll = false
					} else if (item['selected']) {
						choiceCount += 1
						totalPrice += (item['sell_price'] * item['num'])
					}
				})
				
				this.choiceAll = isChoiceAll
				this.totalPrice = totalPrice.toFixed(2)
				this.choiceCount = choiceCount
			},
			
			/**
			 * 单选
			 */
			onChoiceCur(id, index) {
				let selected = this.normalCart[index]['selected']
				this.normalCart[index]['selected'] = selected ? false : true

				let data = {id: id, selected:selected ? 0 : 1, all:0 }
				this.$u.api.apiCartChoice(data).then((result) => {
					if (result.code !== 0) {
						this.$showToast(result.msg)
					}
				})
				
				this.handleChoice()
			},
			
			/**
			 * 全选
			 */
			onChoiceAll() {
				let selected = this.choiceAll ? false : true
				let cartData = this.normalCart
				let newsData = []
				
				cartData.forEach(function(item) {
					item['selected'] = selected
					newsData.push(item)
				})
				
				this.normalCart = newsData
				this.choiceAll = selected
				
				let param = {id: 0, selected:selected ? 1 : 0, all:1 }
				this.$u.api.apiCartChoice(param).then((result) => {
					if (result.code !== 0) {
						this.$showToast(result.msg)
					}
				})

				this.handleChoice()
			},
			
			/**
			 * 编辑
			 */
			onChangeManage() {
				this.isManage = !this.isManage
			},
			
			/**
			 * 去结算
			 */
			onSettle() {
				if (!this.isLogin) {
					return this.$showToast('请登录后再操作！')
				}
				let products = []
				this.normalCart.forEach(function(item) {
					if (item["selected"]) {
						products.push({
							goods_id: item["goods_id"],
							sku_id: item["sku_id"],
							num: item["num"]
						})
					}
				})
				
				const param = {products: products}
				this.$u.api.apiOrderSettle(param).then(result => {
					if (!result.data.pass || !result.data.status) {
						let errorTips = ''
						result.data.pStatusArray.forEach((item) => {
							if (item.errorTips !== false) {
								errorTips = item.errorTips
							}
						})
						return this.$showToast(errorTips)
					} else {
						const url = '/pages/order/purchase/purchase?data='
						uni.navigateTo({url: url +  encodeURIComponent((JSON.stringify({products}))) })
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	
	
	// “编辑”购物组件样式
	.index-tools-widget {
		display: flex;
		justify-content: space-between;
		position: fixed;
		top: var(--window-top);
		left: 0;
		right: 0;
		z-index: 9999;
		font-size: 26rpx;
		color: #333333;
		height: 60rpx;
		line-height: 60rpx;
		padding: 0 30rpx;
		background-color: #FFFFFF;
	}
	
	.index-empty-widget {
		text-align: center;
		padding: 80rpx 0 50rpx;
		background-color: #FFFFFF;
		.img-null {
			width: 300rpx;
			height: 300rpx;
		}
		.muted {
			font-size: 24rpx;
			color: #999999;
			padding: 20rpx 0;
		}
		.go-view {
			color: $-color-main;
			width: 184rpx;
			margin-left: auto;
			margin-right: auto;
			padding: 8rpx 24rpx;
			border-radius: 50rpx;
			border: 1px solid $-color-main;
		}
	}

	// “正常”购物车组件样式
	.index-cart-widget {
		margin: 20rpx;
		padding: 0 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.cart-item {
			display: flex;
			padding: 20rpx 0;
		}
	}
	
	// “失效”购物车组件样式
	.index-invalid-widget {
		margin: 20rpx;
		padding: 0 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.cart-header {
			display: flex;
			justify-content: space-between;
			padding: 20rpx 0;
			border-bottom: 1px solid #f6f6f6;
		}
		.cart-item {
			position: relative;
			display: flex;
			padding: 20rpx 0;
			.left--item {
				display: flex;
				align-items: center;
				margin-right: 20rpx;
				flex-shrink: 0;
				.left-product__status {
					flex-shrink: 0;
					padding: 5rpx 15rpx;
					font-size: 24rpx;
					color: #FFFFFF;
					border-radius: 4rpx;
					margin-right: 20rpx;
					background-color: #CCCCCC;
				}
				.left-invalid__background {
					position: absolute;
					top: 11px;
					left: 93rpx;
					z-index: 10;
					display: flex;
					align-items: center;
					justify-content: center;
					color: rgba(#CCC, 0.5);
					width: 180rpx;
					height: 180rpx;
					border-radius: 12rpx;
					background-color: rgba(#000, 0.5);
				}
			}
		}
	}
	
	// “结算”购物车组件
	.index-footer-widget {
		display: flex;
		align-items: center;
		justify-content: space-between;
		position: fixed;
		left: 0;
		right: 0;
		bottom: var(--window-bottom);
		z-index: 99999;
		height: 100rpx;
		padding: 0 30rpx;
		box-shadow: 0 0 8rpx #EEEEEE;
		background-color: #FFFFFF;
	}
</style>
