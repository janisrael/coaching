<template>
  <div class="right-list-container">
    <div style="float:left; padding: 8px;">
      <el-avatar :size="60" :src="selected.pic" class="dbl-border">
        <img :src="selected.pic"/>
      </el-avatar>
    </div>
    <div style="display: inline-block; width: 90%; padding-left: 15px;">
      <div class="right-detail-header">{{ selected.name }}</div>
      <div class="right-detail-btnprofile" @click="showInfo()"><i class="fas fa-info"></i></div>
      <div class="right-list-sub">
        <div style="display: inline-block; float: left;">
          <el-image
            style="width: 24px; height: 24px"
            :src="selected.flag"
            :fit="fit">
          </el-image>
        </div>
        <div class="right-detail-sub">{{ selected.address }}</div>
      </div>
    </div>
    <el-dialog
      title="Biography"
      :visible.sync="dialogProfile"
      width="40%">

      <div style="float:left; padding: 8px;">
        <el-avatar :size="60" :src="selected.pic" class="dbl-border">
          <img :src="selected.pic"/>
        </el-avatar>
      </div>
      <div style="display: inline-block; width: 70%; padding-left: 15px;">
        <div class="right-detail-header">{{ selected.name }}</div>
        <div class="right-detail-btnprofile" @click="showInfo()"><i class="fas fa-info"></i></div>
        <div class="right-list-sub">
          <div style="display: inline-block; float: left;">
            <el-image
              style="width: 24px; height: 24px"
              :src="selected.flag"
              :fit="fit">
            </el-image>
          </div>
          <div class="right-detail-sub">{{ selected.address }}</div>
        </div>
      </div>
      <div style="display: block; padding: 20px;">
        <div style="color:#fff; font-weight: bold">Experience</div>
        <span style="color:#fff">{{ selected.profile }}</span>
      </div>
      <div style="display: block; padding: 20px;">
        <div style="color:#fff; font-weight: bold">Markets Traded</div>
        <span style="color:#fff">{{ selected.profile }}</span>
      </div>
      <div style="display: block; padding: 20px;">
        <div style="color:#fff; font-weight: bold">Style</div>
        <span style="color:#fff">{{ selected.profile }}</span>
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
