module.exports = {
  darkMode: 'class',
  theme: {
    container: {
      center: true,
    },
    colors: {
      transparent: 'transparent',
      current: 'currentColor',

      bg: '#212121',

      surface: {
        1: '#262626',
        2: '#2e2e2e',
      },

      border: '#3a3a3a',

      text: {
        primary: '#f2f2f2',
        secondary: '#c9c9c9',
        muted: '#9a9a9a',
      },

      primary: {
        DEFAULT: '#e25507',
        hover: '#ff6a1a',
        soft: 'rgba(226, 85, 7, 0.15)',
      },

      success: '#4ade80',
      warning: '#facc15',
      error: '#f87171',
    },

    /* TIPOGRAFIA */
    fontFamily: {
      serif: ['"Source Serif 4"', 'Georgia', 'serif'],
      sans: ['Inter', 'system-ui', 'sans-serif'],
      mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
    },
  },
}
