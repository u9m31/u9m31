<template>
  <v-flex>
    <!-- ２段目 ：CSVリスト表示 -->
    <v-card xs12 class="m-3 px-3">
      <v-card-title class="title">
        <v-icon class="pr-2" @click="closeList">{{ $route.meta.icon }}</v-icon> {{ csvTitle }} {{ /* 給与明細ＣＳＶ検索 */ }}
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          prepend-icon="search"
          label="絞り込み表示"
          single-line
          hide-details
          clearable
        ></v-text-field>
        <v-icon @click="closeList" class="accent ml-5" dark>close</v-icon>
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="tabledata"
        :pagination.sync="pagination"
        :rows-per-page-items='[5,10,20,{"text":"All","value":-1}]'
        :loading="loading"
        :search="search"
        class="elevation-0 p-1"
      >
        <v-progress-linear slot="progress" color="blue" indeterminate></v-progress-linear>

        <template slot="items" slot-scope="props">
          <tr :class="{'pink--text': (props.item.deleted_at != null)}" v-show="props.item.id == detail_id || detail_id == 0">
            <td class="text-xs-center" xs1>{{ (props.index + 1) + (pagination.page - 1) * pagination.rowsPerPage }}</td>
            <template v-for="n in (headers.length - 1)">

              <td v-if="headers[n].text != 'アクション'"
                :class="'text-xs-' + headers[n].align"
                style="white-space: nowrap;"
                v-text="props.item[headers[n].value]"
              ></td>

              <td v-else
                :class="'text-xs-' + headers[n].align"
                style="white-space: nowrap;"
              >
                <v-tooltip right :color="(props.item.deleted_at == null) ? 'primary': 'grey'">
                  <v-btn fab small flat @click="$emit('showDetail', props.item)" slot="activator">
                    <v-icon color="primary">list</v-icon>
                  </v-btn>
                  <span>明細一覧</span>
                </v-tooltip>

                <v-tooltip v-if="props.item.published_at == null" right :color="(props.item.deleted_at == null) ? 'success' : 'grey'">
                  <v-btn fab small  flat @click="dialog_open(props.item, 'pub')" slot="activator"
                    :disabled="props.item.deleted_at != null"
                  >
                    <v-icon color="success">lock</v-icon>
                  </v-btn>
                  <span>公開</span>
                </v-tooltip>
                <v-btn v-else fab small flat disabled>
                  <v-icon color="grey lighten-1">lock_open</v-icon>
                </v-btn>

                <v-tooltip right :color="(props.item.deleted_at == null) ? 'error': 'grey'">
                  <v-btn fab small flat @click="dialog_open(props.item, 'del')" slot="activator"
                    :disabled="props.item.deleted_at != null">
                    <v-icon color="error">delete</v-icon>
                  </v-btn>
                  <span>{{(props.item.deleted_at == null ? '削除' : '削除済')}}</span>
                </v-tooltip>
            </td>
            </template>
          </tr>
        </template>
      </v-data-table>
    </v-card>

    <!-- 確認ダイアログ -->
    <v-dialog v-model="dialog" width="500" persistent>
      <v-card>
        <v-toolbar :color="d.titlecolor" dark>
          <v-toolbar-title>{{ d.title }}</v-toolbar-title>
        </v-toolbar>
        <v-card-text class="subheading">
          <span v-html="d.body"></span>
          <br>よろしいですか？
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-btn flat block @click="dialog_no()" > いいえ </v-btn>
          <v-spacer></v-spacer>
          <v-btn flat block @click="dialog_yes()" :color="d.titlecolor"> はい </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-flex>
</template>

<script>

  export default {
    name: 'CsvPayslipComponent',

    components: {
    },

    props: {
      target: Object,
      detail_id: Number,
    },

    data: () => ({
      loading: false,
      search: '',
      pagination: { sortBy: null, descending: false, },

      // csv list
      csvTitle: '',
      tabledata: [],
      headers: [
        { align: 'center', sortable: false, text: 'No', },
        { align: 'center', sortable: true,  text: '年月',       value: 'ym' },
        { align: 'center', sortable: false, text: 'アクション', },
        { align: 'center', sortable: true,  text: '状態',       value: 'status' },
        { align: 'center', sortable: true,  text: 'CSV-ID',     value: 'id' },
        { align: 'center', sortable: true,  text: '対象者数',   value: 'line' },
        { align: 'center', sortable: true,  text: 'エラー数',   value: 'error' },
        { align: 'left',   sortable: true,  text: 'ファイル名', value: 'filename' },
        { align: 'left',   sortable: true,  text: '登録日時',   value: 'created_at' },
        { align: 'left',   sortable: true,  text: '公開日時',   value: 'published_at' },
        { align: 'left',   sortable: true,  text: '削除日時',   value: 'deleted_at' },
      ],

      // dialog
      dialog: false,
      d: {
        title: '',
        titlecolor: '',
        icon: '',
        type: '',
        item: [],
      },
    }),

    created() {
      if (process.env.MIX_DEBUG) console.log('CsvPayslip Component created.')
      this.initialize()
    },

    methods: {
      initialize() {
      },

      reload() {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component reload')
        this.getCsvPayslip()
      },

      initList() {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component initList')
        this.setCsvTitle()
        this.tabledata = []
        this.search = ''
        this.pagination.sortBy = ''
        this.pagination.descending = false
      },

      closeList() {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component closeList')
        this.tabledata = []
        this.search = ''
        this.pagination.sortBy = ''
        this.pagination.descending = false
        this.$emit('closeList')
      },

      setCsvTitle() {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component set csv title')
        this.csvTitle = ''
        if (this.target.ym) this.csvTitle += this.target.ym + '　'
        else this.csvTitle += '全期間　'
      },

      // 登録済みCSVのリストをサーバから取得する
      getCsvPayslip() {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component getCsvPayslip')

        // 初期化
        this.initList()

        // 検索パラメータ設定
        var params = new URLSearchParams()
        params.append('ym', (this.target.ym ? this.target.ym : ''))
        params.append('deleted', (this.target.deleted ? this.target.deleted : false))

        // 検索要求
        this.loading = true
        axios.post('/api/admin/csvpayslip/index', params)

        // 検索結果［正常］
        .then( function (response) {
          this.loading = false
          if (process.env.MIX_DEBUG) console.log(response)
          if (response.data.data) {
            this.tabledata = response.data.data
            this.setStatus()
          }
        }.bind(this))

        // 検索結果［異常］
        .catch(function (error) {
          this.loading = false
          console.log(error)
          if (error.response) {
            if ([401, 419].includes(error.response.status)) {
              this.$emit('axios-logout')
            }
            else {
              alert('ERROR ' + error.response.status + ' ' + error.response.statusText)
            }
          }
          else {
            alert('ERROR ' + error)
          }
        }.bind(this))
      },

      setStatus() {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component set status')
        var wk = '不明'
        for (var i=0; i<this.tabledata.length; i++) {
          if (this.tabledata[i].status) {
            wk = '不明'
            if (this.tabledata[i].status == 0) { wk = '非公開' }
            else if (this.tabledata[i].status == 1) { wk = '公開' }
            if (this.tabledata[i].deleted_at != null ) { wk = '削除' }
            this.tabledata[i].status = wk
          }
        }
      },

      dialog_open(item, type) {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component Dialog Open')
        this.d.type = type
        this.d.item = item
        this.d.body = 'CSV ID：' + item.id + '<br>' + '対象年月：' + item.ym + '<br>' + 'ファイル：' + item.filename + '<br><br>'
        if (type == 'pub') {
          this.d.title = '明細情報を公開します'
          this.d.titlecolor = 'success'
          this.d.body += '対象のデータを公開します。<br>公開後は非公開とすることはできません。'
        }
        else {
          this.d.title = '明細情報を削除します'
          this.d.titlecolor = 'error'
          this.d.body += '対象のデータを削除します。'
        }
        this.dialog = true
      },

      dialog_yes() {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component Dialog YES')
        this.dialog = false
        var url = '/api/admin/csvpayslip/publish'
        if (this.d.type == 'del') { url = '/api/admin/csvpayslip/delete' }
        this.csvUpdate(url, this.d.item)
        this.d.type = ''
        this.d.item = []
      },

      dialog_no() {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component Dialog NO')
        this.dialog = false
        this.d.type = ''
        this.d.item = []
      },

      // CSVの状態を更新
      csvUpdate(url, item) {
        if (process.env.MIX_DEBUG) console.log('CsvPayslip Component CSV Update')

        // パラメータ設定
        var params = new URLSearchParams()
        params.append('id', item.id)

        // 更新要求
        axios.post(url, params)

        // 更新結果［正常］
        .then( function (response) {
          if (process.env.MIX_DEBUG) console.log(response)
          this.reload()
          if (this.detail_id == item.id) { 
            this.$emit('showDetail', item)
          }
        }.bind(this))

        // 更新結果［異常］
        .catch(function (error) {
          console.log(error)
          if (error.response) {
            if ([401, 419].includes(error.response.status)) {
              this.$emit('axios-logout')
            }
            else {
              alert('ERROR ' + error.response.status + ' ' + error.response.statusText)
            }
          }
          else {
            alert('ERROR ' + error)
          }
        }.bind(this))
      },
    },
  }
</script>
