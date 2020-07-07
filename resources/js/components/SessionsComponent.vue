<template>
  <el-col :span="24">
    <span style="color: rgba(255, 255, 255, 0.7);">{{ filters.length }} sessions left to book</span>
    <el-button size="small" class="btn-buy-session" type="primary" style="float:right;">BUY SESSIONS</el-button>
    <el-col :span="24">
      <div style="display: block;">
        <div v-for="filter in filters" style="display: inline-block;">
          <el-checkbox :id="'id-' + filter.id" :value="filter.name" v-model="checkedFilters" :class="['obj-' + filter.id]" :label="filter.name">
          </el-checkbox>
        </div>
        <div style="display: inline-block; float: right; margin-top: 15px;">
          <span class="demonstration">Date</span>
          <el-date-picker
            v-model="value1"
            type="daterange"
            size="mini"
            default-value="2020-6-27"
            lang="en"
            range-separator="-"
            :start-placeholder="startdate"
            end-placeholder="">
          </el-date-picker>
          <el-popover
            placement="bottom"
            title="Title"
            width="200"
            trigger="click"
            content="this is content, this is content, this is content">
            <div class="icon-info"><i class="fas fa-info"></i></div>
          </el-popover>
        </div>
      </div>
      <div v-for="position in filteredPositions">
        <div :class="['list-' + position.country_id, 'list-item']">
          <div v-if="position.country_id === 1" class="session-listitem" @click="dialogMentor()">
            <i class="far fa-clock"></i>
            <el-avatar :size="60" src="https://empty" @error="errorHandler" class="session-list-avatar">
              <img src="https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
          </div>
          <div v-if="position.country_id === 2" class="session-listitem">
            <i class="fa fa-calendar-check" aria-hidden="true"></i>
            <el-avatar :size="60" src="https://empty" @error="errorHandler" class="session-list-avatar">
              <img src="https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
            <span><i class="fas fa-headset"></i></span>
            <div style="float:right; display: inline-block;font-size: 14px;"><i class="fas fa-calendar-plus" style="margin-right:10px;"></i>CALENDAR</div>
          </div>
          <div v-if="position.country_id === 3" class="session-listitem">
            <i class="el-icon-circle-check"></i>
            <el-avatar :size="60" src="https://empty" @error="errorHandler" class="session-list-avatar">
              <img src="https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
          </div>
          <div v-if="position.country_id === 4" class="session-listitem">
            <i class="fa fa-ban" aria-hidden="true"></i>
            <el-avatar :size="60" src="https://empty" @error="errorHandler" class="session-list-avatar">
              <img src="https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
          </div>
        </div>
        <div v-if="position.country_id === 1" :class="['list-' + position.country_id, 'list-item-btn']">
          <span>BOOK</span>
        </div>

        <div v-if="position.country_id === 2" :class="['list-' + position.country_id, 'list-item-btn']">
          <span>VIEW</span>
        </div>

        <div v-if="position.country_id === 3" :class="['list-' + position.country_id, 'list-item-btn']">
          <span>VIEW</span>
        </div>

        <div v-if="position.country_id === 4" :class="['list-' + position.country_id, 'list-item-btn']">
          <span>VIEW</span>
        </div>
      </div>
    </el-col>
    <el-dialog
      title="Biography"
      :visible.sync="dialogItem"
      width="40%"
      :before-close="handleClose">
      <!--      <div style="float:left; padding: 8px;">-->
      <!--        <el-avatar :size="60" :src="selected.pic" class="dbl-border">-->
      <!--          <img :src="selected.pic"/>-->
      <!--        </el-avatar>-->
      <!--      </div>-->
      <!--      <div style="display: inline-block; width: 70%; padding-left: 15px;">-->
      <!--        <div class="right-detail-header">{{ selected.name }}</div>-->
      <!--        <div class="right-detail-btnprofile" @click="showInfo()"><i class="fas fa-info"></i></div>-->
      <!--        <div class="right-list-sub">-->
      <!--          <div style="display: inline-block; float: left;">-->
      <!--            <el-image-->
      <!--              style="width: 24px; height: 24px"-->
      <!--              :src="selected.flag"-->
      <!--              :fit="fit">-->
      <!--            </el-image>-->
      <!--          </div>-->
      <!--          <div class="right-detail-sub">{{ selected.address }}</div>-->
      <!--        </div>-->
      <!--      </div>-->
      <!--      <div style="display: block; padding: 20px;">-->
      <!--        <div style="color:#fff; font-weight: bold">Experience</div>-->
      <!--        <span style="color:#fff">{{ selected.profile }}</span>-->
      <!--      </div>-->
      <!--      <div style="display: block; padding: 20px;">-->
      <!--        <div style="color:#fff; font-weight: bold">Markets Traded</div>-->
      <!--        <span style="color:#fff">{{ selected.profile }}</span>-->
      <!--      </div>-->
      <!--      <div style="display: block; padding: 20px;">-->
      <!--        <div style="color:#fff; font-weight: bold">Style</div>-->
      <!--        <span style="color:#fff">{{ selected.profile }}</span>-->
      <!--      </div>-->
      <span slot="footer" class="dialog-footer">
    <el-button @click="dialogProfile = false" type="success">Close</el-button>
  </span>
    </el-dialog>
  </el-col>
</template>

<script>
  export default {
    name: 'SessionsComponent',
    data() {
      return {
        startdate: '2020-6-27',
        dialogItem: false,
        positions: [
          {
            name: "MENTOR AVAILABLE",
            country_id: 1,
          },
          {
            name: "BOOKED SESSIONS",
            country_id: 2,
          },
          {
            name: "ATTENDED SESSIONS",
            country_id: 3,
          },
          {
            name: "NO SHOW SESSIONS",
            country_id: 4,
          }
        ],
        filters: [
          {
            name: 'MENTOR AVAILABLE',
            tag: 'mentor',
            id: 1
          },
          {
            name: 'BOOKED SESSIONS',
            tag: 'booked',
            id: 2
          },
          {
            name: 'ATTENDED SESSIONS',
            tag: 'attended',
            id: 3
          },
          {
            name: 'NO SHOW SESSIONS',
            tag: 'noshow',
            id: 4
          }
        ],
        checkedFilters: ['MENTOR AVAILABLE', 'BOOKED SESSIONS','ATTENDED SESSIONS','NO SHOW SESSIONS'],
        value1: ''
      }
    },
    computed: {
      filteredPositions () {
        return this.positions.filter(position => this.checkedFilters.includes(position.name));
      }
    },
    methods: {
      dialogMentor() {
        this.dialogItem = true
      }
    }
  }
</script>

<style scoped>

</style>
