<template>
  <div class="row full-height">

    <el-row class="">
      <el-col :span="24" >
<!--        <loading-->
<!--          :active.sync="loading"-->
<!--          :can-cancel="false"-->
<!--          :is-full-page="fullPage"-->
<!--          :background-color="bg_color"-->
<!--          :color="icon_color"-->
<!--        ></loading>-->
        <div v-if="loading" style="position: absolute;
          width: 100%;
          height: 100%;
          z-index: 20000;">
          <div style="position: relative;
            width: 100px;
            height: 100px;
            z-index: 20001;
            box-sizing: border-box;
            top: calc(50% - 50px);
            left: calc(50% - 50px);">
              <div>
                <svg viewBox='0 0 105 105' xmlns='http://www.w3.org/2000/svg' fill='#fff'><circle cx='12.5' cy='12.5' r='12.5'><animate attributeName='fill-opacity' begin='0s' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='12.5' cy='52.5' r='12.5' fill-opacity='.5'><animate attributeName='fill-opacity' begin='100ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='52.5' cy='12.5' r='12.5'><animate attributeName='fill-opacity' begin='300ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='52.5' cy='52.5' r='12.5'><animate attributeName='fill-opacity' begin='600ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='92.5' cy='12.5' r='12.5'><animate attributeName='fill-opacity' begin='800ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='92.5' cy='52.5' r='12.5'><animate attributeName='fill-opacity' begin='400ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='12.5' cy='92.5' r='12.5'><animate attributeName='fill-opacity' begin='700ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='52.5' cy='92.5' r='12.5'><animate attributeName='fill-opacity' begin='500ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='92.5' cy='92.5' r='12.5'><animate attributeName='fill-opacity' begin='200ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle></svg>
              </div>
          </div>
        </div>
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
                          <div class="flag-container">
                            <country-flag v-if="scope.row.country_code === null" country='' size='normal'/>
                            <country-flag v-else :country='scope.row.country_code' size='normal'/>
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
                      <span v-else  style="display: inline-block; width: 100%; padding: 5px;">
                        <el-col :xs="4" :sm="5" :md="5" :lg="5" :xl="5" class="avatar-wrapper">
                          <el-avatar :size="60" :src="scope.row.avatar" class="dbl-border-disable">
                            <img v-if="scope.row.coach_image === null || scope.row.coach_image === 'null'" :src="default_image" style="filter: grayscale(100%);"/>
                            <img v-else :src="scope.row.coach_image" style="filter: grayscale(100%);"/>
                          </el-avatar>
                        </el-col>
                        <el-col :xs="20" :sm="19" :md="19" :lg="19" :xl="19" style="display: inline-block; padding-left: 10px;">
                          <div class="flag-container">
                            <country-flag v-if="scope.row.country_code === null" country='' size='normal'/>
                            <country-flag v-else :country='scope.row.country_code' size='normal'/>
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
                            <country-flag v-if="scope.row.country_code === null" country='' size='normal'/>
                            <country-flag v-else :country='scope.row.country_code' size='normal'/>
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
          <session-component v-if="loading === false" ref="sessionComponent" :selected="for_sessiondata" :canbook="canbook" :user_id="coach_id" :sales="datasales" :ifshare="ifShare" :can_book="can_book" @change="backData($event)" @reload="reloadData(value)" @showModal="showShareModal" @filterData="filterData"></session-component>
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
      <el-dialog id="dialogInstance" title="Unauthorized Access" :visible.sync="instanceModal" :close-on-click-modal="false" top="15%" style="width: 100%; height: 100%;">
        <el-row>
          <el-col :span="24" class="filter-blocks">
            <h1 style="text-align: center;"> {{ instance_message }}</h1>
          </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
<!--        <el-button @click="instanceModal = false" type="success">Close</el-button>-->
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
      instanceModal: false,
      instance_message: '',
      m_index: 0
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
      if(this.selectedTags.length === 0) {

        if(this.final_range[0] === 0 && this.final_range[1] === 100){
          var coaches = this.coaches
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
    this.read()
    this.setrange()
  },
  methods: {
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
        console.log(response, 'response')
        if(response.data.is_student === true) {
          this.isStudent = true
          this.ifShare = true
          console.log('active student')
          // this.$notify.success({
          //   title: 'Success',
          //   message: 'Live Account Shared!',
          //   type: 'success'
          // });
        } else {
          console.log('not-active')
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

      // if(this.ifShare === false) {
      //
      // }
    },
    setShareValue(value) {
      if(value.value === true) {
        this.ifShare = true
      } else {
        this.ifShare = false
      }
    },
    showShareModal() {
      this.currentComponent = null
      if(this.ifShare === false) {
          this.currentComponent = ShareModalComponent
          setTimeout(() => this.ex_call_modal(), 1);
      } else {
          // this.currentComponent = null
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
          return 'warning-row';
        }
      }

      if(rowIndex === 0) {
        return 'this-is-active';
      }


    },
    reloadData(value) {
      this.schedules = value
      this.getSummary()
    },
    backData(value) {
      var booked = 0
      var attended = 0
      var cancelled = 0
      // console.log(value,'value')
      value.forEach(function(value, index) {
        if(value.status === 'Booked') {
          booked = booked + 1
        }
        if(value.status === 'Attended') {
          attended = attended + 1
        }
        if(value.status === 'Cancelled') {
          cancelled = cancelled + 1
        }
      })
      this.booked = booked
      this.attended = attended
      this.cancelled = cancelled
    },
    setrange() {
      this.final_range[0] = this.value_range[0]
      this.final_range[1] = this.value_range[1]
    },
    filterData(value) {
      // this.loading = true
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
      .get(sched_api + '/' + date1a + '/' + date2a)
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
        //  this.loading = true
        this.reset = this.coaches
        // setTimeout(() => this.loadDefault(this.datacoach), 1)
        setTimeout(() => this.loadDefault(this.datacoach, this.index_load, this.m_index), 1)
        // this.loadingSession = false

      })
      .catch(error => console.log(error))
    },
    async read(action) {
      this.loading = true
      let sched_api = '/api/v1/coaches/schedule'
      let letcurrentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');

      const today = new Date()
      const tomorrow = new Date(today)
      tomorrow.setDate(tomorrow.getDate() + 1)

      let date1 = letcurrentDate
      let date2 = tomorrow.toJSON().slice(0,10).replace(/-/g,'-');
      // fetching data in all promise
      Promise.all([
        await fetch('/api/v1/coaches').then(res =>
          res.ok && res.json() || Promise.reject(res)),
        await fetch(sched_api + '/' + date1 + '/' + date2).then(res => res.ok && res.json() || Promise.reject(res)),
        // await fetch(sched_api).then(res => res.ok && res.json() || Promise.reject(res)),
        await fetch('/api/v1/account/sales').then(res => res.ok && res.json() || Promise.reject(res))
      ]).then(data => {
        console.log(data[0])
        if(data[0].error_code) {
          this.loading = false
          this.instanceModal = true
          this.instance_message = data[0].error_message

          return
        }
        this.datacoach = data[0].data // mentors
        this.datasched = data[1].data // schedules
        this.datasales = data[2].data // sales
        // this.datasched = this.dummyschedules

        this.options = this.datacoach.options
        let coachesraw = this.datacoach.coaches

        // filter mentors if user customer_group is learn to trade
        this.portal_user_id = this.datasales.portal_user.portal_user_id
        // let str_llt = 'ltt'
        let mentor_id = ''

        if(this.datasales.sales.length > 0) {
          console.log('datasalse', this.datasales.sales.length)
          mentor_id = this.datasales.sales[0].coach
          this.mentor_id = mentor_id
        }

        let my_mentor = false
        let index_load = 0
        let count = 0
        if(this.datasales.portal_user.customer_group) {
          this.customer_group = this.datasales.portal_user.customer_group.toLowerCase()
        } else {
          this.customer_group = 'sc2'
        }

        if(this.datasales.portal_user.customer_type) {
          this.customer_type = this.datasales.portal_user.customer_type.toLowerCase()
        } else {
          this.customer_type = 'front end'
        }
      console.log(this.customer_type.toLowerCase(),'customer type')
        if(this.mentor_id !== 0 || this.mentor_id !== '' || this.mentor_id !== undefined) {
          console.log(this.mentor_id, 'mentor_id')
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
                  // if(value.back_end === true) {
                    this.canbook = true
                    my_mentor = true
                    if(count === 1) {
                      index_load = index
                      this.index_load = index
                    }
                  // }
                }
              // } else {
              //   my_mentor = false
              // }
              value['my_mentor'] = my_mentor
            })
          }
        } else {
          if(this.datasales.portal_user.customer_group.toLowerCase() === 'ltt') {
            coachesraw.forEach((value, index) => {
              // count = count + 1
              // if(value.id === this.mentor_id) {
              //   if(this.customer_type.toLowerCase() === 'front end') {
              //     if(value.front_end === true) {
              //       my_mentor = true
              //       this.canbook = true
              //       if(count === 1) {
              //         index_load = index
              //         this.index_load = index
              //       }
              //     }
              //   } else {
              //     if(value.back_end === true) {
              //       this.canbook = true
              //       my_mentor = true
              //       if(count === 1) {
              //         index_load = index
              //         this.index_load = index
              //       }
              //     }
              //   }
              // } else {
                my_mentor = true
                this.canbook = true
              // }
              value['my_mentor'] = my_mentor
            })
          }
        }
        console.log(coachesraw,' coachesraw')
        var user_id = coachesraw[0].id
        this.user_id = user_id // assign global user_id

        this.schedules = this.datasched.schedules // assign schedules to global variables
        var schedraw = this.schedules
        var hasbooked = true

        // check and determine if mentors has booked
        // coachesraw.forEach(function (value, index) {
        //   schedraw.forEach(function (val, i) {
        //     if(val.status !== 'Pending' && value.id === val.coach_id) {
        //       hasbooked = false
        //     }
        //   })
        //   value['has_booked'] = hasbooked
        // })

        this.coaches = coachesraw  // assign mentors to global variables
        let m_index = 0
        this.coaches.forEach((value, index) => {
            console.log(value, 'val = ', this.mentor_id)
          if(value.id === mentor_id) {
            m_index = index
            this.m_index = index
          }
        })
        console.log(m_index,'index')

        var scheds = schedraw
        var coach = coachesraw

        let arr1 = scheds.filter(function (sched) {
          return (sched.status === 'Pending' && sched.coach_id === user_id) || (sched.status !== 'Pending');
        });
        // let arr1 = filtered
        let arr2 = coach
        const mergeById = (a1, a2) =>
          a1.map(itm => ({
            ...a2.find((item) => (item.id === itm.coach_id) && item),
            ...itm
          }));
        this.new_collections = mergeById(arr1, arr2); // merge arr1 (SCHEDULES) to arr2 (Coaches)

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
      console.log(row, 'row')
      if(row.my_mentor) {
        this.canbook = row.my_mentor
      } else {
        this.canbook = row.my_mentor
      }
      // if(row.my_mentor) {
      //   if(row.my_mentor === false) {
      //     console.log(row)
      //     return
      //   }
      // }
      this.passData = row
      if(row) {
        this.selected_id = row.id
      }

      if(this.customer_group.toLowerCase() === 'ltt') {
        this.can_book = false
      } else {
        this.can_book = true
      }
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
      var countBooked = 0;
      var countAttended = 0;
      var countCancelled = 0;
      datares.forEach(function (value, index) {
        if(value.status === 'Booked' && value.coach_id === user_id) {
          countBooked++;
        }
        if(value.status === 'Attended' && value.coach_id === user_id) {
          countAttended++;
        }
        if(value.status === 'Cancelled' && value.coach_id === user_id) {
          countCancelled++;
        }
      })
      this.booked = countBooked
      this.Attended = countAttended
      this.Cancelled = countCancelled
      this.datamerge = datares
      this.for_sessiondata = []
      this.for_sessiondata = this.datamerge
      // console.log(this.for_sessiondata,'for_session')
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
      if(this.booked_options === 'You havent booked this mentor before') {
        this.filter_booked = false
      } else {
        this.filter_booked = true
      }
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

