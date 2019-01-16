<template>
  <transition name="fade">
  <v-dialog v-model="dialog" max-width="650px" persistent>
      <v-card>
      <v-toolbar :color="color ? color : 'primary'" dark>
        <v-toolbar-title>
          <v-icon class="pb-1">{{ icon ? icon : 'cloud_upload' }}</v-icon>
          {{ title ? title : 'CSV アップロード'}}
        </v-toolbar-title>
      </v-toolbar>

        <v-card-text>
          <h2 class="headline">{{filename}}</h2>
          <v-divider></v-divider>
          <v-alert v-model="error.length != 0"  type="error"   outline><pre class="error--text">{{error}}</pre></v-alert>
          <v-alert v-model="result.length != 0" type="success" outline><pre>{{result}}</pre></v-alert>
        </v-card-text>
      
      <v-card-actions>
        <v-btn color="grey darken-1" flat block @click.native="close">閉じる</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
  </transition>
</template>

<script>
  export default {
    name: 'CsvUploadDialog',

    props: {
      color: String,
      icon: String,
      title: String,
    },

    data: () => ({
      dialog: false,
      result: '',
      error: '',
      filename: '',
    }),

    created() {
      if (process.env.MIX_DEBUG) console.log('Csv Upload Dialog created.')
    },

    methods: {
      close() {
        if (process.env.MIX_DEBUG) console.log("Csv Upload Dialog func close")
        this.dialog = false
      },

      open(file, data) {
        if (process.env.MIX_DEBUG) console.log("Csv Upload Dialog func open")
        this.dialog = true
        this.filename = file.name
        this.result = ''
        this.error = ''
        if (data.result) this.result = data.result
        if (data.error) this.error = data.error
      },
    },
  }
</script>
