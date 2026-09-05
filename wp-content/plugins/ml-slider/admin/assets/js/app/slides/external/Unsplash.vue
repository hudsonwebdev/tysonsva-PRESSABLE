<template>
	<media-container>
		<!-- TODO: add loading -->

		<template slot="media-list">
			<li
				v-for="photo in photos"
				:aria-label="sprintf(__('Photo by %s', 'ml-slider'), photo.user.name)"
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
					:class="photo.orientation"
					class="attachment-preview js--select-attachment type-image subtype-jpeg">
					<div class="thumbnail">
						<div class="centered">
							<img
								:alt="sprintf(__('Photo by %s', 'ml-slider'), photo.user.name)"
								:src="photo.urls.thumb"
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
								:src="selected.urls.small"
								:alt="fileName"
								draggable="false">
						</div>
						<div class="details">
							<div class="filename">{{ fileName }}</div>
							<div class="dimensions">
								{{ sprintf(
									_x('%s by %s pixels', '1000 by 1000 pixels', 'ml-slider'),
									selected.width,
									selected.height
								) }}
							</div>
							<a
								:href="selected.links.html"
								target="_blank">{{ __('view original', 'ml-slider') }}</a>
						</div>
					</div>
					<div class="ms-api-user">
						<img
							class="rtl:mr-0 rtl:ml-4"
							:src="selected.user.profile_image.medium">
						<div class="ms-profile-data">
							<div class="ms-profile-details">
								<h3>{{ selected.user.name }}</h3>
								<p class="ms-user-location">{{ selected.user.location }}</p>
								<div
									v-if="selected.user.bio"
									class="ms-user-bio">{{ selected.user.bio }}</div>
							</div>
							<ul class="ms-profile-meta">
								<li v-if="selected.user.username">
									<a
										:href="selected.user.links.html"
										:title="selected.user.links.html"
										target="_blank"
										class="ms-profile-username">{{ __('Profile', 'ml-slider') }}
									</a>
								</li>

								<li
									v-if="selected.user.portfolio_url"
									class="ms-user-portfolio-url">
									<a
										:href="selected.user.portfolio_url"
										:title="selected.user.portfolio_url"
										target="_blank">{{ __('Portfolio', 'ml-slider') }}
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
				{{ __('All photos published on Unsplash can be used for free.', 'ml-slider') }} <a
					target="_blank"
					href="https://unsplash.com/license">{{ __('view license', 'ml-slider') }}</a>
			</p>
		</template>
	</media-container>
</template>

<script>
import { Unsplash } from '../../api'
import MediaContainer from './MediaContainer.vue'

// WordPress's own big_image_size_threshold default - matched here so 'optimized' downloads
// something WordPress won't immediately scale down again (#2365)
const OPTIMIZED_MAX_SIZE = 2560

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
			filters: {},
			mediaButton: {},
			// 'raw'/'full' dropped - both can exceed what WordPress can actually process (up to
			// ~49MP), and WordPress would just scale them back down to 2560px anyway
			// (big_image_size_threshold, #2365) - 'optimized' already covers that ceiling, so
			// the only other option worth offering is Unsplash's own smaller 'regular' preset
			qualityOptions: ['optimized', 'regular'],
			upload: {
				title: '',
				caption: '',
				alt: '',
				description: '',
				// Global default from the Settings & Help page, falling back to 'optimized'
				quality: (window.metaslider_api && window.metaslider_api.unsplashImageQuality) || 'optimized'
			}
		}
	},
	computed: {
		// 'optimized' is a max width/height we set ourselves (see optimizedUrl()), so that number
		// is exact. 'regular' is Unsplash's own documented fixed preset (1080px wide) - also exact.
		qualityLabels() {
			return {
				optimized: this.sprintf(this.__('Optimized - up to %spx (recommended)', 'ml-slider'), OPTIMIZED_MAX_SIZE),
				regular: this.sprintf(this.__('Regular - %spx', 'ml-slider'), 1080)
			}
		},
		fileName() {
			// Not sure if we can get the real file name without a second call on the photo id. likely not important
			return this.selected.id
				? this.selected.user.name.replace(' ', '-').toLowerCase() + '-' + this.selected.id + '-unsplash.jpg'
				: ''
		},
		// Used by External.vue's "Saving..." progress overlay
		previewImageUrl() {
			return this.selected.urls ? this.selected.urls.regular : ''
		},
		// Used by MediaContainer.vue's search box, kept source-specific so each external API can label its own
		serviceName() {
			return 'Unsplash'
		},
		searchInputId() {
			return 'search-unsplash'
		},
		searchPlaceholder() {
			return this.__('Search unsplash.com...', 'ml-slider')
		},
		searchFocusedEvent() {
			return 'metaslider/unsplash-search-focused'
		}
	},
	watch: {
		selected(photo) {
			this.upload.caption = photo.user ? this.sprintf(
				this.__('Photo by %s on Unsplash', 'ml-slider'),
				photo.user.name
			) : this.__('Photo on Unsplash', 'ml-slider')
		}
	},
	mounted() {
		this.notifyInfo('metaslider/unsplash-tab-opened', this.__('Opening Unsplash tab...', 'ml-slider'))
		this.loadFreshImages()
	},
	destroyed() {
		this.notifyInfo('metaslider/unsplash-tab-closed', this.__('Unsplash tab closed', 'ml-slider'))
	},
	methods: {
		async getImages() {
			this.errorMessage = ''
			const { data } = await Unsplash.photos(this.page, this.searchTerm)

			// If no photos were found, let them know
			if (!data.data.length) throw this.__('No photots found.', 'ml-slider')

			// Use this to avoid errors when duplicate images are being sent back
			data.data.forEach(photo => {
				this.photos.some(existingPhoto => {
					return existingPhoto.id === photo.id
				}) || this.photos.push(photo)
			})
			// this.photos.push(...data.data)
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
					this.throwError(error)
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
			const url = this.upload.quality === 'optimized'
				? this.optimizedUrl(this.selected.urls.raw)
				: this.selected.urls[this.upload.quality]
			return Unsplash.download(url, this.selected.id)
		},
		// urls.raw is imgix-backed with no size limit baked in - originals can be up to ~49MP,
		// which can exhaust ImageMagick's memory during WordPress's own thumbnail generation
		// (#2365). Capping it here costs nothing visually since WordPress would scale anything
		// bigger than 2560px down anyway (big_image_size_threshold).
		optimizedUrl(rawUrl) {
			const url = new URL(rawUrl)
			url.searchParams.set('w', OPTIMIZED_MAX_SIZE)
			url.searchParams.set('h', OPTIMIZED_MAX_SIZE)
			url.searchParams.set('fit', 'max')
			return url.toString()
		},
		fetchFilters() {
			// TODO: Call to get available filters (pro override with more?)
			// Not sure how to do this. Maybe we can offer 10 common words?
			// or grab the user's categories?
			this.filters = {}
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
	.ms-user-location {
		color: #aaa;
		font-size: 0.9em;
		margin: 0;
		line-height: 1.3;
	}
	.ms-user-bio {
		margin-top: 0.3em;
		line-height: 1.1;
		font-size: 1em;
		margin-bottom: 0.4rem;
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
