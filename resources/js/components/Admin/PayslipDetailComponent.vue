<template>
  <v-flex>
    <!-- ３段目 ：明細詳細表示 -->
    <v-card xs12 class="m-3 px-3">
      <v-card-title class="title">
        <v-icon class="pr-2" @click="closeDetail">{{ $route.meta.icon }}</v-icon> {{ title }} {{ /* 給与明細詳細検索 */ }}
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
        <v-icon @click="closeDetail" class="accent ml-5" dark>close</v-icon>
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
          <tr :class="{'pink--text': (props.item.deleted_at != null)}">
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
                <v-tooltip right :color="(csv_item.published_at != null) ? 'success': 'grey'">
                  <v-btn fab small  flat @click="pdf_download(props.item)" slot="activator"
                    :disabled="props.item.error != null"
                  >
                    <v-icon :color="(csv_item.published_at != null) ? 'success' : 'grey'">description</v-icon>
                  </v-btn>
                  <span>明細PDF</span>
                </v-tooltip>

                <v-tooltip right :color="(props.item.deleted_at == null) ? 'error': 'grey'">
                  <v-btn fab small flat @click="dialog_open(props.item, 'del')" slot="activator"
                    :disabled="props.item.deleted_at != null || csv_item.deleted_at != null"
                  >
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
    name: 'PayslipDetailComponent',

    components: {
    },

    props: {
      target: Object,
    },

    data: () => ({
      loading: false,
      search: '',
      pagination: { sortBy: '', descending: false, },

      // detail list
      title: '',
      csv_item: {},
      tabledata: [],
      headers: [
        { align: 'center', sortable: false, text: 'No', },
        { align: 'center', sortable: false, text: 'アクション', },
        { align: 'center', sortable: true,  text: 'CSV行',      value: 'line' },
        { align: 'left',   sortable: true,  text: '氏名',       value: 'name' },
        { align: 'left',   sortable: true,  text: 'ログインID', value: 'loginid' },
        { align: 'center', sortable: true,  text: 'DL回数',     value: 'download' },
        { align: 'left',   sortable: true,  text: 'ファイル名', value: 'filename' },
        { align: 'left',   sortable: true,  text: 'CSVエラー',  value: 'error' },
        { align: 'left',   sortable: true,  text: '削除日時',   value: 'deleted_at' },
        { align: 'left',   sortable: true,  text: '削除者ID',   value: 'delete_user_id' },

        { align: 'center', sortable: true,  text: 'CSV-ID',     value: 'csv_id' },
        { align: 'left',   sortable: true,  text: '状態',       value: 'status' },
        { align: 'center', sortable: true,  text: '年月',       value: 'ym' },
        { align: 'center', sortable: true,  text: 'ユーザID',   value: 'user_id' },
      ],

      // dialog
      dialog: false,
      d: {
        title: '',
        titlecolor: '',
        icon: '',
        item: [],
      },

    }),

    created() {
      if (process.env.MIX_DEBUG) console.log('PayslipDetail Component created.')
      this.initialize()
    },

    methods: {
      initialize() {
      },

      reload() {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component reload')
        this.getPayslipDetail()
      },

      initList() {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component initList')
        this.setTitle()
        this.tabledata = []
        this.search = ''
        this.pagination.sortBy = ''
        this.pagination.descending = false
      },

      closeDetail() {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component closeDetail')
        this.tabledata = []
        this.search = ''
        this.pagination.sortBy = ''
        this.pagination.descending = false
        this.$emit('closeDetail')
      },

      setTitle() {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component set title')
        this.title = ''
        this.title = 'ID: ' + this.csv_item.id + '  - ' + this.csv_item.ym
        if (this.csv_item.deleted_at != null) {
          this.title += ' - 削除済'
        }
        else if (this.csv_item.published_at != null) {
          this.title += ' - 公開済'
        }
        else {
          this.title += ' - 未公開'
        }
      },

      // 指定の明細リストサーバから取得する
      getPayslipDetail(item) {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component getPaslipDetail')

        // 初期化
        this.csv_item = (item ? item : this.csv_item)
        this.initList()

        // 検索パラメータ設定
        var params = new URLSearchParams()
        params.append('id', this.csv_item.id)

        // 検索要求
        this.loading = true
        axios.post('/api/admin/payslip/index', params)

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
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component set status')
        var wk = '不明'
        for (var i=0; i<this.tabledata.length; i++) {
          if (this.tabledata[i].status) {
            wk = '不明'
            if (this.tabledata[i].status == 0) { wk = '有効' }
            else if (this.tabledata[i].status == 9) { wk = '削除' }
            if (this.tabledata[i].deleted_at != null ) { wk = '削除' }
            this.tabledata[i].status = wk
          }
        }
      },

      dialog_open(item) {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component Dialog Open')
        this.d.item = item
        this.d.body = 'CSV行：' + item.line + '<br>' + 
                      '対象年月：' + item.ym + '<br>' + 
                      '対象者：' + item.name + '<br>' +
                      'ログインID：' + item.loginid + '<br>' +
                      'ダウンロード回数：' + item.download + ' 回<br>' +
                      '<br>'
        this.d.title = '明細情報を削除します'
        this.d.titlecolor = 'error'
        this.d.body += '対象のデータを削除します。'
        if (item.download > 0) {
          this.d.body += '<br> 利用者は明細をダウンロードしたことがあるようです。'
        }
        this.dialog = true
      },

      dialog_yes() {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component Dialog YES')
        this.dialog = false
        var url = '/api/admin/payslip/delete'
        this.detailUpdate(url, this.d.item.id)
        this.d.item = []
      },

      dialog_no() {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component Dialog NO')
        this.dialog = false
        this.d.item = []
      },

      // 明細の状態を更新
      detailUpdate(url, id) {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component Detail Update')

        // パラメータ設定
        var params = new URLSearchParams()
        params.append('id', id)

        // 更新要求
        axios.post(url, params)

        // 更新結果［正常］
        .then( function (response) {
          if (process.env.MIX_DEBUG) console.log(response)
          this.reload()
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

      pdf_download(item) {
        if (process.env.MIX_DEBUG) console.log('PayslipDetail Component PDF Download')

        // パラメータ設定
        var params = new URLSearchParams()
        params.append('loginid', item.loginid)  // 操作ログ記録用
        params.append('name',    item.name)     // 操作ログ記録用
        params.append('yyyymm',  item.ym)       // 操作ログ記録用
        params.append('csv_id',  item.csv_id)
        params.append('id',  item.id)
        var config = {
          responseType: 'blob',
        }

        // ダウンロード要求
        this.loading = true
        axios.post('/api/admin/payslip/pdf', params, config)

        // 正常
        .then( function (response) {
          this.loading = false
          if (response.data) {
            // PDFデータ取得
            var blob = new Blob([response.data], { "type" : "application/pdf" })

            // 補助ファイル名設定
            var f = ''
            if (item.filename != null) {
              f = '('+ item.filename +')'
            }

            // ファイル名設定 - '給与明細'_'年月'_'氏名''(補助ファイル名)'_'出力日時'.pdf 
            // 　　　　　　　 - 氏名からは「全角空白」、「半角空白」、「スラッシュ」を削除しとく
            var filename = '給与明細'
            filename += '_' + item.ym
            filename += '_'+ item.name.replace(/　/g,'').replace(/ /g,'').replace(/\//g,'')
            filename += f
            filename += '_' + moment(Date.now()).format("YYYYMMDD_HHmmss")
            filename += '.pdf'

            // ＰＤＦダウンロード（表示）　IE11
            if (window.navigator.msSaveBlob) {
              window.navigator.msSaveBlob(blob, filename)
              window.navigator.msSaveOrOpenBlob(blob, filename)
            }

            // ＰＤＦダウンロード　Chrome, Safari, Firefox, etc...
            else {
              const url = window.URL.createObjectURL(blob)
              const link = document.createElement('a')
              link.href = url
              link.setAttribute('download', filename)
              document.body.appendChild(link)
              link.click()
              link.remove()
            }
          }
          else {
            alert('PDF ダウンロードエラー')
          }
        }.bind(this))

        // ダウンロード失敗
        .catch(function (error) {
          this.loading = false
          console.log(error)
          if (error.response) {
            if ([401, 419].includes(error.response.status)) {
              this.$emit('axios-logout')
            }
            else if ([422].includes(error.response.status)) {
              if (process.env.MIX_DEBUG) console.log("PDF Download error 422")
              if (process.env.MIX_DEBUG) console.log(error.response.data)

              // error message blog to json - 通信時に BLOB 指定しているのでエラーメッセージもBLOB.. JSONに戻して取り扱い
              //                                     - responseType: 'blob'
              var reader = new FileReader()
              reader.onload = function(e) { // 非同期処理のため
                var data = JSON.parse(reader.result)
                if (process.env.MIX_DEBUG) console.log(data)
                if (data.errors) {
                  for (let key in data.errors) {
                    if (data.errors[key]) {
                      alert(key + ' : ' + data.errors[key])
                    }
                  }
                }
              }
              reader.readAsText(error.response.data, "UTF-8")
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
