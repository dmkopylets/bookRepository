import 'bootstrap/dist/css/bootstrap.min.css';

Encore
    .addStyleEntry('assets/css/app', [
        './assets/css/app.css', // Your app's CSS
        'node_modules/bootstrap/dist/css/bootstrap.min.css' // Bootstrap CSS
    ]);

// any CSS you import will output into a single css file (app.css in this case)

import '../css/app.css';
