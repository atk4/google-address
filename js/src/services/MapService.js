/* global _ATK_GOOGLE_VERSION_:true, */
import { Loader } from '@googlemaps/js-api-loader';

/**
 * Singleton class for handling google map api.
 */
class MapService {
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
    this.map.loader = new Loader($options);
  }

  /**
   * Get google using a callback.
   *
   * @param callback
   */
  loadGoogleApiCallback (callback = () => { console.log('load'); }) {
    this.map.loader.loadCallback(callback);
  }

  /**
   * Get google api.
   * @returns {Promise}
   */
  loadGoogleApi () {
    return this.map.loader.load();
  }
}

const mapService = new MapService();
Object.freeze(mapService);

export default mapService;
