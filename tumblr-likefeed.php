<?php

/*
 * by Luka Kladaric, allixsenos@gmail.com
 * https://github.com/allixsenos/tumblr-likefeed
 * 
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/ or send a
 * letter to Creative Commons, 444 Castro Street, Suite 900, Mountain View, California, 94041, USA.
 */

// configurable part
$email = "derp@derp.com";
$password = "derrrp";
$num = 50; // maximum is 50, you may lower it if you so desire

$rsstitle = "Likes"; // title of your rss feed
$rsslink = "http://derp.com/"; // where your rss feed links to
$rssdesc = "by derp"; // rss feed description

// END CONFIGURABLE PART

$url = "http://www.tumblr.com/api/likes?email={$email}&password={$password}&num={$num}";

$xml = simplexml_load_file($url);

$items = array();
foreach ($xml->posts->post as $p) {
    if ('photo' == (string)$p->Attributes()->type) { // support only photo type posts
        $item = array(
            'title' => (string)$p->tumblelog->Attributes()->title,
            'url' => (string)$p->Attributes()->url,
            'date' => (string)$p->Attributes()->date,
            'caption' => (string)$p->{'photo-caption'},
            'photos' => array((string)$p->{'photo-url'}[0]),
        );

        if ($p->photoset) {
            $item['photos'] = array();
            foreach ($p->photoset->photo as $pp) {
                $item['photos'][] = (string)$pp->{'photo-url'}[0];
            }
        }

        $items[] = $item;
    }
}

echo '<?xml version="1.0"?>';
?>

<rss version="2.0">
    <channel>
        <title><?= $rsstitle ?></title>
        <link><?= $rsslink ?></link>
        <description><?= $rssdesc ?></description>
    <? foreach ($items as $i): ?>
        <item>
            <title><![CDATA[<?= $i['title'] ?>]]></title>
            <link><?= $i['url'] ?></link>
            <description><? foreach ($i['photos'] as $p): ?>&lt;img src="<?= $p ?>" /&gt;&lt;br /&gt;<? endforeach; ?><?= htmlspecialchars($i['caption']) ?></description>
        </item>
    <? endforeach; ?>
    </channel>
</rss>
