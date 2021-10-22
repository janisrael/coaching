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
                const token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NDlmYWI1Zi1hZmYyLTQ3OGQtODUzOS04MDAwNThkZjZiY2UiLCJqdGkiOiIxNzg3MTNhMDdhNzRhYWUxYTllOTU5Mzc2ZGM3MTc5NDdlMWVhYzZiYjZiMTU1NzliOTllMDA5MmQ1Zjc0NWRiZjhmNzUyZjgwOTg0NzY4MyIsImlhdCI6MTYzNDc1MjYxNi4xNDI0MzYsIm5iZiI6MTYzNDc1MjYxNi4xNDI0NCwiZXhwIjoxNjY2Mjg4NjE2LjEyMDUzMSwic3ViIjoiMzNkYzYyOTMtNWZhNi00MWM5LThhNzctYzE2NzAyYmQ3NWI2Iiwic2NvcGVzIjpbXX0.qU8J7AoXXIDEcT93InnvEMsMyZnfA5sMdRSqC3o5TC4ZKl8i9v-3tNz05BYBS9X4b12ExTAcXGxCk-JSye_bAD1cYnZhxZ-JxQs8UlW5hsTm1Xy4srr5rmPwVLEiRFxczFt0pifS1MeMHM2xrBdIkrvrh7Omt_WyoD8fS7EywZmpc4gpkOOknorGiTk5xZMH-vfsEGov6zGWVWO6OA_Wty2g6HQGO0rDHtdZo83aCfQfQjTFjbV5AAZOurO4S1X04JQQSz3MLWUeNqgz60r3lz7Z4z4Em5xLlOYyCPeFW1q6HL1B1OWBgvaWRCAjImKuZvhel5gGRIpQ6aQOSqAfrKXmWcYGv0Y2W9269SJPjOxdxhg-Ir7sLQBW-DatJjGJD0FRQKxM9H3HMw9iojZGFFZUcqqbB7eP86n3HMvOcSvRxJ_e-kh_uR3YvxamdB1bVePpfWxm8jZO8zYO_Zn9otBVB1h_JiRav___V0R3lqAkJZsLQ6h3zgt1MYHaGdcVmJszrS-oZcpYRGZ_-8l_Zb86BT0febVTjYxhU4_CXiHz8lhJEb2OTfEvbsiFCgAnsIiGRul7yOt0qklZmn1nhclZRz1Xatk9N9w1tWtXt-QOM3mVO5F_m0BHUP_-MHF0MgvJdZr3rRdD5j3oUhU8XtGB4-LBXse6P3BerIpMgv0'
                console.log(this.user_id, this.datasales)
                let user_id = this.user_id = this.datasales.portal_user.portal_user_id
                let url = 'https://dev-api.smartchartsfx.com/v1/coaching/students/54/opt-data-sharing'
                axios.post(url,
                    {
                        Headers: {
                          'Authorization': 'Bearer ' + token,
                          'Accept': 'application/json',
                          'Content-Type': 'application/json',
                          'Cache-Control': 'no-cache'
                        }
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
