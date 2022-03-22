<template>
  <el-col :span="24">
    <div v-if="!noMore" class="arrowDown" @click="scrollToElement({behavior: 'smooth'})">
      <i class="fa fa-angle-down" aria-hidden="true"></i>
    </div>
    <div v-if="noMore && this.selected.length > 16" class="arrowDown" @click="scrollToTop({behavior: 'smooth'})">
      <i class="fa fa-angle-up" aria-hidden="true"></i>
    </div>
    <span style="color: rgba(255, 255, 255, 0.7); padding-top: 12px; display: inline-block;padding-left: 10px;">{{ sales.computed_credits.total_available }} sessions left to book</span>
    <br>
    <span v-if="timezone !== null || timezone !== ''" style="color: rgba(255, 255, 255, 0.7); padding-top: 12px; font-size: 14px; display: inline-block;padding-left: 10px;">
      <i class="fas fa-globe-americas" style="color: #fff"></i>
        <el-select id="tzSelect" class="tz-select" v-model="tzone" size="small" filterable placeholder="Select" @change="convertDate(tzone)">
          <el-option
            v-for="(item, i) in timeZonesList"
            :key="i"
            :label="item"
            :value="item">
          </el-option>
        </el-select>
    </span>
    <el-button size="small" class="btn-buy-session" type="primary" style="float:right; display: none;">BUY SESSIONS</el-button>
    <el-col :span="24">
      <div style="display: block;">
        <div v-for="(filter, i) in filters" :key="i" style="display: inline-block;">
          <div class="desktop-session-filter">
            <el-checkbox :id="'id-' + filter.id" :value="filter.tag" v-model="checkedFilters" :class="['obj-' + filter.id]" :label="filter.tag">
              <span v-if="filter.tag === 'Pending'"><i class="far fa-clock"></i>  MENTOR AVAILABLE</span>
              <span v-if="filter.tag === 'Booked'"><i class="fa fa-calendar-check" aria-hidden="true"></i>  BOOKED SESSIONS</span>
              <span v-if="filter.tag === 'Attended'"><i class="el-icon-circle-check"></i>  ATTENDED SESSIONS</span>
              <span v-if="filter.tag === 'No Show'"><i class="fa fa-ban" aria-hidden="true"></i>  NO SHOW SESSIONS</span>
            </el-checkbox>
          </div>
          <div class="mobile-session-filter">
            <el-checkbox :id="'id-' + filter.id" :value="filter.tag" v-model="checkedFilters" :class="['obj-' + filter.id]" :label="filter.tag">
              <span v-if="filter.tag === 'Pending'"><i class="far fa-clock"></i></span>
              <span v-if="filter.tag === 'Booked'"><i class="fa fa-calendar-check" aria-hidden="true"></i></span>
              <span v-if="filter.tag === 'Attended'"><i class="el-icon-circle-check"></i></span>
              <span v-if="filter.tag === 'No Show'"> <i class="fa fa-ban" aria-hidden="true"></i></span>
            </el-checkbox>
          </div>
        </div>
        <div class="session-daterange" style="cursor: pointer;">
          <span class="demonstration">Date</span>
          <el-date-picker
            v-model="datefilter"
            :picker-options="datePickerOptions"
            type="daterange"
            size="mini"
            lang="en"
            clearable
            :range-separator="range_sep"
            :start-placeholder="date_filter[0]"
            :end-placeholder="date_filter[1]"
            @change="checkDate()"
            style="cursor: pointer;">
          </el-date-picker>
        
          <el-popover
            placement="bottom"
            title="Title"
            width="200"
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
      <el-col id="session-main-container" v-loading="loading" element-loading-background="rgba(0, 0, 0, 0.2)" :span="24" class="session-items-wrapper">
        <transition name="el-fade-in">
          <div v-if="loading" class="loader-container">
            <div class="window-loader">
              <sc-loader/>
            </div>
          </div>
        </transition>
      <el-col :span="24" class="session-items-container">
        <div v-for="(position, index) in even(filteredPositions)" :key="index"  :class="['sessions-item-' + index]">
            <div v-if="position.status === 'Pending' && position.visible === true" class="list-item">
              <div @click="dialogMentor(position)" style="display: block !important;">
              <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="[(ifshare === false ? 'class-disable' : 'class-enable' && canbook === false ? 'class-disable' : 'class-enable' && position.disable_schedule === true ? 'class-disable' : 'class-enable'), 'list-' + position.status, 'session-listitem']">
                <span style="width: 15px; display: inline-block"><i class="far fa-clock"></i></span>
                <el-avatar :size="60" :src="position.coach_image" class="session-list-avatar">
                  <img :src="position.coach_image" :alt="position.coach_image"/>
                </el-avatar> 
                 {{ position.start_time }}    {{ position.date }}  --- - -- 
                  {{ calculateByTimezone(position) }}
                   
                   <!-- Australia - london diff offset {{ position.date_converted }} --> -->
                <!-- <span v-if="($moment.tz(new Date(position.date + ' ' + position.start_time), coach_tzone).utcOffset()) === ($moment.tz(new Date(position.date + ' ' + position.start_time), tzone).utcOffset())" class="session-list-time">
                  {{ position.start_time }} 
                  {{ $moment.tz(new Date(position.date), tzone).format('dddd') }}
                  {{ $moment.tz(new Date(position.date), tzone).format('Do') }}
                  {{ $moment.tz(new Date(position.date), tzone).format('MMM') }}
               
                </span>
                <span v-else class="session-list-time">
                  {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).utcOffset(0, true).format('HH:mm') }} 
                  {{ $moment.tz(new Date(position.date), tzone).format('dddd') }}
                  {{ $moment.tz(new Date(position.date), tzone).format('Do') }}
                  {{ $moment.tz(new Date(position.date), tzone).format('MMM') }}
                </span> -->
                <span v-if="position.availability_type !== null || position.availability_type !== '' || position.availability_type !== undefined">
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
                </span>
              </el-col>
              <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.status === 'Pending'" 
                v-bind:class="[(ifshare === false ? 'class-disable' : 'class-enable' && canbook === false ? 'class-disable' : 'class-enable'), 'list-' + position.status, 'list-item-btn']" >
                <span>BOOK</span>
              </el-col>
              </div>
            </div>
            <div v-if="position.status === 'Booked'" class="list-item">
              <div @click="dialogMentor(position)" style="display: block;">
                <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" :class="[(ifshare === false ? 'class-disable' : 'class-enable' && canbook === false ? 'class-disable' : 'class-enable' && position.disable_schedule === true ? 'class-disable' : 'class-enable'), 'list-' + position.status, 'session-listitem']">
                  <span style="width: 15px; display: inline-block"><i class="fa fa-calendar-check" aria-hidden="true"></i></span>
                  <el-avatar :size="60" :src="position.coach_image" class="session-list-avatar">
                    <img :src="selected.coach_image" :alt="selected.coach_image"/>
                  </el-avatar>
                  <span v-if="($moment.tz(new Date(position.date + ' ' + position.start_time), coach_tzone).utcOffset()) === ($moment.tz(new Date(position.date + ' ' + position.start_time), tzone).utcOffset())" class="session-list-time">
                    {{ position.start_time }} 
                    {{ $moment.tz(new Date(position.date), tzone).format('dddd') }}
                    {{ $moment.tz(new Date(position.date), tzone).format('Do') }}
                    {{ $moment.tz(new Date(position.date), tzone).format('MMM') }}
                  </span>
                  <span v-else class="session-list-time">
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('HH:mm') }} 
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('dddd') }}
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('Do') }}
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('MMM') }}
                  </span>
                <span v-if="position.availability_type !== null || position.availability_type !== '' || position.availability_type !== undefined">
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
                </span>
                </el-col>
                <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.status === 'Booked'" 
                  :class="[(ifshare === false ? 'class-disable' : 'class-enable' && canbook === false ? 'class-disable' : 'class-enable'), 'list-' + position.status, 'list-item-btn']" @click="dialogMentor(position)">
                  <span>VIEW</span>
                </el-col>
              </div>
            </div>
            <div v-if="position.status === 'Attended'" class="list-item" @click="dialogMentor(position)">
              <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" 
                :class="[(ifshare === false ? 'class-disable' : 'class-enable' && canbook === false ? 'class-disable' : 'class-enable' && position.disable_schedule === true ? 'class-disable' : 'class-enable'), 'list-' + position.status, 'session-listitem']">
                <span style="width: 15px; display: inline-block"> <i class="el-icon-circle-check"></i></span>
                <el-avatar :size="60" :src="position.coach_image" class="session-list-avatar">
                  <img :src="position.coach_image" :alt="position.coach_image"/>
                </el-avatar>
                  <span v-if="($moment.tz(new Date(position.date + ' ' + position.start_time), coach_tzone).utcOffset()) === ($moment.tz(new Date(position.date + ' ' + position.start_time), tzone).utcOffset())" class="session-list-time">
                    {{ position.start_time }} 
                    {{ $moment.tz(new Date(position.date), tzone).format('dddd') }}
                    {{ $moment.tz(new Date(position.date), tzone).format('Do') }}
                    {{ $moment.tz(new Date(position.date), tzone).format('MMM') }}
                  </span>
                   <span v-else class="session-list-time">
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('HH:mm') }} 
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('dddd') }}
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('Do') }}
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('MMM') }}
                  </span>
                </span>
                <span v-if="position.availability_type !== null || position.availability_type !== '' || position.availability_type !== undefined">
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
                </span>
              </el-col>
              <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.status === 'Attended'" 
                :class="[(ifshare === false ? 'class-disable' : 'class-enable' && canbook === false ? 'class-disable' : 'class-enable'), 'list-' + position.status, 'list-item-btn']">
                <span>VIEW</span>
              </el-col>
            </div>
            <div v-if="position.status === 'No Show'" class="list-item" @click="dialogMentor(position)">
              <el-col :xs="18" :sm="19" :md="20" :lg="22" :xl="22" 
                :class="[(ifshare === false ? 'class-disable' : 'class-enable' && canbook === false ? 'class-disable' : 'class-enable' && position.disable_schedule === true ? 'class-disable' : 'class-enable'), 'list-' + position.status, 'list-no-show session-listitem']">
                <span style="width: 15px; display: inline-block"><i class="fa fa-ban" aria-hidden="true"></i></span>
                <el-avatar :size="60" :src="position.coach_image" class="session-list-avatar">
                  <img :src="position.coach_image" :alt="position.coach_image"/>
                </el-avatar>
                <span v-if="($moment.tz(new Date(position.date + ' ' + position.start_time), coach_tzone).utcOffset()) === ($moment.tz(new Date(position.date + ' ' + position.start_time), tzone).utcOffset())" class="session-list-time">
                    {{ position.start_time }} 
                    {{ $moment.tz(new Date(position.date), tzone).format('dddd') }}
                    {{ $moment.tz(new Date(position.date), tzone).format('Do') }}
                    {{ $moment.tz(new Date(position.date), tzone).format('MMM') }}
                </span> 
                <span v-else class="session-list-time">
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('HH:mm') }} 
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('dddd') }}
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('Do') }}
                    {{ $moment.tz(new Date(position.date + ' ' + position.start_time), tzone).format('MMM') }}
                </span>
                <span v-if="position.availability_type !== null || position.availability_type !== '' || position.availability_type !== undefined">
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
                </span>
              </el-col>
              <el-col :xs="6" :sm="5" :md="4" :lg="2" :xl="2" v-if="position.status === 'No Show'" :class="[(ifshare === false ? 'class-disable' : 'class-enable' && canbook === false ? 'class-disable' : 'class-enable'), 'list-' + position.status, 'list-no-show list-item-btn']">
                <span>VIEW</span>
              </el-col>
            </div>
        </div>
        <p v-if="count_loading">Loading...</p>
        <!--        <p v-if="noMore">No more</p>-->
      </el-col>
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
              <img :src="schedule_details.coach_image" :alt="schedule_details.coach_image"/>
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
                  <el-button size="small" class="btn-buy-session" type="primary"><i class="fas fa-headset"></i> Remote</el-button>
                  <el-button size="small" class="btn-buy-session" type="primary"><i class="fa fa-user"></i> In-house Only</el-button>
                  <el-button size="small" class="btn-buy-session" type="primary"><i class="fa fa-users"></i> Group</el-button>
              </span>
              <span v-else>
                <el-button v-if="avail_data.includes('Remote only')" size="small" class="btn-buy-session" type="primary">
                  <span><i class="fas fa-headset"></i> Remote</span>
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
          <el-button @click="handleBook(schedule_details)" size="small" type="success" style="margin-right: 20px;" class="sc-button" :loading="btn_loading">
            <div v-if="btn_loading === true" class="button-loader">
              <sc-loader/>
            </div>
            Confirm
          </el-button>
          <el-link @click="handleClose()" style="color: #fff;">Cancel</el-link>
        </span>
        <span v-else-if="session_type === 'Booked'">
          <el-alert
            id="alertBookSession"
            title="To cancel a session you must give at least 24 hours notice."
            type="warning"
            :closable="false"
            show-icon
            style="width: 50%; float:left;">
          </el-alert>
          <el-button :loading="btn_loading" @click="handleDeleteBooking(schedule_details)" style="color: #fff; background-color: transparent !important; border: none !important;" size="small" type="primary" class="sc-button">
            <div v-if="btn_loading === true" class="button-loader">
              <sc-loader/>
            </div>
            Cancel Booking
          </el-button>
          <el-button @click="handleClose()" size="small" type="success">Close</el-button>
        </span>
        <span v-else>
          <el-button @click="handleClose()" size="small" type="success">Close</el-button>
        </span>
      </span>
    </el-dialog>
  </el-col>
</template>

<script>
import { Notification } from 'element-ui';
import Loading from "vue-loading-overlay";
import momentTZ from 'moment-timezone';
import ScLoader from "./Loader/ScLoaderComponent";
import "vue-loading-overlay/dist/vue-loading.css";
export default {
  name: 'SessionsComponent',
  components: {
    // ModalReview,
    Loading,
    ScLoader
  },
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
    },
    canbook: {
      required: false,
      type: Boolean
    },
    ifshare: {
      required: true,
      type: Boolean
    },
    can_book: {
      required: true,
      type: Boolean
    },
    date_filter: {
      required: false,
      type: Array
    },
    coach_token: {
      required: true,
      type: String
    },
    timezone: {
      required: false,
      type: String
    }
  },
  data() {
    return {
      loading: false,
      bg_color: '#000',
      icon_color: '#fff',
      fullPage: true,
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
          tag: 'No Show',
          id: 4
        }
      ],
      range_sep: "-",
      checkedFilters: ['Pending', 'Booked'],
      // checkedFilters: ['Pending'],
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
      country_to_show: '',
      max_count: this.selected.length,
      count: 100,
      count_loading: false,
      session_collection: this.selected.slice(0, this.count),
      disableClass: 'class-disable',
      btn_loading: false,
      datePickerOptions: {
        disabledDate (time) {
          return time.getTime() < Date.now() - 8.64e7;
        }
      },
      defaultTimeZone: momentTZ.tz.guess(),
      timeZonesList: momentTZ.tz.names(),
      value: [],
      original_collection: [],
      tzone: this.timezone,
      coach_tzone: this.timezone
    }
  },
  computed: {
    filteredPositions () {
      if(this.datefilter === '' || this.datefilter === null) {
        let ret = this.session_collection.filter(position => this.checkedFilters.includes(position.status));
        ret.forEach((value, index) => {
          value.date = value.date.replaceAll('-', '/')
          value.start_time = value.start_time.replaceAll('-', '/')
    
        })
        return ret.sort((a, b) => (a.status > b.status) ? 1 : (a.status === b.status)) // sort data pending as end
      } else {
        if(this.datefilter.length > 1) {
          let data = this.session_collection.filter(position => this.checkedFilters.includes(position.status));
          // let ret = data.filter(position => (this.date_collections[0] <= position.date) && (this.date_collections[1] >= position.date))
          let ret = data.filter(position => (position.status !== 'xx' && this.date_collections[0] <= position.date && this.date_collections[1] >= position.date) || (position.status !== 'xx'))

          ret.forEach((value, index) => {
            value.date = value.date.replaceAll('-', '/')
            value.start_time = value.start_time.replaceAll('-', '/')
     
          })

          return ret.sort((a, b) => (a.status > b.status) ? 1 : (a.status === b.status) ) // sort data pending as end
        }
        let ret = this.session_collection.filter(position => this.checkedFilters.includes(position.status));
        ret.forEach((value, index) => {
          value.date = value.date.replaceAll('-', '/')
          value.start_time = value.start_time.replaceAll('-', '/')
                // value['date_converted'] = this.calculateByTimezone(value)
          console.log()
        })
        return ret.sort((a, b) => (a.status > b.status) ? 1 : (a.status === b.status)) // sort data pending as end
      }
    },
    noMore () {
      return this.count >= this.selected.length
    },
    disabled () {
      return this.count_loading || this.noMore
    }
  },
  watch: {
    session_collection: function() {
      if(this.session_collection) {
        this.loading = false
      }
    }
  },
  created: function() {
    this.loading = true
    this.session_data = this.selected.coaches
    // this.getDate()
    this.loading = false
    this.value = this.timezone
  },
  methods: {
    timeConvert(n) {
      var num = n;
      var hours = (num / 60);
      var rhours = Math.floor(hours);
      var minutes = (hours - rhours) * 60;
      var rminutes = Math.round(minutes);
      return num + " minutes = " + rhours + " hour(s) and " + rminutes + " minute(s).";
      },
    calculateByTimezone(value) {
      let coach_tzone = this.coach_tzone
      let tzone = this.tzone
      let date = value.date
      let time = value.start_time
      let date_time = date + ' ' + time
      let new_date = new Date(date_time)

      let offset = this.$moment.tz(new Date(date_time), this.tzone).utcOffset()
      let new_off = offset / 60
      // var now = this.$moment.utc();
      // let withouttimezone =  this.$moment.tz(new Date(date_time)).utcOffset(0, true).format()
      // var Australia_tz_offset = this.$moment.tz(this.coach_tzone).offset(now); 
      // var London_tz_offset = this.$moment.tz(this.tzone).offset(now);

      // let diffe = (Australia_tz_offset - London_tz_offset) / 60
      let converted_offset = this.timeConvert(parseInt(new_off))
      // let orig = this.$moment.tz(new Date(date_time), 'Australia/Sydney').format('YYYY/MM/DD h:mm')
      // let res = this.$moment.tz(new Date(orig), 'Europe/London').format('h:mm A ddd Do MMM')

      let result = this.$moment(date_time).subtract(converted_offset, 'minutes').format('h:mm A ddd Do MMM')
      console.log(new_off, 'diff', date_time)
      return result;
    },
    even: function(arr) {
      return arr.slice().sort(function(a, b) {
        return a.date - b.date;
      });
    },
    convertDate(timezone) {
      this.tzone = timezone
      let s_date = ''
      let e_date = ''
      if(this.datefilter.length === 0) {
          s_date = this.date_filter[0].toString()
          e_date = this.date_filter[1].toString()
      } else {
        s_date = this.datefilter[0].toString()
        e_date = this.datefilter[1].toString()
      }
      
      let date1 = s_date.slice(0,10).replace(/-/g,'-');
      let date2 = e_date.slice(0,10).replace(/-/g,'-');
    
      var s = this.$moment.tz(new Date(date1), this.tzone).format('YYYY-MM-DD')
      var e = this.$moment.tz(new Date(date2), this.tzone).format('YYYY-MM-DD')
      
      let data = []
      data.push(s)
      data.push(e)
      
      this.datefilter = data
      this.date_collections = data
      this.range_sep = '-'
    },
    handleDeleteBooking(schedule_details) {
      this.btn_loading = true
      const today = new Date()
      let session_id = schedule_details.id
      today.setDate(today.getDate()) // add 1 day for date now
      const sessionDate = schedule_details.date + ' ' + schedule_details.start_time
      let str_date = sessionDate.replaceAll('-', '/')
      const dateTime = new Date(str_date)

      let diff = (dateTime - today)
      let calculated_time = (diff / (1000 * 60 * 60))
      if (calculated_time < 24) {
        // The yourDate time is less than 1 days from now
        Notification.error({
          title: "You can't cancel this session",
          dangerouslyUseHTMLString: true,
          message: '<p>You can only cancel sessions through SmartCharts more than 24 hours before the start of the booked session.</p>' +
          'If you need help, please email <a href="mailto:info@smartchartsfx.com" class="text-link" style="color: #15274B !important;">info@smartchartsfx.com.</a></p>',
          duration: 4 * 2000
        })
        this.loading = false
        this.btn_loading = false
        return
      }

      let url = "/api/v1/coaching-session/cancel?schedule_id=" + schedule_details.id + '&pl=' + this.coach_token;
      axios.post(url).then(response => {
        if(response.data.data.status === 'success') {
          Notification.success({
            title: 'Success',
            message: 'Booking cancellation request sent!',
            duration: 4 * 1000
          })
          let coaching_session_id = ''
          if(response.data.data.coaching_session_id) {
            coaching_session_id = response.data.data.coaching_session_id
          } else {
            console.log('no coaching session id')
          }

          schedule_details['coaching_session_id'] = coaching_session_id
          schedule_details['status'] = 'Pending'
          this.session_collection.forEach((item) => {
            if(item.id === schedule_details.id) {
              item['status'] = 'Pending'
              item.id = coaching_session_id
            }
          })

          this.loading = false
          this.btn_loading = false
          this.dialogItem = false
          this.$emit('reload', schedule_details, 'cancel', session_id)
        } else {
          Notification.error({
            title: 'Error',
            message: response.data.data.message,
            duration: 4 * 1000
          })
          this.loading = false
          this.btn_loading = false
        }
      })
        .catch(error => {
          console.log(error)
          this.loading = false
          this.btn_loading = false
        })
    },
    handleBook(value) {
      this.btn_loading = true
      // this.loading = true
      let url = "/api/v1/coaching-session/book?schedule_id=" + value.id + '&pl=' + this.coach_token;
      axios.post(url).then(response => {
          if(response.data.data.status === 'success') {
            Notification.success({
              title: 'Success',
              message: 'Schedule successfully booked',
              duration: 4 * 1000
            })

            //** Update schedule by id **//
            this.session_collection.forEach((item) => {
              if(item.id === value.id) {
                item['status'] = 'Booked'
              }
            })

            this.loading = false
            this.btn_loading = false
            this.dialogItem = false
            this.$emit('reload', value, 'book')
          } else {
            Notification.error({
              title: 'Error',
              message: response.data.data.message,
              duration: 4 * 1000
            })
            this.loading = false
            this.btn_loading = false
          }
        })
        .catch(error => {
          console.log(error)
          this.loading = false
          this.btn_loading = false
        })
    },
    scrollToTop(options) {
      const el = this.$el.getElementsByClassName('sessions-item-0')[0];
      if (el) {
        el.scrollIntoView(options);
      }
    },
    assignme() {
      this.session_collection = this.selected.slice(0, this.count)
      // this.original_collection = this.selected.slice(0, this.count)
    },
    scrollToElement(options) {
      this.count_loading = true
      let thisclass = 'sessions-item-' + this.count
      setTimeout(() => {
        this.count += 16
        this.session_collection = this.selected.slice(0, this.count)
        this.count_loading = false
      }, 100)
      setTimeout(() => this.ex_call_movescroll(options, thisclass), 100)
    },
    ex_call_movescroll(options, thisclass) {
      const el = this.$el.getElementsByClassName(thisclass)[0];
      if (el) {
        el.scrollIntoView(options);
      }
    },
    handleClose() {
      this.session_type = ''
      this.profileTitle = ''
      this.dialogItem = false
    },
    dialogMentor(position) {
      this.session_type = ''
      this.profileTitle = ''
      if(position.status === 'Pending') {
        if(this.ifshare === false) {
          this.$emit('showModal', { value: false })
          return
        } else {
          if(this.canbook === false) {
            console.log('unable to book')
            return
          }
        }
      }

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
      if(position.status === 'No Show') {
        this.profileTitle = 'Your no show Session'
        this.session_type = position.status
      }
      if(this.ifshare === true) {
        this.dialogItem = true
      } else {
        console.log('unable to book')
      }

    },
    checkDate: function(){
      this.loading = true
      let data = []
      if(this.datefilter === null) {
        const today = new Date()
        const endDate = new Date(today)
        endDate.setDate(endDate.getDate() + 30)
        let end_date = endDate.toJSON().slice(0,10).replace(/-/g,'-');
        data.push(this.$moment(today).format('YYYY-MM-DD'))
        data.push(this.$moment(end_date).format('YYYY-MM-DD'))
        this.date_collections = data
        this.$emit('filterData', { value: data })
        this.range_sep = '-'

      } else {
        data.push(this.$moment(this.datefilter[0]).format('YYYY-MM-DD'))
        data.push(this.$moment(this.datefilter[1]).format('YYYY-MM-DD'))
        this.$emit('filterData', { value: data })
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

.custom-date-picker{
  width: 500px !important;
  /*.el-date-picker__header{*/
  /*// custom header style here*/
  /*}*/
}

</style>
