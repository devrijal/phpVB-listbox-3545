<template>
  <!-- template ref : cubebs4/ui-nestable.html -->
  <div>
    <div class="row">
      <div :class="colClass">
        <div class="row">
          <div class="col-md-9" @click="toggleList">
            <input class="form-control pointer" type="text" v-model="selectedRow" disabled
                   v-b-tooltip.hover :title="selectedRow">
          </div>
          <div class="col-md-3" v-if="selected">
            <button type="button" class="btn btn-danger btn-sm btn-block" @click="selected=null">Reset</button>
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
                      @click="toggle(row.id)">
                {{ expanded.includes(row.id) ? 'Expand' : 'Collapse' }}</button>
              <div class="dd-handle" v-b-tooltip.hover.right :title="`${row.kode} - ${row.nama}`" @click="select(row.id)">
                {{row.kode}} - {{row.nama | title}}
                <div class="nested-links">
                  <span class="badge" v-if="selected === row.id"><i class="fa fa-check" style="color:green"></i></span>
                  <span class="badge">{{row.children_count}}</span>
                </div>
              </div>

              <ol class="dd-list" v-if="row.hasOwnProperty('children') && row.children.length > 0"
                  v-for="(child, i) in row.children">

                <li class="dd-item dd-collapsed" data-id="5">
                  <!--              <button :class="expanded.includes(child.id) ? 'dd-collapse' : 'dd-expand'" type="button"-->
                  <!--                      @click="toggle(row.id, child.id)">-->
                  <!--                {{ expanded.includes(child.id) ? 'Expand' : 'Collapse' }}</button>-->
                  <div class="dd-handle" v-b-tooltip.hover.right :title="`${row.kode}.${child.kode} - ${child.nama}`"
                       @click="select(row.id, child.id)">
                    {{row.kode}}.{{child.kode}} - {{child.nama | title}}
                    <div class="nested-links">
                  <span class="badge" v-if="selected === child.id">
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
              if (id === this.presetKlId) {
                this.options = resp.data;
              } else {
                const index = this.options.findIndex(row => row.id === id)
                this.$set(this.options[index], 'children', resp.data);
                this.expanded.push(id)
                // this.options[index].children = resp.data;
              }
            } else {
              eventBus.$emit('openNotif', resp.data);
            }
          })
          .catch(e => console.log(e))
    },
    toggle(id, child_id=0) {
      if (!child_id) {
        const index = this.options.findIndex(row => row.id===id);
        const row = this.options[index];
        if (row.hasOwnProperty('children') && Array.isArray(row.children)) {
          const expindex = this.expanded.indexOf(row.id);
          if (expindex > -1) {
            this.expanded.splice(expindex, 1);
          } else {
            this.expanded.push(row.id)
          }
        } else {
          this.getData(id);
        }

      } else {
        const index = this.options.findIndex(row => row.id===id);
        const cindex = this.options[index].children.findIndex(child => child.id===child_id);
        const row = this.options[index][cindex];
        if (row.hasOwnProperty('children') && Array.isArray(row.children)) {
          const expindex = this.expanded.indexOf(row.id);
          if (expindex > -1) {
            this.expanded.splice(expindex, 1);
          } else {
            this.expanded.push(row.id)
          }
        } else {
          this.getData(child_id);
        }
      }
    },
    toggleList() {
      this.$root.$emit('bv::toggle::collapse', 'collapse-lbk')
    },
    select(id, child_id=0) {
      if(!child_id) {
        this.selectedData = id;
        this.selected = id;
      } else {
        this.selectedData = [id, child_id];
        this.selected = child_id;
      }
    }
  },
  computed: {
    selectedRow() {
      if (!this.selected) {
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
      selected: null,
      selectedData: null
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
.dd-handle {
  cursor: pointer;
}
.pointer {
  cursor: pointer;
}
</style>