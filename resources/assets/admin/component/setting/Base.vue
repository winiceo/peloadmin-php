<style lang="css" module>
    .containerAround {
        animation-name: "TurnAround";
        animation-duration: 1.6s;
        animation-timing-function: linear;
        animation-direction: alternate;
        animation-iteration-count: infinite;
    }
</style>
<template>
    <div class="container-fluid" style="margin-top:10px;">
        <div class="panel panel-default">
            <div class="panel-heading">
                基本信息配置
            </div>
            <div class="panel-body">
                <loading :loadding="loadding"></loading>
                <form class="form-horizontal" @submit.prevent="submit" v-show="!loadding">
                    <!-- Site name. -->
                    <div class="form-group">
                        <label for="site-name" class="col-sm-2 control-label">应用名称</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="site-name"
                                   aria-describedby="site-name-help-block" placeholder="系统名称" v-model="name">
                        </div>
                        <span class="col-sm-4 help-block" id="site-name-help-block"> </span>
                    </div>
                    <!-- End site name. -->


                    <!-- End ICP 备案信息 -->

                    <!-- Button -->
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button v-if="btnLoading" class="btn btn-primary" disabled="disabled">
                                <span class="glyphicon glyphicon-refresh" :class="$style.containerAround"></span>
                            </button>
                            <button v-else-if="error" @click.prevent="requestSiteInfo" class="btn btn-danger">
                                {{ error_message }}
                            </button>
                            <button v-else type="submit" class="btn btn-primary">{{ message }}</button>
                        </div>
                    </div>
                    <!-- End button -->
                </form>
            </div>
        </div>


        <clear-cache></clear-cache>

    </div>
</template>

<script>
    import {SETTINGS_SITE_UPDATE} from '../../store/types';
    import request, {createRequestURI} from '../../util/request';
    import ClearCache from '../../components/modules/setting/ClearCache';

    const settingBase = {
        components: {
            'clear-cache': ClearCache,
        },
        data: () => ({
            btnLoading: false,
            loadding: true,
            error: false,
            error_message: '重新加载',
            message: '提交'
        }),
        computed: {
            name: {
                get() {
                    return this.$store.state.site.name;
                },
                set(name) {
                    this.$store.commit(SETTINGS_SITE_UPDATE, {name});
                }
            },
            keywords: {
                get() {
                    return this.$store.state.site.keywords;
                },
                set(keywords) {
                    this.$store.commit(SETTINGS_SITE_UPDATE, {keywords});
                }
            },
            description: {
                get() {
                    return this.$store.state.site.description;
                },
                set(description) {
                    this.$store.commit(SETTINGS_SITE_UPDATE, {description});
                }
            },
            icp: {
                get() {
                    return this.$store.state.site.icp;
                },
                set(icp) {
                    this.$store.commit(SETTINGS_SITE_UPDATE, {icp});
                }
            }
        },
        methods: {
            requestSiteInfo() {
                request.get(createRequestURI('site/baseinfo'), {
                    validateStatus: status => status === 200
                }).then(({data = {}}) => {
                    this.$store.commit(SETTINGS_SITE_UPDATE, {...data});
                    this.loadding = false;
                }).catch(({response: {data: {message = '加载失败'} = {}} = {}}) => {
                    this.loadding = false;
                    window.alert(message);
                    // this.error_message
                });
            },
            submit() {
                const {name, keywords, description, icp} = this;
                this.btnLoading = true;
                request.patch(createRequestURI('site/baseinfo'), {name, keywords, description, icp}, {
                    validateStatus: status => status === 201
                }).then(() => {
                    this.message = '执行成功';
                    this.btnLoading = false;
                    setTimeout(() => {
                        this.message = '提交';
                    }, 1000);
                }).catch(({response: {data: {message = '加载失败'} = {}} = {}}) => {
                    this.btnLoading = false;
                    this.error = true;
                    this.error_message = message;
                    setTimeout(() => {
                        this.error = false;
                        this.error_message = '重新加载';
                    }, 1000);
                });
            }
        },
        created() {
            this.requestSiteInfo();
        }
    };

    export default settingBase;
</script>
