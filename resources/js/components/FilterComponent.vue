<template>
  <div>
    <el-dialog title="Filter Settings" :visible.sync="filterDialog" top="5%">
      <el-row>
        <el-col :span="24">
          <span style="float: right;"><el-link type="primary" style="color:#fff">Select All </el-link> | <el-link type="primary" style="color:#fff" @click="clear()"> Clear</el-link> </span>
        </el-col>
        <el-col :span="24" class="filter-blocks">
          <span class="filter-title">LANGUAGE</span>

          <div class="cata-sub-nav">
            <div class="nav-prev arrow" @click="left()"><i class="el-icon-arrow-left"></i></div>
            <el-checkbox v-model="checkboxGroup4" v-for="lang in langs" class="filter-checkbox" size="small" :label="lang" :key="lang" border>{{ lang }}</el-checkbox>
            <div class="nav-next arrow" style="" @click="left()"><i class="el-icon-arrow-right"></i></div>
          </div>

          <!--          <div class="scrolling-wrapper" ref="scrolling-wrapper">-->
          <!--            <el-checkbox v-model="checkboxGroup4" v-for="lang in langs" class="filter-checkbox" size="small" :label="lang" :key="lang" border>{{ lang }}</el-checkbox>-->
          <!--          </div>-->
          <!--          <el-button @click="right()" class="nav-next">right</el-button>-->
          <!--          <el-button @click="left()" class="nav-prev">left</el-button>-->
          <!--          <div>-->
          <!--            <el-carousel :interval="4000" height="200px" type="card" :autoplay="false" :item="5">-->
          <!--              <el-carousel-item v-for="lang in langs" :key="lang">-->
          <!--&lt;!&ndash;                <h3 class="medium">{{ item }}</h3>&ndash;&gt;-->
          <!--                <el-checkbox v-model="checkboxGroup4" v-for="lang in langs" class="filter-checkbox" size="small" :label="lang" :key="lang" border>{{ lang }}</el-checkbox>-->
          <!--              </el-carousel-item>-->
          <!--            </el-carousel>-->
          <!--            <el-checkbox v-model="checkboxGroup4" v-for="lang in langs" class="filter-checkbox" size="small" :label="lang" :key="lang" border>{{ lang }}</el-checkbox>-->
          <!--          </div>-->
        </el-col>
        <el-col :span="24" class="filter-blocks">
          <span class="filter-title">EXPERIENCE</span>
          <div>
            <el-col :span="24">
              <span style="display: inline-block !important;"><span style="color: rgba(255, 255, 255, 0.52);">FROM </span><el-input v-model="ex_from" class="input-default" size="mini" clearable style="width: 15%"></el-input>  Years  &nbsp; &nbsp; &nbsp; <span style="color: rgba(255, 255, 255, 0.52);">TO</span>  <el-input v-model="ex_to" class="input-default" size="mini" clearable style="width: 15%"></el-input> Years</span>
              <div class="block">
                <el-slider
                  v-model="value"
                  range
                  show-stops
                  :max="30">
                </el-slider>
              </div>
              <!--              <span style="display: inline-block !important"><el-input></el-input></span>-->
              <!--              <span style="display: inline-block !important">Years</span>-->
            </el-col>
          </div>
        </el-col>
        <el-col :span="24" class="filter-blocks">
          <span class="filter-title">MARKET</span>
          <div>
            <!--            <el-checkbox-group v-model="checkboxGroup1" size="mini">-->
            <!--              <el-checkbox-button v-for="market in markets" class="btn-default" size="small" :label="market" :key="market">{{market}}</el-checkbox-button>-->
            <!--            </el-checkbox-group>-->
            <el-checkbox v-model="checkboxGroup1" v-for="market in markets" class="filter-checkbox" size="small" :label="market" :key="market" border>{{ market}}</el-checkbox>
          </div>
        </el-col>
        <el-col :span="24" class="filter-blocks">
          <span class="filter-title">STYLE</span>
          <div>
            <!--            <el-checkbox-group v-model="checkboxGroup2" size="mini">-->
            <!--              <el-checkbox-button v-for="style in styles" size="small" class="btn-default" :label="style" :key="style">{{ style }}</el-checkbox-button>-->
            <!--            </el-checkbox-group>-->
            <el-checkbox v-model="checkboxGroup2" v-for="style in styles" class="filter-checkbox" size="small" :label="style" :key="style" border>{{ style }}</el-checkbox>
          </div>
        </el-col>
        <el-col :span="24" class="filter-blocks">
          <span class="filter-title">BOOKINGS</span>
          <div>
            <!--            <el-checkbox-group v-model="checkboxGroup3" size="mini">-->
            <!--              <el-checkbox-button v-for="book in books" size="small" class="btn-default" :label="book" :key="book">{{ book }}</el-checkbox-button>-->
            <!--            </el-checkbox-group>-->
            <el-checkbox v-model="checkboxGroup3" v-for="book in books" class="filter-checkbox" size="small" :label="book" :key="book" border>{{ book }}</el-checkbox>
          </div>
        </el-col>
      </el-row>
      <span slot="footer" class="dialog-footer">
        <el-button @click="filterDialog = false" type="success">Update</el-button>
      </span>
    </el-dialog>

  </div>
</template>

<script>

  const langOptions = ['Tagalog', 'English', 'Russian', 'Armenian', 'Chinese', 'Japanese','Korean','Vietnamese','Mandarin'];
  const marketOptions = ['Forex', 'Stock Indices', 'Commodities'];
  const styleOptions = ['End of Day', 'Intra-day', 'Scalper'];
  const bookingOptions = ['Youve booked this mentor before' , 'You havent booked this mentor before'];
  export default {
    name: 'FilterComponent',
    data() {
      return {
        search: '',
        filterDialog: false,
        checkboxGroup1: ['Forex'],
        checkboxGroup2: ['End of Day'],
        checkboxGroup3: ['Youve booked this mentor before'],
        checkboxGroup4: ['English'],
        markets: marketOptions,
        styles: styleOptions,
        books: bookingOptions,
        langs: langOptions,
        ex_from: '',
        ex_to: '',
        range: '',
        value: [4, 8],
        val: 0
      }
    },
    methods: {
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

          //
          // if ($val == 0) {
          //   $(".nav-prev").hide();
          // } else {
          //   $(".nav-prev").show();
          // }
        });
        console.log("init-scroll: " + $(".nav-next").scrollLeft());
        $(".nav-next").on("click", function () {
          $(".cata-sub-nav").animate({ scrollLeft: "+=460" }, 200);
        });
        $(".nav-prev").on("click", function () {
          $(".cata-sub-nav").animate({ scrollLeft: "-=460" }, 200);
        });
      },
      //   right() {
      //     $(".cata-sub-nav").on("scroll", function () {
      //       $val = $(this).scrollLeft();
      //
      //       if ($(this).scrollLeft() + $(this).innerWidth() >= $(this)[0].scrollWidth) {
      //         $val = 0
      //       }
      //       // if ($(this).scrollLeft() + $(this).innerWidth() >= $(this)[0].scrollWidth) {
      //       //   $(".nav-next").hide();
      //       // } else {
      //       //   $(".nav-next").show();
      //       // }
      //
      //       // if ($val == 0) {
      //       //   $(".nav-prev").hide();
      //       // } else {
      //       //   $(".nav-prev").show();
      //       // }
      //     });
      //     console.log("init-scroll: " + $(".nav-next").scrollLeft());
      //     $(".nav-next").on("click", function () {
      //       $(".cata-sub-nav").animate({ scrollLeft: "+=460" }, 200);
      //     });
      //     // $(".nav-prev").on("click", function () {
      //     //   $(".cata-sub-nav").animate({ scrollLeft: "-=460" }, 200);
      //     // });
      //   },
      clear() {
        this.checkboxGroup1 = []
        this.checkboxGroup2 = []
        this.checkboxGroup3 = []
        this.checkboxGroup4 = []

      },
      show() {
        this.filterDialog = true
      },
      close() {
        this.filterDialog = false
      }
    }
  }
</script>

<style scoped>

</style>
