<template>
  <div class="row full-height">
    <el-row class="">
      <el-col :span="24" >
        <transition name="el-fade-in">
          <div v-if="loading" class="loader-container">
            <div class="main-loader">
              <div>
                <svg viewBox='0 0 105 105' xmlns='http://www.w3.org/2000/svg' fill='#fff'><circle cx='12.5' cy='12.5' r='12.5'><animate attributeName='fill-opacity' begin='0s' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='12.5' cy='52.5' r='12.5' fill-opacity='.5'><animate attributeName='fill-opacity' begin='100ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='52.5' cy='12.5' r='12.5'><animate attributeName='fill-opacity' begin='300ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='52.5' cy='52.5' r='12.5'><animate attributeName='fill-opacity' begin='600ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='92.5' cy='12.5' r='12.5'><animate attributeName='fill-opacity' begin='800ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='92.5' cy='52.5' r='12.5'><animate attributeName='fill-opacity' begin='400ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='12.5' cy='92.5' r='12.5'><animate attributeName='fill-opacity' begin='700ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='52.5' cy='92.5' r='12.5'><animate attributeName='fill-opacity' begin='500ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='92.5' cy='92.5' r='12.5'><animate attributeName='fill-opacity' begin='200ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle></svg>
              </div>
            </div>
          </div>
        </transition>
        <el-col :xs="12" :sm="7" :md="8" :lg="6" :xl="6" class="full-height index-col-left">
          <el-col :span="24" style="padding: 10px;" class="coaches-search-desktop">
            <div style="width: 100%; display: inline-block;">
              <el-input
                id="searchBar"
                size="small"
                placeholder="Search for Mentor"
                clearable
                v-model="search">
                <i slot="prefix" class="el-input__icon el-icon-search"></i>
              </el-input>
            </div>

            <el-col v-if="display_message" :span="24" style="padding: 10px;">
              <span class="no-available-coach">
                You don’t currently have a coach allocated you. <br> To request that a coach is allocated to your account, <br>
                please email <a href="mailto:info@smartchartsfx.com" class="text-link">info@smartchartsfx.com.</a>
              </span>
            </el-col>
            <!--            <div style="width: 10%; display: inline-block;">-->
            <!--              <el-button type="primary" class="plain" plain size="small" @click="callFilter()"><i class="fas fa-sliders-h" aria-hidden="true" style="color: rgba(255, 255, 255, 0.68);"></i></el-button>-->
            <!--            </div>-->
          </el-col>
          <el-col :span="24" class="coaches-search-mobile" style="padding: 10px;">
            <el-button type="primary" class="plain" plain size="small" @click="callsearchmodal()"><i class="fa fa-search" aria-hidden="true" style="color: rgba(255, 255, 255, 0.68);"></i></el-button>
            <el-button type="primary" class="plain" plain size="small" @click="callFilter()"><i class="fas fa-sliders-h" aria-hidden="true" style="color: rgba(255, 255, 255, 0.68);"></i></el-button>
          </el-col>
          <div>
            <div class="grid-content bg-purple-dark">
              <el-table
                v-loading="loading_coach"
                :data="activeCards.filter(data => !search || data.last_name.toLowerCase().includes(search.toLowerCase()) || data.first_name.toLowerCase().includes(search.toLowerCase()))"
                :default-sort = "{prop: 'my_mentor', order: 'descending'}"
                ref="singleTable"
                id="tablecoaches"
                :row-class-name="coachesRowClassName"
                element-loading-text="Loading..."
                element-loading-spinner="el-icon-loading"
                element-loading-background="rgba(0, 0, 0, 0.21)"
                highlight-current-row
                @cell-click="getSummary"
                style="width: 100%; cursor: pointer">
                <el-table-column prop="my_mentor" style="padding: 0px;">
                  <template slot-scope="scope">
                    <span v-if="customer_group === 'ltt'" style="display: inline-block; width: 100%;">
                      <span v-if="scope.row.my_mentor === true" style="display: inline-block; width: 100%; padding: 5px;">
                        <el-col :xs="4" :sm="5" :md="5" :lg="5" :xl="5" class="avatar-wrapper">
                          <el-avatar :size="60" :src="scope.row.avatar" class="dbl-border">
                            <img v-if="scope.row.coach_image === null || scope.row.coach_image === 'null'" :src="default_image"/>
                            <img v-else :src="scope.row.coach_image"/>
                          </el-avatar>
                        </el-col>
                        <el-col :xs="20" :sm="19" :md="19" :lg="19" :xl="19" style="display: inline-block; padding-left: 10px;">
                          <div v-if="scope.region !== 'disable'" class="flag-container">
                            <country-flag v-if="scope.row.region === null" country='' size='normal'/>
                            <country-flag v-else :country='scope.row.region' size='normal'/>
                          </div>
                          <div class="left-list-header">{{ scope.row.first_name }} {{ scope.row.last_name }}</div>
                          <span class="coaches-desktop">
                            <div class="left-list-sub">
<!--                                <span style="color: rgb(169, 169, 169);">Experience - </span>-->
                              <!--                                <span v-if="scope.row.experience > 0">{{ scope.row.experience }} years</span>-->
                              <!--                                <span v-else>0</span>-->
                            </div>
                            <div class="left-list-sub">
                              <span style="color: rgb(169, 169, 169);">Markets traded -</span>
                              <span v-for="(mt, index) in scope.row.market_traded" :key="index" style="display: inline-block; word-break: break-word;">
                                <span>{{mt}}</span><span v-if="index+1 < scope.row.market_traded.length">,  </span>
                              </span>
                            </div>
                            <div class="left-list-sub">
                              <span style="color: rgb(169, 169, 169);">Style - </span>
                              <span v-for="(st, index) in scope.row.style" :key="index" style="display: inline-block; word-break: break-word;">
                                <span>{{st}}</span><span v-if="index+1 < scope.row.style.length">, </span>
                              </span>
                            </div>
                          </span>
                          <span class="coaches-mobile">
<!--                            <div class="left-list-sub">Experience - {{ scope.row.experience }} years</div>-->
                          </span>
                          <div class="coaches-list-icons">
                            <el-badge :value="booked" class="item">
                              <i class="fa fa-calendar-check left-list-badge-icon" aria-hidden="true" style="color: #617da5"></i>
                            </el-badge>
                            <el-badge :value="attended" class="item">
                              <i class="el-icon-circle-check left-list-badge-icon" style="color: #92804f"></i>
                            </el-badge>
                            <el-badge :value="cancelled" class="item">
                              <i class="fa fa-ban left-list-badge-icon" aria-hidden="true" style="color: #92505f"></i>
                            </el-badge>
                          </div>
                        </el-col>
                      </span>
                      <span v-else  style="display: inline-block; width: 100%; padding: 5px;">
                        <el-col :xs="4" :sm="5" :md="5" :lg="5" :xl="5" class="avatar-wrapper">
                          <el-avatar :size="60" :src="scope.row.avatar" class="dbl-border-disable">
                            <img v-if="scope.row.coach_image === null || scope.row.coach_image === 'null'" :src="default_image" style="filter: grayscale(100%);"/>
                            <img v-else :src="scope.row.coach_image" style="filter: grayscale(100%);"/>
                          </el-avatar>
                        </el-col>
                        <el-col :xs="20" :sm="19" :md="19" :lg="19" :xl="19" style="display: inline-block; padding-left: 10px;">
                          <div class="flag-container">
                            <country-flag v-if="scope.row.region === null" country='' size='normal'/>
                            <country-flag v-else :country='scope.row.region' size='normal'/>
                          </div>
                          <div class="left-list-header" style="color: #919191 !important;">{{ scope.row.first_name }} {{ scope.row.last_name }}</div>
                          <span class="coaches-desktop">
                            <div class="left-list-sub">
<!--                                <span style="color: rgb(169, 169, 169);">Experience - </span>-->
                              <!--                                <span v-if="scope.row.experience > 0">{{ scope.row.experience }} years</span>-->
                              <!--                                <span v-else>0</span>-->
                            </div>
                            <div class="left-list-sub">
                              <span style="color: rgb(169, 169, 169);">Markets traded -</span>
                              <span v-for="(mt, index) in scope.row.market_traded" :key="index" style="display: inline-block">
                                <span>{{mt}}</span><span v-if="index+1 < scope.row.market_traded.length">,  </span>
                              </span>
                            </div>
                            <div class="left-list-sub">
                              <span style="color: rgb(169, 169, 169);">Style - </span>
                              <span v-for="(st, index) in scope.row.style" :key="index" style="display: inline-block">
                                <span>{{st}}</span><span v-if="index+1 < scope.row.style.length">, </span>
                              </span>
                            </div>
                          </span>
                          <span class="coaches-mobile">

<!--                            <div class="left-list-sub">Experience - {{ scope.row.experience }} years</div>-->
                          </span>
                          <div class="coaches-list-icons">
                            <el-badge :value="booked" class="item">
                              <i class="fa fa-calendar-check left-list-badge-icon" aria-hidden="true" style="color: #617da5"></i>
                            </el-badge>
                            <el-badge :value="attended" class="item">
                              <i class="el-icon-circle-check left-list-badge-icon" style="color: #92804f"></i>
                            </el-badge>
                            <el-badge :value="cancelled" class="item">
                              <i class="fa fa-ban left-list-badge-icon" aria-hidden="true" style="color: #92505f"></i>
                            </el-badge>
                          </div>
                        </el-col>
                      </span>

                    </span>
                    <span v-else style="display: inline-block; width: 100%;">
                      <span style="display: inline-block; width: 100%; padding: 5px;">
                        <el-col :xs="4" :sm="5" :md="5" :lg="5" :xl="5" class="avatar-wrapper">
                          <el-avatar :size="60" :src="scope.row.avatar" class="dbl-border">
                            <img v-if="scope.row.coach_image === null || scope.row.coach_image === 'null'" :src="default_image"/>
                            <img v-else :src="scope.row.coach_image"/>
                          </el-avatar>
                        </el-col>
                        <el-col :xs="20" :sm="19" :md="19" :lg="19" :xl="19" style="display: inline-block; padding-left: 10px;">
                          <div class="flag-container">
                            <country-flag v-if="scope.row.region === null" country='' size='normal'/>
                            <country-flag v-else :country='scope.row.region' size='normal'/>
                          </div>
                          <div class="left-list-header">{{ scope.row.first_name }} {{ scope.row.last_name }}</div>
                          <span class="coaches-desktop">
                            <div class="left-list-sub">
<!--                                <span style="color: rgb(169, 169, 169);">Experience - </span>-->
                              <!--                                <span v-if="scope.row.experience > 0">{{ scope.row.experience }} years</span>-->
                              <!--                                <span v-else>0</span>-->
                            </div>
                            <div class="left-list-sub">
                              <span style="color: rgb(169, 169, 169);">Markets traded -</span>
                              <span v-for="(mt, index) in scope.row.market_traded" :key="index" style="display: inline-block">
                                <span>{{mt}}</span><span v-if="index+1 < scope.row.market_traded.length">,  </span>
                              </span>
                            </div>
                            <div class="left-list-sub">
                              <span style="color: rgb(169, 169, 169);">Style - </span>
                              <span v-for="(st, index) in scope.row.style" :key="index" style="display: inline-block">
                                <span>{{st}}</span><span v-if="index+1 < scope.row.style.length">, </span>
                              </span>
                            </div>
                          </span>
                          <span class="coaches-mobile">
<!--                            <div class="left-list-sub">Experience - {{ scope.row.experience }} years</div>-->
                          </span>
                          <div class="coaches-list-icons">
                            <el-badge :value="booked" class="item">
                              <i class="fa fa-calendar-check left-list-badge-icon" aria-hidden="true" style="color: #617da5"></i>
                            </el-badge>
                            <el-badge :value="attended" class="item">
                              <i class="el-icon-circle-check left-list-badge-icon" style="color: #92804f"></i>
                            </el-badge>
                            <el-badge :value="cancelled" class="item">
                              <i class="fa fa-ban left-list-badge-icon" aria-hidden="true" style="color: #92505f"></i>
                            </el-badge>
                          </div>
                        </el-col>
                      </span>
                    </span>
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </div>
        </el-col>
        <el-col :xs="12" :sm="17" :md="16" :lg="18" :xl="18" class="full-height index-col-right" style="background-image: url('../../images/background.jpg'); background-size: cover;">
          <content-component v-if="loading === false" :selected="passData" :ifshare="ifShare" :canbook="canbook" @showModal="showShareModal" ></content-component>
          <session-component v-if="loading === false" ref="sessionComponent" :coach_token="coach_token" :date_filter="datefilter" :selected="for_sessiondata" :canbook="canbook" :user_id="coach_id" :sales="datasales" :ifshare="ifShare" :can_book="can_book" @reload="reloadData" @showModal="showShareModal" @filterData="filterData"></session-component>
        </el-col>
      </el-col>

      <el-dialog id="dialogFilter" title="Filter Settings" :visible.sync="filterDialog" top="3%">
        <el-row>
          <el-col :span="24">
            <span style="float: right;"><el-link type="primary" style="color:#fff">Select All </el-link> | <el-link type="primary" style="color:#fff" @click="clear()"> Clear</el-link> </span>
          </el-col>
          <el-col :span="24" class="filter-blocks">
            <span class="filter-title">LANGUAGE</span>
            <div class="cata-sub-nav">
              <div class="nav-prev arrow" @click="left()"><i class="el-icon-arrow-left"></i></div>
              <el-checkbox v-model="preselectedTags" v-for="lang in languages" class="filter-checkbox" size="small" :label="lang" :key="lang" border>{{ lang }}</el-checkbox>
              <div class="nav-next arrow" style="" @click="left()"><i class="el-icon-arrow-right"></i></div>
            </div>
          </el-col>
          <el-col :span="24" class="filter-blocks">
            <span class="filter-title">EXPERIENCE</span>
            <div>
              <el-col :span="24">
                <span style="display: inline-block !important;"><span style="color: rgba(255, 255, 255, 0.52);">FROM </span><el-input v-model="value_range[0]" class="input-default" size="mini" clearable style="width: 15%"></el-input>  Years  &nbsp; &nbsp; &nbsp; <span style="color: rgba(255, 255, 255, 0.52);">TO</span>  <el-input v-model="value_range[1]" class="input-default" size="mini" clearable style="width: 15%"></el-input> Years</span>
                <div class="block">
                  <el-slider
                    v-model="value_range"
                    range
                    show-stops
                    :max="100"
                    @change="setrange()">
                  </el-slider>
                </div>
              </el-col>
            </div>
          </el-col>
          <el-col :span="24" class="filter-blocks">
            <span class="filter-title">MARKET</span>
            <div>
              <el-checkbox v-model="preselectedTags" v-for="market in options.market_traded" class="filter-checkbox" size="small" :label="market" :key="market" border>{{ market}}</el-checkbox>
            </div>
          </el-col>
          <el-col :span="24" class="filter-blocks">
            <span class="filter-title">STYLE</span>
            <div>
              <el-checkbox v-model="preselectedTags" v-for="style in options.style" class="filter-checkbox" size="small" :label="style" :key="style" border>{{ style }}</el-checkbox>
            </div>
          </el-col>
          <el-col :span="24" class="filter-blocks">
            <span class="filter-title">BOOKINGS</span>
            <div>
              <el-radio v-model="booked_options" label="Youve booked this mentor before" value="1" border size="medium">Youve booked this mentor before</el-radio>
              <el-radio v-model="booked_options" label="You havent booked this mentor before" value="2" border size="medium">You havent booked this mentor before</el-radio>
              <!--              <el-checkbox v-model="checkboxGroup3" v-for="book in books" class="filter-checkbox" size="small" :label="book" :key="book" border>{{ book }}</el-checkbox>-->
            </div>
          </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
        <el-button @click="handleFilter()" type="success">Update</el-button>
      </span>
      </el-dialog>
      <!--      search modal for mobile-->
      <el-dialog id="dialogSearch" title="Search" :visible.sync="searchModal" top="0%" style="width: 100%; height: 100%;">
        <el-row>
          <el-col :span="24" class="filter-blocks">
            <el-input
              size="small"
              placeholder="Search for Mentor"
              clearable
              v-model="presearch">
              <i slot="prefix" class="el-input__icon el-icon-search"></i>
            </el-input>
          </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
          <el-button @click="searchCoach()" type="success">OK</el-button>
        </span>
      </el-dialog>

      <!-- Instance Modal-->
      <el-dialog id="dialogInstance" title="Unauthorized Access" :visible.sync="instanceModal" :close-on-click-modal="false" top="12%"  width="30%" style="height: 100%;">
        <el-row>
          <el-col :span="24" class="filter-blocks">
            <div style="text-align: center;">
              <i class="el-icon-warning-outline dialog-warning-exclamation">
              </i>
            </div>
            <p style="text-align: center; word-break: break-word">
              Mentoring is one of the most important ways to develop your skillset as a trader. Our mentoring sessions are designed purely to review your live trading.
              to find out more <a href="https://mentors.smartchartsfx.com/" target="_blank" style="color: #9dafff;">Watch this video</a>.
              <br>
              <br>
              <span>To book mentoring sessions, you must be using your live account. Go To Your Account</span>
            </p>
          </el-col>

        </el-row>
        <span slot="footer" class="dialog-footer">
          <div style="text-align: center; width: 100%; display:inline-block;">
            <el-button @click="goToAccount()" type="success">GO TO YOUR ACCOUNT PAGE</el-button>
          </div>
        </span>
      </el-dialog>

      <!-- Credit Modal-->
      <el-dialog id="dialogCredits" title="No Credits" :visible.sync="creditModal" :close-on-click-modal="false" top="13%"  width="30%" style="height: 100%;">
        <el-row>
          <el-col :span="24" class="filter-blocks">
            <div style="text-align: center;">
              <i class="el-icon-warning-outline dialog-warning-exclamation">
              </i>
            </div>
            <!--            <div style="text-align:center;"><i class="fas fa-info" style="padding: 10px 17px;border: 2px solid #67C23A;border-radius: 50%;font-size: 20px;text-align: center;color: #67C23A;"></i></div>-->
            <p style="text-align: center;">
              No Available Credits!</p>
          </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
          <el-button @click="creditModal = false" type="success">CLOSE</el-button>
        </span>
      </el-dialog>

      <!-- dialog share read only live account -->
      <component
        ref="currentComponent"
        :is="currentComponent"
        :ifshare="ifShare"
        :datasales="datasales"
        @setShareValue="setShareValue"
      />

    </el-row>
  </div>

</template>

<script>
import ContentComponent from './ContentComponent.vue'
// import FilterComponent from './FilterComponent.vue'
import Loading from "vue-loading-overlay";
import SessionComponent from './SessionsComponent.vue'
import ShareModalComponent from './ShareModalComponent.vue'
import Region from './region.json'
import {Notification} from "element-ui";
// dummy data
// import json_sales from './dummy_sales.json'
// import json_coaches from './dummy_coaches.json'
// import json_schedules from './dummy_schedules.json'

export default {
  name: 'Index',
  components: {
    Loading,
    ContentComponent,
    SessionComponent,
    ShareModalComponent
  },
  data() {
    return {
      loading: false,
      loading_coach: false,
      // loadingSession: false,
      currentComponent: null,
      importComponent: null,
      dialogFormVisible: false,
      selected: '',
      passData: {},
      fit: 'contain',
      search: '',
      data: [],
      datacoach: [],
      datasched: [],
      datamerge: [],
      schedules: [],
      datasales: [],
      new_collections: [],
      user_id: '',
      options: [],
      coaches: [],
      reset: [],
      filterDialog: false,
      searchModal: false,
      checkboxGroup1: ['Forex'],
      checkboxGroup2: ['End of Day'],
      checkboxGroup3: ['Youve booked this mentor before'],
      checkboxGroup4: ['English'],
      ex_from: '',
      ex_to: '',
      range: '',
      value_range: [0,100],
      final_range: [],
      val: 0,
      selectedTags: [],
      preselectedTags: [],
      languages: [],
      presearch: '',
      default_image: '../../images/default-avatar.jpg',
      for_sessiondata: [],
      coach_id: '',
      ex: {},
      booked: 0,
      attended: 0,
      cancelled: 0,
      filter_booked: true,
      // booked_options: 'You havent booked this mentor before',
      booked_options: 'Youve booked this mentor before',
      datas: {},
      value: [],
      selected_id: '',
      ifShare: false,
      customer_group: '',
      customer_type: '',
      index_load: 0,
      can_book: false,
      isStudent: false,
      portal_user_id: 0,
      fullPage: true,
      bg_color: '#000',
      icon_color: '#fff',
      mentor_id: 0,
      canbook: true,
      instance_message: '',
      m_index: 0,
      instanceModal: false, // default false
      display_message: false, // default false
      creditModal: false, // default false
      selected_row: {},
      region: Region,
      base_url: window.location.origin + '#funds',
      coach_url: '',
      coach_token: APP_TOKEN,
      datefilter: [],
// dummy
//       dummy_sales: json_sales,
//       dummy_schedules: json_schedules,
//       dummy_coaches: json_coaches,
    }
  },
  computed: {
    activeCards: function() {
      var activeCards = [];
      var filters = this.selectedTags;
      var start = this.final_range[0]
      var end = this.final_range[1]
      var filter_booked = this.filter_booked
      // if(this.booked_options === 'You havent booked this mentor before') {
      //   filter_booked = false
      // // } else {
      //   filter_booked = this.filter_booked
      // }
      let coaches = this.coaches
      if(this.selectedTags.length === 0) {
        if(this.final_range[0] === 0 && this.final_range[1] === 100){
          return coaches
          // return coaches.filter(el => (el.has_booked === filter_booked));;
          // return coaches.filter(el => (el.has_booked === filter_booked || el.has_booked !== filter_booked));
        } else {
          this.coaches.forEach(function(card) {
            if(card.experience >= start && card.experience <= end)  {
              activeCards.push(card)
            }
          })
        }
      } else {
        this.coaches
        .forEach(function(card) {
          function cardContainsFilter(filter) {
            let lang = card.languages
            let markets = card.market_traded
            let styles = card.style
            let filt = filters

            let filteredArrLang = lang.filter(el => filt.includes(el));
            let filteredArrMarket = markets.filter(el => filt.includes(el));
            let filteredArrStyle = styles.filter(el => filt.includes(el));
            if((filteredArrLang.length > 0) || (filteredArrMarket.length > 0) || (filteredArrStyle.length > 0)){
              // if(card.experience >= start && card.experience <= end && card.has_booked === filter_booked) {
              if(card.experience >= start && card.experience <= end) {
                activeCards.push(card);
              }
            }
          }
          if(filters.every(cardContainsFilter)) {
            activeCards.push(card);
          }
        });
      }
      return activeCards;
    }
  },
  created: function() {
    this.loading = true
    let str = this.coach_token
    if(this.coach_token === undefined || this.coach_token === null) {
      str = ''
    }
    const slug = str.split('v1?pl=').pop()
    this.coach_token = slug.replaceAll('&title=Widget', '')
    this.read()
    this.setrange()
  },
  methods: {
    goToAccount() {
      window.location.href = this.base_url
    },
    checkUser() {
      let user_id = this.portal_user_id
      let url = '/api/v1/check-student'
      axios.get(url,
        {
          params: {
            'id': user_id
          }
        }
      ).then(response => {
        if(response.data.is_student === true) {
          this.isStudent = true
          this.ifShare = true
        } else {
          this.showShareModal()
          this.isStudent = false
          this.ifShare = false
        }
      }).catch(error => {
        console.log(error)
        this.isStudent = false
        this.showShareModal()
        this.ifShare = false
      })
    },
    setShareValue(value) {
      this.ifShare = value.value === true;
    },
    showShareModal() {
      this.currentComponent = null
      if(this.ifShare === false) {
        this.currentComponent = ShareModalComponent
        setTimeout(() => this.ex_call_modal(), 1);
      } else {
        this.currentComponent = ShareModalComponent
        setTimeout(() => this.ex_call_modal(), 1);
      }
    },
    ex_call_modal() {
      this.$refs.currentComponent.show();
    },
    coachesRowClassName({row, rowIndex}) {
      if(this.customer_group.toLowerCase() === 'ltt') {
        if (row.my_mentor === false) {
          return 'disabled-row';
        }
      }
      if(rowIndex === 0) {
        return 'this-is-active';
      }
    },
    reloadData(value, action, session_id) {
      if(action === 'book') {
        this.schedules.forEach((sched, i) => {
          if(sched.id === value.id) {
            sched['status'] = 'Booked'
            // sched.id = value.coaching_session_id
          }
        })
      }
      if(action === 'cancel'){
        this.schedules.forEach((sched, i) => {
          if(sched.id === session_id) {
            sched.id = value.coaching_session_id
            sched['status'] = 'Pending'
          }
        })
      }



      // this.new_collections.forEach((item, index) => {
      //   if(item.id === value.id) {
      //     item['status'] = 'Booked'
      //   }
      // })
    },
    setrange() {
      this.final_range[0] = this.value_range[0]
      this.final_range[1] = this.value_range[1]
    },
    filterData(value) {
      let sched_api = '/api/v1/coaches/schedule'
      let letcurrentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');
      let date1a = value.value[0]
      let date2a = value.value[1]
      let data = []
      let coachesraw = this.datacoach.coaches
      var user_id = coachesraw[0].id
      let hasbooked = false
      // let coachesraw = []
      axios
        .get(sched_api + '/' + date1a + '/' + date2a + '?status=all&pl=' + this.coach_token)
        .then(response => {
          data = response.data.data.schedules
          this.schedules = data
          // this.schedules = this.dummyschedules // dummy

          // coachesraw.forEach(function (value, index) {
          //   data.forEach(function (val, index) {
          //     if(val.status !== 'Pending' && value.id === val.coach_id) {
          //       hasbooked = true
          //     }
          //   })
          //   value['has_booked'] = hasbooked
          // })

          var schedraw = this.schedules
          //** check availability_type if null set default value as Remote Only **//

          const today = new Date()
          // const today_time = today
          schedraw.forEach((value, index) => {
            let sched_date = value.date + ' ' + value.start_time
            let dateTime = new Date(sched_date)
            if(dateTime > today) {
              value['visible'] = true

              if(value.availability_type === null || value.availability_type === undefined || value.availability_type === '') {
                value['availability_type'] = 'Remote only'
              }

            } else {
              value['visible'] = false
              if(value.availability_type === null || value.availability_type === undefined || value.availability_type === '') {
                value['availability_type'] = 'Remote only'
              }
            }
          })
          //
          // schedraw.forEach((value, index) => {
          //   if(value.availability_type === null || value.availability_type === undefined || value.availability_type === '') {
          //     value['availability_type'] = 'Remote only'
          //   }
          // })
          var coach = coachesraw

          let arr1 = schedraw.filter(function (sched) {
            return (sched.status === 'Pending' && sched.coach_id === user_id) || (sched.status !== 'Pending');
          });

          let arr2 = coach
          const mergeById = (a1, a2) =>
            a1.map(itm => ({
              ...a2.find((item) => (item.id === itm.coach_id) && item),
              ...itm
            }));
          this.new_collections = []
          this.new_collections = mergeById(arr1, arr2); // merge scheds to coach
          this.reset = this.coaches
          setTimeout(() => this.loadDefault(this.datacoach, this.index_load, this.m_index), 1)
        })
        .catch(error => console.log(error))
    },
    async read(action) {
      this.loading = true
      let sched_api = '/api/v1/coaches/schedule'
      let currentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');

      //** assigning default schedule filter **//
      const today = new Date()
      const tomorrow = new Date(today)
      tomorrow.setDate(tomorrow.getDate() + 7)


      let date1 = currentDate
      let date2 = tomorrow.toJSON().slice(0,10).replace(/-/g,'-');
      this.datefilter[0] = date1
      this.datefilter[1] = date2

        // fetching data in all promise
      Promise.all([
        await fetch('/api/v1/coaches?pl=' + this.coach_token).then(res => res.ok && res.json() || Promise.reject(res)),
        await fetch(sched_api + '/' + date1 + '/' + date2 + '?status=all&pl=' + this.coach_token).then(res => res.ok && res.json() || Promise.reject(res)),
        await fetch('/api/v1/account/sales?pl=' + this.coach_token).then(res => res.ok && res.json() || Promise.reject(res))
      ]).then(data => {

        //** for test mode using local data, change to true **//
        // let test_mode = true
        // let data = []
        // if(test_mode === true) {
        //   data[0] = this.dummy_coaches
        //   data[1] = this.dummy_schedules
        //   data[2] = this.dummy_sales
        // }
        // console.log(this.dummy_schedules,'sd')

        //** Check Instance **//
        if(data[0].error_code) {
          this.loading = false
          this.instanceModal = true
          this.instance_message = data[0].error_message
          return
        }
        //** Assigning Response **//
        this.datacoach = data[0].data // mentors
        this.datasched = data[1].data // schedules
        this.datasales = data[2].data // sales

        if(this.datacoach.coaches.length === 0) {
          if(this.customer_type === 'back end') {
            this.display_message = false
          } else {
            this.display_message = true
          }
          this.loading = false
          return
        }
        //** Check available credits **//
        if(this.datasales.computed_credits.total_available === 0) {
          this.creditModal = true
        }

        this.options = this.datacoach.options
        let coachesraw = this.datacoach.coaches

        //** filter mentors if user customer_group is learn to trade **//
        this.portal_user_id = this.datasales.portal_user.portal_user_id
        this.customer_region = this.datasales.portal_user.customer_region
        let mentor_id = ''

        if(this.datasales.sales.length > 0) {
          mentor_id = this.datasales.sales[0].coach
          this.mentor_id = this.datasales.sales[0].coach
          // let ret = this.datasales.sales.sort((a, b) => new Date(a.date) - new Date(b.date))
          // mentor_id = ret[0].coach
          // this.mentor_id = ret[0].coach
        }

        let my_mentor = false
        let index_load = 0
        let count = 0

        //** Check if has customer group **//
        if(this.datasales.portal_user.customer_group) {
          this.customer_group = this.datasales.portal_user.customer_group.toLowerCase()
        } else {
          this.customer_group = 'sc2'
        }

        //** Check if has customer type **//
        if(this.datasales.portal_user.customer_type) {
          this.customer_type = this.datasales.portal_user.customer_type.toLowerCase()
        } else {
          this.customer_type = 'front end'
        }

        //** Check and assign access to the user **//
        if(this.mentor_id !== 0 || this.mentor_id !== '' || this.mentor_id !== undefined) {
          if(this.datasales.portal_user.customer_group.toLowerCase() === 'ltt') {
            coachesraw.forEach((value, index) => {
              count = count + 1
              if(this.customer_type.toLowerCase() === 'front end') {
                if(value.id === this.mentor_id) {
                  if(value.front_end === true) {
                    my_mentor = true
                    this.canbook = true
                    if(count === 1) {
                      index_load = index
                      this.index_load = index
                    }
                  }
                } else {
                  my_mentor = false
                }
              }
              if(this.customer_type.toLowerCase() === 'back end') {
                this.canbook = true
                my_mentor = true
                if(count === 1) {
                  index_load = index
                  this.index_load = index
                }
              }
              value['my_mentor'] = my_mentor
            })
          }
        } else {
          if(this.datasales.portal_user.customer_group.toLowerCase() === 'ltt') {
            coachesraw.forEach((value, index) => {
              my_mentor = true
              this.canbook = true
              value['my_mentor'] = my_mentor
            })
          }
        }

        var user_id = coachesraw[0].id
        this.user_id = user_id // assign global user_id

        this.schedules = this.datasched.schedules // assign schedules to global variables
        var schedraw = this.schedules

        //** check availability_type if null set default value as Remote Only **//
        const today = new Date()
        // const today_time = today
        schedraw.forEach((value, index) => {
          // 2021-11-19 20:00
          let sched_date = value.date + ' ' + value.start_time
          let str_date = sched_date.replaceAll('-', '/')
          let dateTime = new Date(str_date)
            if(today < dateTime) {
              value['visible'] = true
              if(value.availability_type === null || value.availability_type === undefined || value.availability_type === '') {
                value['availability_type'] = 'Remote only'
              }
            } else {
              value['visible'] = false
              if(value.availability_type === null || value.availability_type === undefined || value.availability_type === '') {
                value['availability_type'] = 'Remote only'
              }

            }
        })
        var hasbooked = true

        this.coaches = coachesraw  // assign mentors to global variables
        let m_index = 0

        //** get default sales index by id, index use to default selected mentor on page load **//
        let arrs = Region.data.region
        // let has_coach_in_region = true

        //** check if the customer and coach has match region **//
        let fil_res = this.coaches.find(o => o.region.toLowerCase() === this.customer_region.toLowerCase());
        if(!fil_res) {
          if(this.customer_type === 'back end') {
            this.display_message = false
          } else {
            this.display_message = true
          }

          this.loading = false
        }

        this.coaches.forEach((value, index) => {
          let obj = arrs.find(o => o.code.toLowerCase() === value.region.toLowerCase());

          if(obj) {
            value.region = obj.region
          } else {
            value.region = 'disable'
          }

          if(value.id === mentor_id) {
            m_index = index
            this.m_index = index
          }
        })

        var scheds = schedraw
        var coach = coachesraw

        let arr1 = scheds.filter(function (sched) {
          return (sched.status === 'Pending' && sched.coach_id === user_id) || (sched.status !== 'Pending');
        });
        let arr2 = coach
        const mergeById = (a1, a2) =>
          a1.map(itm => ({
            ...a2.find((item) => (item.id === itm.coach_id) && item),
            ...itm
          }));
        this.new_collections = mergeById(arr1, arr2); // merge arr1 (SCHEDULES) to arr2 (Coaches)

        //** Check if coaches is 0  **//
        if(this.customer_group.toLowerCase() === 'ltt') {
          if(this.coaches.length === 0) {
            if(this.customer_type === 'back end') {
              this.display_message = false
            } else {
              this.display_message = true
            }
          }
          if(this.mentor_id === null || this.mentor_id === '' || this.mentor_id === undefined) {

            if(this.customer_type === 'back end') {
              this.display_message = false
            } else {
              this.display_message = true
            }
          }
        }

        this.reset = this.coaches
        this.languages = this.options.languages // get all languages
        this.checkUser()
        setTimeout(() => this.loadDefault(this.datacoach, index_load, m_index), 1)
      })
    },
    loadDefault(data, index_load, m_index) {
      this.$refs.singleTable.setCurrentRow(this.coaches[m_index])
      this.getSummary(data.coaches[m_index])
      this.loading = false
    },
    getSummary(row, index) {
      this.selected_row = row
      //** on pageload check if my_mentor exist **//
      if(this.customer_group.toLowerCase() === 'ltt') {
        if(row.my_mentor !== '' || row.my_mentor !== null || true) {
          this.canbook = row.my_mentor
        } else {
          this.canbook = false
        }
      } else {
        if(row.my_mentor !== '' || row.my_mentor !== null || true) {
          this.canbook = row.my_mentor
        } else {
          this.canbook = true
        }
      }

      this.passData = row
      if(row) {
        this.selected_id = row.id
      }

      this.can_book = this.customer_group.toLowerCase() !== 'ltt';
      var scheds = this.schedules

      var coach = this.coaches
      var user_id = this.selected_id
      let arr1 = scheds.filter(function (sched) {
        return (sched.status === 'Pending' && sched.coach_id === user_id) || (sched.status !== 'Pending');
      });
      let arr2 = coach
      const mergeById = (a1, a2) =>
        a1.map(itm => ({
          ...a2.find((item) => (item.id === itm.coach_id) && item),
          ...itm
        }));
      var datares = mergeById(arr1, arr2)

      //** filter schedules by selected coach_id **//
      // let ret_data = datares.filter(function(ele){
      //   return (ele.coach_id = row.id)
      // });

      var countBooked = 0;
      var countAttended = 0;
      var countCancelled = 0;
      datares.forEach(function (value, index) {
        value['disable_schedule'] = false
        if(value.status === 'Booked' && value.coach_id === user_id) {
          value['disable_schedule'] = false
          countBooked++;
        }
        if(value.status === 'Attended' && value.coach_id === user_id) {
          value['disable_schedule'] = false
          countAttended++;
        }
        if(value.status === 'Cancelled' && value.coach_id === user_id) {
          countCancelled++;
          value['disable_schedule'] = false
        }
      })

      this.booked = countBooked
      this.Attended = countAttended
      this.Cancelled = countCancelled
      this.datamerge = datares
      this.for_sessiondata = []
      this.for_sessiondata = this.datamerge

      setTimeout(() => this.ex_call_session(), 1)
    },
    ex_call_session() {
      this.$refs.sessionComponent.assignme()
    },
    callFilter() {
      this.reset = this.selectedTags
      this.filterDialog = true
    },
    left() {
      $(".cata-sub-nav").on("scroll", function () {
        this.val = $(this).scrollLeft();

        if ($(this).scrollLeft() + $(this).innerWidth() >= $(this)[0].scrollWidth) {
          $(".nav-next").hide();
        } else {
          $(".nav-next").show();
        }

        if (this.val == 0) {
          $(".nav-prev").hide();
        } else {
          $(".nav-prev").show();
        }

      });
      $(".nav-next").on("click", function () {
        $(".cata-sub-nav").animate({ scrollLeft: "+=460" }, 200);
      });
      $(".nav-prev").on("click", function () {
        $(".cata-sub-nav").animate({ scrollLeft: "-=460" }, 200);
      });
    },
    clear() {
      this.preselectedTags = []
      this.booked_options = 'You havent booked this mentor before'
      this.value_range = [0,100]
    },
    handleFilter() {
      this.loading_coach = true
      if(this.value_range[0] === '') {
        this.value_range[0] = 0
      }
      if(this.value_range[1] === '') {
        this.value_range[1] = 100
      }
      this.selectedTags = []
      this.reset = this.preselectedTags
      this.selectedTags = this.preselectedTags
      this.filter_booked = this.booked_options !== 'You havent booked this mentor before';
      this.final_range[0] = this.value_range[0]
      this.final_range[1] = this.value_range[1]
      this.filterDialog = false

      this.$refs.singleTable.setCurrentRow(this.coaches[this.index_load])
      this.preselectedTags = []
      this.preselectedTags = this.reset
      this.loading_coach = false
    },
    callsearchmodal() {
      this.searchModal = true
    },
    searchCoach() {
      this.search = this.presearch
      this.searchModal = false
    }
  }
}
</script>

<style scoped>
.full-height {
  height: 100vh;
}

</style>
