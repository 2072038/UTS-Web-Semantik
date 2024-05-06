<html>
    <body>
        <h1>News Search</h1>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <label for="labelSearch">Search:</label>
        <input type="text" name="keyword">
        <input type="submit">
    </form>

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
        );
    
        array_push($list, $item);
    }
    
    $numberofresults = 20;
        
    for($i=0; $i<$numberofresults; $i++) {
        $title = $list[$i]['title'];
        $titletolower= strtolower($title);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = htmlspecialchars($_POST['keyword']);
            if (strpos($titletolower, $name) !== false) {
                $link = $list[$i]['link'];
                $description = $list[$i]['descript'];
                $date = date('l F d, Y', strtotime($list[$i]['date']));
                echo '<p><strong><a href="'.$link.'" title="'.$title.'">'.$title.'</a></strong><br />';
                echo '<small><em>Posted on '.$date.'</em></small></p>';
                echo $list[$i]['descript'];
                $found++;
            }
            
        }
    }

    if ($found == 0) {
        echo "No results have been found";
    }
    ?>
</body>
</html>