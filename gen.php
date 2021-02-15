
<!DOCTYPE html>
<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no"
  />
  <title>Clubhouse Thailand Community Calendar</title>
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Clubhouse Thailand Community Calendar" />
  <meta
    property="og:description"
    content="Upcoming"
  />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/light.css">
  <script data-ad-client="ca-pub-3695878871927537" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>
<body>
  <header class="introduction">
    <h1>Clubhouse Thailand Community Calendar</h1>
    <p>
      Post your event link in <a href="https://web.facebook.com/groups/clubhousethailandcommunity">Clubhouse Thailand Community Facebook Group</a>
      and it will show up on this calendar. Updates hourly.
    </p>
  </header>

  <?php
  $data = json_decode(file_get_contents('data/store.json'), true);
  $events = array_filter((array)($data['events']), fn ($e) => !empty($e['date']) && $e['date'] >= '2021-02-15');
  uksort($events, function ($a, $b) use ($events) {
    return strcmp($events[$a]['date'], $events[$b]['date']);
  });
  ?>

<?php function open_table() { ?>
  <table>
    <col width="72">
    <col width="100%">
    <tbody>
<?php } ?>
<?php function close_table() { ?>
</tbody></table>
    <?php } ?>

    <?php open_table(); ?>
    <?php $ldate = ''; $ltime='';?>
  <?php foreach ($events as $k => $v) { ?>
    <?php
    $cdate = substr($v['date'],0,10);
    if ($cdate != $ldate) {
      ?>
      <tr><th colspan=2><h2><?= $cdate ?></h2></th></tr>
      <?php
      $ldate=$cdate;
      $ltime='';
    }
    ?>
    <tr>
      <td>
        <?php $ctime = substr($v['date'],11,5); ?>
        <span style="opacity:<?= $ltime == $ctime ? 0 : 1 ?>"><?= $ctime ?></span>
        <?php $ltime = $ctime; ?>
      </td>
      <td>
        <strong><a href="https://www.joinclubhouse.com/event/<?= $k ?>"><?php echo htmlspecialchars($v['metadata']['title']); ?></a></strong><br />
        <?php echo preg_replace('~^(\S+\s+){6}~', '', htmlspecialchars($v['metadata']['description'])); ?>
      </td>
    </tr>
  <?php } ?>
  <?php close_table(); ?>
  
</body>
