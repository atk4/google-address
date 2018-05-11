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
 * correspond to google address_compoenent name.
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
    const that = this;
    // wait for google map api to be loaded prior to init.
    mapService.map.apiLoaded.then(function(r){
      //intit map
      that.initMap();
      // init input and prevent form submission when using enter.
      // will try to geolocate user on focus.
      if (that.settings.useBrowserLocation) {
        that.$input.on('focus', that, that.geolocate);
      }
      that.$input.on('keydown', function(e) {
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
    this.autocomplete.addListener('place_changed', () => {
      this.fillAddress(this.autocomplete.getPlace());
    });
  }

  /**
   * Collect field in form according to map settings.
   */
  initField() {
    const that = this;
    if (this.settings.fieldMap.length === 0) {
      this.fields = this.getInputsField();
    } else {
      this.fields = this.getMappedFields(this.settings.fieldMap);
    }

    if (this.fields.length > 0) {
      // remove search field from list.
      let idx = this.fields.findIndex(function(field){
        if (field.input.attr('name') === that.$input.attr('name')) {
          return field;
        }
      });
      if (idx >= 0) {
        this.fields.splice(idx, 1);
      }
    }
  }

  /**
   * Will try to geolocate the user via navigator geolocation
   * in order for autocomplete to look for address around user area first.
   *
   * @param plugin This plugin.
   */
  geolocate(plugin) {
    const that = plugin.data;
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        let geolocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        let circle = new google.maps.Circle({
          center: geolocation,
          radius: position.coords.accuracy
        });
        that.autocomplete.setBounds(circle.getBounds());
      });
    }
  }

  /**
   * Fill location field in form.
   *
   * @param place A google place object.
   */
  fillAddress(place) {
    const that = this;
    //clear all fields;
    this.fields.forEach(function(field){
      field.input.val('');
    });

    //return if we do not have an address_compoents
    if (!place.hasOwnProperty('address_components')){
      this.$input.val('');
      return;
    }

    // set field value according to their fieldMap.
    this.fields.forEach(function(field){
      //let cumValue = field.value;
      let value = field.concat.reduce(function(acc, item, idx){
        let temp = '';
        //let glue = item.hasOwnProperty('glue') ? item.glue : that.settings.glue;
        if (item.type === 'lat' || item.type === 'lng') {
          temp = place.geometry.location[item.type]();
        } else {
          for (let i = 0; i < place.address_components.length; i++) {
            let addressType = place.address_components[i].types[0];
            // if field match then insert value.
            if (item.type === addressType) {
              temp = place.address_components[i][item.property];
            }
          }
        }
        if (acc !== '' && idx > 0) {
          temp = (temp === '') ? acc : acc + field.glue + temp;
          //temp = acc + field.glue + temp;
        }
        return temp;
      }, '');
      field.input.val(value);
    });

  }

  /**
   * Map form field with google address_component value
   * according to fieldMap settings.
   *
   * @param fieldMap
   * @returns {*}
   */
  getMappedFields(fieldMap) {
    const that = this;
    return fieldMap.map(function(field){
      let inputName = Object.keys(field)[0];
      let glue = field[inputName].hasOwnProperty('glue') ? field[inputName].glue : that.settings.glue;
      let $input = that.$el.parents('form').find('input[name="' + inputName + '"]');
      return {name: inputName, input: $input, concat: field[inputName].concat, glue: glue};
    });
  }

  /**
   * Return an array of all fields in form. This array contains information
   * on each field associated with
   * their google map property.
   *
   * This is normaly use when field name in form directly correspond to goolge map property name.
   *
   *
   * @returns {{name: string, input: JQuery | jQuery | HTMLElement}[]}
   */
  getInputsField() {
    const that = this;
     return Array.from(this.$el.parents('form').find('input'), function(item){
       const $input = $(item);
       const inputName = $input.attr('name');
       let obj = { name : inputName,
         input: $input,
         glue: '',
       }
       //set lookup value for this field.
       let arrayConcat = [];
       arrayConcat.push({type: inputName, property: that.settings.useLongName ? 'long_name' : 'short_name'});
       obj.concat = arrayConcat;
       return obj;
     });
  }
}

addressLookup.DEFAULTS = {
  options: null,
  types: ['geocode'],
  useBrowserLocation: false,
  countryLimit: null,
  useLongName: true,
  fieldMap: [],
  glue: ' ',
};
