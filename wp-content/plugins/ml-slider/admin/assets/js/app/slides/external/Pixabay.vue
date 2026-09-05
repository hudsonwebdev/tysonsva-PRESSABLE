<template>
	<media-container>
		<template slot="media-list">
			<li
				v-for="photo in photos"
				:aria-label="sprintf(__('Photo by %s', 'ml-slider'), photo.user)"
				:key="photo.id"
				:class="{
					selected: selected === photo,
					details: selected === photo
				}"
				class="attachment save-ready"
				tabindex="0"
				role="checkbox"
				aria-checked="false"
				@click="selected = selected === photo ? {} : photo">
				<div
					:class="orientation(photo)"
					class="attachment-preview js--select-attachment type-image subtype-jpeg">
					<div class="thumbnail">
						<div class="centered">
							<img
								:alt="sprintf(__('Photo by %s', 'ml-slider'), photo.user)"
								:src="photo.previewURL"
								draggable="false">
						</div>
					</div>
				</div>
				<button
					type="button"
					class="check"
					tabindex="-1">
					<span class="media-modal-icon"/>
					<span class="screen-reader-text">{{ __('Deselect', 'ml-slider') }}</span>
				</button>
			</li>
		</template>

		<template slot="sidebar">
			<div v-if="selected.id">
				<div
					tabindex="0"
					class="attachment-details">
					<h2>{{ __('Attachment Details', 'ml-slider') }}</h2>
					<div class="attachment-info">
						<div class="thumbnail thumbnail-image">
							<img
								:src="selected.webformatURL"
								:alt="fileName"
								draggable="false">
						</div>
						<div class="details">
							<div class="filename">{{ fileName }}</div>
							<div class="dimensions">
								{{ sprintf(
									_x('%s by %s pixels', '1000 by 1000 pixels', 'ml-slider'),
									selected.imageWidth,
									selected.imageHeight
								) }}
							</div>
							<a
								:href="selected.pageURL"
								target="_blank">{{ __('view original', 'ml-slider') }}</a>
						</div>
					</div>
					<div class="ms-api-user">
						<img
							v-if="selected.userImageURL"
							class="rtl:mr-0 rtl:ml-4"
							:src="selected.userImageURL">
						<div class="ms-profile-data">
							<div class="ms-profile-details">
								<h3>{{ selected.user }}</h3>
							</div>
							<ul class="ms-profile-meta">
								<li>
									<a
										:href="selected.userURL"
										:title="selected.userURL"
										target="_blank"
										class="ms-profile-username">{{ __('Profile', 'ml-slider') }}
									</a>
								</li>
							</ul>
						</div>
					</div>
					<label class="setting">
						<span class="name">{{ __('Title', 'ml-slider') }}</span>
						<input
							v-model="upload.title"
							type="text">
					</label>
					<label class="setting">
						<span class="name">{{ __('Caption', 'ml-slider') }}</span>
						<textarea v-model="upload.caption"/>
					</label>
					<label class="setting">
						<span class="name">{{ __('Alt Text', 'ml-slider') }}</span>
						<input
							v-model="upload.alt"
							type="text">
					</label>
					<label class="setting">
						<span class="name">{{ __('Description', 'ml-slider') }}</span>
						<textarea v-model="upload.description"/>
					</label>
					<label class="quality setting">
						<span class="name">{{ __('Quality', 'ml-slider') }}</span>
						<select
							v-model="upload.quality"
							class="alignment">
							<option
								v-for="quality in qualityOptions"
								:key="quality"
								:value="quality">
								{{ qualityLabels[quality] }}
							</option>
						</select>
					</label>
				</div>
			</div>
		</template>

		<template slot="copyright">
			<p>
				{{ __('All images published on Pixabay can be used for free.', 'ml-slider') }} <a
					target="_blank"
					href="https://pixabay.com/service/license/">{{ __('view license', 'ml-slider') }}</a>
			</p>
		</template>
	</media-container>
</template>

<script>
import { Pixabay } from '../../api'
import MediaContainer from './MediaContainer.vue'

export default {
	components: {
		MediaContainer
	},
	props: {},
	data() {
		return {
			errorMessage: '',
			canLoadMore: false,
			loadingFresh: true,
			loadingMore: true,
			searchTerm: '',
			page: 1,
			photos: [],
			selected: { id: null },
			filters: [],
			imageType: 'photo',
			mediaButton: {},
			upload: {
				title: '',
				caption: '',
				alt: '',
				description: '',
				// Global default from the Settings & Help page, falling back to 'largeImageURL' -
				// the biggest tier Pixabay reliably provides (capped around 1280px on the long
				// edge for a standard, non-approved API key; Pixabay only serves fullHDURL/imageURL
				// to keys it has manually approved for extended access, #2365)
				quality: (window.metaslider_api && window.metaslider_api.pixabayImageQuality) || 'largeImageURL'
			}
		}
	},
	computed: {
		// fullHDURL/imageURL are only present when Pixabay has approved this API key for
		// extended access, and even then only sometimes present per-photo - so these are offered
		// when actually available on the selected photo, rather than hardcoded as always present.
		// imageURL is Pixabay's true original (unbounded, same risk category as Unsplash's
		// dropped 'raw'/'full' options, #2365) so it's available to pick but never the default.
		qualityOptions() {
			const options = []
			if (this.selected.imageURL) options.push('imageURL')
			if (this.selected.fullHDURL) options.push('fullHDURL')
			options.push('largeImageURL', 'webformatURL')
			return options
		},
		// webformatURL's cap is a fixed, documented Pixabay constant, so that number is exact.
		// largeImageURL/fullHDURL get "up to" since Pixabay's actual cap for them varies by API
		// key approval tier - 1280px is what this integration's key has been observed to return,
		// not a guarantee. imageURL is the true original, so no fixed number applies to it at all.
		qualityLabels() {
			return {
				imageURL: this.__('Original - full resolution', 'ml-slider'),
				fullHDURL: this.sprintf(this.__('Full HD - up to %spx', 'ml-slider'), 1920),
				largeImageURL: this.sprintf(this.__('Large - up to %spx (recommended)', 'ml-slider'), 1280),
				webformatURL: this.sprintf(this.__('Medium - up to %spx', 'ml-slider'), 640)
			}
		},
		// Pixabay serves photos as JPG but illustrations/vectors as PNG (to preserve
		// transparency) via these same URL fields - detect the real extension instead of
		// assuming one, otherwise a PNG gets uploaded mislabeled as .jpg and WordPress's
		// thumbnail generator re-encodes it as real JPEG, flattening transparency to black.
		fileExtension() {
			const url = this.selected.id ? this.selected[this.upload.quality] : ''
			const match = url.match(/\.(jpe?g|png|gif|webp)(?:\?|$)/i)
			return match ? match[1].toLowerCase().replace('jpeg', 'jpg') : 'jpg'
		},
		fileName() {
			return this.selected.id
				? this.selected.user.replace(' ', '-').toLowerCase() + '-' + this.selected.id + '-pixabay.' + this.fileExtension
				: ''
		},
		// Used by External.vue's "Saving..." progress overlay
		previewImageUrl() {
			return this.selected.webformatURL || ''
		},
		// Used by MediaContainer.vue's search box, kept source-specific so each external API can label its own
		serviceName() {
			return 'Pixabay'
		},
		searchInputId() {
			return 'search-pixabay'
		},
		searchPlaceholder() {
			return this.__('Search pixabay.com...', 'ml-slider')
		},
		searchFocusedEvent() {
			return 'metaslider/pixabay-search-focused'
		}
	},
	watch: {
		selected(photo) {
			this.upload.caption = photo.user ? this.sprintf(
				this.__('Photo by %s on Pixabay', 'ml-slider'),
				photo.user
			) : this.__('Photo on Pixabay', 'ml-slider')

			// The previously selected quality tier (e.g. fullHDURL/imageURL) may not exist on
			// this photo - fall back to largeImageURL, always present, rather than leave
			// upload.quality pointing at a field this photo doesn't have
			if (!this.qualityOptions.includes(this.upload.quality)) {
				this.upload.quality = 'largeImageURL'
			}
		},
		imageType() {
			this.loadFreshImages()
		}
	},
	mounted() {
		this.notifyInfo('metaslider/pixabay-tab-opened', this.__('Opening Pixabay tab...', 'ml-slider'))
		this.fetchFilters()
		this.loadFreshImages()
	},
	destroyed() {
		this.notifyInfo('metaslider/pixabay-tab-closed', this.__('Pixabay tab closed', 'ml-slider'))
	},
	methods: {
		orientation(photo) {
			return photo.imageWidth >= photo.imageHeight ? 'landscape' : 'portrait'
		},
		fetchFilters() {
			this.filters = [
				{ value: 'photo', label: this.__('Photos', 'ml-slider') },
				{ value: 'illustration', label: this.__('Illustrations', 'ml-slider') },
				{ value: 'vector', label: this.__('Vectors', 'ml-slider') },
				{ value: 'all', label: this.__('All types', 'ml-slider') }
			]
		},
		async getImages() {
			this.errorMessage = ''
			const { data } = await Pixabay.photos(this.page, this.searchTerm, 0, this.imageType)

			// If no photos were found, let them know
			if (!data.data.length) throw this.__('No photos found.', 'ml-slider')

			// Use this to avoid errors when duplicate images are being sent back
			data.data.forEach(photo => {
				this.photos.some(existingPhoto => {
					return existingPhoto.id === photo.id
				}) || this.photos.push(photo)
			})
		},
		loadFreshImages() {
			this.readyToLoad(false)
			this.page = 1
			this.photos = []
			this.selected = { id: null }
			this.getImages()
				.then(() => this.readyToLoad())
				.catch(error => {
					this.errorMessage = error
					this.loadingFresh = false
				})
		},
		async loadMore() {
			this.page++
			this.loadingMore = true

			// The UX feels clunky if the load is immediate
			await new Promise(resolve => setTimeout(resolve, 1000))
			this.getImages()
				.then(() => this.readyToLoad())
				.catch(() => {
					// Most likely there are no more images
					this.canLoadMore = false
				})
		},
		download() {
			return Pixabay.download(this.selected[this.upload.quality])
		},
		searchByTerm() {
			this.loadFreshImages()
		},
		readyToLoad(status = true) {
			this.canLoadMore = status
			this.loadingMore = !status
			this.loadingFresh = !status
		}
	}
}
</script>

<style lang="scss">
.ms-api-user {
	clear: both;
	border-bottom: 1px solid #ddd;
	display: flex;
	justify-content: flex-start;
	margin-bottom: 1rem;
	padding-bottom: 1rem;
	img {
		border-radius: 50%;
		width: 64px;
		height: 64px;
		min-width: 64px;
		margin-right: 1rem;
	}
	h3 {
		margin: 0;
		line-height: 1.1;
	}
	.ms-profile-data {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
	}
	.ms-profile-meta {
		display: flex;
		margin: 0;
		li {
			margin-right: 0.5em;
			margin-bottom: 0;
		}
	}
}
</style>
