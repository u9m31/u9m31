<template>
  <v-flex>
    <!-- １段目 ：検索？登録？ -->
    <v-card xs12 class="m-3 px-3" v-show="!csvList">
      <v-card-title class="title">
        <v-icon class="pr-2">{{ $route.meta.icon }}</v-icon> {{ $route.meta.name }} {{ /* 給与明細 */ }}
        【{{ (searchTab ? '検索' : '登録') }}】
        <v-spacer></v-spacer>
        <v-btn block depressed
          :outline="searchTab"
          :color="searchTab ? 'primary' : 'blue-gray'"
          @click="searchTab = true"
        >
          <v-icon class="pr-1">search</v-icon>検索
        </v-btn>
        <v-btn block depressed
          :outline="!searchTab"
          :color="!searchTab ? 'primary' : 'blue-gray'"
          @click="searchTab = false"
        >
          <v-icon class="pr-1">cloud_upload</v-icon>登録
        </v-btn>
      </v-card-title>

      <v-layout row wrap class="mx-3 my-2">
        <!-- 共通： 対象年月（カレンダーで月を選択）-->
        <v-flex xs2 md4 lg3>
          <v-menu
            ref="menu"
            v-model="menu"
            :return-value.sync="target.ym"
            :close-on-content-click="false"
            :nudge-right="20"
            lazy transition="scale-transition" offset-y full-width max-width="290px" min-width="290px"
            show-current="true"
          >
            <v-text-field
              readonly
              clearable
              autofocus
              slot="activator"
              v-model="target.ym"
              label="対象年月"
              placeholder="明細の対象年月を選択"
              :hint="'明細の対象年月を選択' + (searchTab?'(指定なしで全期間対象)':'')"
            ></v-text-field>
            <v-date-picker v-model="target.ym" type="month" no-title scrollable locale="ja">
              <v-spacer></v-spacer>
              <v-btn flat color="primary" @click="menu = false">Cancel</v-btn>
              <v-btn flat color="primary" @click="$refs.menu.save(target.ym)">OK</v-btn>
            </v-date-picker>
          </v-menu>
        </v-flex>

        <!-- 検索： 削除済みデータも検索に含める -->
        <v-flex xs5 md4 lg3 v-if="searchTab">
          <v-switch
            :label="`削除済データ ${target.deleted ? '対象' : '対象外'}`"
            v-model="target.deleted"
            color="error"
            class="px-2"
            hint="削除済みデータも検索したい場合に指定"
          ></v-switch>
        </v-flex>

      </v-layout>

      <v-card-actions class="pb-2">
        <!-- 登録ボタン -->
        <csv-upload
          v-show="!searchTab"
          :updata="target"
          color="primary"
          icon="cloud_upload"
          @reload="reload"
          @axios-logout="$emit('axios-logout')"
          url="/api/admin/payslip/upload"
        ></csv-upload>

        <!-- 検索ボタン -->
        <v-btn block flat
          v-show="searchTab"
          @click="getCsvPayslip"
          color="primary"
        >
          <v-icon class="pr-2">search</v-icon>検索
        </v-btn>
      </v-card-actions>
    </v-card>

    <!-- ２段目 ：CSVリスト表示 -->
    <v-card xs12 class="m-3 px-3" v-if="csvList">
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
                <v-tooltip v-if="props.item.published_at == null" right :color="(props.item.deleted_at == null) ? 'success' : 'gray'">
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

                <v-tooltip right :color="(props.item.deleted_at == null) ? 'error': 'gray'">
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
  import csv_upload from './CsvUpload.vue'

  export default {
    name: 'PayslipComponent',

    components: {
      'csv-upload': csv_upload,
    },

    props: {
    },

    data: () => ({
      loading: false,
      search: '',
      pagination: { sortBy: 'created_at', descending: true, },

      // csv list
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

      menu: false,
      searchTab: true,
      csvList: false,
      csvTitle: '',
      target: {
        ym: '',
        deleted: false,
      },

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
      if (process.env.MIX_DEBUG) console.log('Payslip Component created.')
      this.initialize()
    },

    methods: {
      initialize() {
        // 初期表示時は現在年月を設定しておく
        this.target.ym = moment().format('YYYY-MM').toString()
      },

      reload() {
        if (process.env.MIX_DEBUG) console.log('Payslip Component reload')
        this.getCsvPayslip()
      },

      initList() {
        if (process.env.MIX_DEBUG) console.log('Payslip Component initList')
        this.setCsvTitle()
        this.tabledata = []
        this.csvList = true
      },

      closeList() {
        this.tabledata = []
        this.csvList = false
      },

      setCsvTitle() {
        if (process.env.MIX_DEBUG) console.log('Payslip Component set csv title')
        this.csvTitle = ''
        if (this.target.ym) this.csvTitle += this.target.ym + '　'
        else this.csvTitle += '全期間　'
      },

      // 登録済みCSVのリストをサーバから取得する
      getCsvPayslip() {
        if (process.env.MIX_DEBUG) console.log('Payslip Component getCsvPayslip')

        // 初期化
        this.initList()

        // 検索パラメータ設定
        var params = new URLSearchParams()
        params.append('ym', (this.target.ym ? this.target.ym : ''))
        params.append('deleted', this.target.deleted)

        // 検索要求
        this.loading = true
        axios.post('/api/admin/payslip/csvlist', params)

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
        if (process.env.MIX_DEBUG) console.log('Payslip Component Dialog Open')
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
        if (process.env.MIX_DEBUG) console.log('Payslip Component Dialog YES')
        this.dialog = false
        var url = '/api/admin/payslip/publish'
        if (this.d.type == 'del') { url = '/api/admin/payslip/delete' }
        this.csvUpdate(url, this.d.item.id)
        this.d.type = ''
        this.d.item = []
      },

      dialog_no() {
        if (process.env.MIX_DEBUG) console.log('Payslip Component Dialog NO')
        this.dialog = false
        this.d.type = ''
        this.d.item = []
      },


      // CSVの更新
      csvUpdate(url, id) {
        if (process.env.MIX_DEBUG) console.log('Payslip Component CSV Update')

        // パラメータ設定
        var params = new URLSearchParams()
        params.append('id', id)

        // 更新要求
        axios.post(url, params)

        // 検索結果［正常］
        .then( function (response) {
          if (process.env.MIX_DEBUG) console.log(response)
          this.reload()
        }.bind(this))

        // 検索結果［異常］
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
