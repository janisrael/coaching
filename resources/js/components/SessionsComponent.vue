<template>
  <el-col :span="24">
    <span style="color: rgba(255, 255, 255, 0.7);">{{ positions.length }} sessions left to book</span>
    <el-button size="small" class="btn-buy-session" type="primary" style="float:right;">BUY SESSIONS</el-button>
    <el-col :span="24">
      <div style="display: block;">
        <div v-for="filter in filters" style="display: inline-block;">
          <div class="desktop-session-filter">
          <el-checkbox :id="'id-' + filter.id" :value="filter.name" v-model="checkedFilters" :class="['obj-' + filter.id]" :label="filter.name">
          </el-checkbox>
          </div>
          <div class="mobile-session-filter">
            <el-checkbox :id="'id-' + filter.id" :value="filter.name" v-model="checkedFilters" :class="['obj-' + filter.id]" :label="filter.name">
              <span v-if="filter.name === 'MENTOR AVAILABLE'"><i class="far fa-clock"></i></span>
              <span v-if="filter.name === 'BOOKED SESSIONS'"><i class="fa fa-calendar-check" aria-hidden="true"></i></span>
              <span v-if="filter.name === 'ATTENDED SESSIONS'"><i class="el-icon-circle-check"></i></span>
              <span v-if="filter.name === 'NO SHOW SESSIONS'"> <i class="fa fa-ban" aria-hidden="true"></i></span>
            </el-checkbox>
          </div>
        </div>
        <div class="session-daterange">
          <span class="demonstration">Date</span>
          <el-date-picker
            v-model="value1"
            type="daterange"
            size="mini"
            default-value="2020-6-27"
            lang="en"
            clearable
            :range-separator="range_sep"
            :start-placeholder="currentDate"
            end-placeholder=""
            @change="handleDatePick()">
          </el-date-picker>
          <el-popover
            placement="bottom"
            title="Title"
            width="200"
            trigger="click"
            content="this is content, this is content, this is content">
            <div class="icon-info"><i class="fas fa-info"></i></div>
          </el-popover>
          <el-popover
            placement="top-start"
            title="Title"
            width="500"
            trigger="click"
            content="this is content, this is content, this is content">
            <div style="color:#fff">this is a test</div>
            <span slot="reference" class="session-info"><i class="fas fa-info"></i></span>
          </el-popover>
        </div>
      </div>
      <el-row class="session-items-container">
        <div v-for="position in filteredPositions">
        <transition name="el-fade-in">
        <div v-if="position.country_id === 1" class="list-item" @click="dialogMentor(position)">
          <div :class="['list-' + position.country_id, 'session-listitem']">
            <i class="far fa-clock"></i>
            <el-avatar :size="60" class="session-list-avatar">
              <img :src="selected.avatar"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
          </div>
          <div v-if="position.country_id === 1" :class="['list-' + position.country_id, 'list-item-btn']">
            <span>BOOK</span>
          </div>
        </div>

        <div v-if="position.country_id === 2" class="list-item" @click="dialogMentor(position)">
          <div :class="['list-' + position.country_id, 'session-listitem']">
            <i class="fa fa-calendar-check" aria-hidden="true"></i>
            <el-avatar :size="60" class="session-list-avatar">
              <img :src="selected.avatar"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
            <span><i class="fas fa-headset"></i></span>
            <div style="float:right; display: inline-block;font-size: 14px;" @click="alert('test')"><i class="fas fa-calendar-plus" style="margin-right:10px;"></i>CALENDAR</div>
          </div>
          <div v-if="position.country_id === 2" :class="['list-' + position.country_id, 'list-item-btn']">
            <span>VIEW</span>
          </div>
        </div>

        <div v-if="position.country_id === 3" class="list-item" @click="dialogMentor(position)">
          <div :class="['list-' + position.country_id, 'session-listitem']">
            <i class="el-icon-circle-check"></i>
            <el-avatar :size="60" class="session-list-avatar">
              <img :src="selected.avatar"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
          </div>
          <div v-if="position.country_id === 3" :class="['list-' + position.country_id, 'list-item-btn']">
            <span>VIEW</span>
          </div>
        </div>

        <div v-if="position.country_id === 4" class="list-item" @click="dialogMentor(position)">
          <div :class="['list-' + position.country_id, 'session-listitem']">
            <i class="fa fa-ban" aria-hidden="true"></i>
            <el-avatar :size="60" src="https://empty" class="session-list-avatar">
              <img :src="selected.avatar"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
          </div>
          <div v-if="position.country_id === 4" :class="['list-' + position.country_id, 'list-item-btn']">
            <span>VIEW</span>
          </div>
        </div>
        </transition>
      </div>
      </el-row>
    </el-col>
    <el-dialog
      id="sessionProfiledialog"
      :title="profileTitle"
      :visible.sync="dialogItem"
      width="40%">
      <el-row>
        <div style="float:left; padding: 8px;">
          <el-avatar :size="60" :src="selected.pic" class="dbl-border">
            <img :src="selected.pic"/>
          </el-avatar>
        </div>
        <div style="display: inline-block; width: 70%; padding-left: 15px;">
          <div class="right-detail-header" style="width: 100%;">{{ selected.first_name }} {{ selected.first_name }}</div>
          <div class="right-list-sub">
            <div style="display: inline-block; float: left; margin-left: -15px;">
              <country-flag  v-if="selected.country_code !== null || selected.country_code !== ''" :country='selected.country_code' size='normal'/>
            </div>
            <div  v-if="selected.country !== null || selected.country !== ''" class="right-detail-sub-session">{{ selected.country }}</div>
          </div>
        </div>
      <el-col :span="24">
        <div style="display: block; padding: 20px;">
          <span><i class="far fa-clock"></i> 09:00 TUESDAY 9/15 <el-button size="small" class="btn-buy-session" type="primary" style="margin-left: 20px;">In English</el-button></span>
        </div>
        <div style="display: block; padding: 20px;">
          <span><i class="fa fa-map-marker" aria-hidden="true"></i> Abbey House, 12-13, Charter , Leicester, LE1 3UD</span>
        </div>
        <div style="display: block; padding: 20px;">
          <span>Attended<el-button size="small" class="btn-buy-session" type="primary" style="margin-left: 20px;"><i class="fa fa-headphones" aria-hidden="true"></i>Remotely</el-button></span>
        </div>
      </el-col>
      </el-row>
        <span slot="footer" class="dialog-footer">
        <el-button @click="dialogItem = false" type="success">Close</el-button>
      </span>
    </el-dialog>
  </el-col>
</template>

<script>
  export default {
    name: 'SessionsComponent',
    props: {
      selected: {
        required: true,
        type: Object
      }
    },
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
            name: "BOOKED SESSIONS",
            country_id: 2,
          },
          {
            name: "BOOKED SESSIONS",
            country_id: 2,
          },
          {
            name: "BOOKED SESSIONS",
            country_id: 2,
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
            name: "ATTENDED SESSIONS",
            country_id: 3,
          },
          {
            name: "ATTENDED SESSIONS",
            country_id: 3,
          },
          {
            name: "ATTENDED SESSIONS",
            country_id: 3,
          },
          {
            name: "ATTENDED SESSIONS",
            country_id: 3,
          },
          {
            name: "NO SHOW SESSIONS",
            country_id: 4,
          },
          {
            name: "NO SHOW SESSIONS",
            country_id: 4,
          },
          {
            name: "NO SHOW SESSIONS",
            country_id: 4,
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
        range_sep: "",
        checkedFilters: ['MENTOR AVAILABLE', 'BOOKED SESSIONS','ATTENDED SESSIONS','NO SHOW SESSIONS'],
        value1: '',
        currentDate: '',
        profileTitle: ''
      }
    },
    computed: {
      filteredPositions () {
        return this.positions.filter(position => this.checkedFilters.includes(position.name));
      }
    },
    created: function() {
      this.getDate()
    },
    methods: {
      getDate() {
        // var currentDate = new Date();
        this.currentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');
      },
      dialogMentor(position) {
        console.log(position)
        if(position.name = 'MENTOR AVAILABLE') {
          this.profileTitle = 'Your Attended Session'
        }
        this.dialogItem = true
      },
      handleDatePick() {
        if(this.value1 === null) {
          this.range_sep = ''
        } else {
         this.range_sep = '-'
        }

      }
    }
  }
</script>

<style scoped>
 .right-list-sub {
   display: inline-block;
 }

 .transition-box {
   margin-bottom: 10px;
   width: 200px;
   height: 100px;
   border-radius: 4px;
   background-color: #409EFF;
   text-align: center;
   color: #fff;
   padding: 40px 20px;
   box-sizing: border-box;
   margin-right: 20px;
 }
</style>
