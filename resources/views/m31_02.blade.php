<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style type="text/css">
  .w25  { width: 25%; }
  .w50  { width: 50%; }
  .w75  { width: 75%; }
  .w100 { width: 100%; }
  .left   { text-align: left; }
  .center { text-align: center; }
  .right  { text-align: right; }
  .bk_white   { background-color: #fff; }
  .bk_primary { background-color: #3490dc; }
  .bk_table   { background-color: #eee; }
  .font_S { font-size: 8; }
  .font_M { font-size: 10; }
  .font_L { font-size: 12; } 
</style>
</head>
<body class="bk_white"
  ><div
   ><table border="0" width="100%" cellpadding="10" cellspacing="0">
      <!-- タイトル：社名＋給与明細　　対象年月--> 
      <tr class="bk_primary font_L">
        <td class="w75 left">@isset($company) {{ $company }} @else 会社名不明 @endisset　　給与明細</td>
        <td class="w25 right">@isset($ym) {{ $ym }} @else 対象年月不明 @endisset </td>
      </tr>
    </table>
  </div>
  
  <div
   ><table border="1" width="100%" cellpadding="2" cellspacing="0">
      <tr class="font_M">
        <!-- 氏名 -->
        <td class="w25 left   bk_primary">&nbsp;&nbsp;氏名</td>
        <td class="w25 center bk_white  ">@isset($name) {{ $name }} @else 氏名不明 @endisset </td>
        <!-- 総支給額 ： CSVの３カラム目(C)のデータ-->
        <td class="w25 left   bk_primary">&nbsp;&nbsp;@isset($head[3]) {{ $head[3]  }} @endisset </td>
        <td class="w25 right  bk_white  ">@isset($data[3]) {{ $data[3]  }} @endisset </td>
      </tr>
    </table>
  </div>

  <div
   ><table border="0" width="100%" cellpadding="0" cellspacing="2">
      <tr>
      <td class="w50"
       ><table border="1" width="100%" cellpadding="2" cellspacint="0">
          <tr class="bk_primary font_M">
            <td class="w100 center font_M" colspan="2">支　給</td>
          </tr>
          <!-- 支給の明細部分 ： CSVの４カラム目(D)～１４カラム目(N)までのデータ -->
          @for ($i=4; $i<=14; $i++)
            @if ($i % 2 == 0)
              <tr class="bk_white font_S">
            @else
              <tr class="bk_table font_S">
            @endif
                <td class="w50 left ">&nbsp;@isset($head[$i]) {{ $head[$i] }} @endisset </td>
                <td class="w50 right">      @isset($data[$i]) {{ $data[$i] }} @endisset &nbsp;</td>
              </tr>
          @endfor

          <!-- 支給の合計部分 ： CSVの１５カラム目(O)のデータ-->
          <tr class="font_M">
            <td class="w50 left bk_primary">&nbsp;&nbsp;@isset($head[15]) {{ $head[15] }} @endisset </td>
            <td class="w50 right">                      @isset($data[15]) {{ $data[15] }} @endisset &nbsp;</td>
          </tr>
        </table>
      </td>

      <td class="w50"
       ><table border="1" width="100%" cellpadding="2" cellspacint="0">
          <tr class="bk_primary font_M">
            <td class="w100 center font_M" colspan="2">控　除</td>
          </tr>
          <!-- 控除の明細部分 ： CSVの１６カラム目(P)～２６カラム目(Z)までのデータ-->
          @for ($i=16; $i<=26; $i++)
            @if ($i % 2 == 0)
              <tr class="bk_white font_S">
            @else
              <tr class="bk_table font_S">
            @endif
                <td class="w50 left ">&nbsp;@isset($head[$i]) {{ $head[$i] }} @endisset </td>
                <td class="w50 right">      @isset($data[$i]) {{ $data[$i] }} @endisset &nbsp;</td>
              </tr>
          @endfor

          <!-- 控除の合計部分 ： CSVの２７カラム目(AA)のデータ-->
          <tr class="font_M">
            <td class="w50 left bk_primary">&nbsp;&nbsp; @isset($head[27]) {{ $head[27] }} @endisset </td>
            <td class="w50 right">                       @isset($data[27]) {{ $data[27] }} @endisset &nbsp;</td>
          </tr>
        </table>
      </td>
      </tr>
    </table>
  </div>
</body>
</html>
