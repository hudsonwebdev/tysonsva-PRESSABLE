<template>
	<div class="attachments-browser external-media-importer">
		<div
			v-if="downloading"
			class="ms-hero-status">
			<div
				v-if="uploadPercentage > 0"
				class="ms-upload-progress">
				<div class="ms-upload-image">
					<span
						:aria-label="$refs['external-api'].fileName"
						tabindex="0"
						role="checkbox"
						aria-checked="false"
						style="width:150px;height:150px"
						class="attachment save-ready">
						<div class="attachment-preview js--select-attachment type-image subtype-jpeg portrait">
							<div class="thumbnail">
								<div class="centered">
									<img
										:src="$refs['external-api'].previewImageUrl"
										:alt="$refs['external-api'].fileName"
										draggable="false">
								</div>
							</div>
						</div>
					</span>
				</div>
				<div class="ms-progress">
					<div
						:style="{width: uploadPercentage + '%'}"
						class="ms-progress-bar"/>
					<span class="text-lg">{{ downloadingMessage ? downloadingMessage : __('Crunching...', 'ml-slider') }}</span>
				</div>
			</div>
		</div>
		<component
			v-show="!downloading"
			ref="external-api"
			:is="component">
			<template slot="search-tools">
				<slot name="search-tools"/>
			</template>
		</component>
	</div>
</template>

<script>
import { EventManager } from '../../utils'
import { Axios } from '../../api'
import unsplash from './Unsplash.vue'
import pixabay from './Pixabay.vue'

export default {
	components: {
		unsplash,
		pixabay
	},
	props: {
		source: {
			type: [String],
			default: 'unsplash'
		},
		slideshowId: {
			type: [String, Number],
			default: null
		},
		slideId: {
			type: [String, Number],
			default: null
		},
		slideType: {
			type: [String],
			default: 'image'
		}
	},
	data() {
		return {
			page: 1,
			component: null,
			mediaButton: {},
			ourMediaButton: {},
			downloading: false,
			uploadPercentage: 1,
			downloadingMessage: '',
			pendingScrollSlideId: null
		}
	},
	watch: {
		downloading() {
			this.ourMediaButton.disabled = this.downloading
			if (this.downloading) window.metaslider.about_to_reload = true
		}
	},
	created() {
		// Addons (e.g. Pro's Pixabay module) register their own picker components on
		// window.metaslider.components, keyed by source name - prefer that, and fall back to
		// resolving the source as a locally-registered component name (e.g. 'unsplash')
		this.component = (window.metaslider.components && window.metaslider.components[this.source]) || this.source
	},
	mounted() {
		// Set up the download progress bar %
		EventManager.$on('metaslider/external-api-percentage', ({ percentage }) => {
			this.uploadPercentage = percentage
		})

		// If the user has some images selected from the original media selector, remove them
		const importerContainer = document.querySelector('.external-media-importer')

		let clearButton = importerContainer.closest('.media-modal-content')
		clearButton = clearButton.querySelector('button.clear-selection')
		clearButton && clearButton.click()

		// Manually deselect any possible remaining images
		const selectedImages = document.querySelectorAll('.attachment.save-ready.selected')
		selectedImages && selectedImages.forEach(image => {
			image.click()
		})

		// Hack into the media upload button when the component is active
		let modalContainer = importerContainer.closest('.media-modal-content')
		this.mediaButton = modalContainer.querySelector('.media-frame-toolbar .media-toolbar-primary button.media-button')

		// Clone the button and use our version instead (original restored on destroy)
		this.ourMediaButton = this.mediaButton.cloneNode()
		this.ourMediaButton.classList.add('float-right', 'rtl\:float-left')
		this.ourMediaButton.innerHTML = this.mediaButton.innerHTML
		this.mediaButton.parentNode.insertBefore(this.ourMediaButton, this.mediaButton)

		// Only enabled once something is actually selected in the picker
		this.ourMediaButton.disabled = !this.$refs['external-api'].selected.id
		this.$watch(() => this.$refs['external-api'].selected, (selected) => {
			this.ourMediaButton.disabled = !(selected && selected.id)
		})
		this.mediaButton.style.visibility = 'hidden'

		// The component isn't destroyed on tab switching, so this could be added multiple times. That's ok.
		this.ourMediaButton.addEventListener('click', this.interceptAddButton)

	},
	destroyed() {
		// Delete our button and show the original button
		this.ourMediaButton.removeEventListener('click', this.interceptAddButton)
		this.ourMediaButton.parentNode.removeChild(this.ourMediaButton)
		this.mediaButton.style.visibility = 'visible'

		const container = document.getElementById('image-api-container')
		container && container.parentNode.removeChild(container)

		if (window.metaslider.about_to_reload) {

			delete window.metaslider.about_to_reload

			// Close any WP media modals (currently we only have two)
			window.create_slides && window.create_slides.close()
			window.update_slide_frame && window.update_slide_frame.close()

		}

		// The media modal stays open through the whole upload and only actually closes above -
		// scrolling any earlier would happen behind it, invisibly (#2363)
		if (this.pendingScrollSlideId) {
			window.metaslider.app.scrollToSlide(this.pendingScrollSlideId)
			this.pendingScrollSlideId = null
		}
	},
	methods: {
		async interceptAddButton(event) {

			// Child components must impliment some of these referenced methods
			if (this.$refs['external-api'].selected.id) {
				this.downloading = true
				this.downloadingMessage = this.__('Saving...', 'ml-slider')

				const mediaType = this.$refs['external-api'].mediaType || 'image'

				const { data } = await this.$refs['external-api'].download()
				const uploadData = this.$refs['external-api'].upload
				const formData = new FormData()
				const name = this.$refs['external-api'].fileName

				// Add the file
				formData.append('files[' + name + ']', data, name)

				// Add the data (captions, etc)
				Object.keys(this.$refs['external-api'].upload).forEach(key => {
					let value = uploadData[key]
					formData.append('image_data[' + name + '][' + key + ']', value)
				})

				// Add additional info as needed
				formData.append('slideshow_id', this.slideshowId)
				this.slideType && formData.append('slide_type', this.slideType)
				this.slideId && formData.append('slide_id', this.slideId)

				// Pro module (Local Video)
				if (mediaType === 'video') {
					formData.append('action', 'ms_import_video')

					const response = await Axios.post('import/video', formData).catch(error => {
						this.notifyError('metaslider/video-import-error', error, true)
						this.$destroy() // Close the module
					})

					if (response) {
						this.uploadPercentage = 100
						this.downloadingMessage = this.__('Complete!', 'ml-slider')
						await new Promise(resolve => setTimeout(resolve, 1500))

						const attachmentId = response.data.data.attachment_id

						if (this.slideId) {
							// Existing slide - let the pro module (Local Video) attach this as
							// a video source via its own postmeta/AJAX actions
							EventManager.$emit('metaslider/pixabay-video-imported', {
								slideId: this.slideId,
								attachmentId
							})
						} else {
							// No slide yet (Add Slide > Local Video) - let the pro module create
							// a brand new Local Video slide from this attachment
							EventManager.$emit('metaslider/pixabay-video-created', { attachmentId })
						}

						this.$destroy()
					}
					return
				}

				formData.append('action', 'ms_import_images')

				const thumbnail = await Axios.post('import/images', formData).catch(error => {
					this.notifyError('metaslider/image-import-error', error, true)
					this.slideId = true // Prevent page reload
					this.$destroy() // Close the module
				})

				// incread slider to 100 and wait a second
				this.uploadPercentage = 100
				this.downloadingMessage = this.__('Complete!', 'ml-slider')
				await new Promise(resolve => setTimeout(resolve, 1500))

				// Add the new slide(s) to the list without a full page reload
				if (!this.slideId) {
					const importedSlides = (thumbnail && thumbnail.data) || []

					if (!importedSlides.length) {
						// Fall back to a reload if we didn't get anything usable back
						window.location.reload(true)
					} else if (window.location.href.indexOf('metaslider-start') > -1) {
						// No slideshow existed yet - jump to the editor for the one that was just created
						window.location.href = 'admin.php?page=metaslider&id=' + importedSlides[0].slideshow_id
					} else {
						// Mount and insert each new slide, same shared helper the Media Library "Add Slide" flow in admin.js uses
						window.metaslider.app.mountNewSlides(importedSlides)

						// Scroll once destroyed() has actually closed the media modal, not now while it's still open (#2363)
						this.pendingScrollSlideId = importedSlides[importedSlides.length - 1].slide_id

						// Same message/pluralization as the Media Library "Add Slide" flow in admin.js
						const message = importedSlides.length === 1
							? this.__('1 slide added successfully', 'ml-slider')
							: this.__('%s slides added successfully', 'ml-slider')
						this.notifySuccess('metaslider/slides-created', this.sprintf(message, importedSlides.length), true)
						this.triggerEvent('metaslider/save')
					}
				}

				// Set the new image if we are on a slide
				if (this.slideId) {
					// import/images responds with one row per imported image - we only ever import one here
					const importedSlide = thumbnail && thumbnail.data && thumbnail.data[0]

					if (importedSlide) {
						// Update the actual <img> in the thumb - it sits on top of and hides any .thumb background-image
						const new_image = document.querySelector('[data-slide-id="' + this.slideId + '"] .thumb img')
						if (new_image) {
							new_image.setAttribute(
								'srcset',
								`${importedSlide.thumbnail_url_large} 1024w, ${importedSlide.thumbnail_url_medium} 768w, ${importedSlide.thumbnail_url_small} 240w`
							)
							new_image.setAttribute('src', importedSlide.thumbnail_url_small)
						}

						// Set the new image preview if we are editing a Local video's cover
						if(this.proUser && this.slideType === 'local_video') {
							const image_preview = document.querySelector('#slide-' + this.slideId + ' .update-cover-image');
							image_preview.style.backgroundImage = 'url(' + importedSlide.thumbnail_url_small + ')';
							image_preview.innerHTML = '';
						}
					}

					// Update any image data fields as necessary (field does not need to exist)
					EventManager.$emit('metaslider/image-meta-updated', ['' + this.slideId], this.$refs['external-api'].upload)
				}

				// We're done!
				this.$destroy()
			}
			// Don't need error handling / validation here
		}
	}
}
</script>

<style lang="scss">
.external-media-importer {
	ul.attachments li {
		max-width: 175px;
	}
}
.ms-hero-status {
	display: flex;
    align-items: center;
	// justify-content: center;
	flex-direction: column;
    width: 100%;
	height: 100%;
	.ms-upload-progress {
		height: 100%;
	}
	.ms-progress {
		width: 50%;
		span {
			line-height: 24px;
		}
	}
}
.ms-upload-image {
	margin: 1rem 0;
	border: 2px solid rgba(204, 204, 204, 0.7);
	img {
		width: 100%;
		display: block;
	}
}
</style>
