import Vue from 'vue';
import Vuetify from 'vuetify/lib/framework';

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        themes: {
          light: {
            primary: '#d55a3a',
            secondary: '#30595f',
            accent: '#f6d860',
            error: '#b71c1c',
            yellow: '#f6d860',
            red: "#e73c0e",
            green: "#48f448",
            often: '#28A745',
            rarely:"#dc3545",
            sometimes:"#F6D860"
          },
        },
      },
});
