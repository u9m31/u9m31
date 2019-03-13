<template>
  <v-flex>
    <v-card xs12 class="m-3 px-3">

      <v-card-title class="title">
        <v-icon class="pr-2">{{ $route.meta.icon }}</v-icon> {{ $route.meta.name }} {{ /* 操作ログ */ }}
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
            <template v-for="n in (headers.length - 1)">
              <td :class="'text-xs-' + headers[n].align" style="white-space: nowrap;" v-text="props.item[headers[n].value]"></td>
            </template>
          </tr>
        </template>
      </v-data-table>

      <v-spacer></v-spacer>

      <v-card-actions>
        <csv-download url="/api/admin/actlog/download" color="primary" @axios-logout="$emit('axios-logout')"></csv-download>
      </v-card-actions>
    </v-card>
  </v-flex>
</template>

<script>
  import csv_download from './CsvDownload.vue'

  export default {
    name: 'ActlogComponent',

    components: {
      'csv-download': csv_download,
    },

    props: {
    },

    data: () => ({
      loading: false,
      search: '',
      pagination: { sortBy: 'created_at', descending: true, },

      tabledata: [],
      headers: [
        { align: 'center', sortable: false, text: 'No',       },
        { align: 'left',   sortable: true,  text: '日時',     value: 'created_at' },
        { align: 'left',   sortable: true,  text: '氏名',     value: 'name' },
        { align: 'left',   sortable: true,  text: '操作',     value: 'action' },
        { align: 'center', sortable: true,  text: '結果',     value: 'status' },
        { align: 'left',   sortable: true,  text: 'データ',   value: 'data' },
        { align: 'left',   sortable: true,  text: 'ＩＰ',     value: 'remote_addr' },
        { align: 'left',   sortable: true,  text: 'ＵＡ',     value: 'user_agent' },
      ],
    }),

    created() {
      if (process.env.MIX_DEBUG) console.log('Actlog Component created.')
      this.initialize()
    },

    methods: {
      initialize() {
        this.getData()
      },

      getData() {
        if (process.env.MIX_DEBUG) console.log('Actlog Component getData')
        this.loading = true
        axios.post('/api/admin/actlog')

        .then( function (response) {
          this.loading = false
          if (process.env.MIX_DEBUG) console.log(response)
          if (response.data.data) {
            this.tabledata = this.setAction(response.data.data)
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

      setAction(data) {
        if (process.env.MIX_DEBUG) console.log('Actlog Component setAction')
        var wk = ''
        for (let i=0; i<data.length; i++) {
          switch (data[i].route) {

            // Login - Logout
            case 'show.login' : wk = 'ログイン画面'; break;
            case 'login' :      wk = 'ログイン'; break;
            case 'logout' :     wk = 'ログアウト'; break;

            // USER
            case 'admin.user.index' :    wk = '社員一覧'; break;
            case 'admin.user.store' :    wk = '社員追加'; break;
            case 'admin.user.destroy' :  wk = '社員削除'; break;
            case 'admin.user.download' : wk = '社員CSV_DL'; break;
            case 'admin.user.upload' :   wk = '社員CSV_UP'; break;

            // CsvPayslip
            case 'admin.csvpayslip.index' :   wk = '給与明細検索'; break;
            case 'admin.csvpayslip.upload' :  wk = '給与明細登録'; break;
            case 'admin.csvpayslip.delete' :  wk = '給与明細削除'; break;
            case 'admin.csvpayslip.publish' : wk = '給与明細公開'; break;

            // CsvPayslip
            case 'admin.payslip.index' :  wk = '給与明細一覧'; break;
            case 'admin.payslip.delete' : wk = '給与明細削除'; break;
            case 'admin.payslip.pdf' :    wk = '給与明細PDF'; break;

            // Actlog
            case 'admin.actlog.index' :    wk = '操作履歴一覧'; break;
            case 'admin.actlog.download' : wk = '操作履歴ダウンロード'; break;

            // OTHER
            default: wk = data[i].route

          }
          data[i].action = wk
        }
        return data
      },
    },
  }
</script>
