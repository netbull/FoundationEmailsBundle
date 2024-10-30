const Encore = require('@symfony/webpack-encore');
const { exec } = require('child_process');

// Use legacy OpenSSL provider for Node.js 17+ compatibility
process.env.NODE_OPTIONS = "--openssl-legacy-provider";

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  // directory where compiled assets will be stored
  .setOutputPath('src/Resources/build')
  // public path used by the web server to access the output path
  .setPublicPath('/build')
  // only needed for CDN's or sub-directory deploy
  //.setManifestKeyPrefix('build/')

  /*
   * ENTRY CONFIG
   *
   * Add 1 entry for each "page" of your app
   * (including one that's included on every page - e.g. "app")
   *
   * Each entry will result in one JavaScript file (e.g. app.js)
   * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
   */
  .addStyleEntry('email', './src/Resources/scss/email.scss')

  // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
  .splitEntryChunks()

  // will require an extra script tag for runtime.js
  // but, you probably want this, unless you're building a single-page app
  .enableSingleRuntimeChunk()

  /*
   * FEATURE CONFIG
   *
   * Enable & configure other features below. For a full
   * list of features, see:
   * https://symfony.com/doc/current/frontend.html#adding-more-features
   */
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning(false)

  // enables @babel/preset-env polyfills
  .configureBabel(() => {}, {
    useBuiltIns: 'usage',
    corejs: 3
  })

  // enables Sass/SCSS support with Dart Sass explicitly defined
  .enableSassLoader((options) => {
    options.implementation = require('sass'); // Explicitly use Dart Sass
    options.sourceMap = !Encore.isProduction(); // Enable source maps only in development
  })
  .addPlugin({
    apply: (compiler) => {
      compiler.hooks.done.tap('RenderTemplatesPlugin', () => {
        // Trigger the render command when SCSS or Inky files change
        exec('php bin/console netbull:emails:render', (err, stdout, stderr) => {
          if (err) {
            console.error(`Error: ${stderr}`);
            return;
          }
          console.log(`Templates re-rendered: ${stdout}`);
        });
      });
    }
  });

module.exports = Encore.getWebpackConfig();
