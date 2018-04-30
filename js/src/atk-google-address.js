import atk from 'atk';
import mapService from 'services/MapService';
import addressLookup from 'plugins/addressLookup';

// Register atkAddressLoukup as jQuery plugin.
atk.registerPlugin('AddressLookup', addressLookup);
// Add mapService to atk object.
atk.mapService = mapService;
