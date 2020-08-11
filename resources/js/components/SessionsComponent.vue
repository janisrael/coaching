<template>
  <el-col :span="24">
    <span style="color: rgba(255, 255, 255, 0.7); padding-top: 12px; display: inline-block;">{{ sales.computed_credits.total_available }} sessions left to book</span>
    <el-button size="small" class="btn-buy-session" type="primary" style="float:right;">BUY SESSIONS</el-button>
    <el-col :span="24">
      <div style="display: block;">
        <div v-for="filter in filters" style="display: inline-block;">
          <div class="desktop-session-filter">
          <el-checkbox :id="'id-' + filter.id" :value="filter.tag" v-model="checkedFilters" :class="['obj-' + filter.id]" :label="filter.tag">
            <span v-if="filter.tag === 'Pending'">MENTOR AVAILABLE</span>
            <span v-if="filter.tag === 'Booked'">BOOKED SESSIONS</span>
            <span v-if="filter.tag === 'Attended'">ATTENDED SESSIONS</span>
            <span v-if="filter.tag === 'Cancelled'">NO SHOW SESSIONS</span>
          </el-checkbox>
          </div>
          <div class="mobile-session-filter">
            <el-checkbox :id="'id-' + filter.id" :value="filter.tag" v-model="checkedFilters" :class="['obj-' + filter.id]" :label="filter.tag">
              <span v-if="filter.tag === 'Pending'"><i class="far fa-clock"></i></span>
              <span v-if="filter.tag === 'Booked'"><i class="fa fa-calendar-check" aria-hidden="true"></i></span>
              <span v-if="filter.tag === 'Attended'"><i class="el-icon-circle-check"></i></span>
              <span v-if="filter.tag === 'Cancelled'"> <i class="fa fa-ban" aria-hidden="true"></i></span>
            </el-checkbox>
          </div>
        </div>
        <div class="session-daterange">
          <span class="demonstration">Date</span>
          <el-date-picker
            v-model="datefilter"
            type="daterange"
            size="mini"
            :default-value="currentDate"
            lang="en"
            clearable
            :range-separator="range_sep"
            :start-placeholder="currentDate"
            end-placeholder=""
            @change="checkDate()">
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
      <el-col v-loading="loading" element-loading-text="Loading Schedules..." element-loading-spinner="el-icon-loading" element-loading-background="rgba(0, 0, 0, 0.21)":span="24" class="session-items-container">
        <div v-for="position in even(filteredPositions)">
        <transition name="el-fade-in">
        <div v-if="position.status === 'Pending'" class="list-item" @click="dialogMentor(position)">
          <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="['list-' + position.status, 'session-listitem']">
            <span style="width: 15px; display: inline-block"><i class="far fa-clock"></i></span>
            <el-avatar :size="60" :src="position.coach_image" class="session-list-avatar">
              <img :src="position.coach_image"/>
            </el-avatar>
            <span class="session-list-time">
              {{ position.start_time }}  {{ $moment(position.date).format('dddd') }}  {{ $moment(position.date).format('MM/DD')}}
            </span>
            <span v-if="position.availability_type.includes('Can do either')">
              <span><i class="fas fa-headset" style="font-size: 14px"></i></span>
              <span><i class="fa fa-user" style="font-size: 14px"></i></span>
              <span><i class="fa fa-users" style="font-size: 14px"></i></span>
            </span>
            <span v-else>
              <span v-if="position.availability_type.includes('Remote only')"><i class="fas fa-headset" style="font-size: 14px"></i></span>
              <span v-if="position.availability_type.includes('In-house only')"><i class="fa fa-user" style="font-size: 14px"></i></span>
              <span v-if="position.availability_type.includes('Group')"><i class="fa fa-users" style="font-size: 14px"></i></span>
            </span>
          </el-col>
          <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.status === 'Pending'" :class="['list-' + position.status, 'list-item-btn']">
            <span>BOOK</span>
          </el-col>
        </div>
        <div v-if="position.status === 'Booked'" class="list-item" @click="dialogMentor(position)">
          <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="['list-' + position.status, 'session-listitem']">
            <span style="width: 15px; display: inline-block"><i class="fa fa-calendar-check" aria-hidden="true"></i></span>
            <el-avatar :size="60" :src="position.coach_image" class="session-list-avatar">
              <img :src="selected.coach_image"/>
            </el-avatar>
            <span class="session-list-time">
               {{ position.start_time }}  {{ $moment(position.date).format('dddd') }}  {{ $moment(position.date).format('MM/DD')}}
            </span>
            <span v-if="position.availability_type.includes('Can do either')">
              <span><i class="fas fa-headset" style="font-size: 14px"></i></span>
              <span><i class="fa fa-user" style="font-size: 14px"></i></span>
              <span><i class="fa fa-users" style="font-size: 14px"></i></span>
            </span>
            <span v-else>
              <span v-if="position.availability_type.includes('Remote only')"><i class="fas fa-headset" style="font-size: 14px"></i></span>
              <span v-if="position.availability_type.includes('In-house only')"><i class="fa fa-user" style="font-size: 14px"></i></span>
              <span v-if="position.availability_type.includes('Group')"><i class="fa fa-users" style="font-size: 14px"></i></span>
            </span>
<!--            <div class="session-list-time session-list-time-calendar" @click="alert('test')"><i class="fas fa-calendar-plus" style="margin-right:10px;"></i><span class="session-calendar-caption">CALENDAR</span></div>-->
          </el-col>
          <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.status === 'Booked'" :class="['list-' + position.status, 'list-item-btn']">
            <span>VIEW</span>
          </el-col>
        </div>

        <div v-if="position.status === 'Attended'" class="list-item" @click="dialogMentor(position)">
          <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="['list-' + position.status, 'session-listitem']">
            <span style="width: 15px; display: inline-block"> <i class="el-icon-circle-check"></i></span>
            <el-avatar :size="60" :src="position.coach_image" class="session-list-avatar">
              <img :src="position.coach_image"/>
            </el-avatar>
            <span class="session-list-time">
             {{ position.start_time }}  {{ $moment(position.date).format('dddd') }}  {{ $moment(position.date).format('MM/DD')}}
            </span>
            <span v-if="position.availability_type.includes('Can do either')">
              <span><i class="fas fa-headset" style="font-size: 14px"></i></span>
              <span><i class="fa fa-user" style="font-size: 14px"></i></span>
              <span><i class="fa fa-users" style="font-size: 14px"></i></span>
            </span>
            <span v-else>
              <span v-if="position.availability_type.includes('Remote only')"><i class="fas fa-headset" style="font-size: 14px"></i></span>
              <span v-if="position.availability_type.includes('In-house only')"><i class="fa fa-user" style="font-size: 14px"></i></span>
              <span v-if="position.availability_type.includes('Group')"><i class="fa fa-users" style="font-size: 14px"></i></span>
            </span>
          </el-col>
          <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.status === 'Attended'" :class="['list-' + position.status, 'list-item-btn']">
            <span>VIEW</span>
          </el-col>
        </div>

        <div v-if="position.status === 'Attended'" class="list-item" @click="dialogMentor(position)">
          <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="['list-' + position.status, 'session-listitem']">
            <span style="width: 15px; display: inline-block"><i class="fa fa-ban" aria-hidden="true"></i></span>
            <el-avatar :size="60" :src="position.coach_image" class="session-list-avatar">
              <img :src="position.coach_image"/>
            </el-avatar>
            <span class="session-list-time">
               {{ position.start_time }}  {{ $moment(position.date).format('dddd') }}  {{ $moment(position.date).format('MM/DD')}}
            </span>
            <span v-if="position.availability_type.includes('Can do either')">
              <span><i class="fas fa-headset" style="font-size: 14px"></i></span>
              <span><i class="fa fa-user" style="font-size: 14px"></i></span>
              <span><i class="fa fa-users" style="font-size: 14px"></i></span>
            </span>
            <span v-else>
              <span v-if="position.availability_type.includes('Remote only')"><i class="fas fa-headset" style="font-size: 14px"></i></span>
              <span v-if="position.availability_type.includes('In-house only')"><i class="fa fa-user" style="font-size: 14px"></i></span>
              <span v-if="position.availability_type.includes('Group')"><i class="fa fa-users" style="font-size: 14px"></i></span>
            </span>
          </el-col>
          <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.status === 'Attended'" :class="['list-' + position.status, 'list-item-btn']">
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
      width="45%">
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
        <el-col :span="4">
          <div style="float:left; padding: 8px;">
            <el-avatar :size="80" :src="schedule_details.coach_image" class="dbl-border">
              <img :src="schedule_details.coach_image"/>
            </el-avatar>
          </div>
        </el-col>
        <el-col :span="20">
          <div style="display: inline-block; width: 80%; padding-left: 15px;">
            <div class="right-detail-header">{{ schedule_details.first_name }} {{ schedule_details.last_name }}</div>
            <div class="right-list-sub">
              <div style="display: inline-block; float: left; margin-left: -15px;">
                <country-flag  v-if="schedule_details.country_code !== null || schedule_details.country_code !== ''" :country='schedule_details.country_code' size='normal'/>
              </div>
              <!--              <div v-if="schedule_details.country !== null || schedule_details.country !== '' || schedule_details.country !== 'null'" class="right-detail-sub-session">{{ schedule_details.country }}</div>-->
              <div class="right-detail-sub-session"> {{ country_to_show }} </div>
            </div>
          </div>
          <div style="display: block; padding: 10px;">
            <span><i class="far fa-clock"></i>
            {{ $moment(schedule_details.date).format('dddd') }}   {{ $moment(schedule_details.date).format('MM/DD')}}  {{ schedule_details.start_time }} - {{ schedule_details.end_time }}
              <el-button size="small" class="btn-buy-session" type="primary" style="margin-left: 20px;">In English</el-button></span>
          </div>
          <div style="display: block; padding: 10px;">
            <!--            <span v-if="schedule_details.country !== null || schedule_details.country !== '' || schedule_details.country !== 'null'"><i class="fa fa-map-marker" aria-hidden="true"></i> {{ schedule_details.country }}</span>-->
            <span><i class="fa fa-map-marker" aria-hidden="true"></i> {{ country_to_show }}</span>
          </div>
          <div style="display: block; padding: 10px;">
            <span>Attend
              <span v-if="avail_data.includes('Can do either')">
                  <el-button size="small" class="btn-buy-session" type="primary"><i class="fas fa-headset"></i> Remote Only</el-button>
                  <el-button size="small" class="btn-buy-session" type="primary"><i class="fa fa-user"></i> In-house Only</el-button>
                  <el-button size="small" class="btn-buy-session" type="primary"><i class="fa fa-users"></i> Group</el-button>
              </span>
              <span v-else>
                <el-button v-if="avail_data.includes('Remote only')" size="small" class="btn-buy-session" type="primary">
                  <span><i class="fas fa-headset"></i> Remote Only</span>
                </el-button>
                <el-button v-if="avail_data.includes('In-house only')" size="small" class="btn-buy-session" type="primary">
                  <span><i class="fa fa-user"></i> In-house Only</span>
                </el-button>
                <el-button v-if="avail_data.includes('Group')" size="small" class="btn-buy-session" type="primary">
                  <span><i class="fa fa-users"></i> Group</span>
                </el-button>
              </span>
            </span>
          </div>
        </el-col>
      </el-row>
      <span slot="footer" class="dialog-footer">
        <span v-if="session_type === 'Pending'">
          <el-link @click="handleClose()" style="color: #fff; margin-right: 20px;">Cancel</el-link>
          <el-button @click="handleClose()" size="small" type="success">Confirm</el-button>
        </span>
        <span v-else-if="session_type === 'Booked'">
          <el-alert
            id="alertBookSession"
            title="To cancel a session you must give at least 24 hours notice."
            type="warning"
            show-icon
            style="width: 70%; float:left;">
          </el-alert>
          <el-link @click="handleClose()" style="color: #fff; margin-right: 20px;">Delete Booking</el-link>
          <el-button @click="handleClose()" size="small" type="success">Update</el-button>
        </span>
        <span v-else>
          <el-button @click="handleClose()" size="small" type="success">Close</el-button>
        </span>
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
        type: Array
      },
      user_id: {
        required: true,
        type: String
      },
      sales: {
        required: true,
        type: Object
      }
    },
    data() {
      return {
        loading: false,
        dialogItem: false,
        filters: [
          {
            name: 'MENTOR AVAILABLE',
            tag: 'Pending',
            id: 1
          },
          {
            name: 'BOOKED SESSIONS',
            tag: 'Booked',
            id: 2
          },
          {
            name: 'ATTENDED SESSIONS',
            tag: 'Attended',
            id: 3
          },
          {
            name: 'NO SHOW SESSIONS',
            tag: 'Cancelled',
            id: 4
          }
        ],
        range_sep: "",
        checkedFilters: ['Pending', 'Booked','Attended','Cancelled'],
        datefilter: [],
        currentDate: '',
        profileTitle: '',
        session_type: '',
        session_data: [],
        new_collections: [],
        schedule_profile: {},
        schedule_details: {},
        date_collections: [],
        now: new Date().toJSON().slice(0,10).replace(/-/g,'-'),
        checkboxAvail: [],
        avail_data: [],
        data:{},
        schedules: [],
        coaches: {},
        country_to_show: ''
      }
    },
    computed: {
      filteredPositions () {
        if(this.datefilter === '' || this.datefilter === null) {
            return this.selected.filter(position => this.checkedFilters.includes(position.status));
        } else {
          if(this.datefilter.length > 1) {
            var data = this.selected.filter(position => this.checkedFilters.includes(position.status));
            return data.filter(position => (this.date_collections[0] <= position.date) && (this.date_collections[1] >= position.date))
          }
          return this.selected.filter(position => this.checkedFilters.includes(position.status));
        }
      }
    },
    created: function() {
      this.loading = true
      this.session_data = this.selected.coaches
      this.getDate()
    },
    methods: {
      even: function(arr) {
        return arr.slice().sort(function(a, b) {
          return a.date - b.date;
        });
      },
      handleClose() {
        this.session_type = ''
        this.profileTitle = ''
        this.dialogItem = false
      },
      getDate() {
        this.currentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');
        this.loading = false
      },
      dialogMentor(position) {
        this.session_type = ''
        this.profileTitle = ''
        // this.schedule_profile = position.coaches
        if ((typeof (position.country) === 'undefined' || (position.country === null))) {
          this.country_to_show = 'No Specified Country'
        } else {
          this.country_to_show = position.country
        }
        this.schedule_details = position
        this.avail_data = position.availability_type
        if(position.status === 'Pending') {
          this.profileTitle = 'Mentor available, book session'
          this.session_type = position.status
        }
        if(position.status === 'Booked') {
          this.profileTitle = 'Your Booked Session'
          this.session_type = position.status
        }
        if(position.status === 'Attended') {
          this.profileTitle = 'Your Attended Session'
          this.session_type = position.status
        }
        if(position.status === 'Cancelled') {
          this.profileTitle = 'Your no show Session'
          this.session_type = position.status
        }
        this.dialogItem = true
      },
      checkDate: function(){
        if(this.datefilter === null) {
          this.range_sep = ''
        } else {
          var data = []
          data.push(this.$moment(this.datefilter[0]).format('YYYY-MM-DD'))
          data.push(this.$moment(this.datefilter[1]).format('YYYY-MM-DD'))
          this.date_collections = data
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
