<template>
  <div class="container">
    <el-dialog
      id="dialogShare"
      :visible.sync="dialogShare"
      top="5%"
      width="50%"
      center>
      <div style="display: inline-block; width: 100%; text-align: center;">
        <div style="display: inline-block;">
          <i class="el-icon-warning-outline dialog-warning-exclamation"></i>
          <h3 v-if="ifshare === false" style="font-weight: 300;word-break: break-word; padding: 0 40px;">
            Mentoring is one of the most important ways to develop your skillset as a trader.
            <br>
            Our mentoring sessions are designed to review your live trading.
            <br>
            To find out more, <a href="https://vimeo.com/637480170" target="_blank" style="color: #9ecaff;text-decoration: underline;cursor: pointer;">watch this video.</a></h3>
          <h3 v-else style="font-weight: 300;word-break: break-word; padding: 0 40px;">Your information is being shared</h3>
        </div>
        <div style="display: inline-block;">
          <span v-if="ifshare === false" style="display: inline-block;width: 100%;">
            <p style="color: rgba(255, 255, 255, .51);">N.B. To book metoring sessions, we require you to share your Read-only live account Capital index account with us.</p>
          </span>
          <span v-else style="display: inline-block;width: 100%;">
            <p style="color: rgba(255, 255, 255, .51);">To revoke your permission to share read-only account with Learn to Trade Ltd,</p>
            <p style="color: rgba(255, 255, 255, .51); word-break: break-word;">please email: <a href = "mailto: info@smartchartsfx.com" class="text-link">info@smartchartsfx.com</a>,</p>
            <p style="color: rgba(255, 255, 255, .51); word-break: break-word;">if you revoke your permission to share your journal, you will no longer be able to book mentoring sessions.</p>
          </span>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button v-if="ifshare === false" :loading="loading" type="success" @click="onShare()">SHARE READ ONLY LIVE ACCOUNT</el-button>
        <el-button v-if="ifshare === true" type="success" @click="dialogShare = false">Close</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
  export default {
    name: 'ShareModalComponent',
    props: {
      datasales: {
        require: true,
        type: Object
      },
      ifshare: {
        required: true,
        type: Boolean
      }
    },
    data() {
      return {
        dialogShare: true,
        user_id: '',
        loading: false
      }
    },
    created() {
      this.user_id = this.datasales.portal_user.portal_user_id
    },
    methods: {
      onShare() {
        this.loading = true
        let user_id = this.datasales.portal_user.portal_user_id
        let url = '/api/v1/share'
        axios.get(url,
          {
            params: {
              'id': user_id
            }
          }
         ).then(response => {
            if(response.data.data) {
              console.log(response.data.data.status,'success')
              this.$emit('setShareValue', { value: true })
              console.log('SHARE SUCCESS')
              // this.$notify.success({
              //   title: 'Success',
              //   message: 'Live Account Shared!',
              //   type: 'success'
              //   });
              this.dialogShare = false
            } else {
              console.log('Unable to share!')
              // this.$notify.error({
              //   title: 'Error',
              //   message: 'Unable to share!',
              //   type: 'error'
              // });
              this.loading = false
              this.dialogShare = false
            }
          })
          .catch(error => {
            console.log(error)
            console.log('Unable to share!')
            // this.$notify.error({
            // title: 'Error',
            // message: 'Unable to share!',
            // type: 'error'
            // });
            this.loading = false
            this.dialogShare = false
          })
      },
      show() {
        this.dialogShare = true
      }
    }
  }
</script>
<style scoped>

</style>
