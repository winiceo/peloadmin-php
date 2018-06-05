<template>
    <div class="container-fluid" style="margin-top:15px;">
        <div class="panel panel-default">
            <div class="panel-heading">
                添加币种
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

    const AddCoin = {
        data: () => ({
            name: '',
            symbol: "",
            decimals: 0,
            weight: 0,
            fee:0,
            withdraw_enable:0,
            radio: {
                on: true,
                off: false,
            },
            add: {
                loadding: false,
                error: false,
                error_message: ''
            },
            message: {
                success: null,
                error: null,
            },
        }),
        watch: {
            'categories'() {
                if (this.categories.length <= 0) {
                    this.message.error = '请先添加币种分类，在进行添加币种';
                }
            }
        },
        methods: {
            send() {
                this.resetMessage();
                if (!this.validate()) return;
                const {name = '', symbol = '', decimals = 0,withdraw_enable=0,fee=0,weight=0} = this;
                let btn = $("#myButton").button('loading');
                request.post(createRequestURI('site/coins'), {
                    name, symbol,decimals,withdraw_enable,fee, weight
                }, {
                    validateStatus: status => status === 201
                })
                    .then(response => {
                        this.sendComplate(btn);
                        this.message.success = '添加成功';
                        this.$router.replace({path: '/setting/coin'});
                    })
                    .catch(({response: {data = {}} = {}}) => {
                        btn.button('reset');
                        let Message = new plusMessageBundle(data);
                        this.message.error = Message.getMessage();
                    });
            },
            sendComplate(btn) {
                btn.button('complete');
                setTimeout(() => {
                    btn.button('reset');
                    this.name = '',
                        this.category = 0;
                    this.weight = 0;
                }, 1500);
            },

            validate() {
                const {name = '', symbol = '', decimals = 0} = this;
                if (!name) {
                    this.message.error = '请输入币种名字';
                    return false;
                }
                if (!symbol) {
                    this.message.error = '请输入币种符号';
                    return false;
                }
                if (!decimals) {
                    this.message.error = '请输入小数位数';
                    return false;
                }
                return true;
            },
            resetMessage() {
                let msg = this.message;
                msg.error = msg.success = null;
            }
        },
        created() {

        }
    };

    export default AddCoin;
</script>