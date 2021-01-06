<template>
  <!-- template ref : https://bootstrap-vue.org/docs/components/form-select -->
  <select class="mb-3 custom-select" v-model="selected">
    <option :value="null">-- Please select an option --</option>
    <optgroup v-for="row in options" :label="`${row.kode} - ${row.nama}`">
      <option v-for="child in row.children" :value="child.id">{{row.kode}}.{{child.kode}} - {{child.nama}}</option>
    </optgroup>
  </select>
</template>

<script>
module.exports = {
  name: "listbox-kementerian-select",
  props: {
    getUrl: {
      type: String,
      default: 'index'
    },
    cmd: {
      type: String,
      default: 'getRef'
    },
    presetKlId: {
      type: Number,
      default: 62
    },
    value: {
      type: Number,
      default: null
    }
  },
  methods: {
    getData(id=this.presetKlId) {
      const url = `${this.getUrl}/${this.cmd}/${id}`;

      axios.get(url)
          .then(resp => {
            if (!resp.data.hasOwnProperty('class')) {
              this.options = resp.data;
            } else {
              eventBus.$emit('openNotif', resp.data);
            }
          })
          .catch(e => console.log(e))
    },
  },
  data() {
    return {
      options: [],
      selected: null
    }
  },
  filters: {
    title(string) {
      if(string.length > 64) {
        return string.substring(0, 64) + '...';
      }
      return string;
    }
  },
  created() {
    this.getData();
  },
  watch: {
    selected() {
      this.$emit('input', parseInt(this.selected));
    }
  }
}
</script>

<style scoped>
</style>
