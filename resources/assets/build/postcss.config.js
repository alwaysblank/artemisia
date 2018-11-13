/* eslint-disable */
const cleancss = require('postcss-clean');
const postcssPresetEnv = require('postcss-preset-env');

const cleancssConfig = {
  level: {
    1: {
      all: true,
      specialComments: 0
    },
    2: {
      all: false,
      removeDuplicateRules: true
    }
  }
};

module.exports = ({ file, options }) => {
  return {
    parser: options.enabled.optimize ? 'postcss-safe-parser' : undefined,
    plugins: [
      postcssPresetEnv(),
      cleancss(options.enabled.optimize ? cleancssConfig : false)
    ]
  };
};
