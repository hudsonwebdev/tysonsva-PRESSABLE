<template>
    <div class="rounded-md">
        <!-- Success: the address is added to the mailing list immediately -->
        <div v-if="state === 'sent'" class="text-center">
            <h3 class="m-0 p-3 text-lg leading-6 font-medium text-white bg-orange rounded-top">
                {{ __('Thank you for choosing MetaSlider!', 'ml-slider') }}
            </h3>
            <div class="p-5">
                <p class="text-lg leading-5 text-gray-darker mb-0" v-html="confirmationMessage()" />
            </div>
        </div>

        <!-- Failure: never claim success we can't verify -->
        <div v-else-if="state === 'error'" class="text-center">
            <h3 class="m-0 p-3 text-lg leading-6 font-medium text-white bg-orange rounded-top">
                {{ __('We could not subscribe you', 'ml-slider') }}
            </h3>
            <div class="p-5">
                <p class="text-lg leading-5 text-gray-darker mb-0">
                    {{ errorMessage || __('Your preference was saved, but we could not add your address to the mailing list. Please check it and try again.', 'ml-slider') }}
                </p>
                <p class="mt-3 mb-0 text-sm leading-5 text-gray-dark" v-html="errorSupportMessage()" />
            </div>
            <div class="relative rounded-md shadow-sm px-5">
                <input type="email" class="form-input block w-full md:text-sm md:leading-5" v-model="optinEmail" :placeholder="__('Email address', 'ml-slider')" />
            </div>
        </div>

        <div v-else class="text-center">
            <h3 class="m-0 p-3 text-lg leading-6 font-medium text-white bg-orange rounded-top">
                {{ __('Thanks for using MetaSlider Slideshow', 'ml-slider') }}
            </h3>
            <div class="p-5">
                <p class="text-lg leading-6 text-gray-darker"> {{ __('Get occasional emails about important MetaSlider Slideshow security and feature updates.', 'ml-slider') }} </p>
                <p class="text-base leading-5 text-gray-darker mb-0"> {{ __('You can unsubscribe at any time.', 'ml-slider') }} </p>
            </div>
            <div class="relative rounded-md shadow-sm px-5">
                <input type="email" class="form-input block w-full md:text-sm md:leading-5" v-model="optinEmail" :disabled="isSending" :placeholder="__('Email address', 'ml-slider')" />
            </div>
            <p class="p-3 mt-0 max-w-xl text-sm leading-5 text-gray-dark" v-html="modalPrivacyPolicy()" />
        </div>

        <!-- Only rendered when the server sends a report - which only happens
             when metaslider_always_show_connect_report is forced true, since
             it names internal endpoints. Shows exactly what reached the
             connect service and what Mailjet said, without needing devtools -
             the outbound calls are server side and never appear in the
             browser's network tab. -->
        <div v-if="report" class="px-5 pb-2">
            <details class="text-left">
                <summary class="cursor-pointer text-sm leading-5 text-gray-dark select-none">
                    {{ __('Connection details', 'ml-slider') }}
                    <span :class="['subscribed', 'duplicate'].indexOf(report.status) > -1 ? 'text-green-dark' : 'text-red-dark'">
                        ({{ report.status }})
                    </span>
                </summary>
                <pre class="mt-2 p-3 max-h-64 overflow-auto bg-gray-lightest text-xs leading-4 whitespace-pre-wrap break-all">{{ reportText }}</pre>
            </details>
        </div>

        <div class="mt-6 sm:grid sm:gap-3 sm:grid-flow-row-dense px-4 pb-5">
            <template v-if="state === 'sent'">
                <span class="flex w-full rounded-md shadow-sm">
                    <button @click="finish()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 text-base leading-6 font-medium text-white shadow-sm bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        {{ __('Got it', 'ml-slider') }}
                    </button>
                </span>
            </template>
            <template v-else>
                <span class="flex w-full rounded-md shadow-sm sm:col-start-2">
                    <button @click="opt('yes')" type="button" :disabled="!isValidEmail || isSending" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 text-base leading-6 font-medium text-white shadow-sm bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 sm:text-sm sm:leading-5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="isSending">{{ __('Sending...', 'ml-slider') }}</span>
                        <span v-else-if="state === 'error'">{{ __('Try again', 'ml-slider') }}</span>
                        <span v-else>{{ __('Agree and continue', 'ml-slider') }}</span>
                    </button>
                </span>
                <span class="mt-3 flex w-full rounded-md sm:mt-0 sm:col-start-1">
                    <button @click="opt('no')" type="button" :disabled="isSending" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 text-base leading-6 font-medium text-black shadow-sm bg-gray hover:text-white hover:bg-gray-darker active:bg-gray-darkest transition ease-in-out duration-150 sm:text-sm sm:leading-5 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('No thanks', 'ml-slider') }}
                    </button>
                </span>
            </template>
        </div>
    </div>
</template>

<script>
import { Settings } from '../api'
import { EventManager } from '../utils'
export default {
    data() {
		return {
            optinEmail: '',
            // idle | sending | sent | error
            state: 'idle',
            // Diagnostic payload from the server. Only present when the
            // metaslider_always_show_connect_report filter is forced true -
            // WP_DEBUG has no effect on it.
            report: null,
            // A specific, safe-to-show reason for the error state - e.g. "not
            // a valid email address". Only set for failures that happen before
            // anything reaches the connect service, so it's never internal detail.
            errorMessage: ''
		}
	},
    filename: 'AnalyticsNotice',
    created() {
        this.$parent.classes = 'w-full max-w-lg rounded-lg'
        this.$parent.forceOpen = () => {
            // Never turn a completed opt-in into an opt-out, and don't abandon
            // a request that is still in flight. Returning false tells
            // UtilityModal to keep this guard installed, so a second close
            // attempt while still sending is guarded again too, rather than
            // falling through to a hard unmount of this component.
            if (this.isSending) {
                return false
            }
            if (this.state === 'sent') {
                this.finish()
                return
            }
            this.opt('no')
            this.$parent.forceOpen = false  
        }
        Settings.getUserSetting().then(({data}) => {
			this.optinEmail = data.data
		})
        Settings.saveUserSetting('analytics_onboarding_status', 'no')
    },
    mounted() {
        this.notifyInfo('metaslider/add-slide-css-manager-notice-opened', this.__('Analytics notice opened', 'ml-slider'))
        document.addEventListener('keydown', this.handleKeydown)
    },
    beforeDestroy() {
        this.notifyInfo('metaslider/add-slide-css-manager-notice-closed', this.__('Analytics notice closed', 'ml-slider'))
        document.removeEventListener('keydown', this.handleKeydown)
    },
    computed: {
        isValidEmail() {
            // A real format check, not just "non-empty" - a plain string used
            // to enable the button and get silently swapped for the current
            // user's address server-side, subscribing an address the visitor
            // never typed.
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test((this.optinEmail || '').trim())
        },
        isSending() {
            return this.state === 'sending'
        },
        reportText() {
            try {
                return JSON.stringify(this.report, null, 2)
            } catch (error) {
                return String(this.report)
            }
        }
    },
    methods: {
        handleKeydown(event) {
            if (event.key !== 'Escape') {
                return
            }
            // Don't let Escape cancel a request that's already in flight, and
            // don't record an opt-out for someone who already opted in
            if (this.isSending) {
                return
            }
            if (this.state === 'sent') {
                this.finish()
                return
            }
            this.opt('no')
        },
        modalPrivacyPolicy() {
            return this.sprintf(this.__('See our %s.', 'ml-slider'), '<a target="_blank" class="underline" href="https://www.metaslider.com/privacy-policy">' + this.__('privacy policy', 'ml-slider') + '</a>', 'ml-slider')
        },
        errorSupportMessage() {
            return this.sprintf(
                this.__('If this keeps happening, please %s.', 'ml-slider'),
                '<a target="_blank" class="underline" href="https://www.metaslider.com/support/">' + this.__('contact support', 'ml-slider') + '</a>'
            )
        },
        confirmationMessage() {
            return this.sprintf(
                this.__("You're subscribed with %s. We'll keep you posted on important updates.", 'ml-slider'),
                '<strong>' + this.escapeHtml(this.optinEmail) + '</strong>'
            )
        },
        escapeHtml(value) {
            const element = document.createElement('div')
            element.textContent = value
            return element.innerHTML
        },
        // Close the modal and carry on with the tour
        finish() {
            this.$parent.forceOpen = false
            this.$parent.close()
            EventManager.$emit('metaslider/start-tour')
        },
        async opt(type) {
            if (type === 'no') {
                this.$parent.forceOpen = false
                this.$parent.close()
                await Settings.saveUserSetting('analytics_onboarding_status', 'no')
                EventManager.$emit('metaslider/start-tour')
                return
            }

            // Stay open while this runs, rather than closing optimistically
            // before we know the address actually reached Mailjet.
            this.state = 'sending'
            this.report = null
            this.errorMessage = ''

            try {
                // Three independent option writes - nothing here depends on
                // another's result, so there's no need to wait on them one at
                // a time before the next step, which does need optin_email
                // to have landed first.
                await Promise.all([
                    Settings.saveUserSetting('analytics_onboarding_status', 'yes'),
                    Settings.saveGlobalSettingsSingle('optin_via', 'modal'),
                    Settings.saveGlobalSettingsSingle('optin_email', this.optinEmail)
                ])

                // A bit contrived but keeps the api from needing a patch endpoint
                const { data: current } = await Settings.getGlobalSettings()
                const settings = { ...current.data, optIn: true }
                const { data: saved } = await Settings.saveGlobalSettings(JSON.stringify(settings))

                // 'subscribed' = added just now, 'duplicate' = was already
                // handed off previously - both are success for the UI
                const result = saved && saved.data ? saved.data.optinEmailSent : null
                this.report = saved && saved.data ? saved.data.optinReport || null : null
                this.state = ['subscribed', 'duplicate'].indexOf(result) > -1 ? 'sent' : 'error'
            } catch (error) {
                // wp_send_json_error() responses (e.g. an invalid email
                // address rejected server-side) carry the real message here.
                // Deliberately NOT setting this.report here - that renders
                // the "Connection details" panel, which is meant to be dev-only
                // (gated server-side behind metaslider_always_show_connect_report).
                // A raw axios/network error was never checked against that
                // filter, so showing it here would leak internal detail to
                // every user instead of just developers testing the flow.
                const serverMessage = error && error.response && error.response.data
                    ? error.response.data.data
                    : null
                // Safe to show as-is: this only fires before anything reaches
                // the connect service, so there's no internal detail in it -
                // and "your preference was saved" would be wrong here, since
                // the invalid address is rejected before optIn is ever saved.
                this.errorMessage = serverMessage || this.__('We could not save your preference. Please try again.', 'ml-slider')
                this.state = 'error'
            }
        },
    }
}
</script>
