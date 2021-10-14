<template>
  <div class="row full-height">

    <el-row class="">
      <el-col :span="24" >
            <el-col v-if="loading" v-loading.fullscreen="loading" element-loading-text="Loading..." element-loading-background="rgba(0, 0, 0, 0.3)" element-loading-spinner="el-icon-loading" :span="24" style="height: 100vh; width: 100%; display: inline-block; background-color: rgba(0, 0, 0, .50); position: absolute; z-index: 99;">
            

            </el-col>
        <el-col :xs="12" :sm="7" :md="8" :lg="6" :xl="6" class="full-height index-col-left">
          <el-col :span="24" style="padding: 10px;" class="coaches-search-desktop">
            <div style="width: 88%; display: inline-block;">
              <el-input
                id="searchBar"
                size="small"
                placeholder="Search for Mentor"
                clearable
                v-model="search">
                <i slot="prefix" class="el-input__icon el-icon-search"></i>
              </el-input>
            </div>
            <div style="width: 10%; display: inline-block;">
              <el-button type="primary" class="plain" plain size="small" @click="callFilter()"><i class="fas fa-sliders-h" aria-hidden="true" style="color: rgba(255, 255, 255, 0.68);"></i></el-button>
            </div>
          </el-col>
          <el-col :span="24" class="coaches-search-mobile" style="padding: 10px;">
            <el-button type="primary" class="plain" plain size="small" @click="callsearchmodal()"><i class="fa fa-search" aria-hidden="true" style="color: rgba(255, 255, 255, 0.68);"></i></el-button>
            <el-button type="primary" class="plain" plain size="small" @click="callFilter()"><i class="fas fa-sliders-h" aria-hidden="true" style="color: rgba(255, 255, 255, 0.68);"></i></el-button>
          </el-col>
          <div>
            <div class="grid-content bg-purple-dark">
              <el-table
                :data="activeCards.filter(data => !search || data.last_name.toLowerCase().includes(search.toLowerCase()) || data.first_name.toLowerCase().includes(search.toLowerCase()))"
                ref="singleTable"
                id="tablecoaches"
                :row-class-name="coachesRowClassName"
                element-loading-text="Loading..."
                element-loading-spinner="el-icon-loading"
                element-loading-background="rgba(0, 0, 0, 0.21)"
                highlight-current-row
                @cell-click="getSummary"
                style="width: 100%; cursor: pointer">
                <el-table-column>
                  <template slot-scope="scope">
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
                           <span style="color: rgb(169, 169, 169);">Experience - </span>
                            <span v-if="scope.row.experience > 0">{{ scope.row.experience }} years</span>
                            <span v-else>0</span>
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
                        <div class="left-list-sub">Experience - {{ scope.row.experience }} years</div>
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
                  </template>
                </el-table-column>

              </el-table>
            </div>
          </div>
        </el-col>
        <el-col :xs="12" :sm="17" :md="16" :lg="18" :xl="18" class="full-height index-col-right" style="background-image: url('../../images/background.jpg'); background-size: cover;">
          <content-component v-if="loading === false" :selected="passData" @showModal="showShareModal" ></content-component>
          <session-component v-if="loading === false" ref="sessionComponent" :selected="for_sessiondata" :user_id="coach_id" :sales="datasales" :ifshare="ifShare" @change="backData($event)" @reload="reloadData(value)" @showModal="showShareModal" @filterData="filterData"></session-component>
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
import SessionComponent from './SessionsComponent.vue'
import ShareModalComponent from './ShareModalComponent.vue'

export default {
  name: 'Index',
  components: {
    ContentComponent,
    SessionComponent,
    ShareModalComponent
  },
  data() {
    return {
      loading: false,
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
      ifShare: false
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

          return coaches.filter(el => (el.has_booked === filter_booked));;
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
                if(card.experience >= start && card.experience <= end && card.has_booked === filter_booked) {
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
          this.currentComponent = null
      }
      
    },
    ex_call_modal() {
      this.$refs.currentComponent.show();
    },
    coachesRowClassName(index) {
      return 'this-is-active'
    },
    reloadData(value) {
      this.schedules = value
      this.getSummary()
    },
    backData(value) {
      var booked = 0
      var attended = 0
      var cancelled = 0
      console.log(value,'value')
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
      this.loading = true
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
      console.log(response,'data')
        // this.schedules = this.dummyschedules // dummy
      
        coachesraw.forEach(function (value, index) {
          data.forEach(function (val, index) {
            if(val.status !== 'Pending' && value.id === val.coach_id) {
              hasbooked = true
            }
          })
          value['has_booked'] = hasbooked
        })
        
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
        setTimeout(() => this.loadDefault(this.datacoach), 1)
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
        await fetch('/api/v1/coaches').then(res => res.ok && res.json() || Promise.reject(res)),
        await fetch(sched_api + '/' + date1 + '/' + date2).then(res => res.ok && res.json() || Promise.reject(res)),
        // await fetch(sched_api).then(res => res.ok && res.json() || Promise.reject(res)),
        await fetch('/api/v1/account/sales').then(res => res.ok && res.json() || Promise.reject(res))
      ]).then(data => {

        this.datacoach = data[0].data
        // console.log(this.datacoach,'datacoach')
        this.datasched = data[1].data // schedules
        this.datasales = data[2].data
        // this.datasched = this.dummyschedules
        this.options = this.datacoach.options
        var coachesraw = this.datacoach.coaches

        var user_id = coachesraw[0].id
        this.user_id = user_id
        // var user_id = this.user_id

        this.schedules = this.datasched.schedules
        // this.schedules = this.dummyschedules // dummy
        var schedraw = this.schedules
        var hasbooked = false
        coachesraw.forEach(function (value, index) {
          schedraw.forEach(function (val, index) {
            if(val.status !== 'Pending' && value.id === val.coach_id) {
              hasbooked = true
            }
          })
          value['has_booked'] = hasbooked
        })

        this.coaches = coachesraw
        this.$refs.singleTable.setCurrentRow(this.coaches[0])
        // this.user_id = this.coaches[0].id
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
        this.new_collections = mergeById(arr1, arr2); // merge scheds to coach
        // console.log('merge', this.new_collections)
        this.reset = this.coaches
        this.languages = this.options.languages
        if(this.ifShare === false) {
            this.showShareModal()
        }
        setTimeout(() => this.loadDefault(this.datacoach), 1)
      })
    },
    loadDefault(data) {
      this.getSummary(data.coaches[0])
      this.loading = false
    },
    getSummary(row, index) {
      this.passData = row
      if(row) {
        this.selected_id = row.id
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
      this.$refs.singleTable.setCurrentRow(this.coaches[0])
      this.preselectedTags = []
      this.preselectedTags = this.reset
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

