<?php
$data = json_decode(file_get_contents('data/store.json'), true);

$dates = [];
foreach ($data['events'] as $event) {
    if (!empty($event['date'])) {
        $cdate = substr($event['date'], 0, 10);
        $dates[$cdate] = true;
    }
}

$today = strftime("%Y-%m-%d");
$past_dates = array_filter($dates, fn($d) => $d < $today, ARRAY_FILTER_USE_KEY);
ksort($past_dates);

function generate_page($target, $criteria)
{
    global $data;
    ob_start();
    ?>
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
  <meta property="og:image" content="https://ss.dt.in.th/api/screenshots/thaiclubhouse.png" />
  <meta
    property="og:description"
    content="View upcoming Clubhouse events in Thailand here"
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
$events = array_filter(($data['events']), fn($e) => !empty($e['date']) && $criteria($e));
    uksort($events, function ($a, $b) use ($events) {
        return strcmp($events[$a]['date'], $events[$b]['date']);
    });
    ?>
  <table>
    <col width="72">
    <col width="100%">
    <tbody>

    <?php $ldate = '';
    $ltime = '';?>
  <?php foreach ($events as $k => $v) {?>
    <?php
$cdate = substr($v['date'], 0, 10);
        if ($cdate != $ldate) {
            ?>
      <tr><th colspan=2><h2><?=$cdate?></h2></th></tr>
      <?php
$ldate = $cdate;
            $ltime = '';
        }
        ?>
    <tr>
      <td>
        <?php $ctime = substr($v['date'], 11, 5);?>
        <span style="opacity:<?=$ltime == $ctime ? 0 : 1?>"><?=$ctime?></span>
        <?php $ltime = $ctime;?>
      </td>
      <td>
        <strong><a href="https://www.joinclubhouse.com/event/<?=$k?>"><?php echo htmlspecialchars($v['metadata']['title']); ?></a></strong><br />
        <?php echo preg_replace('~^(\S+\s+){6}~', '', htmlspecialchars($v['metadata']['description'])); ?>
        <?php
foreach ($v['sources'] as $sourceUrl => $date) {
            echo "<a style='opacity: 0.64; color: inherit' href='$sourceUrl'>[post]</a>";
        }
        ?>
      </td>
    </tr>
  <?php }?>
</tbody></table>

  <footer>
    <?php
print_footer();
    ?>
      </footer>
<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="/__/firebase/8.2.7/firebase-app.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="/__/firebase/8.2.7/firebase-analytics.js"></script>

<!-- Initialize Firebase -->
<script src="/__/firebase/init.js"></script>
</body>
<?php
$page = ob_get_clean();
    echo "$target\n";
    file_put_contents($target, $page);
}

function print_footer()
{
    global $past_dates;
    $lmonth = '';
    echo '<a href="./">Today and upcoming</a>';
    foreach (array_keys($past_dates) as $date) {
        $month = substr($date, 0, 7);
        if ($month != $lmonth) {
            echo " &middot; $month:";
            $lmonth = $month;
        }
        $mday = substr($date, 8);
        echo " <a href=$date.html>$mday</a>";
    }
    echo ' &middot; built by <a href="https://github.com/dtinth">dtinth</a>';
    echo ' &middot; <a href="https://github.com/dtinth/thaiclubhouse">source</a>';
}

echo $today . "\n";
generate_page('public/index.html', fn($e) => $e['date'] >= $today);

foreach (array_keys($past_dates) as $date) {
    generate_page('public/' . $date . '.html', fn($e) => substr($e['date'], 0, 10) == $date);
}

?>