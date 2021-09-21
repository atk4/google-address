import atk from 'atk';
import mapService from './services/google.maps.service';
import addressLookupPlugin from './plugins/address-lookup.plugin';

if (typeof atk !== 'undefined') {
  // Register atkAddressLoukup as jQuery plugin.
  atk.registerPlugin('AddressLookup', addressLookupPlugin);

  if (!atk.mapService) {
    atk.mapService = mapService;
  }
}
