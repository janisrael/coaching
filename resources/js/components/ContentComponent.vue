<template>
  <div class="right-list-container">
    <div style="float:left; padding: 8px; width: 6%; display: inline-block;">
      <el-avatar :size="60" :src="selected.pic" class="dbl-border">
        <img :src="selected.pic"/>
      </el-avatar>
    </div>
<!--    {{ selected }}-->
    <div style="display: inline-block; width: 90%; padding-left: 15px;">
      <div class="right-detail-header">{{ selected.first_name }} {{ selected.last_name }}</div>
      <div class="right-detail-btnprofile" @click="showInfo()"><i class="fas fa-info"></i></div>
      <div class="right-list-sub">
        <div style="display: inline-block; float: left; margin-left: -15px;">
          <country-flag :country='selected.country_code' size='normal'/>
        </div>
        <div class="right-detail-sub">{{ selected.country }}</div>
      </div>
    </div>
    <el-dialog
      title="Biography"
      :visible.sync="dialogProfile"
      top="3%"
      width="40%">
      <div style="float:left; padding: 8px;">
        <el-avatar :size="60" :src="selected.pic" class="dbl-border">
          <img :src="selected.pic"/>
        </el-avatar>
      </div>
      <div style="display: inline-block; width: 70%; padding-left: 15px;">
        <div class="right-detail-header">{{ selected.first_name }} {{ selected.last_name }}</div>
        <div class="right-list-sub">
          <div style="display: inline-block; float: left; margin-left: -15px;">
            <country-flag :country='selected.country_code' size='normal'/>
          </div>
          <div class="right-detail-sub">{{ selected.country }}</div>
        </div>
      </div>
      <div style="display: block; padding: 20px;">
        <div class="info-head">Experience</div>
        <span class="info-sub">{{ selected.experience_summary }}</span>
      </div>
      <div style="display: block; padding: 20px; padding-bottom: 10px;">
        <div class="info-head">Markets Traded</div>
        <span class="info-sub">{{ selected.market_traded_summary }}</span>
      </div>
      <div style="display: block; padding: 20px; padding-bottom: 10px;">
        <div class="info-head">Style</div>
        <span class="info-sub">{{ selected.style_summary }}</span>
      </div>
      <span slot="footer" class="dialog-footer">
    <el-button @click="dialogProfile = false" type="success">Close</el-button>
  </span>
    </el-dialog>

  </div>
</template>

<script>
  export default {
    name: 'ContentComponent',
    props: {
      selected: {
        required: true,
        type: Object
      }
    },
    data() {
      return {
        handleClose: '',
        fit: 'contain',
        dialogProfile: false
      }
    },
    methods: {
      showInfo() {
        this.dialogProfile = true
        console.log(this.selected.profile)
      }
    }
  }
</script>

<style scoped>

</style>
