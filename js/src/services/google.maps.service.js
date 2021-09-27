/* global _ATK_GOOGLE_VERSION_:true, */
import { Loader } from '@googlemaps/js-api-loader';

/**
 * Singleton class for handling google map api.
 */
class GoogleMapsService {
  static getInstance () {
    return this.instance;
  }

  constructor () {
    if (!this.instance) {
      this.instance = this;
      this.version = () => _ATK_GOOGLE_VERSION_;
      this.map = {
        api: null,
        loader: null
      };
    }
    return this.instance;
  }

  /**
   * Set map loader options.
   * @param $options
   */
  setMapLoader ($options) {
    if (!this.map.loader) {
      this.map.loader = new Loader($options);
    } else {
      console.warn('Loader already set with libraries: ', this.map.loader.libraries);
    }
  }

  /**
   * Get google using a callback.
   *
   * @param callback
   */
  loadGoogleApiCallback (callback = () => { console.log('load'); }, $options = {}) {
    if (!this.map.loader) {
      this.setMapLoader($options);
    }

    this.map.loader.loadCallback(callback);
  }

  /**
   * Get google api.
   * @returns {Promise}
   */
  loadGoogleApi ($options) {
    if (!this.map.loader) {
      this.setMapLoader($options);
    }

    return this.map.loader.load();
  }
}

const mapService = new GoogleMapsService();
Object.freeze(mapService);

export default mapService;
