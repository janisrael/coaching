<template>
  <div>
    <transition name="el-fade-in-linear" mode="out-in">
      <div v-if="selected" class="right-list-container">
        <el-col :xs="4" :sm="4" :md="4" :lg="2" :xl="2" class="content-avatar-container" @click="showInfo()" style="cursor: pointer;">
          <div @click="showInfo()" style="cursor: pointer; display: block; width: 100%; height: 100%;">
            <el-avatar :size="60" :src="selected.coach_image" class="dbl-border">
              <img v-if="selected.coach_image === null || selected.coach_image === 'null' || selected.coach_image === undefined" :src="default_image"/>
              <img v-else :src="selected.coach_image"/>
            </el-avatar>
          </div>
        </el-col>

        <el-col :xs="20" :sm="20" :md="20" :lg="22" :xl="22" class="content-coaches-name">
          <div v-if="selected.last_name" class="right-detail-header" @click="showInfo()" style="cursor: pointer;">{{ selected.first_name }} {{ selected.last_name }}</div>
          <div v-else class="right-detail-header" @click="showInfo()" style="cursor: pointer;">No Selected Mentor</div>
          <div class="right-detail-btnprofile" @click="showInfo()"><i class="fas fa-info"></i></div>
          <div class="right-list-sub">
            <div style="display: inline-block; float: left; margin-left: -15px;">
              <country-flag v-if="selected.region === null || selected.region === undefined" country='' size='normal'/>
              <country-flag v-else :country='selected.region' size='normal'/>
            </div>
            <div  v-if="selected.country === null || selected.country === '' || selected.country === undefined" class="right-detail-sub">No Specified Country</div>
            <div  v-else class="right-detail-sub">{{ selected.country }}</div>
          </div>

        </el-col>
          <el-dialog
            title="Biography"
            id="dialogProfile"
            :visible.sync="dialogProfile"
            top="3%"
            width="60%">
            <div style="float:left; padding: 8px;">
              <el-avatar :size="60" :src="selected.coach_image" class="dbl-border">
                <img v-if="selected.avatcoach_imagear === null || selected.coach_image === 'null' || selected.coach_image === undefined" :src="default_image"/>
                <img v-else :src="selected.coach_image"/>
              </el-avatar>
            </div>
            <div style="display: inline-block; width: 70%; padding-left: 15px;">
              <div class="right-detail-header" style="color: #fff !important;">{{ selected.first_name }} {{ selected.last_name }}</div>
              <div class="right-list-sub">
                <div style="display: inline-block; float: left; margin-left: -15px;">
                  <country-flag v-if="selected.country_code === null" country='' size='normal'/>
                  <country-flag v-else :country='selected.country_code' size='normal'/>
                </div>
                <div v-if="selected.country === null || selected.country === '' || selected.country === undefined" class="right-detail-sub">No Specified Country</div>
                <div v-else class="right-detail-sub">{{ selected.country }}</div>
              </div>
            </div>
            <div style="display: block; padding: 20px;">
              <div class="info-head">Experience</div>
              <span class="info-sub sum_experience">{{ selected.experience_summary }}</span>
            </div>
            <div style="display: block; padding: 20px; padding-bottom: 10px;">
              <div class="info-head">Markets Traded</div>
              <span class="info-sub sum_experience">{{ selected.market_traded_summary }}</span>
            </div>
            <div style="display: block; padding: 20px; padding-bottom: 10px;">
              <div class="info-head">Style</div>
              <span class="info-sub sum_experience">{{ selected.style_summary }}</span>
            </div>
            <span slot="footer" class="dialog-footer">
              <el-button @click="dialogProfile = false" type="success">Close</el-button>
            </span>
          </el-dialog>

      </div>
    </transition>
    <div v-if="ifshare === true" @click="showModal()" style="display: inline-block; float:right; position: absolute; right: 30px; top: 80px; color: #fff; border: 1px solid #fff; border-radius: 50%; padding: 6px 7px; transform: rotate(44deg); cursor: pointer;">
        <i class="fas fa-link"></i>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContentComponent',
  props: {
    selected: {
      required: false,
      type: Object
    },
    ifshare: {
      required: true,
      type: Boolean
    }
  },
  data() {
    return {
      handleClose: '',
      fit: 'contain',
      dialogProfile: false,
      default_image: '../../images/default-avatar.jpg'
    }
  },
  methods: {
    getPostBody (post) {
      let body = this.stripTags(post);

      return body.length > 300 ? body.substring(0, 300) + '... Read more' : body;
    },
    stripTags (text) {
      return text.replace(/(<([^>]+)>)/ig, '');
    },
    showInfo() {
      this.dialogProfile = true
      console.log(this.selected.profile)
    },
    showModal() {
        this.$emit('showModal', { value: 'link' })
    }
  }
}
</script>

<style scoped>

</style>
