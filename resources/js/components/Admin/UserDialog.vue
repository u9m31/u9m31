<template>
  <transition name="fade">
  <v-dialog v-model="dialog" max-width="650px" persistent>
    <v-btn slot="activator" color="primary" dark class="mb-2" flat @click="open(null)"><v-icon class="pr-2">person_add</v-icon></v-btn>
    <v-card>
      <v-toolbar :color="titlecolor" dark>
        <v-toolbar-title><v-icon class="pb-1">{{ $route.meta.icon }}</v-icon> {{ $route.meta.name }} | {{ title }}</v-toolbar-title>
      </v-toolbar>

      <v-card-text>
        <v-container>
          <v-layout column wrap>
            <v-text-field class="pb-3" label="名前" placeholder="氏名を入力してください"
                          v-model="items.name"    
                          :error-messages="error.name"
                          :rules="[rules.required, rules.min2]"
                          maxlength="64"
                          required
                          counter
                          :disabled="type == 'D'"
            ></v-text-field>
            <v-text-field class="pb-3" label="ログインID" placeholder="社員ＩＤを入力してください"
                          v-model="items.loginid" 
                          :error-messages="error.loginid" 
                          :rules="[rules.required, rules.min6]"
                          maxlength="128"
                          required
                          counter
                          :disabled="type != 'C'"
            ></v-text-field>
            <v-text-field class="pb-2" label="パスワード" :placeholder="placeholder_password"
                          v-model="items.pass"    
                          :error-messages="error.pass"
                          maxlength="128"
                          counter
                          :disabled="type == 'D'"
            ></v-text-field>
            <v-checkbox v-model="items.role"    label="管理者権限" :disabled="type == 'D'"></v-checkbox>
          </v-layout>
        </v-container>
      </v-card-text>
      
      <v-card-actions>
        <v-btn color="grey    darken-1" flat block @click.native="close">キャンセル</v-btn>
        <v-btn color="primary darken-1"  flat block @click.native="save" v-show="type != 'D'">保存</v-btn>
        <v-btn color="error   darken-1"  flat block @click.native="destroy" v-show="type == 'D'">削除</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
  </transition>
</template>

<script>
  export default {
    name: 'UserDialog',

    props: {
    },

    data: () => ({
      dialog: false,
      title: '編集',
      titlecolor: 'primary',
      placeholder_password: '',
      type: '',

      items: { 
        loginid: '',
        name: '',
        pass: '',
        role: false,
      },
      orig: {},
      error: {},


      rules: {
        required: value => !!value || 'Required.',
        min2: value => value.length >= 2 || 'Min 2 characters',
        min6: value => value.length >= 6 || 'Min 6 characters',
      },
    }),

    created() {
      if (process.env.MIX_DEBUG) console.log('User Dialog created.')
    },

    methods: {

      clearVar() {
        this.dialog = true
        this.clearError()
        this.items = JSON.parse(JSON.stringify(this.error))
        this.orig = JSON.parse(JSON.stringify(this.error))
      },

      clearError() {
        this.error = { 
          loginid: '', 
          name: '', 
          pass: '', 
          role: false, 
        }
      },

      close() {
        if (process.env.MIX_DEBUG) console.log("User Dialog func close")
        this.dialog = false
      },

      save() {
        if (process.env.MIX_DEBUG) console.log("User Dialog func save")

        // 変更があった時だけ通信
        if (JSON.stringify(this.orig).replace(/[\s|　]+/g,'') !== JSON.stringify(this.items).replace(/[\s|　]+/g,'')){
          this.store()
        } 

        // 変更がなければただ閉じる
        else {
          this.close()
        }
      },

      open(item, flg) {
        if (process.env.MIX_DEBUG) console.log("User Dialog func open")

        // INIT VAR
        this.clearVar()

        // SET TYPE
        if (flg) this.type = 'D' // DELETE
        else if (item) this.type = 'U' // UPDATE
        else this.type = 'C' // CREATE

        // USER CREATE
        if (this.type == 'C') {
          this.title = "新規追加"
          this.titlecolor = 'primary',
          this.placeholder_password = "パスワードを指定してください（未指定の場合はログインＩＤを設定）"
        }

        // USER UPDATE
        if (this.type == 'U') {
          this.title = "編集"
          this.titlecolor = 'accent',
          this.placeholder_password = "変更する場合はパスワードを指定してください（未指定の場合は変更しない）"
        }

        // USER DELETE
        if (this.type == 'D') {
          this.title = "削除"
          this.titlecolor = 'error',
          this.placeholder_password = ""
        }

          // SET ITEM
        if (item) {
          if (item.loginid) this.items.loginid = item.loginid
          if (item.name) this.items.name = item.name
          if (item.role) {
            if (item.role == '管理者') {
              this.items.role = true
            }
          }
          // COPY ORIG
          this.orig = JSON.parse(JSON.stringify(this.items))
        }
      },

      store() {
        if (process.env.MIX_DEBUG) console.log("User Dialog func store")
        var params = new URLSearchParams()
        params.append('loginid', this.items.loginid)
        params.append('name', this.items.name)
        params.append('pass', this.items.pass)
        params.append('role', (this.items.role ? 5 : 10))
        params.append('type', this.type)

        this.clearError()

        axios.post('/api/admin/user/store', params)

        .then( function (response) {
          this.$emit('reload')
          alert(this.items.name + "を保存しました")
          this.close()  // 保存が正常終了したら閉じる
        }.bind(this))

        .catch(function (error) {
          if (process.env.MIX_DEBUG) console.log("User Dialog store error")
          console.log(error)
          if (error.response && [401, 419].includes(error.response.status)) {
            this.$emit('axios-logout')
          }
          else if (error.response && [423].includes(error.response.status)) {
            this.$emit('setsearch', this.items.loginid)
            alert(error.response.data.message)
            return
          }
          else if (error.response && [422].includes(error.response.status)) {
            alert(error.response.data.message)
            if (error.response.data.errors) {
              for (let key in this.error) {
                if (error.response.data.errors[key]) {
                  this.error[key] = error.response.data.errors[key]
                }
              }
            }
            return
          }
          this.close()
        }.bind(this))
      },

      destroy() {
        if (process.env.MIX_DEBUG) console.log("User Dialog func destroy")
        var params = new URLSearchParams()
        params.append('loginid', this.items.loginid)

        axios.post('/api/admin/user/destroy', params)

        .then( function (response) {
          this.$emit('reload')
          alert(this.items.name + "\n" + "を削除しました")
          this.close()  // 保存が正常終了したら閉じる
        }.bind(this))

        .catch(function (error) {
          if (process.env.MIX_DEBUG) console.log("User Dialog destroy error")
          console.log(error)
          if (error.response && [401, 419].includes(error.response.status)) {
            this.$emit('axios-logout')
          }
          if (error.response && [422].includes(error.response.status)) {
            alert(error.response.data.message) 
            return
          }
          this.close()
        }.bind(this))
      },

    },
  }
</script>
