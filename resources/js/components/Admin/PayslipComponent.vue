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
          :color="searchTab ? 'primary' : 'blue-grey lighten-5'"
          @click="searchTab = true"
        >
          <v-icon class="pr-1">search</v-icon>検索
        </v-btn>
        <v-btn block depressed
          :outline="!searchTab"
          :color="!searchTab ? 'primary' : 'blue-grey lighten-5'"
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
          url="/api/admin/csvpayslip/upload"
        ></csv-upload>

        <!-- 検索ボタン -->
        <v-btn block flat
          v-show="searchTab"
          @click="showList()"
          color="primary"
        >
          <v-icon class="pr-2">search</v-icon>検索
        </v-btn>
      </v-card-actions>
    </v-card>

    <!-- ２段目 ：CSVリスト表示 -->
    <csv-payslip 
      v-show="csvList"
      ref="csvPayslip"
      :target="target"  
      :detail_id="detail_id"
      @axios-logout="$emit('axios-logout')"
      @closeList="closeList"
      @showDetail="showDetail"
    ></csv-payslip>

    <!-- ３段目 ：明細リスト表示 -->
    <payslip-detail 
      v-show="detailList"
      ref="detailPayslip"
      :target="target"  
      @axios-logout="$emit('axios-logout')"
      @closeDetail="closeDetail()"
    ></payslip-detail>
  </v-flex>
</template>

<script>
  import csv_upload from './CsvUpload.vue'
  import csv_payslip from './CsvPayslipComponent.vue'
  import payslip_detail from './PayslipDetailComponent.vue'

  export default {
    name: 'PayslipComponent',

    components: {
      'csv-upload': csv_upload,
      'csv-payslip': csv_payslip,
      'payslip-detail': payslip_detail,
    },

    props: {
    },

    data: () => ({
      menu: false,
      searchTab: true,
      csvList: false,
      detailList: false,
      detail_id: 0,
      target: {
        ym: '',
        deleted: false,
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
        this.showList()
      },

      showList() {
        if (process.env.MIX_DEBUG) console.log('Payslip Component SHOW List')
        this.csvList = true
        this.$refs.csvPayslip.getCsvPayslip()
      },

      closeList() {
        if (process.env.MIX_DEBUG) console.log('Payslip Component CLOSE List')
        this.csvList = false
        this.closeDetail()
      },

      showDetail(item) {
        if (process.env.MIX_DEBUG) console.log('Payslip Component SHOW Detail')
        if (this.detail_id == item.id) { 
            this.closeDetail() 
        } 
        else {
          this.detailList = true
          this.detail_id = item.id
          this.$refs.detailPayslip.getPayslipDetail(item)
        }
      },

      closeDetail() {
        if (process.env.MIX_DEBUG) console.log('Payslip Component CLOSE Detail')
        this.detailList = false
        this.detail_id = 0
      },
    },
  }
</script>
