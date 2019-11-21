var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .addStyleEntry('global', './public/scss/application.scss')
    .addLoader({ test: /\.scss$/, loader: 'import-glob-loader' })
    .enableSassLoader(function (options) {
        options.includePaths = [
            'public'
        ];
        options.outputStyle = 'expanded';
    })
    .autoProvidejQuery()
    .cleanupOutputBeforeBuild()
    .enableSingleRuntimeChunk()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();
