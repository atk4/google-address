/**
 * Singleton class for handling google map api.
 */

class MapService {

  static getInstance() {
    return this.instance;
  }

  constructor() {
    if (!this.instance) {
      this.instance = this;
      this.map = {};
    }
    return this.instance;
  }

  /**
   * Callback associate with the loading of google api.
   * Resolve a Promise when callback is executed.
   * You can then place your init function inside apiLoaded then() method.
   *
   * ex: in order to check for api to be fully loaded prior to fire things up:
   *    mapService.map.apiLoaded.then(function(){
   *       //Api is loaded and ready, do your stuff.
   *    });
   *
   */
  initGoogleApi() {
    this.map.apiLoaded = Promise.resolve(true);
  }

  /**
   * Return an autocomplete field.
   *
   * @param el The html input element to associate autocomplete with.
   * @returns {google.maps.places.Autocomplete}
   */
  getAutocomplete(el) {
    return new google.maps.places.Autocomplete(el);
  }
}

let mapService = new MapService();
Object.freeze(mapService);

export default mapService;
