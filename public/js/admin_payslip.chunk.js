(window.webpackJsonp=window.webpackJsonp||[]).push([[3],{XXZ8:function(t,e,a){"use strict";a.r(e);var s=a("TqlJ"),i={name:"CsvPayslipComponent",components:{},props:{target:Object,detail_id:Number},data:function(){return{loading:!1,search:"",pagination:{sortBy:null,descending:!1},csvTitle:"",tabledata:[],headers:[{align:"center",sortable:!1,text:"No"},{align:"center",sortable:!0,text:"年月",value:"ym"},{align:"center",sortable:!1,text:"アクション"},{align:"center",sortable:!0,text:"状態",value:"status"},{align:"center",sortable:!0,text:"CSV-ID",value:"id"},{align:"center",sortable:!0,text:"対象者数",value:"line"},{align:"center",sortable:!0,text:"エラー数",value:"error"},{align:"left",sortable:!0,text:"ファイル名",value:"filename"},{align:"left",sortable:!0,text:"登録日時",value:"created_at"},{align:"left",sortable:!0,text:"公開日時",value:"published_at"},{align:"left",sortable:!0,text:"削除日時",value:"deleted_at"}],dialog:!1,d:{title:"",titlecolor:"",icon:"",type:"",item:[]}}},created:function(){console.log("CsvPayslip Component created."),this.initialize()},methods:{initialize:function(){},reload:function(){console.log("CsvPayslip Component reload"),this.getCsvPayslip()},initList:function(){console.log("CsvPayslip Component initList"),this.setCsvTitle(),this.tabledata=[],this.search="",this.pagination.sortBy="",this.pagination.descending=!1},closeList:function(){console.log("CsvPayslip Component closeList"),this.tabledata=[],this.search="",this.pagination.sortBy="",this.pagination.descending=!1,this.$emit("closeList")},setCsvTitle:function(){console.log("CsvPayslip Component set csv title"),this.csvTitle="",this.target.ym?this.csvTitle+=this.target.ym+"　":this.csvTitle+="全期間　"},getCsvPayslip:function(){console.log("CsvPayslip Component getCsvPayslip"),this.initList();var t=new URLSearchParams;t.append("ym",this.target.ym?this.target.ym:""),t.append("deleted",!!this.target.deleted&&this.target.deleted),this.loading=!0,axios.post("/api/admin/csvpayslip/index",t).then(function(t){this.loading=!1,console.log(t),t.data.data&&(this.tabledata=t.data.data,this.setStatus())}.bind(this)).catch(function(t){this.loading=!1,console.log(t),t.response?[401,419].includes(t.response.status)?this.$emit("axios-logout"):alert("ERROR "+t.response.status+" "+t.response.statusText):alert("ERROR "+t)}.bind(this))},setStatus:function(){console.log("CsvPayslip Component set status");for(var t="不明",e=0;e<this.tabledata.length;e++)this.tabledata[e].status&&(t="不明",0==this.tabledata[e].status?t="非公開":1==this.tabledata[e].status&&(t="公開"),null!=this.tabledata[e].deleted_at&&(t="削除"),this.tabledata[e].status=t)},dialog_open:function(t,e){console.log("CsvPayslip Component Dialog Open"),this.d.type=e,this.d.item=t,this.d.body="CSV ID："+t.id+"<br>対象年月："+t.ym+"<br>ファイル："+t.filename+"<br><br>","pub"==e?(this.d.title="明細情報を公開します",this.d.titlecolor="success",this.d.body+="対象のデータを公開します。<br>公開後は非公開とすることはできません。"):(this.d.title="明細情報を削除します",this.d.titlecolor="error",this.d.body+="対象のデータを削除します。"),this.dialog=!0},dialog_yes:function(){console.log("CsvPayslip Component Dialog YES"),this.dialog=!1;var t="/api/admin/csvpayslip/publish";"del"==this.d.type&&(t="/api/admin/csvpayslip/delete"),this.csvUpdate(t,this.d.item),this.d.type="",this.d.item=[]},dialog_no:function(){console.log("CsvPayslip Component Dialog NO"),this.dialog=!1,this.d.type="",this.d.item=[]},csvUpdate:function(t,e){console.log("CsvPayslip Component CSV Update");var a=new URLSearchParams;a.append("id",e.id),axios.post(t,a).then(function(t){console.log(t),this.reload(),this.detail_id==e.id&&this.$emit("showDetail",e)}.bind(this)).catch(function(t){console.log(t),t.response?[401,419].includes(t.response.status)?this.$emit("axios-logout"):alert("ERROR "+t.response.status+" "+t.response.statusText):alert("ERROR "+t)}.bind(this))}}},l=a("KHd+"),o=Object(l.a)(i,function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-flex",[a("v-card",{staticClass:"m-3 px-3",attrs:{xs12:""}},[a("v-card-title",{staticClass:"title"},[a("v-icon",{staticClass:"pr-2",on:{click:t.closeList}},[t._v(t._s(t.$route.meta.icon))]),t._v(" "+t._s(t.csvTitle)+" "+t._s()+"\n      "),a("v-spacer"),t._v(" "),a("v-spacer"),t._v(" "),a("v-text-field",{attrs:{"prepend-icon":"search",label:"絞り込み表示","single-line":"","hide-details":"",clearable:""},model:{value:t.search,callback:function(e){t.search=e},expression:"search"}}),t._v(" "),a("v-icon",{staticClass:"accent ml-5",attrs:{dark:""},on:{click:t.closeList}},[t._v("close")])],1),t._v(" "),a("v-data-table",{staticClass:"elevation-0 p-1",attrs:{headers:t.headers,items:t.tabledata,pagination:t.pagination,"rows-per-page-items":[5,10,20,{text:"All",value:-1}],loading:t.loading,search:t.search},on:{"update:pagination":function(e){t.pagination=e}},scopedSlots:t._u([{key:"items",fn:function(e){return[a("tr",{directives:[{name:"show",rawName:"v-show",value:e.item.id==t.detail_id||0==t.detail_id,expression:"props.item.id == detail_id || detail_id == 0"}],class:{"pink--text":null!=e.item.deleted_at}},[a("td",{staticClass:"text-xs-center",attrs:{xs1:""}},[t._v(t._s(e.index+1+(t.pagination.page-1)*t.pagination.rowsPerPage))]),t._v(" "),t._l(t.headers.length-1,function(s){return["アクション"!=t.headers[s].text?a("td",{class:"text-xs-"+t.headers[s].align,staticStyle:{"white-space":"nowrap"},domProps:{textContent:t._s(e.item[t.headers[s].value])}}):a("td",{class:"text-xs-"+t.headers[s].align,staticStyle:{"white-space":"nowrap"}},[a("v-tooltip",{attrs:{right:"",color:null==e.item.deleted_at?"primary":"grey"}},[a("v-btn",{attrs:{slot:"activator",fab:"",small:"",flat:""},on:{click:function(a){return t.$emit("showDetail",e.item)}},slot:"activator"},[a("v-icon",{attrs:{color:"primary"}},[t._v("list")])],1),t._v(" "),a("span",[t._v("明細一覧")])],1),t._v(" "),null==e.item.published_at?a("v-tooltip",{attrs:{right:"",color:null==e.item.deleted_at?"success":"grey"}},[a("v-btn",{attrs:{slot:"activator",fab:"",small:"",flat:"",disabled:null!=e.item.deleted_at},on:{click:function(a){return t.dialog_open(e.item,"pub")}},slot:"activator"},[a("v-icon",{attrs:{color:"success"}},[t._v("lock")])],1),t._v(" "),a("span",[t._v("公開")])],1):a("v-btn",{attrs:{fab:"",small:"",flat:"",disabled:""}},[a("v-icon",{attrs:{color:"grey lighten-1"}},[t._v("lock_open")])],1),t._v(" "),a("v-tooltip",{attrs:{right:"",color:null==e.item.deleted_at?"error":"grey"}},[a("v-btn",{attrs:{slot:"activator",fab:"",small:"",flat:"",disabled:null!=e.item.deleted_at},on:{click:function(a){return t.dialog_open(e.item,"del")}},slot:"activator"},[a("v-icon",{attrs:{color:"error"}},[t._v("delete")])],1),t._v(" "),a("span",[t._v(t._s(null==e.item.deleted_at?"削除":"削除済"))])],1)],1)]})],2)]}}])},[a("v-progress-linear",{attrs:{slot:"progress",color:"blue",indeterminate:""},slot:"progress"})],1)],1),t._v(" "),a("v-dialog",{attrs:{width:"500",persistent:""},model:{value:t.dialog,callback:function(e){t.dialog=e},expression:"dialog"}},[a("v-card",[a("v-toolbar",{attrs:{color:t.d.titlecolor,dark:""}},[a("v-toolbar-title",[t._v(t._s(t.d.title))])],1),t._v(" "),a("v-card-text",{staticClass:"subheading"},[a("span",{domProps:{innerHTML:t._s(t.d.body)}}),t._v(" "),a("br"),t._v("よろしいですか？\n      ")]),t._v(" "),a("v-divider"),t._v(" "),a("v-card-actions",[a("v-btn",{attrs:{flat:"",block:""},on:{click:function(e){return t.dialog_no()}}},[t._v(" いいえ ")]),t._v(" "),a("v-spacer"),t._v(" "),a("v-btn",{attrs:{flat:"",block:"",color:t.d.titlecolor},on:{click:function(e){return t.dialog_yes()}}},[t._v(" はい ")])],1)],1)],1)],1)},[],!1,null,null,null);o.options.__file="CsvPayslipComponent.vue";var n=o.exports,r={name:"PayslipDetailComponent",components:{},props:{target:Object},data:function(){return{loading:!1,search:"",pagination:{sortBy:"",descending:!1},title:"",csv_item:{},tabledata:[],headers:[{align:"center",sortable:!1,text:"No"},{align:"center",sortable:!1,text:"アクション"},{align:"center",sortable:!0,text:"CSV行",value:"line"},{align:"left",sortable:!0,text:"氏名",value:"name"},{align:"left",sortable:!0,text:"ログインID",value:"loginid"},{align:"center",sortable:!0,text:"DL回数",value:"download"},{align:"left",sortable:!0,text:"ファイル名",value:"filename"},{align:"left",sortable:!0,text:"CSVエラー",value:"error"},{align:"left",sortable:!0,text:"削除日時",value:"deleted_at"},{align:"left",sortable:!0,text:"削除者ID",value:"delete_user_id"},{align:"center",sortable:!0,text:"CSV-ID",value:"csv_id"},{align:"left",sortable:!0,text:"状態",value:"status"},{align:"center",sortable:!0,text:"年月",value:"ym"},{align:"center",sortable:!0,text:"ユーザID",value:"user_id"}],dialog:!1,d:{title:"",titlecolor:"",icon:"",item:[]}}},created:function(){console.log("PayslipDetail Component created."),this.initialize()},methods:{initialize:function(){},reload:function(){console.log("PayslipDetail Component reload"),this.getPayslipDetail()},initList:function(){console.log("PayslipDetail Component initList"),this.setTitle(),this.tabledata=[],this.search="",this.pagination.sortBy="",this.pagination.descending=!1},closeDetail:function(){console.log("PayslipDetail Component closeDetail"),this.tabledata=[],this.search="",this.pagination.sortBy="",this.pagination.descending=!1,this.$emit("closeDetail")},setTitle:function(){console.log("PayslipDetail Component set title"),this.title="",this.title="ID: "+this.csv_item.id+"  - "+this.csv_item.ym,null!=this.csv_item.deleted_at?this.title+=" - 削除済":null!=this.csv_item.published_at?this.title+=" - 公開済":this.title+=" - 未公開"},getPayslipDetail:function(t){console.log("PayslipDetail Component getPaslipDetail"),this.csv_item=t||this.csv_item,this.initList();var e=new URLSearchParams;e.append("id",this.csv_item.id),this.loading=!0,axios.post("/api/admin/payslip/index",e).then(function(t){this.loading=!1,console.log(t),t.data.data&&(this.tabledata=t.data.data,this.setStatus())}.bind(this)).catch(function(t){this.loading=!1,console.log(t),t.response?[401,419].includes(t.response.status)?this.$emit("axios-logout"):alert("ERROR "+t.response.status+" "+t.response.statusText):alert("ERROR "+t)}.bind(this))},setStatus:function(){console.log("PayslipDetail Component set status");for(var t="不明",e=0;e<this.tabledata.length;e++)this.tabledata[e].status&&(t="不明",0==this.tabledata[e].status?t="有効":9==this.tabledata[e].status&&(t="削除"),null!=this.tabledata[e].deleted_at&&(t="削除"),this.tabledata[e].status=t)},dialog_open:function(t){console.log("PayslipDetail Component Dialog Open"),this.d.item=t,this.d.body="CSV行："+t.line+"<br>対象年月："+t.ym+"<br>対象者："+t.name+"<br>ログインID："+t.loginid+"<br>ダウンロード回数："+t.download+" 回<br><br>",this.d.title="明細情報を削除します",this.d.titlecolor="error",this.d.body+="対象のデータを削除します。",t.download>0&&(this.d.body+="<br> 利用者は明細をダウンロードしたことがあるようです。"),this.dialog=!0},dialog_yes:function(){console.log("PayslipDetail Component Dialog YES"),this.dialog=!1;this.detailUpdate("/api/admin/payslip/delete",this.d.item.id),this.d.item=[]},dialog_no:function(){console.log("PayslipDetail Component Dialog NO"),this.dialog=!1,this.d.item=[]},detailUpdate:function(t,e){console.log("PayslipDetail Component Detail Update");var a=new URLSearchParams;a.append("id",e),axios.post(t,a).then(function(t){console.log(t),this.reload()}.bind(this)).catch(function(t){console.log(t),t.response?[401,419].includes(t.response.status)?this.$emit("axios-logout"):alert("ERROR "+t.response.status+" "+t.response.statusText):alert("ERROR "+t)}.bind(this))},pdf_download:function(t){console.log("PayslipDetail Component PDF Download");var e=new URLSearchParams;e.append("loginid",t.loginid),e.append("name",t.name),e.append("yyyymm",t.ym),e.append("csv_id",t.csv_id),e.append("id",t.id);this.loading=!0,axios.post("/api/admin/payslip/pdf",e,{responseType:"blob"}).then(function(e){if(this.loading=!1,e.data){var a=new Blob([e.data],{type:"application/pdf"}),s="";null!=t.filename&&(s="("+t.filename+")");var i="給与明細";if(i+="_"+t.ym,i+="_"+t.name.replace(/　/g,"").replace(/ /g,"").replace(/\//g,""),i+=s,i+="_"+moment(Date.now()).format("YYYYMMDD_HHmmss"),i+=".pdf",window.navigator.msSaveBlob)window.navigator.msSaveBlob(a,i),window.navigator.msSaveOrOpenBlob(a,i);else{var l=window.URL.createObjectURL(a),o=document.createElement("a");o.href=l,o.setAttribute("download",i),document.body.appendChild(o),o.click(),o.remove()}}else alert("PDF ダウンロードエラー")}.bind(this)).catch(function(t){if(this.loading=!1,console.log(t),t.response)if([401,419].includes(t.response.status))this.$emit("axios-logout");else if([422].includes(t.response.status)){console.log("PDF Download error 422"),console.log(t.response.data);var e=new FileReader;e.onload=function(t){var a=JSON.parse(e.result);if(console.log(a),a.errors)for(var s in a.errors)a.errors[s]&&alert(s+" : "+a.errors[s])},e.readAsText(t.response.data,"UTF-8")}else alert("ERROR "+t.response.status+" "+t.response.statusText);else alert("ERROR "+t)}.bind(this))}}},c=Object(l.a)(r,function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-flex",[a("v-card",{staticClass:"m-3 px-3",attrs:{xs12:""}},[a("v-card-title",{staticClass:"title"},[a("v-icon",{staticClass:"pr-2",on:{click:t.closeDetail}},[t._v(t._s(t.$route.meta.icon))]),t._v(" "+t._s(t.title)+" "+t._s()+"\n      "),a("v-spacer"),t._v(" "),a("v-spacer"),t._v(" "),a("v-text-field",{attrs:{"prepend-icon":"search",label:"絞り込み表示","single-line":"","hide-details":"",clearable:""},model:{value:t.search,callback:function(e){t.search=e},expression:"search"}}),t._v(" "),a("v-icon",{staticClass:"accent ml-5",attrs:{dark:""},on:{click:t.closeDetail}},[t._v("close")])],1),t._v(" "),a("v-data-table",{staticClass:"elevation-0 p-1",attrs:{headers:t.headers,items:t.tabledata,pagination:t.pagination,"rows-per-page-items":[10,25,50,{text:"All",value:-1}],loading:t.loading,search:t.search},on:{"update:pagination":function(e){t.pagination=e}},scopedSlots:t._u([{key:"items",fn:function(e){return[a("tr",{class:{"pink--text":null!=e.item.deleted_at}},[a("td",{staticClass:"text-xs-center",attrs:{xs1:""}},[t._v(t._s(e.index+1+(t.pagination.page-1)*t.pagination.rowsPerPage))]),t._v(" "),t._l(t.headers.length-1,function(s){return["アクション"!=t.headers[s].text?a("td",{class:"text-xs-"+t.headers[s].align,staticStyle:{"white-space":"nowrap"},domProps:{textContent:t._s(e.item[t.headers[s].value])}}):a("td",{class:"text-xs-"+t.headers[s].align,staticStyle:{"white-space":"nowrap"}},[a("v-tooltip",{attrs:{right:"",color:null!=t.csv_item.published_at?"success":"grey"}},[a("v-btn",{attrs:{slot:"activator",fab:"",small:"",flat:"",disabled:null!=e.item.error},on:{click:function(a){return t.pdf_download(e.item)}},slot:"activator"},[a("v-icon",{attrs:{color:null!=t.csv_item.published_at?"success":"grey"}},[t._v("description")])],1),t._v(" "),a("span",[t._v("明細PDF")])],1),t._v(" "),a("v-tooltip",{attrs:{right:"",color:null==e.item.deleted_at?"error":"grey"}},[a("v-btn",{attrs:{slot:"activator",fab:"",small:"",flat:"",disabled:null!=e.item.deleted_at||null!=t.csv_item.deleted_at},on:{click:function(a){return t.dialog_open(e.item,"del")}},slot:"activator"},[a("v-icon",{attrs:{color:"error"}},[t._v("delete")])],1),t._v(" "),a("span",[t._v(t._s(null==e.item.deleted_at?"削除":"削除済"))])],1)],1)]})],2)]}}])},[a("v-progress-linear",{attrs:{slot:"progress",color:"blue",indeterminate:""},slot:"progress"})],1)],1),t._v(" "),a("v-dialog",{attrs:{width:"500",persistent:""},model:{value:t.dialog,callback:function(e){t.dialog=e},expression:"dialog"}},[a("v-card",[a("v-toolbar",{attrs:{color:t.d.titlecolor,dark:""}},[a("v-toolbar-title",[t._v(t._s(t.d.title))])],1),t._v(" "),a("v-card-text",{staticClass:"subheading"},[a("span",{domProps:{innerHTML:t._s(t.d.body)}}),t._v(" "),a("br"),t._v("よろしいですか？\n      ")]),t._v(" "),a("v-divider"),t._v(" "),a("v-card-actions",[a("v-btn",{attrs:{flat:"",block:""},on:{click:function(e){return t.dialog_no()}}},[t._v(" いいえ ")]),t._v(" "),a("v-spacer"),t._v(" "),a("v-btn",{attrs:{flat:"",block:"",color:t.d.titlecolor},on:{click:function(e){return t.dialog_yes()}}},[t._v(" はい ")])],1)],1)],1)],1)},[],!1,null,null,null);c.options.__file="PayslipDetailComponent.vue";var d=c.exports,p={name:"PayslipComponent",components:{"csv-upload":s.a,"csv-payslip":n,"payslip-detail":d},props:{},data:function(){return{menu:!1,searchTab:!0,csvList:!1,detailList:!1,detail_id:0,target:{ym:"",deleted:!1}}},created:function(){console.log("Payslip Component created."),this.initialize()},methods:{initialize:function(){this.target.ym=moment().format("YYYY-MM").toString()},reload:function(){console.log("Payslip Component reload"),this.showList()},showList:function(){console.log("Payslip Component SHOW List"),this.csvList=!0,this.$refs.csvPayslip.getCsvPayslip()},closeList:function(){console.log("Payslip Component CLOSE List"),this.csvList=!1,this.closeDetail()},showDetail:function(t){console.log("Payslip Component SHOW Detail"),this.detail_id==t.id?this.closeDetail():(this.detailList=!0,this.detail_id=t.id,this.$refs.detailPayslip.getPayslipDetail(t))},closeDetail:function(){console.log("Payslip Component CLOSE Detail"),this.detailList=!1,this.detail_id=0}}},v=Object(l.a)(p,function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-flex",[a("v-card",{directives:[{name:"show",rawName:"v-show",value:!t.csvList,expression:"!csvList"}],staticClass:"m-3 px-3",attrs:{xs12:""}},[a("v-card-title",{staticClass:"title"},[a("v-icon",{staticClass:"pr-2"},[t._v(t._s(t.$route.meta.icon))]),t._v(" "+t._s(t.$route.meta.name)+" "+t._s()+"\n      【"+t._s(t.searchTab?"検索":"登録")+"】\n      "),a("v-spacer"),t._v(" "),a("v-btn",{attrs:{block:"",depressed:"",outline:t.searchTab,color:t.searchTab?"primary":"blue-grey lighten-5"},on:{click:function(e){t.searchTab=!0}}},[a("v-icon",{staticClass:"pr-1"},[t._v("search")]),t._v("検索\n      ")],1),t._v(" "),a("v-btn",{attrs:{block:"",depressed:"",outline:!t.searchTab,color:t.searchTab?"blue-grey lighten-5":"primary"},on:{click:function(e){t.searchTab=!1}}},[a("v-icon",{staticClass:"pr-1"},[t._v("cloud_upload")]),t._v("登録\n      ")],1)],1),t._v(" "),a("v-layout",{staticClass:"mx-3 my-2",attrs:{row:"",wrap:""}},[a("v-flex",{attrs:{xs2:"",md4:"",lg3:""}},[a("v-menu",{ref:"menu",attrs:{"return-value":t.target.ym,"close-on-content-click":!1,"nudge-right":20,lazy:"",transition:"scale-transition","offset-y":"","full-width":"","max-width":"290px","min-width":"290px","show-current":"true"},on:{"update:returnValue":function(e){return t.$set(t.target,"ym",e)},"update:return-value":function(e){return t.$set(t.target,"ym",e)}},model:{value:t.menu,callback:function(e){t.menu=e},expression:"menu"}},[a("v-text-field",{attrs:{slot:"activator",readonly:"",clearable:"",autofocus:"",label:"対象年月",placeholder:"明細の対象年月を選択",hint:"明細の対象年月を選択"+(t.searchTab?"(指定なしで全期間対象)":"")},slot:"activator",model:{value:t.target.ym,callback:function(e){t.$set(t.target,"ym",e)},expression:"target.ym"}}),t._v(" "),a("v-date-picker",{attrs:{type:"month","no-title":"",scrollable:"",locale:"ja"},model:{value:t.target.ym,callback:function(e){t.$set(t.target,"ym",e)},expression:"target.ym"}},[a("v-spacer"),t._v(" "),a("v-btn",{attrs:{flat:"",color:"primary"},on:{click:function(e){t.menu=!1}}},[t._v("Cancel")]),t._v(" "),a("v-btn",{attrs:{flat:"",color:"primary"},on:{click:function(e){return t.$refs.menu.save(t.target.ym)}}},[t._v("OK")])],1)],1)],1),t._v(" "),t.searchTab?a("v-flex",{attrs:{xs5:"",md4:"",lg3:""}},[a("v-switch",{staticClass:"px-2",attrs:{label:"削除済データ "+(t.target.deleted?"対象":"対象外"),color:"error",hint:"削除済みデータも検索したい場合に指定"},model:{value:t.target.deleted,callback:function(e){t.$set(t.target,"deleted",e)},expression:"target.deleted"}})],1):t._e()],1),t._v(" "),a("v-card-actions",{staticClass:"pb-2"},[a("csv-upload",{directives:[{name:"show",rawName:"v-show",value:!t.searchTab,expression:"!searchTab"}],attrs:{updata:t.target,color:"primary",icon:"cloud_upload",url:"/api/admin/csvpayslip/upload"},on:{reload:t.reload,"axios-logout":function(e){return t.$emit("axios-logout")}}}),t._v(" "),a("v-btn",{directives:[{name:"show",rawName:"v-show",value:t.searchTab,expression:"searchTab"}],attrs:{block:"",flat:"",color:"primary"},on:{click:function(e){return t.showList()}}},[a("v-icon",{staticClass:"pr-2"},[t._v("search")]),t._v("検索\n      ")],1)],1)],1),t._v(" "),a("csv-payslip",{directives:[{name:"show",rawName:"v-show",value:t.csvList,expression:"csvList"}],ref:"csvPayslip",attrs:{target:t.target,detail_id:t.detail_id},on:{"axios-logout":function(e){return t.$emit("axios-logout")},closeList:t.closeList,showDetail:t.showDetail}}),t._v(" "),a("payslip-detail",{directives:[{name:"show",rawName:"v-show",value:t.detailList,expression:"detailList"}],ref:"detailPayslip",attrs:{target:t.target},on:{"axios-logout":function(e){return t.$emit("axios-logout")},closeDetail:function(e){return t.closeDetail()}}})],1)},[],!1,null,null,null);v.options.__file="PayslipComponent.vue";e.default=v.exports}}]);