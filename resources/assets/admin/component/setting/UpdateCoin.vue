<template>
    <div class="container-fluid" style="margin-top:15px;">
        <div class="panel panel-default">
            <div class="panel-heading">
                更新币种
            </div>
            <div class="panel-body form-horizontal">
                <div class="row">
                    <div class="col-md-11 col-md-offset-1">
                        <div class="form-group">
                            <label for="inputName" class="control-label col-md-2">名字</label>
                            <div class="col-md-5">
                                <input v-model="name" type="text" class="form-control" id="inputName"
                                       placeholder="币种名称">
                            </div>
                            <div class="col-md-5">
                                <div class="help-block">输入币种名字</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputSymbol" class="control-label col-md-2">名字</label>
                            <div class="col-md-5">
                                <input v-model="symbol" type="text" class="form-control" id="inputSymbol"
                                       placeholder="币种符号">
                            </div>
                            <div class="col-md-5">
                                <div class="help-block">输入币种符号</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDecimals" class="control-label col-md-2">小数位数</label>
                            <div class="col-md-5">
                                <input v-model="decimals" type="text" class="form-control" id="inputDecimals"
                                       placeholder="小数位数">
                            </div>
                            <div class="col-md-5">
                                <div class="help-block">输入小数位数</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">提现</label>
                            <div class="col-md-6">
                                <label class="radio-inline">
                                    <input type="radio" :value="radio.on" v-model="withdraw_enable"
                                           > 开启
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" :value="radio.off" v-model="withdraw_enable"
                                           > 关闭
                                </label>
                            </div>
                            <div class="col-md-4">
                                <span class="help-block">是否可以提现</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputFee" class="control-label col-md-2">手续费</label>
                            <div class="col-md-5">
                                <input v-model="fee" type="text" class="form-control" id="inputFee" placeholder="提现手续费">
                            </div>
                            <div class="col-md-5">
                                <div class="help-block">提现手续费</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputWeight" class="control-label col-md-2">币种权重</label>
                            <div class="col-md-5">
                                <input v-model="weight" id="inputWeight" type="text" class="form-control"
                                       placeholder="币种权重">
                            </div>
                            <div class="col-md-5">
                                <div class="help-block">输入币种权重
                                    <small>（越大越靠前）</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-md-2"></label>
                            <div class="col-md-5">
                                <button type="submit" @click="send()" id="myButton" data-complete-text="添加成功"
                                        data-loading-text="提交中..." class="btn btn-primary" autocomplete="off">
                                    确认
                                </button>
                            </div>
                            <div class="col-md-5">
                                <span class="text-success" v-show="message.success">{{ message.success }}</span>
                                <span class="text-danger" v-show="message.error">{{ message.error }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped lang="scss">
    .btn-group {
        margin-bottom: 8px;
    }
</style>
<script>
    import request, {createRequestURI} from '../../util/request';
    import plusMessageBundle from 'plus-message-bundle';

    const UpdateCoin = {
        data: () => ({
            loadding: true,
            coin: {},
            coin_id: 0,
            name: '',
            symbol: "",
            decimals: 0,
            weight: 0,
            fee: 0,
            withdraw_enable: 0,
            radio: {
                on: 1,
                off: 0,
            },
            message: {
                success: null,
                error: null,
            }
        }),

        methods: {
            send() {

                this.resetMessage();

                const {name = '', symbol = '',coin_id = 0, decimals = 0,withdraw_enable=0,fee=0,weight=0} = this;
                if (!name   || !coin_id) {
                    this.add.error = true;
                    this.add.error_message = '参数不完整';
                    return false;
                }

                let data = {};
                if (name != this.coin.name) {
                    data.name = name;
                }


                if (weight != this.coin.weight) {
                    data.weight = parseInt(weight);
                }

                let btn = $("#myButton").button('loading');

                request.patch(createRequestURI(`site/coins/${coin_id}`), {
                    ...data
                }, {
                    validateStatus: status => status === 201
                })
                    .then(response => {
                        this.sendComplate(btn);
                        this.message.success = '更新成功';
                        this.$router.replace({path: '/setting/coins'});
                    })
                    .catch(({response: {data = {}} = {}}) => {
                        btn.button('reset');
                        let Message = new plusMessageBundle(data);
                        this.message.error = Message.getMessage();
                        if (data.name) {
                            error = data.name[0];
                        }
                        if (data.category) {
                            error = data.category[0];
                        }
                        this.add.loadding = false;
                    });
            },

            sendComplate(btn) {
                btn.button('complete');
                setTimeout(() => {
                    btn.button('reset');
                    this.coin.name = this.name;
                    this.coin.coin_category_id = this.category;
                    this.coin.weight = this.weight;
                }, 1500);
            },

            dismisAddAreaError() {
                this.add.error = false;
            },

            setCategory(id) {
                this.category = id;
            },

            // 获取标签分类
            getCategories() {
                this.loadding = true;
                request.get(createRequestURI('site/coins/categories'), {
                    validateStatus: status => status === 200
                })
                    .then(({data = []}) => {
                        this.loadding = false;
                        this.categories = data;
                    })
                    .catch(() => {
                        this.loadding = false;
                    });
            },

            // 获取标签详情
            getCoin() {
                request.get(createRequestURI(`site/coins/${this.coin_id}`), {
                    validateStatus: status => status === 200
                })
                    .then(({data = {}}) => {
                        this.coin = {...data};
                        this.name = data.name;
                        this.symbol = data.symbol;
                        this.decimals = data.decimals;
                        this.withdraw_enable = data.withdraw_enable;
                        this.fee = data.fee;
                        this.weight = data.weight;
                    })
                    .catch(() => {

                    })
            },
            resetMessage() {
                let msg = this.message;
                msg.error = msg.success = null;
            }
        },

        computed: {
            // 按钮是否处于激活状态
            canSend() {
                let nameChanged = (this.name != '' && (this.name != this.coin.name));
                let cateChanged = (this.category !== 0 && (this.coin.coin_category_id !== this.category));
                let weightChanged = (this.weight !== null && (this.coin.weight !== this.weight));
                return (nameChanged || cateChanged || weightChanged);
            }
        },
        created() {
            const {
                coin_id = 0
            } = this.$route.params;

            if (!coin_id) {
                window.alert('参数错误');
                setTimeout(() => {
                    this.$router.go(-1);
                }, 2000);
            }
            this.coin_id = coin_id;
            this.getCoin();

        }
    };

    export default UpdateCoin;
</script>