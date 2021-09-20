import atk from 'atk';
import mapService from 'services/MapService';
import addressLookup from 'plugins/addressLookup';

if (typeof atk !== 'undefined') {
  // Register atkAddressLoukup as jQuery plugin.
  atk.registerPlugin('AddressLookup', addressLookup);

  if (!atk.mapService) {
    atk.mapService = mapService;
  }
}
