/**
 * Import global things:
 */

import Vue from 'vue'
import { Slideshow, Toolbar } from './slideshows'
import { MediaContainer } from './slides'
import { EventManager } from './utils'
import { Axios } from './api'
import store from './store'
import './components'

Vue.component('metaslider', Slideshow)
Vue.component('metaslider-toolbar', Toolbar)
const MetaSlider = new Vue({ store }).$mount('#metaslider-ui')

/**
 * Mounts and inserts newly created slide rows into the slide list without a full page reload.
 * Shared by the Media Library "Add Slide" flow (admin.js) and the Pixabay/Unsplash "Add Slide"
 * flow (External.vue), so this only needs to be maintained in one place.
 *
 * @param {Array} rows Slide rows as returned by the create_image_slide/import_images endpoints - each needs a 'slide_id' and 'html' field
 */
function mountNewSlides(rows) {
	rows.forEach(row => {
		const compiled = Vue.compile(row.html)
		const el = (new Vue({
			render: compiled.render,
			staticRenderFns: compiled.staticRenderFns
		}).$mount()).$el

		if (window.metaslider.newSlideOrder === 'last') {
			document.querySelector('#metaslider-slides-list > tbody').append(el)
		} else {
			document.querySelector('#metaslider-slides-list > tbody').prepend(el)
		}
	})
}

/**
 * Scrolls a slide into view by id. Split out from mountNewSlides() so a caller that needs to
 * wait for something else first (e.g. a media modal to actually finish closing, #2363) can defer
 * just the scroll instead of the whole mount.
 *
 * @param {number|string} slideId
 */
function scrollToSlide(slideId) {
	const el = document.getElementById('slide-' + slideId)
	el && el.scrollIntoView({ behavior: 'smooth' })
}

// these exports are available globaly through window.metaslider.app.{name}
if (!window.metaslider) {
	window.metaslider = {}
}
window.metaslider.app = { Vue, MetaSlider, Slideshow, EventManager, Axios, store, MediaContainer, mountNewSlides, scrollToSlide }
