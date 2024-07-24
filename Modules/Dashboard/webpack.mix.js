const dotenvExpand = require("dotenv-expand");
dotenvExpand(
    require("dotenv").config({ path: "../../.env" /*, debug: true*/ })
);

const mix = require("laravel-mix");
require("laravel-mix-merge-manifest");
const path = require("path");

mix.setPublicPath("../../public").mergeManifest();

mix.js(__dirname + "/Resources/assets/js/app.js", "js/dashboard.js")
    .sass(__dirname + "/Resources/assets/sass/app.scss", "public/css")
    .webpackConfig({
        resolve: {
            alias: {
                bmslibs: path.resolve(
                    __dirname,
                    "./../../resources/js/components/libs"
                ),
            },
        },
    });

if (mix.inProduction()) {
    mix.version();
}
