import $ from 'jquery';
import atk from 'atk';

/**
 * Attach a Google map place autocomplete search
 * to a form input.
 *
 * Usage: $('input').atkAddressLookup(fieldMap:[])
 * Where fieldMap contains the input field in form that need to be
 * set with google address_component value from search input.
 *
 * if fieldMap is empty, then assumption is made that form input control name
 * correspond to google address_component name.
 *
 * fieldMap example:
 * "fieldMap": [{
 *     "name": "address",
 *     "value": {
 *      "def": [{"type": "street_number", "prop": "long_name"}, {"type": "route", "prop": "long_name"}],
 *      "glue": " / "
 *    }
 *  },
 *  {
 *    "name": "address2",
 *    "value": {
 *    "def": [{"type": "administrative_area_level_2", "prop": "long_name"}]}
 *  }]
 *
 */
export default class addressLookupPlugin {
  constructor (element, options) {
    this.$el = $(element);
    this.$input = this.$el.find('input');
    this.settings = options;
    this.autocomplete = null;
    this.fields = [];
    this.main();
  }

  main () {
    atk.mapService.loadGoogleApi().then((google) => {
      this.initAutocomplete(google);
      this.$input.on('keydown', function (e) {
        if (e.keyCode === 13) {
          e.preventDefault();
          e.stopPropagation();
        }
      });
    });

    this.initField();
  }

  /**
   * Initialize lookup input to Google autocomplete.
   */
  initAutocomplete (google) {
    this.autocomplete = new google.maps.places.Autocomplete(this.$input[0]);
    if (this.settings.options) {
      this.autocomplete.setOptions(this.settings.options);
    }
    if (this.settings.countryLimit) {
      this.autocomplete.setComponentRestrictions({ country: this.settings.countryLimit });
    }
    this.autocomplete.setTypes(this.settings.types);
    if (this.settings.useBrowserLocation) {
      this.geoLocate();
    }
    this.autocomplete.addListener('place_changed', () => {
      this.setInputsValue(this.autocomplete.getPlace());
      if (this.settings.autoClear) {
        this.$input.val('');
      }
      this.settings.callback(this.autocomplete);
    });
  }

  /**
   * Collect field in form according to map settings.
   */
  initField () {
    const inputs = this.getInputsField().map(input => {
      const map = this.settings.fieldMap.filter(field => field.name === input.name);
      if (map.length) {
        input.value = map[0].value;
      }

      return input;
    });
    // remove ourself from the list.
    this.fields = inputs.filter(input => input.name !== this.$input.attr('name'));
  }

  /**
   * Will try to geoLocate the user via navigator geolocation
   * in order for autocomplete to look for address around user area first.
   * Require https.
   */
  geoLocate () {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
        const geolocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        const circle = new google.maps.Circle({
          center: geolocation,
          radius: position.coords.accuracy
        });
        this.autocomplete.setBounds(circle.getBounds());
      });
    }
  }

  /**
   * Fill location field in form.
   *
   * @param place A google place object.
   */
  setInputsValue (place) {
    this.fields.forEach((field) => {
      field.input.val('');
    });

    // return if we do not have an address_components
    if (!place?.address_components) {
      this.$input.val('');

      return;
    }
    // set field value according to their fieldMap.
    this.fields.forEach((field) => {
      field.input.val(this.getFieldValue(field.value, place));
    });
  }

  getFieldValue (value, place) {
    let fieldValue = '';
    value.def.forEach((comp, idx) => {
      fieldValue += (comp.type === 'lat' || comp.type === 'lng')
        ? this.getLatLngFromPlace(comp, place)
        : this.getAddressComponentFromPlace(comp, place);
      if (idx < value.def.length - 1 && value.glue) {
        fieldValue += value.glue;
      }
    });

    return fieldValue;
  }

  getAddressComponentFromPlace (comp, place) {
    const addressComponents = place.address_components.filter(acomp => acomp.types.includes(comp.type));
    const value = addressComponents[0]?.[comp.prop];

    return value || '';
  }

  getLatLngFromPlace (comp, place) {
    return place.geometry.location[comp.type]();
  }

  /**
   * Map form control with google address_component value
   * according to fieldMap settings.
   *
   * @param controls
   * @returns {*}
   */
  getMappedFields (controls) {
    return controls.map(control => {
      return { input: this.$el.parents(this.settings.formSelector).find('input[name="' + control.name + '"]'), ...control };
    });
  }

  /**
   * Return an array of all controls in form.
   *
   * This is normally use when field name in form directly correspond to Google map property name.
   *
   */
  getInputsField () {
    const all = this.$el.parents(this.settings.formSelector).find('input').filter((idx, input) => {
      // include inputs wich need to be map.
      if (this.settings.fieldMap.filter(field => field.name === input.getAttribute('name')).length > 0) {
        return true;
      }
      // include supported default inputs.
      if (this.settings.supportFields.filter(fieldName => fieldName === input.getAttribute('name')).length > 0) {
        return true;
      }
      return false;
    });
    return Array.from(all, (input) => {
      return {
        input: $(input),
        name: $(input).attr('name'),
        value: {
          def: [{ type: $(input).attr('name'), prop: this.settings.useLongName ? 'long_name' : 'short_name' }]
        }
      };
    });
  }
}

addressLookupPlugin.DEFAULTS = {
  options: null, // the google autocomplete options.
  formSelector: 'div.ui.form',
  callback: (ac) => {},
  types: ['address'],
  useBrowserLocation: true,
  countryLimit: null,
  useLongName: true,
  fieldMap: [],
  glue: ' ',
  autoClear: true,
  supportFields : [
    'lat',
    'lng',
    'street_number',
    'route',
    'country',
    'administrative_area_level_1',
    'administrative_area_level_2',
    'administrative_area_level_3',
    'administrative_area_level_4',
    'administrative_area_level_5',
    'colloquial_area',
    'locality',
    'sublocality',
    'sublocality_level_1',
    'sublocality_level_2',
    'sublocality_level_3',
    'sublocality_level_4',
    'sublocality_level_5',
    'neighborhood',
    'premise',
    'subpremise',
    'plus_code',
    'postal_code',
    'natural_feature',
    'airport',
    'park',
    'point_of_interest',
    'floor',
    'establishment',
    'landmark',
    'parking',
    'post_box',
    'postal_town',
    'room',
    'street_address',
    'bus_station',
    'train_station',
    'transit_station',
  ],
};
