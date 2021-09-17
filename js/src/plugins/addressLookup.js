import $ from 'jquery';
import mapService from '../services/MapService';

/**
 * Attach a Google map place autocomplete search
 * to a form input.
 *
 * Usage: $('input').atkAddressLookup(fieldMap:[])
 * Where fieldMap contains the input field in form that need to be
 * map with google address_component value from search input.
 *
 * if fieldMap is empty, then this will assume that form input field name
 * correspond to google address_component name.
 *
 * fieldMap example:
 *  "fieldMap":[
 *              {"address":
 *                  {
 *                   "concat":[
 *                             {"type":"street_number","property":"long_name"},
 *                             {"type":"route","property":"long_name"}
 *                            ],
 *                   "glue":" "
 *                  }
 *              },
 *              {"address2":
 *                  {
 *                  "concat":[
 *                            {"type":"locality","property":"long_name"}
 *                            ],
 *                  "glue":" "
 *                  }
 *             },
 *             {"country":
 *                {
 *                "concat":[
 *                          {"type":"country","property":"short_name"},
 *                          {"type":"postal_code","property":"long_name"}
 *                         ],
 *                "glue":" / "
 *             }
 *             ]
 *
 *   In this example,
 *    input name address in form to be fill by concatenating the value return by google address_component street_number and route, both using long_name property.
 *        ex: address = '4566 street name'
 *    input name address2 in form to be fill by using the value return by google address_component locality using long_name property.
 *        ex: address2 = 'london'
 *    input name country in form to be fill by concatenating the value return by google address_component country and postal_code but with country using short_name property and using "/" as glue.
 *        ex: country = 'UK / 34342'
 */
export default class addressLookup {

  constructor(element, options) {
    this.$el = $(element);
    this.$input = this.$el.find('input');
    this.settings = options;
    this.autocomplete = null;
    this.fields = [];
    this.main();
  }

  main()
  {
    // wait for google map api to be loaded prior to init.
    mapService.map.apiLoaded.then((r) => {
      this.initMap();
      this.$input.on('keydown', function(e) {
          if (e.keyCode === 13){
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
  initMap() {
    this.autocomplete = mapService.getAutocomplete(this.$input[0]);
    if (this.settings.options) {
      this.autocomplete.setOptions(this.settings.options);
    }
    if (this.settings.countryLimit) {
      this.autocomplete.setComponentRestrictions({country: this.settings.countryLimit});
    }
    this.autocomplete.setTypes(this.settings.types)
    if (this.settings.useBrowserLocation) {
      this.geoLocate();
    }
    this.autocomplete.addListener('place_changed', () => {
      this.setInputsValue(this.autocomplete.getPlace());
    });
  }

  /**
   * Collect field in form according to map settings.
   */
  initField() {
    let fields;
    if (this.settings.fieldMap.length === 0) {
      fields = this.getInputsField();
    } else {
      fields = this.getMappedFields(this.settings.fieldMap);
    }
    // remove ourself from the list.
    this.fields = fields.filter(field => field.name !== this.$input.attr('name'));

    console.log(this.fields);
  }

  /**
   * Will try to geoLocate the user via navigator geolocation
   * in order for autocomplete to look for address around user area first.
   * Require https.
   */
  geoLocate() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
        let geolocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        let circle = new google.maps.Circle({
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
  setInputsValue(place) {
    this.fields.forEach((field) => {
      field.input.val('');
    });

    //return if we do not have an address_components
    if (!place?.address_components){
      this.$input.val('');

      return;
    }
    // set field value according to their fieldMap.
    this.fields.forEach((field) => {
      field.input.val(this.getFieldValue(field.value, place));
    });

  }

  getFieldValue(value, place) {
    let fieldValue = '';
    value.def.forEach((comp, idx) => {
      fieldValue += (comp.type === 'lat' || comp.type === 'lng') ?
        this.getLatLngFromPlace(comp, place) :
        this.getAddressComponentFromPlace(comp, place);
      if (idx < value.def.length - 1 && value.glue) {
        fieldValue += value.glue;
      }
    });

    return fieldValue;
  }

  getAddressComponentFromPlace(comp, place) {
    const addressComponents = place.address_components.filter(acomp => acomp.types.includes(comp.type));
    const value = addressComponents[0]?.[comp.prop];

    return value ? value : '';
  }

  getLatLngFromPlace(comp, place) {
    return place.geometry.location[comp.type]();
  }
  /**
   * Map form field with google address_component value
   * according to fieldMap settings.
   *
   * @param controls
   * @returns {*}
   */
  getMappedFields(controls) {
    return controls.map(control => {
      return {input: this.$el.parents(this.settings.formSelector).find('input[name="' + control.name + '"]'), ...control};
    });
  }

  /**
   * Return an array of all fields in form. This array contains information
   * on each field associated with
   * their google map property.
   *
   * This is normally use when field name in form directly correspond to Google map property name.
   *
   *
   * @returns {{name: string, input: JQuery | jQuery | HTMLElement}[]}
   */
  getInputsField() {
     return Array.from(this.$el.parents(this.settings.formSelector).find('input'), (input) => {
       return {
         input: $(input),
         name: $(input).attr('name'),
         value: {
           def: [{type: $(input).attr('name'), prop: this.settings.useLongName ? 'long_name' : 'short_name'}]
         }
       }
     });
  }
}

addressLookup.DEFAULTS = {
  options: null,
  formSelector: 'div.ui.form',
  types: ['address'],
  useBrowserLocation: true,
  countryLimit: null,
  useLongName: true,
  fieldMap: [],
  glue: ' ',
};
