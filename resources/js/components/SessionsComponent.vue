<template>
  <el-col :span="24">
    <span style="color: rgba(255, 255, 255, 0.7); padding-top: 12px; display: inline-block;">{{ positions.length }} sessions left to book</span>
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
            width="700"
            trigger="click"
            content="this is content, this is content, this is content">
            <div style="color:#fff">
              <div>
                <span class="info-legend"><span style="color: #b8e986; width: 25%; display: inline-block;"><i class="far fa-clock"></i> MENTOR AVAILABLE </span><span>These are available sessions you can book for the mentor you have selected</span></span>
              </div>
              <div>
                <span class="info-legend"><span style="color: #b5e5fe; width: 25%; display: inline-block;"><i class="fa fa-calendar-check" aria-hidden="true"></i> BOOKED SESSIONS </span><span>These are the upcomming sessions you have booked.</span></span>
              </div>
              <div>
                <span class="info-legend"><span style="color: #ffcd50; width: 25%; display: inline-block;"><i class="el-icon-circle-check"></i> ATTENDED SESSIONS </span><span>These are sessions you have attended to date.</span></span>
              </div>
              <div>
                <span class="info-legend"><span style="color: #f96b6c; width: 25%; display: inline-block;"><i class="fa fa-ban" aria-hidden="true"></i> NO SHOW SESSIONS </span><span>These are sessions you forgot to attend or failed to cancel with 24+ hours notice.</span></span>
              </div>
              <span class="info-legend">N.B. to filter the viewed sessions, select/deselect the session type on the key.</span>
            </div>
            <span slot="reference" class="session-info"><i class="fas fa-info"></i></span>
          </el-popover>
        </div>
      </div>
      <el-col :span="24" class="session-items-container">
        <div v-for="position in filteredPositions">
        <transition name="el-fade-in">
        <div v-if="position.schedules.session_type === 1" class="list-item" @click="dialogMentor(position)">
          <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="['list-' + position.schedules.session_type, 'session-listitem']">
            <i class="far fa-clock"></i>
            <el-avatar :size="60" :src="position.coaches.avatar" class="session-list-avatar">
              <img :src="position.coaches.avatar"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
          </el-col>
          <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.schedules.session_type === 1" :class="['list-' + position.schedules.session_type, 'list-item-btn']">
            <span>BOOK</span>
          </el-col>
        </div>

        <div v-if="position.schedules.session_type === 2" class="list-item" @click="dialogMentor(position)">
          <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="['list-' + position.schedules.session_type, 'session-listitem']">
            <i class="fa fa-calendar-check" aria-hidden="true"></i>
            <el-avatar :size="60" :src="position.coaches.avatar" class="session-list-avatar">
              <img :src="selected.coaches.avatar"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
            <span><i class="fas fa-headset"></i></span>
            <div class="session-list-time session-list-time-calendar" @click="alert('test')"><i class="fas fa-calendar-plus" style="margin-right:10px;"></i><span class="session-calendar-caption">CALENDAR</span></div>
          </el-col>
          <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.schedules.session_type === 2" :class="['list-' + position.schedules.session_type, 'list-item-btn']">
            <span>VIEW</span>
          </el-col>
        </div>

        <div v-if="position.schedules.session_type === 3" class="list-item" @click="dialogMentor(position)">
          <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="['list-' + position.schedules.session_type, 'session-listitem']">
            <i class="el-icon-circle-check"></i>
            <el-avatar :size="60" :src="position.coaches.avatar" class="session-list-avatar">
              <img :src="position.coaches.avatar"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
          </el-col>
          <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.schedules.session_type === 3" :class="['list-' + position.schedules.session_type, 'list-item-btn']">
            <span>VIEW</span>
          </el-col>
        </div>

        <div v-if="position.schedules.session_type === 4" class="list-item" @click="dialogMentor(position)">
          <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="['list-' + position.schedules.session_type, 'session-listitem']">
            <i class="fa fa-ban" aria-hidden="true"></i>
            <el-avatar :size="60" :src="position.coaches.avatar" class="session-list-avatar">
              <img :src="position.coaches.avatar"/>
            </el-avatar>
            <span class="session-list-time">09:00 TUESDAY 9/15</span>
          </el-col>
          <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.schedules.session_type === 4" :class="['list-' + position.schedules.session_type, 'list-item-btn']">
            <span>VIEW</span>
          </el-col>
        </div>
        </transition>
      </div>
      </el-col>
    </el-col>
    <el-dialog
      id="sessionProfiledialog"
      :title="profileTitle"
      :visible.sync="dialogItem"
      width="40%">
      <div class="dialog-header">
        <span>
          <span v-if="session_type === 1" style="color: #b8e986;"><i class="far fa-clock"></i></span>
          <span v-if="session_type === 2" style="color: #b5e5fe;"><i class="fa fa-calendar-check" aria-hidden="true"></i></span>
          <span v-if="session_type === 3" style="color: #ffcd50;"><i class="el-icon-circle-check"></i></span>
          <span v-if="session_type === 4" style="color: #f96b6c;"><i class="fa fa-ban" aria-hidden="true"></i></span>
        {{ profileTitle }}
        </span>
      </div>
      <el-row class="dialog-body">
        <div style="float:left; padding: 8px;">
<!--          {{ schedule_profile.avatar }}-->
          <el-avatar :size="60" :src="schedule_profile.avatar" class="dbl-border">
            <img :src="schedule_profile.avatar"/>
          </el-avatar>
        </div>
        <div style="display: inline-block; width: 70%; padding-left: 15px;">
          <div class="right-detail-header" style="width: 100%;">{{ schedule_profile.first_name }} {{ schedule_profile.first_name }}</div>
          <div class="right-list-sub">
            <div style="display: inline-block; float: left; margin-left: -15px;">
              <country-flag  v-if="schedule_profile.country_code !== null || schedule_profile.country_code !== ''" :country='schedule_profile.country_code' size='normal'/>
            </div>
            <div  v-if="schedule_profile.country !== null || schedule_profile.country !== ''" class="right-detail-sub-session">{{ schedule_profile.country }}</div>
          </div>
        </div>
      <el-col :span="24">
        <div style="display: block; padding: 20px;">
          <span><i class="far fa-clock"></i> 09:00 TUESDAY 9/15 <el-button size="small" class="btn-buy-session" type="primary" style="margin-left: 20px;">In English</el-button></span>
        </div>
        <div style="display: block; padding: 20px;">
          <span><i class="fa fa-map-marker" aria-hidden="true"></i> {{ schedule_profile.country }}</span>
        </div>
        <div style="display: block; padding: 20px;">
          <span>Attended<el-button size="small" class="btn-buy-session" type="primary" style="margin-left: 20px;"><i class="fa fa-headphones" aria-hidden="true"></i>Remotely</el-button></span>
        </div>
      </el-col>
      </el-row>
        <span slot="footer" class="dialog-footer">
        <el-button @click="handleClose()" type="success">Close</el-button>
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
        loading: false,
        startdate: '2020-6-27',
        dialogItem: false,
        positions: [
          {
            name: "MENTOR AVAILABLE",
            session_type: 1,
            user_id: '00520000002qtmXAAQ'
          },
          {
            name: "BOOKED SESSIONS",
            session_type: 2,
            user_id: '00520000002qtm3AAA'
          },
          {
            name: "BOOKED SESSIONS",
            session_type: 2,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "BOOKED SESSIONS",
            session_type: 2,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "BOOKED SESSIONS",
            session_type: 2,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "BOOKED SESSIONS",
            session_type: 2,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "ATTENDED SESSIONS",
            session_type: 3,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "ATTENDED SESSIONS",
            session_type: 3,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "ATTENDED SESSIONS",
            session_type: 3,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "ATTENDED SESSIONS",
            session_type: 3,
            user_id: '00520000002qtmUAAQ'
          },
          {
            name: "ATTENDED SESSIONS",
            session_type: 3,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "NO SHOW SESSIONS",
            session_type: 4,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "NO SHOW SESSIONS",
            session_type: 4,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "NO SHOW SESSIONS",
            session_type: 4,
            user_id: '0050Q000004EkGZQA0'
          },
          {
            name: "NO SHOW SESSIONS",
            session_type: 4,
            user_id: '005w00000049dvgAAA'
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
        profileTitle: '',
        session_type: '',
        session_data: [],
        new_collections: [],
        schedule_profile: {}
      }
    },
    computed: {
      filteredPositions () {
        return this.new_collections.filter(position => this.checkedFilters.includes(position.schedules.name));
      }
    },
    created: function() {
      this.session_data = this.selected.coaches
      this.loading = true
      this.getDate()
      this.mapData()
    },
    methods: {
      mapData() {
        var collections = []
        var new_collections = []
        var coach = this.selected.coaches
        var sched = this.positions
        var allcollections = []
        sched.forEach(function(value, index) {
          coach.forEach(function(coachvalue, index) {
            if(value.user_id === coachvalue.id) {
              collections = coachvalue
            }
          })
          allcollections = { coaches: collections, schedules: value}
          new_collections.push(allcollections)
        })
        this.new_collections = new_collections
        console.log(new_collections)
        this.loading = false
      },
      handleClose() {
        this.session_type = ''
        this.profileTitle = ''
        this.dialogItem = false
      },
      getDate() {
        // var currentDate = new Date();
        this.currentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');
      },
      dialogMentor(position) {
        this.session_type = ''
        this.profileTitle = ''
        this.schedule_profile = position.coaches
        if(position.schedules.session_type === 1) {
          this.profileTitle = 'Your Attended Session'
          this.session_type = position.schedules.session_type
        }
        if(position.schedules.session_type === 2) {
          this.profileTitle = 'Your Booked Session'
          this.session_type = position.schedules.session_type
        }
        if(position.schedules.session_type === 3) {
          this.profileTitle = 'Your Attended Session'
          this.session_type = position.schedules.session_type
        }
        if(position.schedules.session_type === 4) {
          this.profileTitle = 'Your no show Session'
          this.session_type = position.schedules.session_type
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
