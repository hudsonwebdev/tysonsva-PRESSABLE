<template>
<form @keydown.enter.prevent="" autocomplete="off" class="bg-white shadow relative" :class="[containerMargin]" action="#" method="POST">
<div class="px-4 py-5 sm:p-6">
	<h3 class="text-lg m-0 leading-6 font-medium text-gray-darkest">
		<slot name="header"/>
	</h3>
	<div class="mt-2 max-w-xl text-sm leading-5 text-gray-dark">
		<div class="m-0 pt-0">
			<slot name="description"/>
		</div>
	</div>
	<div class="mt-5 md:flex md:items-center">
		<div class="w-24">
			<div class="relative rounded-md shadow-sm">
				<input
					:value="value"
					:aria-label="headerText"
					disabled
					class="form-input opacity-50 pointer-events-none block w-full md:text-sm md:leading-5"/>
			</div>
		</div>
		<span class="mt-3 inline-flex rounded-md shadow-sm md:mt-0 md:ml-3 md:w-auto">
			<button disabled type="button" class="opacity-50 pointer-events-none w-full inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-white bg-orange transition ease-in-out duration-150 md:w-auto md:text-sm md:leading-5">
				{{ __('Save', 'ml-slider') }}
			</button>
		</span>
		<span class="mt-3 inline-flex items-center md:mt-0 md:ml-2">
			<a :original-title="proText" href="https://www.metaslider.com/upgrade?utm_source=lite&amp;utm_medium=banner&amp;utm_campaign=pro" target="_blank" class="dashicons dashicons-lock is-pro-setting tipsy-tooltip-top"></a>
		</span>
	</div>
</div>
<transition name="settings-fade" mode="in-out">
	<loading-element v-if="$parent.$attrs.loading"/>
</transition>
</form>
</template>

<script>
import { default as LoadingElement } from './shimmers/_textSingleShimmer'
export default {
	props: {
		value: {},
		containerMargin: {
			type: String,
			default: 'mb-4'
		}
	},
	components: {
		'loading-element' : LoadingElement
	},
	data() {
		return {}
	},
	created() {},
	mounted() {},
	methods: {},
	computed: {
		proText() {
			const slot = this.$slots['proText'];
			if (!slot || !slot.length) return '';

			return slot[0].text || '';
		},
		headerText() {
			const slot = this.$slots['header'];
			if (!slot || !slot.length) return '';

			return slot[0].text || '';
		}
	}
}
</script>
