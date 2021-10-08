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
                
                    <h2 style="font-weight: 300;word-break: break-word; padding: 0 40px;">Mentoring is one of the most important ways to develop your skillset as a trader. Our mentoring sessions are designed to review your live trading. To find out more, <span style="display: inline-block;color: #619fe7;text-decoration: underline;cursor: pointer;">watch this video.</span></h2>
                </div>
                <div style="display: inline-block;">
                    <p style="color: rgba(255, 255, 255, .51);">N.B. To book metoring sessions, we require you to share your Read-only live account Capital index account with us.</p>
                </div>
                <div style="display: inline-block; margin-top: 20px;margin-bottom: 20px;">
                    <el-button type="success" @click="onShare()">SHARE READ ONLY LIVE ACCOUNT</el-button>
                </div>
            </div>
            </el-dialog>
    </div>
</template>

<script>
    export default {
        name: 'ShareModalComponent',
        props: {
        
        },
        data() {
            return {
                dialogShare: true
            }
        },
        created() {

        },
        methods: {
            onShare() {
                this.$emit('setShareValue', { value: true })
                this.$notify({
                    title: 'Success',
                    message: 'Live Account Shared!',
                    type: 'success'
                    });
                this.dialogShare = false
            },
            show() {
                this.dialogShare = true
            }
        }
    }
</script>
<style scoped>

</style>