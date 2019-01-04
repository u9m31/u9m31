<template>
  <v-btn block flat
    :color="color ? color : 'primary'"
    :loading="csvuploading"
    :disabled="csvuploading"
    @click="$refs.input_csvup.click()" 
  >
    <v-icon dark class="mr-1">{{icon ? icon : 'cloud_upload' }}</v-icon> {{title ? title : 'CSV アップロード'}}
    <v-progress-circular slot="csvuploading" indeterminate color="primary" dark></v-progress-circular>

    <input
      name="file"
      :value="csvupfile"
      type="file"
      style="display: none"
      ref="input_csvup"
      accept=".csv,.txt"
      :multiple="multiple"
      @change="onFilePicked"
    >
    <upload-dialog ref="UploadDialog"></upload-dialog>
  </v-btn>
</template>

<script>
  import upload_dialog from './CsvUploadDialog.vue'

  export default {
    name: 'CSVUpload',

    components: {
      'upload-dialog': upload_dialog,
    },

    props: {
      color: String,
      icon: String,
      title: String,
      url: String,
      multiple: String,
      updata: Object,
    },

    data: () => ({
      csvuploading: false,
      csvupfile: null,
    }),

    created() {
      if (process.env.MIX_DEBUG) console.log('CSV Upload Btn created.')
    },

    methods: {
      // アップロードボタンを押すと "<input type=file"　を実行してファイル選択ダイアログを開く
      // ファイル選択ダイアログで何らかの動き(change)があると関数(onFilePicked)を呼ぶ
      // 複数ファイル選択の指示(multple)があった場合に備えてファイル１つずつを非同期で処理しとく(未完）
      async onFilePicked(e) {
        if (process.env.MIX_DEBUG) console.log('CSV Upload onFilePicked')
        const files = e.target.files
        if(files[0] == undefined) return  // ファイル未選択は何もしない

        // ファイルを１つずつ送信 - 非同期処理
        for (var i=0 ; i<files.length ; i++) {
          if (process.env.MIX_DEBUG) console.log("FILE:" + files[i].name + " (" + files[i].size + " byte)")
          await this.csvupload(files[i])
        }
      },

      csvupload(file) {
        return new Promise((resolve, reject) => {
          if (process.env.MIX_DEBUG) console.log('CSV Upload csv upload')
          var config = {
            headers: {'Content-Type': 'multipart/form-data'}
          }

          // 送信ファイル設定
          var formData = new FormData()
          formData.append('csvfile', file)

          // 追加送信パラメータ設定（指定があれば）
          if (this.updata) {
            for (let key in this.updata) {
              formData.append(key, this.updata[key])
            }
          }

          // アップロード実行
          this.csvuploading = true
          axios.post(this.url, formData, config)

          // アップロード 正常
          .then( function (response) {
            this.csvuploading = false
            if (process.env.MIX_DEBUG) console.log("CSV Upload success")
            if (process.env.MIX_DEBUG) console.log(response.data)
            this.resultDialog(file, response.data.import)
          }.bind(this))

          // アップロード 異常
          .catch(function (error) {
            this.csvuploading = false
            if (process.env.MIX_DEBUG) console.log("CSV Upload error")
            if (process.env.MIX_DEBUG) console.log(error)
            if (error.response) {
              if ([401, 419].includes(error.response.status)) {
                this.$emit('axios-logout')
              }
              else if ([422].includes(error.response.status)) {
                if (process.env.MIX_DEBUG) console.log("CSV Upload error 422")
                if (process.env.MIX_DEBUG) console.log(error.response.data.errors)
                if (error.response.data.errors) {
                  for (let key in error.response.data.errors) {
                    if (error.response.data.errors[key]) {
                      alert(file.name + ' : ' + error.response.data.errors[key])
                    }
                  }
                }
              }
              else {
                alert('ERROR ' + error.response.status + ' ' + error.response.statusText)
              }
            }
            else {
              alert('ERROR ' + error)
            }
          }.bind(this))

        return resolve(file)
        })
      },

      resultDialog(file, data) {
        var res = []
        // ERROR
        res.error = ''
        if (data.errors) {
          res.error = this.getResult(data.errors, 'データエラー: ', ' 箇所')
        }

        // UPDATE
        res.result = ''
        if (data.update) {
          res.result = this.getResult(data.update, '更新 : ', ' レコード')
          res.result += '\n'
        }

        // INSERT
        if (data.insert) {
          res.result += this.getResult(data.insert, '新規 : ', ' レコード')
        }

        //  一覧を再読み込み
        this.$emit('reload')

        // ダイアログを開く
        this.$refs.UploadDialog.open(file, res)
      },

      getResult(data, comment1, comment2) {
        var res = comment1 + data.length + comment2 + '\n'
        for (var i=0; i<data.length; i++) {
          res += '　　' + data[i].line + ' 行目 : ' + data[i].message + '\n'  
        }
        return res
      },
    },
  }
</script>
