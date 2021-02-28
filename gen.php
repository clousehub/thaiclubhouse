<?php

/*

>>>>>>>>>>> CONTENT WARNING: UGLY CODE. <<<<<<<<<<<

I built this site in a rush so the code is very bad.
MANY BEST PRACTICES GETS THROWN OUT THE WINDOW!!!!

 */

require 'vendor/autoload.php';

if (!file_exists('data')) die('No data directory found, please run `git clone https://github.com/clousehub/thaiclubhouse-data.git data`');

$shards = [];
foreach (glob('data/store_shards/*.json') as $filename) {
    $shards[] = json5_decode(iconv('utf-8', 'utf-8//IGNORE', file_get_contents($filename)), true, 512, JSON_INVALID_UTF8_SUBSTITUTE | JSON_INVALID_UTF8_IGNORE);
}
$data = array_merge_recursive(...$shards);

$dates = [];
foreach ($data['events'] as $event) {
    if (!empty($event['date'])) {
        $cdate = substr($event['date'], 0, 10);
        $dates[$cdate] = true;
    }
}

$today = strftime("%Y-%m-%d");
$past_dates = array_filter($dates, fn($d) => $d <= $today, ARRAY_FILTER_USE_KEY);
ksort($past_dates);

function generate_page($target, $criteria, $socialDate, $mode)
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
  <meta property="og:image" content="https://ss.dt.in.th/api/screenshots/thaiclubhouse.png?date=<?=$socialDate;?>" />
  <meta
    property="og:description"
    content="View upcoming Clubhouse events in Thailand here"
  />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/light.css">
  <script data-ad-client="ca-pub-3695878871927537" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <script defer src="js/index.js"></script>
</head>
<body>
  <header class="introduction">
    <h1>👋 thaiclubhouse.web.app <small style="display: block; opacity: 0.5; font-weight: normal; font-size: 0.75em;"><a href="https://web.facebook.com/groups/clubhousethailandcommunity" style="color: inherit">Clubhouse Thailand Community</a> Calendar</small></h1>
    <style>
      @media (max-width: 720px) {
        h1 { font-size: 1.5em; }
      }
      @media (max-width: 360px) {
        h1 { font-size: 1.25em; }
      }
    </style>
    <!-- p>
      Post your <strong>scheduled</strong> event link in <a href="https://web.facebook.com/groups/clubhousethailandcommunity">Clubhouse Thailand Community Facebook Group</a> (or comment with event link in any post inside the group) and it will show up on this calendar. Updates hourly.
    </p -->
  </header>

  <?php if ($mode == 'home') {?>
  <div style="background: #f5e5d1; padding: 12px; border-radius: 5px; display: flex;">
    <div style="flex: none;">ℹ️</div>
    <p style="margin: 0 0 0 1em; flex: 1;">
      <strong><a href="https://docs.google.com/forms/d/e/1FAIpQLScor_K1u6GZG_JJWLyxTfC4T72smZftcKBn2_ZudlESyQdx9w/viewform?usp=sf_link">คลิกที่นี่เพื่อเพิ่มอีเว้นต์ในหน้านี้</a></strong>
      หรือทวีตลิงค์ติดแฮชแท็ก <a href="https://twitter.com/search?q=%23ClubhouseTH">#ClubhouseTH</a> <a href="https://twitter.com/search?q=%23%E0%B9%84%E0%B8%97%E0%B8%A2%E0%B8%84%E0%B8%A5%E0%B8%B1%E0%B8%9A%E0%B9%80%E0%B8%AE%E0%B9%89%E0%B8%B2%E0%B8%AA%E0%B9%8C">#ไทยคลับเฮ้าส์</a>
      <!-- span style="opacity: 0.5">(ขณะนี้ระบบดึงข้อมูลจาก <a href="https://web.facebook.com/groups/clubhousethailandcommunity">Facebook Group</a> ขัดข้อง)</span -->
    </p>
  </div>
  <?php }?>

  <?php
$events = array_filter(($data['events']), fn($e) => !empty($e['date']) && $criteria($e));
    uksort($events, function ($a, $b) use ($events) {
        return strcmp($events[$a]['date'], $events[$b]['date']);
    });
    ?>
  <table>
    <col width="64">
    <col width="100%">
    <tbody>

    <?php $ldate = '';
    $ltime = '';?>
  <?php foreach ($events as $k => $v) {?>
    <?php
$cdate = substr($v['date'], 0, 10);
        if ($cdate != $ldate) {
            ?>
      <tr><th colspan=2><h2><?= DateFormatter::format_date($cdate) ?></h2></th></tr>
      <?php
$ldate = $cdate;
            $ltime = '';
        }
        ?>
    <tr>
      <td nowrap>
        <?php $ctime = substr($v['date'], 11, 5);?>
        <span style="opacity:<?=$ltime == $ctime ? 0 : 1?>"><?=$ctime?></span>
        <?php $ltime = $ctime;?>
      </td>
      <td>
        <strong><a href="https://www.joinclubhouse.com/event/<?=$k?>"><?php echo htmlspecialchars($v['metadata']['title']); ?></a></strong><br />
        <?php echo preg_replace('~^(\S+\s+){6}~', '', htmlspecialchars($v['metadata']['description'])); ?>
        <?php
foreach ($v['sources'] as $sourceUrl => $date) {
            echo "<a style='opacity: 0.64; color: inherit' href='$sourceUrl'>[ref]</a>";
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
<script>firebase.analytics();</script>
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
    echo '<br>built by <a href="https://github.com/dtinth">dtinth</a>';
    echo ' &middot; <a href="https://github.com/clousehub/thaiclubhouse">source code</a>';
    echo ' &middot; <a href="https://github.com/clousehub/thaiclubhouse-data">json</a>';
    echo ' &middot; <a href="https://www.facebook.com/groups/clubhousethailandcommunity/permalink/434602387593590/">report issue</a>';
    echo '<br><a href="https://web.facebook.com/groups/clubhousethailandcommunity">Clubhouse Thailand Community Facebook Group</a>';
}

echo $today . "\n";
generate_page('public/index.html', fn($e) => $e['date'] >= $today, $today, 'home');

foreach (array_keys($past_dates) as $date) {
    generate_page('public/' . $date . '.html', fn($e) => substr($e['date'], 0, 10) == $date, $date, 'archive');
}

?>
