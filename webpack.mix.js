const mix = require('laravel-mix');
const fs = require('fs');
const path = require('path');

const moduleNames = fs.readdirSync(path.join(__dirname, 'Modules'));

require('laravel-mix-merge-manifest');

function removeMixManifest() {
    const manifestPath = path.join(__dirname, 'public/mix-manifest.json');

    if (fs.existsSync(manifestPath)) {
        fs.unlinkSync(manifestPath);
    }
}

removeMixManifest();

moduleNames.forEach((moduleName) => {
    const sourceDir = path.join(__dirname, 'resources/js/components/layout');
    const destDir = path.join(__dirname, 'Modules', moduleName, 'Resources/assets/js/components/layout');
    const destPublicDir = path.join(__dirname, 'public', 'Modules', moduleName, 'Resources/assets/js/components/layout');

    mix.copyDirectory(sourceDir, destDir);
    mix.copyDirectory(sourceDir, destPublicDir);
});

mix.mergeManifest();

mix.js('resources/js/app.js', 'public/js')
    .react()
    .copyDirectory('resources/img', 'public/img')
    .copyDirectory('Modules/Products/Resources/assets/img', 'public/img')
    .sass('resources/sass/app.scss', 'public/css')
    .webpackConfig({
        resolve: {
            alias: {
                bmslibs: path.resolve(__dirname, 'resources/js/components/libs'),
            },
        },
    });