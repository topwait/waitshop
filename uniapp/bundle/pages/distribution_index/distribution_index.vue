<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 【非分销会员】 -->
		<view class="wrong-container" v-if="indexData.length && !indexData.user.is_distribution">
			<view class="wrong-header-widget">
				<view class="userInfo">
					<view class="avatar">
						<image class="u-equal-bfb u-radius-circular" :src="indexData.user.avatar"></image>
					</view>
					<view>
						<view class="u-font-36 u-font-weight u-color-white">{{indexData.user.nickname}}</view>
						<view class="u-font-24 u-color-white u-margin-top-10">ID：{{indexData.user.sn}}</view>
					</view>
				</view>
			</view>
			
			<view class="wrong-apply-widget">
				<view class="icon">
					<u-icon name="edit-pen-fill" color="#F29100" size="80" v-if="!indexData.apply || isReset"></u-icon>
					<view class="image stay" v-if="indexData.apply.status == 0 && !isReset">
						<u-icon name="hourglass-half-fill" color="#FFF" size="80"></u-icon>
					</view>
					<view class="image success" v-if="indexData.apply.status == 1 && !isReset">
						<u-icon name="checkbox-mark" size="80" color="#FFF"></u-icon>
					</view>
					<view class="image fail" v-if="indexData.apply.status == 2 && !isReset">
						<u-icon name="close" size="70" color="#FFF"></u-icon>
					</view>
				</view>
				<view class="title" v-if="indexData.apply && !isReset">审核结果</view>
				<view class="title" v-else>申请成为分销会员</view>
				<view class="main">
					<u-form ref="uForm" :model="form" v-if="!indexData.apply || isReset">
						<u-form-item :label-width="150" label="真实姓名："><u-input v-model="form.nickname" placeholder="请输入您的真实姓名" /></u-form-item>
						<u-form-item :label-width="150" label="手机号码："><u-input v-model="form.telephone" placeholder="请输入您的手机号码" /></u-form-item>
						<u-form-item :label-width="150" label="现住省份：">
							<view class="u-color-muted" @click="onShowRegion" v-if="!form.region">请选择省市区</view>
							<view v-else @click="onShowRegion">{{form.region}}</view>
							<u-select v-model="showRegion" mode="mutil-column-auto" :list="regionList" @confirm="onRegionChange"></u-select>
						</u-form-item>
						<u-form-item :label-width="150" label="邀请码："><u-input v-model="form.invite_code" placeholder="(选填)" /></u-form-item>
						<u-form-item :label-width="150" label="申请原因："><u-input v-model="form.apply_reason" type="textarea" placeholder="(选填)" /></u-form-item>
					</u-form>
					<view class="submit-status-warp" v-if="indexData.apply && !isReset">
						<view class="u-font-36 u-font-weight u-margin-top-20">申请正在审核中，请耐心等待</view>
						<view class="u-font-26 u-color-major u-margin-top-20" v-if="indexData.apply.status === 0">处理中...</view>
						<view class="u-font-26 u-color-major u-margin-top-20" v-if="indexData.apply.status === 1">审核结果: {{indexData.apply.audit_reason}}</view>
						<view class="u-font-26 u-color-major u-margin-top-20" v-if="indexData.apply.status === 2">失败原因: {{indexData.apply.audit_reason}}</view>
					</view>
					<view class="submit-content-warp" v-if="indexData.apply && !isReset">
						<view class="form-item">
							<view class="lable">真实姓名：</view>
							<view class="input">{{indexData.apply.nickname}}</view>
						</view>
						<view class="form-item">
							<view class="lable">手机号码：</view>
							<view class="input">{{indexData.apply.telephone}}</view>
						</view>
						<view class="form-item">
							<view class="lable">现住省份：</view>
							<view class="input">{{indexData.apply.telephone}}</view>
						</view>
						<view class="form-item">
							<view class="lable">申请原因：</view>
							<view class="input">{{indexData.apply.apply_reason}}</view>
						</view>
					</view>
				</view>
			</view>
			
			<view style="height:700rpx;" v-if="!indexData.apply || isReset"></view>
			<view style="height:580rpx;" v-else></view>
			
			<view class="wrong-footer-widget">
				<view class="submit-btn" v-if="!indexData.apply || isReset" @click="$u.debounce(onApply, 500)">提交申请</view>
				<view class="submit-btn" v-if="indexData.apply.status==2 && !isReset" @click="$u.debounce(onReset, 500)">重新申请</view>
				<view class="submit-tips">申请提交后，我们将会在1-2个工作日内给您回复</view>
			</view>
		</view>
		
		<!-- 【是分销会员】 -->
		<block v-else>
			<!-- 分销部件 -->
			<view class="index-distribution-widget">
				<view class="user" v-if="indexData.user">
					<view class="avatar">
						<image :src="indexData.user.avatar" :lazy-load="true" class="u-radius-circular u-equal-bfb"></image>
						<view class="level">{{ indexData.user ? indexData.user.distribution_level : '分销会员' }}</view>
					</view>
					<view class="info">
						<view class="u-font-36 u-color-white u-margin-bottom-24">{{indexData.user.nickname}}</view>
						<view class="u-font-24 u-color-body">我的邀请人：{{indexData.user.first_leader || '暂无推荐人'}}</view>
					</view>
				</view>	
			</view>
			
			<!-- 余额组件 -->
			<view class="index-balance-widget">
				<view class="balance-item">
					<view class="text">今日预估收益</view>
					<view class="number">￥{{indexData.todayEarnings || 0}}</view>
				</view>
				<view class="balance-item">
					<view class="text">本月预估收益</view>
					<view class="number">￥{{indexData.monthEarnings || 0}}</view>
				</view>
				<view class="balance-item">
					<view class="text">累计收益</view>
					<view class="number">￥{{indexData.totalEarnings || 0}}</view>
				</view>
			</view>
			
			<!-- 高度站位 -->
			<view style="height: 84rpx;"></view>
			
			<!-- 佣金部件 -->
			<view class="index-reward-widget">
				<view class="reward-item">
					<view class="reward-item__type">可提现</view>
					<view class="reward-item__price">{{ indexData.canExtract || 0 }}</view>
					<view class="reward-item__tips">已解锁</view>
					<view class="reward-item__btn" @click="$toPage('/bundle/pages/withdraw_apply/withdraw_apply?login=true')">立即提现</view>
				</view>
				<view class="reward-item">
					<view class="reward-item__type">待返佣</view>
					<view class="reward-item__price">{{ indexData.stayUnlock || 0 }}</view>
					<view class="reward-item__tips">待解锁</view>
					<view class="reward-item__btn" @click="$toPage('/bundle/pages/distribution_order/distribution_order?login=true')">查看记录</view>
				</view>
			</view>
			
			<!-- 功能部件 -->
			<view class="index-tools-widget">
				<u-grid :col="2" hover-class="none" >
					<u-grid-item :custom-style="{padding: '50rpx 0'}" @click="onPoster">
						<u-image src="/bundle/static/wallet/account_log.png" width="60rpx" height="60rpx"></u-image>
						<view class="grid-text">邀请海报</view>
					</u-grid-item>
					<u-grid-item :custom-style="{padding: '50rpx 0'}" @click="$toPage('/bundle/pages/distribution_order/distribution_order?login=true')">
						<u-image src="/bundle/static/wallet/integral.png" width="60rpx" height="60rpx"></u-image>
						<view class="grid-text">分销订单</view>
					</u-grid-item>
					<u-grid-item :custom-style="{padding: '50rpx 0'}" @click="$toPage('/bundle/pages/distribution_record/distribution_record?login=true')">
						<u-image src="/bundle/static/wallet/recharge.png" width="60rpx" height="60rpx"></u-image>
						<view class="grid-text">佣金明细</view>
					</u-grid-item>
					<u-grid-item :custom-style="{padding: '50rpx 0'}" @click="$toPage('/bundle/pages/distribution_fans/distribution_fans?login=true')">
						<u-image src="/bundle/static/wallet/fans.png" width="60rpx" height="60rpx"></u-image>
						<view class="grid-text">我的粉丝</view>
					</u-grid-item>
				</u-grid>
			</view>
			
			<!-- 分销海报 -->
			<wait-popup-share shareType="user" :show="showPoster" @close="showPoster = false"></wait-popup-share>
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
	import area from '@/utils/area'
	import {toPage} from '@/utils/tools'
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 全国地区数据
				regionList: [],
				// 显示地区选择
				showRegion: false,
				// 分销海报
				showPoster: false,
				// 分销主页数据
				indexData: [],
				// 是否重置申请
				isReset: false,
				// 申请表单数据
				form: {
					nickname: '',
					telephone: '',
					invite_code: '',
					apply_reason: '',
					province_id: 0,
					city_id: 0,
					district_id: 0,
					region: ''
				}
			}
		},
		onLoad() {
			this.getDistributionIndex()
			this.$nextTick(() => {
				this.regionList = area
			})
		},
		methods: {
			/**
			 * 获取分销数据
			 */
			getDistributionIndex() {
				this.$u.api.apiDistributionIndex().then(result => {
					if (result.code === 0) {
						this.indexData = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading data error')						
					}
				})
			},
			
			/**
			 * 申请成为分销会员
			 */
			onApply() {
				if (!this.form.nickname) return this.$showToast('请填写真实姓名')
				if (!this.form.telephone) return this.$showToast('请填写手机号码')
				this.$u.api.apiDistributionApply(this.form).then(result => {
					if (result.code === 0) {
						this.getDistributionIndex()
					} 
					return this.$showToast(result.msg)
				})
			},
			
			/**
			 * 重新申请
			 */
			onReset() {
				this.isReset = true
			},
			
			/**
			 * 显示选择地区
			 */
			onShowRegion() {
				this.showRegion = !this.showRegion
			},
			
			/**
			 * 选择所在地区
			 */
			onRegionChange(region) {
				this.form.province_id = region[0].value;
				this.form.city_id = region[1].value;
				this.form.district_id = region[2].value;
				this.form.region = region[0].label + " " + region[1].label + " " + region[2].label
			},
			
			/**
			 * 弹出分享海报
			 */
			onPoster() {
				this.showPoster = true
			}
		}
	}
</script>

<style lang="scss">
	// 分销部件样式
	.index-distribution-widget {
		height: 280rpx;
		background-color: #ff3140;
		background-repeat: no-repeat;
		background-position: center right;
		background-size: auto 100%;
		background-image: url('data:image/png;base64,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');
		.user {
			display: flex;
			position: absolute;
			left: 30rpx;
			top: 44rpx;
			.avatar {
				width: 110rpx;
				height: 110rpx;
				padding: 2rpx;
				position: relative;
				border-radius: 50rpx;
				background-color: #FFFFFF;
			}
			.level {
				position: absolute;
				bottom: -20rpx;
				font-size: 22rpx;
				line-height: 29rpx;
				text-align: center;
				color: #fff;
				width: 110rpx;
				height: 34rpx;
				border-radius: 17rpx;
				background: #f79c0c;
				border: 1px solid #fff;
			}
			.info {
				margin-left: 30rpx;
				margin-top: 16rpx;
			}
		}
	}
	
	// 余额组件样式
	.index-balance-widget {
		position: absolute;
		top: 220rpx;
		left: 20rpx;
		right: 20rpx;
		display: flex;
		justify-content: space-between;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.balance-item {
			width: 33.33%;
			margin: 30rpx 0;
			text-align: center;
			box-sizing: border-box;
			border-right: 1px solid #F2F2F2;
			&:last-child { border-right: none; }
			.text { font-size: 26rpx; color: #999; margin-bottom: 10rpx; }
			.number { color: #000; font-size: 32rpx; }
		}
	}
	
	// 佣金部件样式
	.index-reward-widget {
		display: flex;
		justify-content: space-between;
		margin: 30rpx 20rpx;
		padding: 20rpx;
		border-radius: 12rpx;
		box-shadow: 0 0 5rpx #EEEEEE;
		background-color: #FFFFFF;
		.reward-item {
			display: flex;
			flex-direction: column;
			align-items: center;
			width: 50%;
			&:first-child { 
				border-right: 1px solid rgba($color: #EEEEEE, $alpha: 0.5); 
			}
			.reward-item__type {
				font-size: 26rpx;
				color: #000000;
				padding-top: 20rpx;
			}
			.reward-item__price {
				font-size: 32rpx;
				font-weight: bold;
				padding: 20rpx;
			}
			.reward-item__tips {
				font-size: 24rpx;
				color: #999999;
			}
			.reward-item__btn {
				color: #FFFFFF;
				font-size: 24rpx;
				border-radius: 30rpx;
				padding: 10rpx 20rpx;
				margin: 20rpx 0;
				background-color: #FF5058;
			}
		}
	}
	
	// 分销工具部件样式
	.index-tools-widget {
		margin: 20rpx;
		.u-grid {
			border-radius: 12rpx !important;
		}
		.grid-text {
			font-size: 28rpx;
			color: #333333;
			margin-top: 20rpx;
		}
	}
	
	// 非分销会员样式
	.wrong-container {
		// 申请头部样式
		.wrong-header-widget {
			height: 300rpx;
			background-color: #FF5058;
			.userInfo {
				display: flex;
				align-items: center;
				margin-left: 20rpx;
				padding-top: 30rpx;
				.avatar {
					display: flex;
					align-items: center;
					justify-content: center;
					width: 110rpx;
					height: 110rpx;
					padding: 4rpx;
					margin-right: 20rpx;
					border-radius: 50%;
					background-color: #FFF;
				}
			}
		}
		
		// 申请表单部件样式
		.wrong-apply-widget {
			position: absolute;
			top: 220rpx;
			left: 20rpx;
			right: 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			.icon {
				display: flex;
				align-items: center;
				justify-content: center;
				width: 150rpx;
				height: 150rpx;
				padding: 10rpx;
				margin: 0 auto;
				margin-top: -75rpx;
				border-radius: 50%;
				background-color: #FFFFFF;
				.image {
					display: flex;
					align-items: center;
					justify-content: center;
					width: 100%;
					height: 100%;
					border-radius: 50%;
					background-color: #F29100;
					&.success {
						background-color: #18B566;
					}
					&.fail {
						background-color: #FF5058;
					}
					&.stay {
						background-color: #F29100;
					}
				}
			}
			.title {
				color: #333;
				font-size: 36rpx;
				font-weight: bold;
				text-align: center;
				margin: 20rpx 0;
			}
			.main {
				padding: 20rpx 40rpx;
				.submit-status-warp {
					display: flex;
					flex-direction: column;
					align-items: center;
					padding-bottom: 50rpx;
					border-bottom: 1rpx #e5e5e5 dashed;
					.status-icon {
						display: flex;
						justify-content: center;
						width: 100rpx;
						height: 100rpx;
						border-radius: 50%;
						background-color: #ff2c3c;
					}
				}
				.submit-content-warp {
					margin: 30rpx 0;
					margin-bottom: 20rpx;
					.form-item {
						display: flex;
						align-items: center;
						padding: 14rpx 0;
						font-size: 28rpx;
						color: #666;
						.input { color: #333333; }
					}
				}
			}
		}
		
		// 底部按钮样式
		.wrong-footer-widget {
			margin: 20rpx 0;
			.submit-btn {
				color: #FFFFFF;
				font-size: 30rpx;
				text-align: center;
				height: 82rpx;
				line-height: 82rpx;
				margin: 0 20rpx;
				border-radius: 60rpx;
				background: #ff2c3c;
				&.revoke {
					margin-top: 20rpx;
					background-color: #CCC;
				}
			}
			.submit-tips {
				color: #999999;
				text-align: center;
				font-size: 24rpx;
				margin-top: 20rpx;
			}
		}
	}
</style>
