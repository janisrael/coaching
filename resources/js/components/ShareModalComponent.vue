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
                const token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NDlmYWI1Zi1hZmYyLTQ3OGQtODUzOS04MDAwNThkZjZiY2UiLCJqdGkiOiI5MDkzMWNlODc1MjE1MTBkMjk1Yzc4MTVhM2E0YzVjYzgwZTY3M2EwYTJjZmIwMWFmZTlkZTk0MGFhNDI3ZWE1MWJiY2M3MTNmZjZmMTNiMyIsImlhdCI6MTYzNDEzNTExNi43MTc1NjUsIm5iZiI6MTYzNDEzNTExNi43MTc1NjgsImV4cCI6MTY2NTY3MTExNi43MDcyNDQsInN1YiI6IiIsInNjb3BlcyI6W119.m0S8EYA1ZTM97lp0n726aprnvyy25vViDrKPNUfywq_ao05xDDbPG7FjbUOMMUoTAK8jDVOVyzr0ouK5seaBBd-BdnZu-Wby2W0ooBvlV7v0K3Y7oBOG9PBIO88HnocXw17hGS9SMbZKgIfTOqN_lLRaodGbAdrJgAYPfKEfhSzYKKL1FBXvFdL8lbTyZpPmJWUJZPPwSm2HCS_v42b7cdQkMqGkrcGx0OcciwadQv9yA2N_EGR_q4iSEp4rBurHPjSz9jp10fS1erEVxK2v8OvTVquD4EaLGubc2gmaMgKEETIzC8h6biUktUI0orMA5-gFi8ptEyRb_6TYStPAwx51mK1y3sADHmS9gus4PoquWXgPfVPDyAw0UWTL_A4HCgnYWfo0YQBuTSP0oEbnhFP3d9d4Avoo5Mc6RhU1CprtBqb6rd-LY625OXtcspKhTt1XLto_PWwvxiffoQf0uAeQS7hQIvxeGYsGWEGtj_JvaVejxArqKccHq8mnDY4WSKUotejQDsMoMq8nBvaPx9JSOwMzRLUQXlIxLXJp5ShxMrnHaW1PRe98xFpg3ftVF23pHxONk5BQ_k8Rt2ovnmcuIaoVBfoA7_r75SeitgMCo4bSTr0VtA5MCfZXSZ7GnriibX7SicWkVT2jLXDt3_EEb6BdOT2vVqwknHEmiRI'
                console.log(this.user_id, this.datasales)
                let user_id = this.user_id = this.datasales.portal_user.portal_user_id
                let url = 'https://dev-api.smartchartsfx.com/v1/students/' + user_id + '/opt-data-sharing'
                axios.post(url,
                    { 
                        headers: { 'Authorization' : `Bearer ${token}`}
                    }
                 ).then(response => {
                    console.log(response, 'response')
                        if(response.data.data.status === 'success') {
                            console.log(response.data.data.status,'success')
                            this.$emit('setShareValue', { value: true })
                            this.$notify.success({
                                title: 'Success',
                                message: 'Live Account Shared!',
                                type: 'success'
                                });
                            this.dialogShare = false
                        }
                    })
                    .catch(error => {
                    console.log(error)
                        this.$notify.error({
                        title: 'Error',
                        message: 'Unable to share!',
                        type: 'error'
                        });
                    // this.isLoading = false
                    this.dialogShare = false
                    })
                // console.log(response.data.data.status,'success')
                // this.$emit('setShareValue', { value: true })
                // this.$notify.success({
                //     title: 'Success',
                //     message: 'Live Account Shared!',
                //     type: 'success'
                //     });
                // this.dialogShare = false
                this.$emit('setShareValue', { value: true }) // temporarary
            },
            show() {
                this.dialogShare = true
            }
        }
    }
</script>
<style scoped>

</style>