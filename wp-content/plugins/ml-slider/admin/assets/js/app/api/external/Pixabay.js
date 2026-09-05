import Axios from 'axios'
import { EventManager } from '../../utils'

// Since this uses an external api we need a new instance
const api = Axios.create({
	baseURL: 'https://connect.metaslider.com/wp-json/pixabay/v1/'
})

const Pixabay = {
	photos(page = 1, search = '', nocache = 0, imageType = 'photo') {

		if (search) {
			return this.searchPhotos(page, search, imageType)
		}

		return api.get('images/all', {
			params: { page: page, nocache: nocache, image_type: imageType }
		})
	},
	searchPhotos(page = 1, search = '', imageType = 'photo') {
		return api.post('images/search', {
			page: page,
			search: search,
			image_type: imageType
		})
	},
	// Unlike Unsplash, Pixabay doesn't require a separate "register the download" call
	async download(url) {
		return Axios.get(url, {
			responseType: 'blob',
			onDownloadProgress: progressEvent => {

				// Leave the last 20% for the final confirmation from the server
				let percentage = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)) - 20
				EventManager.$emit('metaslider/external-api-percentage', {
					percentage: percentage > 1 ? percentage : 1
				})
			}
		})
	}
}

export default Pixabay
