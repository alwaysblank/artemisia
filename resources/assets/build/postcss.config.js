/* eslint-disable */

const cssnanoConfig = {
  preset: ['default', { discardComments: { removeAll: true } }]
};

module.exports = ({ file, options }) => {
  return {
    parser: options.enabled.optimize ? 'postcss-safe-parser' : undefined,
    plugins: {
      autoprefixer: true,
      'postcss-import': {},
      'postcss-object-fit-images': {},
      'postcss-nth-child-fix': {},
      'autoprefixer': {},
      'postcss-custom-properties': {},
      'postcss-calc': {},
      'postcss-custom-media': {},
      'postcss-media-minmax': {},
      'postcss-custom-selectors': {},
      'postcss-color-function': {},
      'postcss-color-rgba-fallback': {},
      'pleeease-filters': {},
      'postcss-selector-not': {},
      'postcss-normalize': {},
      cssnano: options.enabled.optimize ? cssnanoConfig : false,
    },
  };
};
