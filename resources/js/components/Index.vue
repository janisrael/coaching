<template>
  <div class="row full-height">
    <el-row class="">
      <el-col :span="24">
        <el-col :span="6" style="background: #15224a" class="full-height test">
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
              <el-button type="primary" class="plain" plain size="small"><i class="fas fa-sliders-h" aria-hidden="true" style="color: rgba(255, 255, 255, 0.68);"></i></el-button>
            </div>
          </el-col>
          <div>
            <div class="grid-content bg-purple-dark">
              <el-table
                :data="tableData"
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
                        <el-image
                          style="width: 20px; height: 20px"
                          :src="scope.row.flag"
                          :fit="fit"></el-image>
                      </div>
                      <div class="left-list-header">{{ scope.row.name }}</div>
                      <div class="left-list-sub">Experience -{{ scope.row.expe }} years</div>
                      <div class="left-list-sub">{{ scope.row.position }}</div>
                      <div class="left-list-sub">Style -{{ scope.row.style }}</div>
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
            </div>
          </div>
        </el-col>
        <el-col :span="18" style="background: #293f5d; padding: 20px;" class="full-height">
          <!--                <div class="grid-content bg-purple-dark">asdasd</div>-->
          <content-component :selected="passData" ></content-component>
          <session-component></session-component>
        </el-col>
      </el-col>
    </el-row>
  </div>

</template>

<script>
  import ContentComponent from './ContentComponent.vue'
  import SessionComponent from './SessionsComponent.vue'
  export default {
    name: 'Index',
    components: {
      ContentComponent,
      SessionComponent
    },
    data() {
      return {
        tableData: [{
          date: '2016-05-03',
          name: 'Tom Morillo',
          pic: 'https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png',
          expe: '12',
          position: 'Markets Trader FX',
          style: 'Intraday',
          address: 'United Kingdom',
          flag: 'https://image.flaticon.com/icons/png/512/555/555417.png',
          profile: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        }, {
          date: '2016-05-02',
          name: 'Jose Rizal',
          pic: 'https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png',
          expe: '3',
          position: 'Markets Trader FX',
          style: 'Intraday',
          address: 'Philippines',
          flag: 'https://image.flaticon.com/icons/png/512/555/555603.png',
          profile: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        }, {
          date: '2016-05-04',
          name: 'Kim Jung On',
          pic: 'https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png',
          expe: '5',
          position: 'Markets Trader FX',
          style: 'Intraday',
          address: 'North Korea',
          flag: 'https://image.flaticon.com/icons/png/512/555/555642.png',
          profile: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        }, {
          date: '2016-05-01',
          name: 'Vlad Putin',
          pic: 'https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png',
          expe: '6',
          position: 'Markets Trader FX',
          style: 'Intraday',
          address: 'Russia',
          flag: 'https://image.flaticon.com/icons/png/512/555/555451.png',
          profile: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        }],
        passData: {},
        fit: 'contain',
        search: ''
      }
    },
    methods: {
      getSummary(row) {

        this.passData = row
      }
    }
  }
</script>

<style scoped>
  .full-height {
    height: 100vh;
  }

</style>

