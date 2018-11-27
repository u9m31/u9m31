<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style type="text/css">
  .title25 { width: 25%; }
  .title50 { width: 50%; }
  .left {  text-align: left; }
  .center {  text-align: center; }
  .right {  text-align: right; }
  .color_white { background-color: #fff; }
  .color_blue  { background-color: #99f; }
  .font_s { font-size: 8; }
  .font_m { font-size: 12; }
</style>
</head>
<body class="color_white">
  <table border="1" width="100%" cellpadding="10" cellspacing="10">
    <tr class="color_blue">
      <td class="title25 font_s left  ">@isset($test01) {{ $test01 }} @endisset</td>
      <td class="title50 font_m center">@isset($test02) {{ $test02 }} @endisset</td>
      <td class="title25 font_s right ">@isset($test03) {{ $test03 }} @endisset</td>
    </tr>
  </table>
</body>
</html>
