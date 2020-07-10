<template>
  <div class="row full-height">
    <el-row class="">
      <el-col :span="24" >
        <el-col :sm="24" :md="24" :lg="6" :xl="6" style="background: #15224a" class="full-height test">
          <el-col :span="24" style="padding: 10px;">
            <div style="width: 88%; display: inline-block;">
              <el-input
                id="searchBar"
                size="small"
                placeholder="Search for Coach"
                v-model="search">
                <i slot="prefix" class="el-input__icon el-icon-search"></i>
              </el-input>
            </div>
            <div style="width: 10%; display: inline-block;">
              <el-button type="primary" class="plain" plain size="small" @click="callFilter()"><i class="fas fa-sliders-h" aria-hidden="true" style="color: rgba(255, 255, 255, 0.68);"></i></el-button>
            </div>
          </el-col>
          <div>
            <div v-for="list in data" class="grid-content bg-purple-dark">
<!--              <div v-for="item in list">-->
<!--                {{ item.last_name }}-->
              <el-table
                :data="list"
                highlight-current-row
                @cell-click="getSummary"
                style="width: 100%; cursor: pointer">
                <el-table-column>
                  <template slot-scope="scope">
                    <div style="float:left; padding: 8px;">
                      <el-avatar :size="60" :src="scope.row.pic" class="dbl-border">
                        <img :src="scope.row.pic"/>
                      </el-avatar>
                    </div>
                    <div style="display: inline-block; width: 80%; padding-left: 10px;">
                      <div style="display: block; float: right;">
                        <country-flag :country='scope.row.country_code' size='normal'/>
                      </div>
                      <div class="left-list-header">{{ scope.row.first_name }} {{ scope.row.last_name }}</div>
                      <div class="left-list-sub">Experience - {{ scope.row.experience }} years</div>
                      <div class="left-list-sub">
                        Markets traded -
                        <span v-for="mt in scope.row.market_traded">{{ mt }}, </span>
                      </div>
                      <div class="left-list-sub">
                        Style -
                        <span v-for="st in scope.row.style">{{ st }}, </span>
                      </div>
                      <div style="float:right; margin-top: -20px;">
                        <el-badge :value="1" class="item">
                          <i class="fa fa-calendar-check left-list-badge-icon" aria-hidden="true" style="color: #617da5"></i>
                        </el-badge>
                        <el-badge :value="1" class="item">
                          <i class="el-icon-circle-check left-list-badge-icon" style="color: #92804f"></i>
                        </el-badge>
                        <el-badge :value="1" class="item">
                          <i class="fa fa-ban left-list-badge-icon" aria-hidden="true" style="color: #92505f"></i>
                        </el-badge>
                      </div>
                    </div>
                  </template>
                </el-table-column>
              </el-table>
<!--              </div>-->
            </div>
          </div>
        </el-col>
        <el-col :sm="24" :md="24" :lg="18" :xl="18" style="background: #293f5d; padding: 20px;" class="full-height">
          <!--                <div class="grid-content bg-purple-dark">asdasd</div>-->
          <content-component :selected="passData" ></content-component>
          <session-component></session-component>
        </el-col>
      </el-col>
    </el-row>
    <component ref="modalComponent" :is="importComponent" :selected="selected" @clear="clear()"/>
  </div>


</template>

<script>
  import ContentComponent from './ContentComponent.vue'
  import FilterComponent from './FilterComponent.vue'
  import SessionComponent from './SessionsComponent.vue'
  export default {
    name: 'Index',
    components: {
      FilterComponent,
      ContentComponent,
      SessionComponent
    },
    data() {
      return {
        importComponent: null,
        dialogFormVisible: false,
        selected: '',
        passData: {},
        fit: 'contain',
        search: '',
        data: []
      }
    },
    created: function() {
      this.read()
    },
    methods: {
      async read() {
        // const data = window.axios.get('/api/v1/coaches');
        const res = await fetch('/api/v1/coaches');
        const data = await res.json();
        this.data = data;
        // console.log(this.data)
      },
      getSummary(row) {
        console.log(row)
        // this.passData = row
      },
      callFilter() {
        this.importComponent = FilterComponent
        setTimeout(() => this.ex_callmodal(), 1)
        // this.dialogFormVisible = true
      },
      ex_callmodal() {
        this.$refs.modalComponent.show()
      }
    }
  }
</script>

<style scoped>
  .full-height {
    height: 100vh;
  }

</style>

