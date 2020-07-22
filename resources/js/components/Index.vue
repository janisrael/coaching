<template>
  <div class="row full-height">
    <el-row class="">
      <el-col :span="24" >
        <el-col :sm="8" :md="6" :lg="6" :xl="6" class="full-height index-col-left">
          <el-col :span="24" style="padding: 10px;" class="coaches-search-desktop">
            <div style="width: 88%; display: inline-block;">
              <el-input
                id="searchBar"
                size="small"
                placeholder="Search for Coach"
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
                v-loading="loading"
                :data="activeCards.filter(data => !search || data.last_name.toLowerCase().includes(search.toLowerCase()) || data.first_name.toLowerCase().includes(search.toLowerCase()))"
                ref="singleTable"
                id="tablecoaches"
                element-loading-text="Loading..."
                element-loading-spinner="el-icon-loading"
                element-loading-background="rgba(0, 0, 0, 0.21)"
                highlight-current-row
                @cell-click="getSummary"
                style="width: 100%; cursor: pointer">
                <el-table-column>
                  <template slot-scope="scope">
                    <div class="avatar-wrapper">
                      <el-avatar :size="60" :src="scope.row.pic" class="dbl-border">
                        <img :src="scope.row.pic"/>
                      </el-avatar>
                    </div>
                    <div style="display: inline-block; width: 79%; padding-left: 10px;">
                      <div class="flag-container">
                        <country-flag :country='scope.row.country_code' size='normal'/>
                      </div>
                      <div class="left-list-header">{{ scope.row.first_name }} {{ scope.row.last_name }}</div>
                      <span class="coaches-desktop">
                        <div class="left-list-sub">Experience - {{ scope.row.experience }} years</div>
                        <div class="left-list-sub">
                          Markets traded -
                          <span v-for="mt in scope.row.market_traded">{{ mt }}, </span>
                        </div>
                        <div class="left-list-sub">
                          Style -
                          <span v-for="st in scope.row.style">{{ st }}, </span>
                        </div>
                        <div class="left-list-sub">
                          <span v-for="lang in scope.row.languages">{{ lang }}, </span>
                        </div>
                      </span>

                      <span class="coaches-mobile">
                        <div class="left-list-sub">Experience - {{ scope.row.experience }} years</div>
                      </span>


                      <div class="coaches-list-icons">
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
        <el-col :sm="16" :md="18" :lg="18" :xl="18" class="full-height index-col-right">
          <content-component :selected="passData" ></content-component>
          <session-component></session-component>
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
                    :max="30"
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
              <el-checkbox v-model="checkboxGroup3" v-for="book in books" class="filter-checkbox" size="small" :label="book" :key="book" border>{{ book }}</el-checkbox>
            </div>
          </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
        <el-button @click="handleFilter()" type="success">Update</el-button>
      </span>
      </el-dialog>

      <!--      search modal for mobile-->

      <el-dialog title="Search" :visible.sync="searchModal" top="0%" style="width: 100%; height: 100%;">
        <el-row>
          <el-col :span="24" class="filter-blocks">
            <el-input
              size="small"
              placeholder="Search for Coach"
              clearable
              v-model="search">
              <i slot="prefix" class="el-input__icon el-icon-search"></i>
            </el-input>
          </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
        <el-button @click="searchModal = false" type="success">OK</el-button>
      </span>
      </el-dialog>

    </el-row>
    <!--    <component ref="modalComponent" :is="importComponent" :selected="selected" @clear="clear()"/>-->
  </div>

</template>

<script>
  import ContentComponent from './ContentComponent.vue'
  // import FilterComponent from './FilterComponent.vue'
  import SessionComponent from './SessionsComponent.vue'

  const bookingOptions = ['Youve booked this mentor before' , 'You havent booked this mentor before'];
  export default {
    name: 'Index',
    components: {
      ContentComponent,
      SessionComponent
    },
    data() {
      return {
        loading: false,
        importComponent: null,
        dialogFormVisible: false,
        selected: '',
        passData: {},
        fit: 'contain',
        search: '',
        data: [],
        options: [],
        coaches: [],
        reset: [],
        filterDialog: false,
        searchModal: false,
        checkboxGroup3: ['Youve booked this mentor before'],
        books: bookingOptions,
        ex_from: '',
        ex_to: '',
        range: '',
        value_range: [0,30],
        final_range: [],
        val: 0,
        selectedTags: [],
        preselectedTags: [],
        languages: []
      }
    },
    computed: {
      activeCards: function() {
        if(this.selectedTags.length == 0) {
          return this.coaches;
        }
        var activeCards = [];
        var filters = this.selectedTags;
        var start = this.final_range[0]

        var end = this.final_range[1]
        console.log(this.final_range[0])
        this.coaches.forEach(function(card) {

          function cardContainsFilter(filter) {
            // if(card.experience >= start && card.experience <= end) {
              card.languages.forEach(function(lang, index) {
                if(lang === filter) {
                  activeCards.push(card);
                }
              })
              card.market_traded.forEach(function(market, index) {
                if(market === filter) {
                  activeCards.push(card);
                }
              })
              card.style.forEach(function(style, index) {
                if(style === filter) {
                  activeCards.push(card);
                }
              })
                // activeCards.push(card);
              // }
            }
          // }
          if(filters.every(cardContainsFilter)) {
            activeCards.push(card);
          }
        });
        return activeCards;
      }
    },
    created: function() {
      this.loading = true
      this.read()
    },
    methods: {
      setrange() {
        this.final_range[0] = this.value_range[0]
        this.final_range[1] = this.value_range[1]
      },
      async read() {
        const res = await fetch('/api/v1/coaches');
        const data = await res.json();
        this.data = data.data;
        this.options = this.data.options
        this.coaches = this.data.coaches
        this.reset = this.coaches
        this.languages = this.options.languages
        setTimeout(() => this.loadDefault(this.data), 1)
      },
      loadDefault(data) {
        this.getSummary(data.coaches[0])
        this.loading = false
      },
      getSummary(row) {
        this.passData = row
      },
      callFilter() {
        this.reset = this.selectedTags
        this.filterDialog = true
        // this.importComponent = FilterComponent
        // this.selected = this.options
        // setTimeout(() => this.ex_callmodal(), 1)
        // this.dialogFormVisible = true
      },
      // ex_callmodal() {
      //   this.$refs.modalComponent.show()
      // },
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
        console.log("init-scroll: " + $(".nav-next").scrollLeft());
        $(".nav-next").on("click", function () {
          $(".cata-sub-nav").animate({ scrollLeft: "+=460" }, 200);
        });
        $(".nav-prev").on("click", function () {
          $(".cata-sub-nav").animate({ scrollLeft: "-=460" }, 200);
        });
      },
      clear() {
        this.preselectedTags = []
        this.value_range[0] = 0
        this.value_range[1] = 30
      },
      handleFilter() {
        this.selectedTags = []
        this.selectedTags = this.preselectedTags
        this.final_range[0] = this.value_range[0]
        this.final_range[1] = this.value_range[1]
        this.filterDialog = false
      },
      callsearchmodal() {
        this.searchModal = true
      }
    }
  }
</script>

<style scoped>
  .full-height {
    height: 100vh;
  }

</style>

