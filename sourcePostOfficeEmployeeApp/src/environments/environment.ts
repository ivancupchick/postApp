// This file can be replaced during build by using the `fileReplacements` array.
// `ng build --prod` replaces `environment.ts` with `environment.prod.ts`.
// The list of file replacements can be found in `angular.json`.

const PHP_API_SERVER = 'http://127.0.0.1:80';
const PHP_API_SERVER_MAMP = 'http://localhost:8888';

export const environment = {
  production: false,
  url: PHP_API_SERVER_MAMP
  // firebase: {
  //   apiKey: 'AIzaSyDUy4tEk8ieeS56V9PCpx-ehlCFOgcMTb8',
  //   authDomain: 'testforpostoffice.firebaseapp.com',
  //   databaseURL: 'https://testforpostoffice.firebaseio.com',
  //   projectId: 'testforpostoffice',
  //   storageBucket: 'testforpostoffice.appspot.com',
  //   messagingSenderId: '137622054041',
  //   appId: '1:137622054041:web:032fcdacdcb5113cea4408'
  // }
};

/*
 * For easier debugging in development mode, you can import the following file
 * to ignore zone related error stack frames such as `zone.run`, `zoneDelegate.invokeTask`.
 *
 * This import should be commented out in production mode because it will have a negative impact
 * on performance if an error is thrown.
 */
// import 'zone.js/dist/zone-error';  // Included with Angular CLI.
