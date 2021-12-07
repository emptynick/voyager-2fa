<template>
    <Card title="Manage 2FA">
        <Alert v-if="active">
            <p v-html="nl2br(allowDisable ? __('2fa::2fa.active_alert_disable') : __('2fa::2fa.active_alert'))"></p>
        </Alert>
        <div class="flex w-full mt-2">
            <div class="flex-none text-center">
                <div v-html="qr"></div>
                <div v-html="twofakey"></div>
            </div>
            <div class="flex-grow mx-2">
                <p>{{ __('2fa::2fa.scan_info') }}</p>
                <input type="text" class="input" v-model="code" :placeholder="__('2fa::2fa.new_otp')">
                <div class="inline-flex space-x-2 mt-2">
                    <button class="button blue" @click.prevent="enable">{{ active ? __('2fa::2fa.renew') : __('voyager::generic.enable') }}</button>
                    <button v-if="active && allowDisable" class="button" @click.prevent="disable">{{ __('voyager::generic.disable') }}</button>
                </div>
            </div>
        </div>
    </Card>
</template>

<script lang="ts">
import axios from 'axios';

export default {
    props: {
        twofakey: String,
        qr: String,
        active: Boolean,
        allowDisable: Boolean,
    },
    mounted() {
        console.log(this.allowDisable);
    },
    data() {
        return {
            code: null,
        }
    },
    methods: {
        enable() {
            axios.post(this.route('voyager.voyager-manage-2fa'), {
                enable: true,
                key: this.twofakey,
                code: this.code,
            }).then((response) => {
                if (response.status == 200) {
                    new this.$notification(this.__('2fa::2fa.activated')).color('green').timeout().show();
                    location.reload();
                }
            }).catch(() => {});
        },
        disable() {
            if (this.allowDisable) {
                new this.$notification(this.__('2fa::2fa.disable_confirm')).color('yellow').timeout().confirm().show()
                .then((result) => {
                    if (result) {
                        axios.post(this.route('voyager.voyager-manage-2fa'), {
                            enable: false,
                        }).then((response) => {
                            if (response.status == 200) {
                                new this.$notification(this.__('2fa::2fa.disabled')).color('green').timeout().show();
                                location.reload();
                            }
                        }).catch(() => {});
                    }
                });
            }
        }
    }
}
</script>