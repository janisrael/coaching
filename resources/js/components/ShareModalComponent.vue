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

                    <i class="el-icon-warning-outline" style="color: #6e8e6a;font-size: 60px;transform: rotate(180deg);"></i>

                    <h2 v-if="ifshare === false" style="font-weight: 300;word-break: break-word; padding: 0 40px;">Mentoring is one of the most important ways to develop your skillset as a trader. Our mentoring sessions are designed to review your live trading. To find out more, <span style="display: inline-block;color: #619fe7;text-decoration: underline;cursor: pointer;">watch this video.</span></h2>
                    <h2 v-else style="font-weight: 300;word-break: break-word; padding: 0 40px;">Your information is being shared</h2>
                </div>
                <div style="display: inline-block;">
                    <span v-if="ifshare === false" >
                        <p style="color: rgba(255, 255, 255, .51);">N.B. To book metoring sessions, we require you to share your Read-only live account Capital index account with us.</p>
                    </span>
                    <span v-else>
                        <p style="color: rgba(255, 255, 255, .51);">To revoke your permission to share read-only account with Learn to Trade Ltd,</p>
                        <p style="color: rgba(255, 255, 255, .51); word-break: break-word;">please email: <a href = "mailto: info@smartchartsfx.com" style="color: #3a95ff;">info@smartchartsfx.com</a>, if you revoke your permission to share your journal, you will no longer be able to book mentoring sessions.</p>
                    </span>
                </div>
                <div style="display: inline-block; margin-top: 20px;margin-bottom: 20px;">
                    <el-button v-if="ifshare === false" type="success" @click="onShare()">SHARE READ ONLY LIVE ACCOUNT</el-button>
                    <el-button v-if="ifshare === true" type="success" @click="dialogShare = false">OK</el-button>

                </div>
            </div>
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
                user_id: ''
            }
        },
        created() {
          this.user_id = this.datasales.portal_user.portal_user_id
        },
        methods: {
          onShare() {
            let user_id = this.datasales.portal_user.portal_user_id
            let url = '/api/v1/share'
            axios.get(url,
                {
                  params: {
                    'id': user_id
                  }
                }
             ).then(response => {
                console.log(response, 'response')
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
