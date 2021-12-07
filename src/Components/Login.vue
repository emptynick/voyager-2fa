<template>
    <div>
        <div class="w-full mt-1">
            <label for="email" class="label">{{ __('voyager::auth.email') }}</label>
            <div class="mt-1 rounded-md shadow-sm">
                <input type="email" name="email" id="email" class="input w-full mb-4 placeholder-gray-400" :value="data.email" :autofocus="!otpRequested">
            </div>
        </div>
        <div class="w-full mt-6">
            <label for="password" class="label">{{ __('voyager::auth.password') }}</label>
            <div class="mt-1 rounded-md shadow-sm">
                <input type="password" name="password" id="password" class="input w-full mb-3 placeholder-gray-400" :value="data.password">
            </div>
        </div>
        <div class="w-full mt-6" v-if="otpRequested">
            <label for="otp" class="label">One time password</label>
            <div class="mt-1 rounded-md shadow-sm">
                <input type="text" name="otp" id="otp" class="input w-full mb-3 placeholder-gray-400" :value="data.otp" autofocus>
            </div>
        </div>
        <div class="w-full flex justify-between mt-4">
            <div class="select-none">
                <input type="checkbox" class="input" name="remember" id="remember" :checked="data.remember == 'on' || false">
                <label for="remember" class="text-sm leading-8 mx-1">{{ __('voyager::auth.remember_me') }}</label>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <button class="button large accent w-full justify-center" type="submit">
                {{ __('voyager::auth.login') }}
            </button>
        </div>
    </div>
</template>

<script lang="ts">
export default {
    props: {
        errors: Array,
        data: Object,
    },
    computed: {
        otpRequested() {
            return this.errors.includes(this.__('2fa::2fa.enter_otp')) || this.errors.includes(this.__('2fa::2fa.otp_empty')) || this.errors.includes(this.__('2fa::2fa.otp_wrong'));
        }
    }
}
</script>