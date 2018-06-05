<template>
    <div class="clearfix">
        <div class="container-fluid" style="padding-top: 70px; position: relative;">

            <nav class="navbar navbar-default navbar-fixed-top" style="position: absolute;">
                <div class="navbar-header">

                    <span class="navbar-brand">
                        <i class="glyphicon glyphicon-asterisk"></i>
                        资产设置
                    </span>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <!-- The settings nav. -->
                  <ul class="nav navbar-nav">
                    <router-link to="/assets" tag="li" active-class="active" exact>
                      <a href="#">用户资产</a>
                    </router-link>
                    <router-link to="/assets/config" tag="li" active-class="active">
                      <a href="#">配置</a>
                    </router-link>
                    <router-link to="/assets/statistics" tag="li" active-class="active">
                      <a href="#">统计</a>
                    </router-link>
                    <router-link to="/assets/cashes" tag="li" active-class="active">
                      <a href="#">提现审批</a>
                    </router-link>
                  </ul>
                </div>  
            </nav>
            <module-alert></module-alert>
            <router-view />
        </div>
    </div>
</template>
<script>
    import Alert from '../Alert';
    export default{
        components: { [Alert.name]: Alert },
    }
</script>