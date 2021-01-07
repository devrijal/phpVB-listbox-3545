<template>
  <!-- template ref : cubebs4/ui-nestable.html -->
  <div>
    <div class="row">
      <div :class="colClass">
        <div class="row">
          <div class="col-md-auto">
            <span @click="toggleList" class="pointer">
              {{label}} <i :class="{'fa fa-caret-down': !listToggle, 'fa fa-caret-up': listToggle}"></i></span>
          </div>
        </div>
        <div class="row">
          <div class="col-md-auto">
            <span v-for="(item, index) in selected" class="badge badge-info badge-pill" v-b-tooltip.hover
                  :title="item.nama" v-if="selected.length > 0">
              {{selectedTitle(item)}} <i class="fa fa-close pointer" @click="remove(item, index)"></i></span>
            <span v-else @click="toggleList"> -- Click to select option -- </span>
          </div>
        </div>


      </div>
    </div>
    <b-collapse id="collapse-lbk">
      <div class="row cf nestable-list">
        <div class="dd" :class="colClass" id="nestable">

          <ol class="dd-list">
            <li class="dd-item" v-if="options.length > 0" v-for="(row, i) in options"
                :class="{'dd-collapsed': !expanded.includes(row.id)}">
              <button :class="expanded.includes(row.id) ? 'dd-collapse' : 'dd-expand'" type="button"
                      @click="toggle(row.id, i)">
                {{ expanded.includes(row.id) ? 'Expand' : 'Collapse' }}</button>
              <div class="dd-handle" v-b-tooltip.hover.right :title="`${row.kode} - ${row.nama}`" @click="select(row)">
                {{row.kode}} - {{row.nama | title}}
                <div class="nested-links">
                  <span class="badge" v-if="selected.findIndex(item => item.id===row.id) > -1"><i class="fa fa-check" style="color:green"></i></span>
                  <span class="badge">{{row.children_count}}</span>
                </div>
              </div>

              <ol class="dd-list" v-if="row.hasOwnProperty('children') && row.children.length > 0"
                  v-for="(child, p2i) in row.children">

                <li class="dd-item dd-collapsed" data-id="5">
                  <!--              <button :class="expanded.includes(child.id) ? 'dd-collapse' : 'dd-expand'" type="button"-->
                  <!--                      @click="toggle(child.id, [i, p2i])">-->
                  <!--                {{ expanded.includes(child.id) ? 'Expand' : 'Collapse' }}</button>-->
                  <div class="dd-handle" v-b-tooltip.hover.right :title="`${row.kode}.${child.kode} - ${child.nama}`"
                       @click="select(child)">
                    {{row.kode}}.{{child.kode}} - {{child.nama | title}}
                    <div class="nested-links">
                  <span class="badge" v-if="selected.findIndex(item => item.id===child.id) > -1">
                    <i class="fa fa-check" style="color:green"></i></span>
                      <span class="badge">{{child.children_count}}</span>
                    </div>
                  </div>
                </li>
              </ol>

            </li>
          </ol>
        </div>
      </div>
    </b-collapse>

  </div>

</template>

<script>
module.exports = {
  name: "listbox-kementerian",
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
    colClass: {
      type: String,
      default: 'col-md-12'
    },
    value: {
      type: Array,
      default: null
    },
    label: {
      type : String,
      default: 'Kementerian'
    }
  },
  methods: {
    getData(id=this.presetKlId, index=0) {
      const url = `${this.getUrl}/${this.cmd}/${id}`;

      axios.get(url)
          .then(resp => {
            if (!resp.data.hasOwnProperty('class')) {
              if (id === this.presetKlId) {
                this.options = resp.data;
              } else {
                if (!Array.isArray(index)) {
                  this.$set(this.options[index], 'children', resp.data);
                } else {
                  this.$set(this.options[index[0]].children[index[1]], 'children', resp.data);
                }
                this.expanded.push(id)
              }
            } else {
              eventBus.$emit('openNotif', resp.data);
            }
          })
          .catch(e => console.log(e))
    },
    toggle(id, index) {
      if (!Array.isArray(index)) {
        const row = this.options[index];
        if (row.hasOwnProperty('children') && Array.isArray(row.children)) {
          const expindex = this.expanded.indexOf(row.id);
          if (expindex > -1) {
            this.expanded.splice(expindex, 1);
          } else {
            this.expanded.push(row.id)
          }
        } else {
          this.getData(id, index);
        }

      } else {
        const row = this.options[index[0]].children[index[1]];
        if (row.hasOwnProperty('children') && Array.isArray(row.children)) {
          const expindex = this.expanded.indexOf(row.id);
          if (expindex > -1) {
            this.expanded.splice(expindex, 1);
          } else {
            this.expanded.push(row.id)
          }
        } else {
          this.getData(id, index);
        }
      }
    },
    toggleList() {
      this.listToggle =!this.listToggle;
      this.$root.$emit('bv::toggle::collapse', 'collapse-lbk')
    },
    select(item) {
      const index = this.selected.findIndex(row => item.id===row.id);

      if (index > -1) {
        const notification = {
          "class" : "warning",
          "callback":"infoSnackbar",
          "notification": "Item already selected"
        }
        eventBus.$emit('openNotif', notification);
      } else {
        this.selected.push(item)
      }
    },
    remove(item, index) {
      this.selected.splice(index, 1)
    },
    selectedTitle(item) {
      let title;
      if (parseInt(item.parent_id) !== this.presetKlId) {
        const p1_index = this.options.findIndex(row => row.id===item.parent_id)
        const p1 = this.options[p1_index]
        title = `${p1.kode}.${item.kode} - ${item.nama}`

      } else {
        title = `${item.kode} - ${item.nama}`;
      }
      return title.substring(0, 20) + "...";
    }
  },
  computed: {
    selectedRow() {
      if (!this.selected || this.selected.length === 0) {
        return "-- Click to select options --";
      } else {
        if (Array.isArray(this.selectedData)) {
          const index = this.options.findIndex(row => row.id === this.selectedData[0]);
          const cindex = this.options[index].children.findIndex(row => row.id === this.selectedData[1]);
          const parent = this.options[index];
          const child = parent.children[cindex];
          // return `${parent.kode}.${child.kode} - ${this.$options.filters.title(child.nama)}`
          return `${parent.kode}.${child.kode} - ${child.nama}`
        } else {
          const index = this.options.findIndex(row => row.id === this.selectedData);
          const row = this.options[index];
          return `${row.kode} - ${row.nama}`
        }
      }
    }
  },
  data() {
    return {
      options: [],
      expanded: [],
      selected: [],
      selectedData: null,
      listToggle: false
    }
  },
  filters: {
    title(string) {
      if(string.length > 63) {
        return string.substring(0, 63) + '...';
      }
      return string;
    }
  },
  created() {
    this.getData();
  },
  watch: {
    selected() {
      let list = []
      this.selected.forEach(item => {
        list.push(item.id);
      });
      this.$emit('input', list);
    }
  }
}
</script>

<style scoped>
.dd-handle {
  cursor: pointer;
}
.pointer {
  cursor: pointer;
}

.badge-pill {
  padding-right: .6em;
  padding-left: .6em;
  border-radius: 10rem;
}
</style>