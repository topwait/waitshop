<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>

		<!-- 提现方式部件 -->
		<view class="index-method-widget" v-if="withdrawData.withdraw_way.length > 0">
			<u-tabs :list="withdrawData.withdraw_way" 
				active-color="#FF5058" 
				:is-scroll="true"
				:current="tabIndex"
				@change="onSwitchTab">
			</u-tabs>
		</view>
		
		<!-- 提现表单部件 -->
		<template v-if="withdrawData.withdraw_way[tabIndex] != null">
			<!-- 微信收款码 -->
			<template v-if="withdrawData.withdraw_way[tabIndex].value == 4">
				<view class="index-form-widget">
					<u-field label-width="160" label="微信账号" v-model="withdraw.account" placeholder="请输入微信账号"></u-field>
					<u-field label-width="160" label="真实姓名" v-model="withdraw.real_name" placeholder="请输入真实姓名"></u-field>
					<u-field label-width="160" label="备注" v-model="withdraw.apply_remark" placeholder="(选填)"></u-field>
					<view class="u-margin-top-20">
						<wait-uploader tips="微信收款码" v-model="withdraw.money_qr_code"></wait-uploader>
					</view>
				</view>
			</template>
			
			<!-- 支付包收款码 -->
			<template v-if="withdrawData.withdraw_way[tabIndex].value == 5">
				<view class="index-form-widget">
					<u-field label-width="160" label="支付宝账号" v-model="withdraw.account" placeholder="请输入支付宝账号"></u-field>
					<u-field label-width="160" label="真实姓名" v-model="withdraw.real_name" placeholder="请输入真实姓名"></u-field>
					<u-field label-width="160" label="备注" v-model="withdraw.apply_remark" placeholder="(选填)"></u-field>
					<view class="u-margin-top-20">
						<wait-uploader tips="支付宝收款码" v-model="withdraw.money_qr_code"></wait-uploader>
					</view>
				</view>
			</template>
			
			<!-- 银行卡 -->
			<template v-if="withdrawData.withdraw_way[tabIndex].value == 3">
				<view class="index-form-widget">
					<u-field label-width="160" label="银行卡账号" v-model="withdraw.account" placeholder="请输入银行卡账号"></u-field>
					<u-field label-width="160" label="持卡人姓名" v-model="withdraw.real_name" placeholder="请输入持卡人姓名"></u-field>
					<u-field label-width="160" label="提现银行" v-model="withdraw.bank" placeholder="请输入银行名称"></u-field>
					<u-field label-width="160" label="银行支行" v-model="withdraw.subbank" placeholder="如: 洛溪银行"></u-field>
					<u-field label-width="160" label="备注" v-model="withdraw.apply_remark" placeholder="(选填)"></u-field>
				</view>
			</template>
		</template>

		<!-- 提现金额部件 -->
		<view class="index-money-widget">
			<view class="input u-flex u-row-between">
				<view class="u-flex u-flex-1">
					<text>¥</text>
					<input type="number" v-model="withdraw.money" placeholder="0.00" />
				</view>
				<view class="u-font-22">
					<text class="u-flex u-row-right u-color-major" @click="onTotalWithdraw">全部提现</text>
					<view class="u-margin-top-10 u-color-muted">可提现余额￥{{withdrawData.earnings}}</view>
				</view>
			</view>
			<view class="u-margin-top-30 u-font-22 u-color-muted">
			    提示： 提现需扣除手续费0.5%
			</view>
		</view>
		
		<!-- 提现按钮部件 -->
		<view class="withdraw-btn" @click="onWithdraw">确认提现</view>
		<view class="withdraw-log" @click="onRecord">提现记录</view>

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
	import {toPage} from '@/utils/tools'
	export default {
		data() {
			return {
				isFirstLoading: true,
				tabIndex: 0,
				withdrawData: {
					earnings: 0,			     //可提现金额
					withdraw_way: [] ,			 //提现的方式
					withdraw_min_money: '',      //最小提现金额
					withdraw_max_money: '',      //最大提现金额
					withdraw_service_charge: '', //提现手续百分比
				},
				withdraw: {
					type: [], 		   //提现类型
					money: '', 		   //提现余额
					account: '',       //收款账户
					real_name: '',     //真实姓名
					apply_remark: '',  //备注
					money_qr_code: '', //收款码
					bank: '', 		   // 提现银行
					subbank: ''        //支行银行卡
				}
			}
		},
		onShow() {
			this.getWithdrawConfig()
		},
		methods: {
			/**
			 * 获取提现配置
			 */
			getWithdrawConfig() {
				this.$u.api.apiWithdrawConfig().then(result => {
					if (result.code === 0) {
						this.withdrawData = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading config error')
					}
				})
			},

			/**
			 * 全部提现
			 */
			onTotalWithdraw() {
				this.withdraw.money = this.withdrawData.earnings
			},

			/**
			 * 申请提现
			 */
			onWithdraw() {
				if (this.withdraw.money == '') return this.$showToast('请输入提现金额')
				if (this.withdraw.money <= 0) return this.$showToast('提现金额不能少于0')
				if (this.withdrawData.withdraw_way[this.tabIndex].value === 3) {
					if (this.withdraw.account == '') return this.$showToast('银行卡账号不可为空')
					if (this.withdraw.real_name == '') return this.$showToast('纸卡人姓名不可为空')
					if (this.withdraw.bank == '') return this.$showToast('提现银行不可为空')
					if (this.withdraw.subbank == '') return this.$showToast('银行支行不可为空')
				} else if (this.withdrawData.withdraw_way[this.tabIndex].value === 4) {
					if (this.withdraw.account == '') return this.$showToast('微信账号不可为空')
					if (this.withdraw.real_name == '') return this.$showToast('真实姓名不可为空')
					if (this.withdraw.money_qr_code.toString() == '') return this.$showToast('微信收款码不可为空')
				}  else if (this.withdrawData.withdraw_way[this.tabIndex].value === 5) {
					if (this.withdraw.account == '') return this.$showToast('支付宝不可为空')
					if (this.withdraw.real_name == '') return this.$showToast('真实姓名不可为空')
					if (this.withdraw.money_qr_code.toString() == '') return this.$showToast('支付宝收款码不可为空')
				}

				const param = {
					type: this.withdrawData.withdraw_way[this.tabIndex].value, //提现类型
					money: this.withdraw.money, //提现余额
					account: this.withdraw.account, //收款账户
					real_name: this.withdraw.real_name, //真实姓名
					apply_remark: this.withdraw.apply_remark, //备注
					money_qr_code: this.withdraw.money_qr_code.toString(), //收款码
					bank: this.withdraw.bank, // 提现银行
					subbank: this.withdraw.subbank //支行银行卡
				}
				this.$u.api.apiWithdrawApply(param).then(result => {
					if (result.code === 0) {
						this.withdraw.money = ''
						toPage('/bundle/pages/withdraw_record/withdraw_record?login=true')
					} else {
						this.$showToast(result.msg)
					}
				})
			},
			
			/**
			 * 提现记录
			 */
			onRecord(id) {
				toPage('/bundle/pages/withdraw_record/withdraw_record?login=true')
			},
			
			/**
			 * 切换选项卡
			 */
			onSwitchTab(index) {
				this.tabIndex = index
			}
		}
	}
</script>

<style lang="scss">
	// 提现方式部件样式
	.index-method-widget {
		margin: 30rpx;
		padding: 10rpx;
		border-radius: 20rpx;
		background-color: #FFFFFF;
	}
	
	// 提现表单部件样式
	.index-form-widget {
		margin: 30rpx;
		padding: 24rpx;
		border-radius: 20rpx;
		background-color: #FFFFFF;
	}
	
	// 提现金额部件样式
	.index-money-widget {
		width: 100%;
		height: 350rpx;
		padding: 66rpx;
		margin: 30rpx;
		border-radius: 20rpx;
		background-color: #FFFFFF;
		.input {
			padding: 24rpx 0;
			font-size: 46rpx;
			border-bottom: 1rpx solid #E5E5E5;
			input {
				padding-left: 30rpx;
				font-size: 66rpx;
				height: 80rpx;
			}
		}
	}
	
	// 提现确认按钮样式
	.withdraw-btn {
		height: 84rpx;
		line-height: 84rpx;
		margin: 0 30rpx;
		margin-top: 60rpx;
		color: #FFFFFF;
		text-align: center;
		border-radius: 60rpx;
		background-color: #FF2C3C;
	}
	
	// 提现记录按钮样式
	.withdraw-log {
		margin-top: 30rpx;
		text-align: center;
		font-size: 26rpx;
		color: #999;
	}
</style>
