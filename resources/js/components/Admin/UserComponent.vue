<template>
  <v-flex>
    <v-card xs12 class="m-3 px-3">

      <v-card-title class="title">
        <v-icon class="pr-2">{{ $route.meta.icon }}</v-icon> {{ $route.meta.name }} {{ /* 社員管理 */ }}
        <user-dialog ref="userDialog" @reload="reload" @setsearch="setsearch"></user-dialog>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          prepend-icon="search"
          label="Search"
          single-line
          hide-details
          clearable
        ></v-text-field>
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="tabledata"
        :pagination.sync="pagination"
        :rows-per-page-items='[10,25,50,{"text":"All","value":-1}]'
        :loading="loading"
        :search="search"
        class="elevation-0 p-1"
      >
        <v-progress-linear slot="progress" color="blue" indeterminate></v-progress-linear>

        <template slot="items" slot-scope="props">
          <tr>
            <td class="text-xs-center" xs1>{{ (props.index + 1) + (pagination.page - 1) * pagination.rowsPerPage }}</td>
            <template v-for="n in (headers.length - 2)">
              <td :class="'text-xs-' + headers[n].align" style="white-space: nowrap;" v-text="props.item[headers[n].value]"></td>
            </template>
            <td class="text-xs-center" xs1>
              <v-btn flat small fab @click="dialogOpen(props.item)"><v-icon color="success">edit</v-icon></v-btn>
              <v-btn flat small fab @click="dialogOpen(props.item,true)"><v-icon color="error">delete</v-icon></v-btn>
            </td>
          </tr>
        </template>
      </v-data-table>

      <v-spacer></v-spacer>

      <v-card-actions>
        <v-btn flat block color="primary" @click="dialogOpen(null)"><v-icon>person_add</v-icon>新規追加</v-btn>
        <v-spacer></v-spacer>
        <csv-download url="/api/admin/user/download" color="primary"></csv-download>
      </v-card-actions>
    </v-card>
  </v-flex>
</template>

<script>
  import user_dialog from './UserDialog.vue'
  import csv_download from './CsvDownload.vue'

  export default {
    name: 'UserComponent',

    components: {
      'user-dialog': user_dialog,
      'csv-download': csv_download,
    },

    props: {
    },

    data: () => ({
      loading: false,
      search: '',
      pagination: { sortBy: 'name', descending: false, },

      tabledata: [],
      headers: [
        { align: 'center', sortable: false, text: 'No',       },
        { align: 'left',   sortable: true,  text: '社員ID',   value: 'loginid' },
        { align: 'left',   sortable: true,  text: '氏名',     value: 'name' },
        { align: 'center', sortable: true,  text: '権限',     value: 'role' },
        { align: 'center', sortable: false, text: 'アクション',       },
      ],
    }),

    created() {
      if (process.env.MIX_DEBUG) console.log('User Component created.')
      this.initialize()
    },

    methods: {
      initialize() {
        this.getUsers()
      },

      reload() {
        if (process.env.MIX_DEBUG) console.log('User Component reload')
        this.getUsers()
      },

      setsearch(id) {
        if (process.env.MIX_DEBUG) console.log('User Component set Search')
        this.search = id
      },

      getUsers() {
        if (process.env.MIX_DEBUG) console.log('User Component getUsers')
        this.loading = true
        axios.post('/api/admin/user')

        .then( function (response) {
          this.loading = false
          if (process.env.MIX_DEBUG) console.log(response)
          if (response.data.users) {
            this.tabledata = response.data.users
            this.setRole()
          }
        }.bind(this))

        .catch(function (error) {
          this.loading = false
          console.log(error)
          if (error.response && [401, 419].includes(error.response.status)) {
            this.$emit('axios-logout')
          }
        }.bind(this))
      },

      setRole() {
        for (var i=0; i<this.tabledata.length; i++) {
          if (this.tabledata[i].role) {
            if (this.tabledata[i].role == 5) { this.tabledata[i].role = '管理者'  }
            if (this.tabledata[i].role == 10) { this.tabledata[i].role = 'ユーザ'  }
          }
        }
      },

      dialogOpen(item,flg) {
        if (process.env.MIX_DEBUG) console.log('User Component dialog open')
        this.$refs.userDialog.open(item, (flg || false))
      }
    },
  }
</script>
