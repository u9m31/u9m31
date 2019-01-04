<template>
  <v-flex>
    <!-- １段目 ：検索？登録？ -->
    <v-card xs12 class="m-3 px-3" v-show="!csvList">
      <v-card-title class="title">
        <v-icon class="pr-2">{{ $route.meta.icon }}</v-icon> {{ $route.meta.name }} {{ /* 給与明細 */ }}
        【{{ activeTab }}】
        <v-spacer></v-spacer>
        <v-btn block depressed 
          :outline="activeTab == '検索'" 
          :color="activeTab == '検索' ? 'primary' : 'blue-gray'" 
          @click="activeTab = '検索'"
        >
          <v-icon class="pr-1">search</v-icon>検索
        </v-btn>
        <v-btn block depressed 
          :outline="activeTab != '検索'" 
          :color="activeTab != '検索' ? 'primary' : 'blue-gray'" 
          @click="activeTab = '登録'"
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
              :hint="'明細の対象年月を選択' + (activeTab=='検索'?'(指定なしで全期間対象)':'')"
            ></v-text-field>
            <v-date-picker v-model="target.ym" type="month" no-title scrollable locale="ja">
              <v-spacer></v-spacer>
              <v-btn flat color="primary" @click="menu = false">Cancel</v-btn>
              <v-btn flat color="primary" @click="$refs.menu.save(target.ym)">OK</v-btn>
            </v-date-picker>
          </v-menu>
        </v-flex>
      </v-layout>

      <v-card-actions class="pb-2">
        <!-- 登録ボタン -->
        <csv-upload 
          v-show="activeTab == '登録'"
          :updata="target" 
          color="primary"
          icon="cloud_upload"
          @reload="reload"
          @axios-logout="$emit('axios-logout')"
          url="/api/admin/payslip/upload" 
        ></csv-upload>

        <!-- 検索ボタン -->
        <v-btn block flat 
          v-show="activeTab == '検索'"
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
          <td class="text-xs-center" xs1>{{ (props.index + 1) + (pagination.page - 1) * pagination.rowsPerPage }}</td>
          <template v-for="n in (headers.length - 1)">
            <td :class="'text-xs-' + headers[n].align" style="white-space: nowrap;" v-text="props.item[headers[n].value]"></td>
          </template>
        </template>
      </v-data-table>
    </v-card>

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
        { align: 'center', sortable: true,  text: '状態',       value: 'status' },
        { align: 'center', sortable: true,  text: 'CSV-ID',     value: 'id' },
        { align: 'center', sortable: true,  text: '対象者数',   value: 'line' },
        { align: 'center', sortable: true,  text: 'エラー数',   value: 'error' },
        { align: 'left',   sortable: true,  text: 'ファイル名', value: 'filename' },
        { align: 'left',   sortable: true,  text: '登録日時',   value: 'created_at' },
        { align: 'left',   sortable: true,  text: '公開日時',   value: 'published_at' },
        { align: 'center', sortable: false, text: 'アクション', },
      ],

      menu: false,
      activeTab: '検索',
      csvList: false,
      csvTitle: '',
      target: {
        ym: '',
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
            this.tabledata[i].status = wk
          }
        }
      },
    },
  }
</script>
