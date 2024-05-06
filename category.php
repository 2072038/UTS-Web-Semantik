<html>
    <body>
        <h1>News Category</h1>
</body>
</html>

<?php
$rss = new DOMDocument();
$rss->load('https://rss.nytimes.com/services/xml/rss/nyt/HomePage.xml');
$list = array();
$found=0;
        
foreach ($rss->getElementsByTagName('item') as $node) {
    $item = array ( 
        'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
        'descript' => $node->getElementsByTagName('description')->item(0)->nodeValue,
        'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
        'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
        'category' => $node->getElementsByTagName('category')->item(0)->nodeValue,
    );
    
    array_push($list, $item);
}

$numberofresults = 20;
        
for($i=0; $i<$numberofresults; $i++) {
    $title = $list[$i]['title'];
    $link = $list[$i]['link'];
    $description = $list[$i]['descript'];
    $date = date('l F d, Y', strtotime($list[$i]['date']));
    $category = $list[$i]['category'];
    echo '<p><strong><a href="'.$link.'" title="'.$title.'">'.$title.'</a></strong><br />';
    echo '<small><em>Posted on '.$date.'</em></small></p>';
    echo $list[$i]['descript'];
    echo '<br>Category: ' . $category;
}
?>