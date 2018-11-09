<template>
  <v-btn block flat
    :color="color ? color : 'primary'"
    :loading="csvdownloading"
    :disabled="csvdownloading"
    @click="csvdownload(filename, url)"
  >
    <v-icon dark class="mr-1">{{icon ? icon : 'cloud_download' }}</v-icon> {{title ? title : 'CSV ダウンロード'}}
    <v-progress-circular slot="csvdownload" indeterminate color="primary" dark></v-progress-circular>
  </v-btn>
</template>

<script>
  export default {
    name: 'CSVDownload',

    props: {
      color: String,
      icon: String,
      title: String,
      url: String,
      filename: String,
    },

    data: () => ({
      csvdownloading: false,
    }),

    created() {
      if (process.env.MIX_DEBUG) console.log('CSV Download Btn created.')
    },

    methods: {
      csvdownload(filename, url) {
        if (process.env.MIX_DEBUG) console.log("CSV Download func csvdownload")
        var config = {
          responseType: 'blob',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }

        this.csvdownloading = true
        axios.post(this.url, config)

        .then( function (response) {
          this.csvdownloading = false
          this.saveCsvFile(response)
        }.bind(this))

        .catch(function (error) {
          if (process.env.MIX_DEBUG) console.log("CSV Download csvdownload error")
          this.csvdownloading = false
          console.log(error)
          if (error.response && [401, 419].includes(error.response.status)) {
            this.$emit('axios-logout')
          }
        }.bind(this))
      },

      saveCsvFile(res) {
        // CSVデータ取得 - BOM 付与
        var blob = new Blob(['\ufeff' + res.data], { type: 'text/csv' })

        // ファイル名設定 - ファイル名には日時をつけて拡張子 csv を設定
        //                - ボタンの引数で指定された名前があれば尊重 
        //                - 指定なしならルーティングのページ名をつけておく
        //                - （サーバから指定されたファイル名は無視してます）
        var filename = this.filename
        if (! filename) {
          filename = this.$route.meta.name
        }
        filename += '_' + moment(Date.now()).format("YYYYMMDD_HHmmss") + '.csv'

        // IE11 ( msSaveBlog が有効なら)
        if (window.navigator.msSaveBlob) {
          window.navigator.msSaveBlob(blob, filename)
          window.navigator.msSaveOrOpenBlob(blob, filename)
        }

        // IE11 以外なら( Chrome, Firefox, Android, etc...)
        else {
          const url = window.URL.createObjectURL(blob)
          const link = document.createElement('a')
          link.href = url
          link.setAttribute('download', filename)
          link.click()
        }
      },
    },
  }
</script>
